<?php

namespace App\Http\Controllers;

use App\Mail\ResetPasswordCodeMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class PasswordResetController extends Controller
{
    // Enviar codigo
    public function sendResetCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ]);

        $code = random_int(100000, 999999);

        $record = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if ($record && now()->diffInSeconds($record->created_at) < 60) {
            return response()->json([
                'error' => 'Espera antes de solicitar otro código'
            ], 429);
        }

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'token' => Hash::make($code),
                'created_at' => now()
            ]
        );

        Mail::to($request->email)->send(new ResetPasswordCodeMail($code));

        return response()->json([
            'message' => 'Se ha enviado un código de recuperación a tu correo'
        ]);
    }

    // Resetear contraseña
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'code' => 'required',
            'password' => 'required|min:6|confirmed'
        ]);

        $record = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$record) {
            return response()->json([
                'error' => 'Código inválido'
            ], 400);
        }

        if (now()->diffInMinutes($record->created_at) > 10) {
            return response()->json([
                'error' => 'El código expiró'
            ], 400);
        }

        if (!Hash::check($request->code, $record->token)) {
            return response()->json([
                'error' => 'Código incorrecto'
            ], 400);
        }

        User::where('email', $request->email)->update([
            'password' => Hash::make($request->password)
        ]);

        DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->delete();

        return response()->json([
            'message' => 'Contraseña actualizada correctamente'
        ]);
    }
}
