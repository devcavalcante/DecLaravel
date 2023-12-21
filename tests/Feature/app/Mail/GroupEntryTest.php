<?php

namespace Tests\Feature\app\Mail;

use App\Enums\TypeUserEnum;
use App\Mail\GroupEntry;
use App\Mail\RegisterEmail;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class GroupEntryTest extends TestCase
{
    public function testShouldSendGroupEntry()
    {
        // Configuração do mock
        Mail::fake();

        // Seu código que envia o e-mail
        $representative = 'representante@example.com';
        Mail::to($representative)->send(new GroupEntry(TypeUserEnum::REPRESENTATIVE));

        // Verificação do envio do e-mail
        Mail::assertSent(GroupEntry::class, function ($mail) use ($representative) {
            return $mail->to[0]['address'] === $representative;
        });
    }
}
