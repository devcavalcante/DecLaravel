<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\TypeUser;

class DestroyTypeUserRequestTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Teste de sucesso: Verificar se é possível excluir um tipo de usuário existente.
     *
     * @return void
     */
    public function testDestroyTypeUser()
    {
        // Cria um tipo de usuário no banco de dados usando o model factory
        $typeUser = TypeUser::factory()->create();

        // Envia uma solicitação para excluir o tipo de usuário criado
        $response = $this->deleteJson('/api/group/type-user/' . $typeUser->id);

        // Verifica se a solicitação foi bem-sucedida e se a resposta está vazia (204)
        $response->assertStatus(204);

        // Verifica se o tipo de usuário foi removido corretamente do banco de dados
        $this->assertDatabaseMissing('type_users', ['id' => $typeUser->id]);
    }

    /**
     * Teste de falha: Verificar se a exclusão de um tipo de usuário inexistente retorna um erro 404.
     *
     * @return void
     */
    public function testDestroyNonExistingTypeUser()
    {
        // Cria um ID inválido para um tipo de usuário inexistente
        $invalidId = 999;

        // Envia uma solicitação para excluir o tipo de usuário inexistente
        $response = $this->deleteJson('/api/group/type-user/' . $invalidId);

        // Verifica se a solicitação retornou um erro 404
        $response->assertStatus(404);
    }
}
