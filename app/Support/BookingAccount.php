<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

final class BookingAccount
{
    public static function complete(?User $user): bool
    {
        return $user !== null
            && trim((string) $user->first_name) !== ''
            && trim((string) $user->last_name) !== ''
            && $user->birth_date !== null
            && $user->birth_date->format('Y-m-d') <= now('Asia/Tehran')->format('Y-m-d');
    }

    public static function payload(?User $user): array
    {
        return [
            'authenticated' => $user !== null,
            'user' => $user === null ? null : [
                'id' => $user->getKey(),
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'mobile' => $user->mobile,
                'role' => $user->role,
                'birth_date_jalali' => $user->birth_date === null ? null : PersianDate::format($user->birth_date),
            ],
            'profile_complete' => self::complete($user),
        ];
    }

    public static function save(Request $request, User $user): void
    {
        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'birth_date_jalali' => ['required', 'string', 'max:10'],
        ]);

        try {
            $birthDate = PersianDate::birthDateToGregorian($data['birth_date_jalali']);
        } catch (\InvalidArgumentException) {
            throw ValidationException::withMessages([
                'birth_date_jalali' => 'تاریخ تولد شمسی معتبر وارد کنید؛ مانند ۱۳۷۵/۰۶/۱۵. تاریخ نباید در آینده باشد.',
            ]);
        }

        // Explicit allowlist: never accept mobile, role or other account attributes.
        $user->update([
            'first_name' => trim($data['first_name']),
            'last_name' => trim($data['last_name']),
            'birth_date' => $birthDate,
        ]);
    }
}
