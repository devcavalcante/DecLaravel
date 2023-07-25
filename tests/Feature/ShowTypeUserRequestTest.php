<?php
namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\TypeUser;

class ShowTypeUserRequestTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Teste de sucesso: Verificar se é possível exibir um tipo de usuário existente.
     *
     * @return void
     */
    public function testShowTypeUser()
    {
        // Cria um tipo de usuário no banco de dados usando o model factory
        $typeUser = TypeUser::factory()->create();

        // Envia uma solicitação para exibir o tipo de usuário criado
        $response = $this->getJson('/api/group/type-user/' . $typeUser->id);

        // Verifica se a solicitação foi bem-sucedida e se os dados retornados são corretos
        $response->assertStatus(200)
            ->assertJson([
                'id' => $typeUser->id,
                'name' => $typeUser->name,
            ]);
    }

    /**
     * Teste de falha: Verificar se um tipo de usuário inexistente retorna um erro 404.
     *
     * @return void
     */
    public function testShowNonExistingTypeUser()
    {
        // Cria um ID inválido para um tipo de usuário inexistente
        $invalidId = 999;

        // Envia uma solicitação para exibir o tipo de usuário inexistente
        $response = $this->getJson('/api/group/type-user/' . $invalidId);

        // Verifica se a solicitação retornou um erro 404
        $response->assertStatus(404);
    }
}
