<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\TreatmentPlan;
use App\Models\TreatmentPlanStage;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AppointmentSessionService
{
    public function update(
        User $actor,
        Appointment $appointment,
        array $input,
    ): Appointment {
        return DB::transaction(function () use (
            $actor,
            $appointment,
            $input,
        ): Appointment {
            $session = Appointment::query()
                ->lockForUpdate()
                ->findOrFail($appointment->getKey());

            abort_unless(
                $actor->isPsychologist()
                && (int) $session->psychologist_id === (int) $actor->id,
                403
            );

            // مدیریت جلسه از چرخه پرداخت و لغو جدا است.
            if ($session->status !== Appointment::STATUS_CONFIRMED) {
                throw ValidationException::withMessages([
                    'appointment' => [
                        'فقط نوبت تأییدشده قابل مدیریت است.',
                    ],
                ]);
            }

            $data = Validator::make($input, [
                'therapy_session_type' => [
                    'required',
                    Rule::in([
                        Appointment::THERAPY_TYPE_FREE,
                        Appointment::THERAPY_TYPE_PLAN,
                    ]),
                ],

                'treatment_plan_id' => [
                    'nullable',
                    'required_if:therapy_session_type,plan',
                    'integer',
                    'min:1',
                ],

                'treatment_plan_stage_id' => [
                    'nullable',
                    'required_if:therapy_session_type,plan',
                    'integer',
                    'min:1',
                ],

                'attendance_status' => [
                    'required',
                    Rule::in([
                        Appointment::ATTENDANCE_PENDING,
                        Appointment::ATTENDANCE_HELD,
                        Appointment::ATTENDANCE_ABSENT,
                    ]),
                ],

                'meeting_url' => [
                    'nullable',
                    'string',
                    'url:http,https',
                    'max:2048',
                ],

                'session_subject' => [
                    'nullable',
                    'string',
                    'max:255',
                ],

                'session_discussion' => [
                    'nullable',
                    'string',
                    'max:10000',
                ],

                'session_recommendations' => [
                    'nullable',
                    'string',
                    'max:10000',
                ],

                'session_exercises' => [
                    'nullable',
                    'string',
                    'max:10000',
                ],
            ])->validate();

            $planId = null;
            $stageId = null;

            if (
                $data['therapy_session_type']
                === Appointment::THERAPY_TYPE_PLAN
            ) {
                $plan = TreatmentPlan::query()
                    ->whereKey($data['treatment_plan_id'])
                    ->where('client_id', $session->client_id)
                    ->where('psychologist_id', $session->psychologist_id)
                    ->lockForUpdate()
                    ->first();

                if (! $plan) {
                    throw ValidationException::withMessages([
                        'treatment_plan_id' => [
                            'مسیر درمان انتخاب‌شده متعلق به این مراجع و مشاور نیست.',
                        ],
                    ]);
                }
// ارتباط تاریخی موجود حفظ می‌شود، اما اتصال جدید به مسیر
// غیرفعال یا تغییر مرحله آن مجاز نیست.
                $isExistingPlanAssignment =
                    $session->therapy_session_type === Appointment::THERAPY_TYPE_PLAN
                    && (int) $session->treatment_plan_id === (int) $plan->id
                    && (int) $session->treatment_plan_stage_id
                    === (int) $data['treatment_plan_stage_id'];

                if (
                    $plan->status !== TreatmentPlan::STATUS_ACTIVE
                    && ! $isExistingPlanAssignment
                ) {
                    throw ValidationException::withMessages([
                        'treatment_plan_id' => [
                            'اتصال جلسه جدید به مسیر درمان آرشیوشده مجاز نیست.',
                        ],
                    ]);
                }

                $stage = TreatmentPlanStage::query()
                    ->whereKey($data['treatment_plan_stage_id'])
                    ->where('treatment_plan_id', $plan->id)
                    ->lockForUpdate()
                    ->first();

                if (! $stage) {
                    throw ValidationException::withMessages([
                        'treatment_plan_stage_id' => [
                            'مرحله انتخاب‌شده متعلق به این مسیر درمان نیست.',
                        ],
                    ]);
                }

                $planId = $plan->id;
                $stageId = $stage->id;
            }

            $session->therapy_session_type =
                $data['therapy_session_type'];

            $session->treatment_plan_id = $planId;
            $session->treatment_plan_stage_id = $stageId;

            $session->attendance_status =
                $data['attendance_status'];

            // نبودن یک فیلد در ورودی به معنای پاک‌کردن آن نیست.
            foreach ([
                         'session_subject',
                         'session_discussion',
                         'session_recommendations',
                         'session_exercises',
                     ] as $field) {
                if (array_key_exists($field, $data)) {
                    $session->{$field} = $data[$field];
                }
            }

            if ($session->session_type === Appointment::SESSION_TYPE_ONLINE) {
                if (array_key_exists('meeting_url', $data)) {
                    $session->meeting_url = $data['meeting_url'];
                }
            } else {
                $session->meeting_url = null;
            }

            $session->save();

            return $session;
        });
    }
}
