<?php

namespace App\Http\Controllers;

use App\Support\BookingAccount;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BookingAccountController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        return response()->json(BookingAccount::payload($request->user()))
            ->header('Cache-Control', 'no-store');
    }

    public function update(Request $request): JsonResponse
    {
        BookingAccount::save($request, $request->user());

        return $this->show($request);
    }
}
