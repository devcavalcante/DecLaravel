<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\TypeUser;
use App\Http\Requests\UpdateTypeUserRequest;

class UpdateTypeUserRequestTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Teste de erro: Enviar um valor inválido para o campo "name" e verificar se a validação falha.
     *
     * @return void
     */
    public function testValidationFailedOnUpdate()
    {
        // Dados inválidos para o campo "name" (menos de 4 caracteres)
        $data = [
            "name" => "abc"
        ];

        // Obtenha um tipo de usuário existente do banco de dados
        $typeUser = TypeUser::factory()->create();

        $response = $this->putJson('/api/group/type-user/' . $typeUser->id, $data);

        // Verifica se a solicitação falhou devido à validação
        $response->assertStatus(422);
           // ->assertJsonValidationErrors(['name']);

        // Verifica se o modelo TypeUser não foi atualizado após a solicitação falhada
        $typeUser->refresh();
        $this->assertNotEquals($typeUser->name, $data['name']);
    }

    /**
     * Teste de sucesso: Atualizar o tipo de usuário com um valor válido para o campo "name".
     *
     * @return void
     */
    public function testUpdateSuccess()
    {
        // Cria um tipo de usuário no banco de dados
        $typeUser = TypeUser::factory()->create();

        // Dados válidos para o campo "name"
        $data = [
            "name" => "Novo Nome"
        ];

        $response = $this->putJson('/api/group/type-user/' . $typeUser->id, $data);

        // Verifica se a solicitação foi bem-sucedida
        $response->assertStatus(201);
        //->assertJson([
           // 'name' => $data['name'],
        //]);

        // Verifica se o modelo TypeUser foi atualizado corretamente
        $typeUser->refresh();
        $this->assertEquals($typeUser->name, $data['name']);
    }

    /**
     * Teste de erro: Tente atualizar o tipo de usuário sem fornecer o campo "name".
     *
     * @return void
     */
    public function testUpdateFailedMissingName()
    {
        // Cria um tipo de usuário no banco de dados
        $typeUser = TypeUser::factory()->create();

        // Tenta atualizar o tipo de usuário sem fornecer o campo "name"
        $response = $this->putJson('/api/group/type-user/' . $typeUser->id, []);

        // Verifica se a solicitação falhou devido à validação
        $response->assertStatus(422);
            //->assertJsonValidationErrors(['name']);
    }
}
