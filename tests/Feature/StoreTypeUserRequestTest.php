<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Http\Requests\StoreTypeUserRequest;

class StoreTypeUserRequestTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Teste de sucesso: Enviar um valor válido para o campo "name" e verificar se a validação passa.
     *
     * @return void
     */
    public function testValidationSuccess()
    {
        $response = $this->postJson('/api/group/type-user', [
            'name' => 'Administrador', // Valor válido para o campo "name"
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'name' => 'Administrador',
            ]);
    }

    public function testValidationFailedMissingName()
    {
        // Tenta criar um tipo de usuário sem fornecer o campo "name"
        $response = $this->postJson('/api/group/type-user', []);

        // Verifica se a solicitação falhou devido à validação
        $response->assertStatus(422);
    }

    public function testValidationFailedInvalidDataType()
    {

        $response = $this->postJson('/api/group/type-user', [
            "name" => 123
        ]);

        // Verifica se a resposta JSON contém o fragmento de erro esperado
        $response->assertStatus(422);
    }

    public function testValidationFailedNameTooShort()
    {
        // Dados inválidos para o campo "name" (menos de 4 caracteres)
        $response = $this->postJson('/api/group/type-user',[
            "name" => "abc"
        ]);

        // Verifica se a resposta JSON contém o fragmento de erro esperado
        $response->assertStatus(422);
    }
}
