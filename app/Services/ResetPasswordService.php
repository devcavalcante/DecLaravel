<?php

namespace App\Services;

use App\Mail\ResetPassword;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Password;

class ResetPasswordService
{
    public function sendResetPasswordEmail(string $email, string $token)
    {
        // Enviar e-mail de redefinição de senha
        Mail::to($email)->send(new ResetPassword($token));

        return 'E-mail de redefinição de senha enviado com sucesso!';
    }

    public function resetPassword(array $data)
    {
        $email = Arr::get($data, 'email');
        $token = Arr::get($data, 'token');
        $password = Arr::get($data, 'password');

        try {
            $status = Password::reset(
                $data,
                function ($user, $password) {
                    $user->password = $password;
                    $user->save();
                }
            );
            if ($status === Password::PASSWORD_RESET) {
                // Enviar e-mail de confirmação de senha redefinida
                $resetPasswordEmailService = new ResetPasswordService();
                $resetPasswordEmailService->sendResetPasswordEmail($email, $token);

                return __($status);
            } else {
                return __($status);
            }
        } catch (\Exception $e) {
            return __('Erro ao redefinir a senha');
        }
    }

}
