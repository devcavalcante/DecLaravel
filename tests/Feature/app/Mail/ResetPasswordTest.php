<?php

namespace Tests\Feature\app\Mail;

use App\Enums\TypeUserEnum;
use App\Mail\ResetPassword; //
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ResetPasswordTest extends TestCase
{
    public function testShouldSendResetPasswordEmail()
    {
        // Configuração do mock
        Mail::fake();

        // Simule o envio do e-mail de redefinição
        $representative = 'representante@example.com';
        $token = 'token-de-redefinicao-de-senha'; // Gere o token necessário
        Mail::to($representative)->send(new ResetPassword($token));

        // Verifique se o e-mail de redefinição foi enviado corretamente
        Mail::assertSent(ResetPassword::class, function ($mail) use ($representative, $token) {
            return $mail->to[0]['address'] === $representative &&
                $mail->token === $token; // Verifique se o token está presente
        });
    }
}
