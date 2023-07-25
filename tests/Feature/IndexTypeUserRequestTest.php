<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\TypeUser;

class IndexTypeUserRequestTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Teste de sucesso: Verificar se é possível listar todos os tipos de usuários existentes.
     *
     * @return void
     */
    public function testIndexTypeUsers()
    {
        // Cria dois tipos de usuários no banco de dados usando o model factory
        $typeUser1 = TypeUser::factory()->create();
        $typeUser2 = TypeUser::factory()->create();

        // Envia uma solicitação para listar todos os tipos de usuários
        $response = $this->getJson('/api/group/type-user');

        // Verifica se a solicitação foi bem-sucedida e se a resposta contém os tipos de usuários
        $response->assertStatus(200)
            ->assertJson([
                [
                    'id' => $typeUser1->id,
                    'name' => $typeUser1->name,
                ],
                [
                    'id' => $typeUser2->id,
                    'name' => $typeUser2->name,
                ],
            ]);
    }

    /**
     * Teste de sucesso: Verificar se a listagem de tipos de usuários está vazia quando não há nenhum no banco de dados.
     *
     * @return void
     */
    public function testIndexEmptyTypeUsers()
    {
        // Envia uma solicitação para listar todos os tipos de usuários quando não há nenhum no banco de dados
        $response = $this->getJson('/api/group/type-user');

        // Verifica se a solicitação foi bem-sucedida e se a resposta está vazia
        $response->assertStatus(200)
            ->assertJson([]);
    }
}
