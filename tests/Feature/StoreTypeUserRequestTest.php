<?php

namespace Tests\Feature;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Validation\ValidationException;
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
        $response = $this->postJson('/api/type_users', [
            'name' => 'Administrator', // Valor válido para o campo "name"
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'name' => 'Administrator',
            ]);
    }

    /**
     * Teste de erro: Enviar um valor inválido para o campo "name" e verificar se a validação falha.
     *
     * @return void
     */
    public function testValidationFailed()
    {
        $response = $this->postJson('/api/type_users', [
            'name' => 'abc', // Valor inválido para o campo "name" (menos de 4 caracteres)
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    /**
     * Teste de erro: Enviar um valor inválido para o campo "name" e verificar se a mensagem de erro personalizada é retornada corretamente na resposta JSON.
     *
     * @return void
     */
    public function testCustomErrorMessage()
    {
        $response = $this->postJson('/api/type_users', [
            'name' => 'abc', // Valor inválido para o campo "name" (menos de 4 caracteres)
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'name' => 'O campo nome deve ter no mínimo 4 caracteres.',
            ]);
    }

    /**
     * Teste de erro: Enviar uma solicitação sem o campo "name" e verificar se a validação falha e retorna a mensagem de erro correta.
     *
     * @return void
     */
    public function testValidationFailedMissingName()
    {
        $response = $this->postJson('/api/type_users', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }
}
