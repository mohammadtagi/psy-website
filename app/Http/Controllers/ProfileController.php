<?php

namespace App\Http\Controllers;

use App\Http\Requests\CompleteProfileRequest;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function edit(Request $request)
    {
        return view('profile.edit', ['user' => $request->user()]);
    }

    public function update(CompleteProfileRequest $request)
    {
        $request->user()->update($request->only('name', 'gender', 'birth_date'));

        return redirect()
            ->route('dashboard')
            ->with('status', 'پروفایل با موفقیت ذخیره شد.');
    }
}
