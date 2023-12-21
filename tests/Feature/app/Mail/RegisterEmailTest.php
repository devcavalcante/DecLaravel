<?php

namespace Tests\Feature\app\Mail;

use App\Enums\TypeUserEnum;
use App\Mail\RegisterEmail;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class RegisterEmailTest extends TestCase
{
    public function testShouldSendRegisterEmail()
    {
        // Configuração do mock
        Mail::fake();

        // Seu código que envia o e-mail
        $representative = 'representante@example.com';
        Mail::to($representative)->send(new RegisterEmail(TypeUserEnum::REPRESENTATIVE));

        // Verificação do envio do e-mail
        Mail::assertSent(RegisterEmail::class, function ($mail) use ($representative) {
            return $mail->to[0]['address'] === $representative;
        });
    }
}
