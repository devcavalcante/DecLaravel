<?php

namespace Tests\Feature\app\Http\Controllers;

use App\Models\TypeGroup;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TypeGroupControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function testIndexTypeGroups()
    {
        // Cria dois tipos de grupos no banco de dados usando o model factory
        $typeGroup1 = TypeGroup::factory()->create();
        $typeGroup2 = TypeGroup::factory()->create();

        // Envia uma solicitação para listar todos os tipos de grupos
        $response = $this->getJson('/api/group/type-group');

        // Verifica se a solicitação foi bem-sucedida e se a resposta contém os tipos de grupos
        $response->assertStatus(200)
            ->assertJson([
                [
                    'id'   => $typeGroup1->id,
                    'name' => $typeGroup1->name,
                ],
                [
                    'id'   => $typeGroup2->id,
                    'name' => $typeGroup2->name,
                ],
            ]);
    }

    /**
     * Teste de sucesso: Verificar se a listagem de tipos de grupos está vazia quando não há nenhum no banco de dados.
     *
     * @return void
     */
    public function testIndexEmptyTypeGroups()
    {
        // Envia uma solicitação para listar todos os tipos de grupos quando não há nenhum no banco de dados
        $response = $this->getJson('/api/group/type-group');

        // Verifica se a solicitação foi bem-sucedida e se a resposta está vazia
        $response->assertStatus(200)
            ->assertJson([]);
    }

    public function testShowTypeGroup()
    {
        // Cria um tipo de grupo no banco de dados usando o model factory
        $typeGroup = TypeGroup::factory()->create();

        // Envia uma solicitação para exibir o tipo de grupo criado
        $response = $this->getJson('/api/group/type-group/' . $typeGroup->id);

        // Verifica se a solicitação foi bem-sucedida e se os dados retornados são corretos
        $response->assertStatus(200)
            ->assertJson([
                'id'   => $typeGroup->id,
                'name' => $typeGroup->name,
            ]);
    }

    /**
     * Teste de falha: Verificar se um tipo de grupo inexistente retorna um erro 404.
     *
     * @return void
     */
    public function testShowNotExistsTypeGroup()
    {
        // Cria um ID inválido para um tipo de grupo inexistente
        $invalidId = 999;

        // Envia uma solicitação para exibir o tipo de grupo inexistente
        $response = $this->getJson('/api/group/type-group/' . $invalidId);

        // Verifica se a solicitação retornou um erro 404
        $response->assertStatus(404);
    }

    public function testValidationSuccess()
    {
        $response = $this->postJson('/api/group/type-group', [
            'name' => 'Comitê', // Valor válido para o campo "name"
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'name' => 'Comitê',
            ]);
    }

    public function testValidationFailedMissingName()
    {
        // Tenta criar um tipo de grupo sem fornecer o campo "name"
        $response = $this->postJson('/api/group/type-group', []);

        // Verifica se a solicitação falhou devido à validação
        $response->assertStatus(422);
    }

    public function testValidationFailedInvalidDataType()
    {

        $response = $this->postJson('/api/group/type-group', [
            "name" => 123,
        ]);

        // Verifica se a resposta JSON contém o fragmento de erro esperado
        $response->assertStatus(422);
    }

    public function testValidationFailedNameTooShort()
    {
        // Dados inválidos para o campo "name" (menos de 4 caracteres)
        $response = $this->postJson('/api/group/type-group', [
            "name" => "abc",
        ]);

        // Verifica se a resposta JSON contém o fragmento de erro esperado
        $response->assertStatus(422);
    }

    public function testValidationFailedOnUpdate()
    {
        // Dados inválidos para o campo "name" (menos de 4 caracteres)
        $data = [
            "name" => "abc",
        ];

        // Obtenha um tipo de grupo existente do banco de dados
        $typeGroup = TypeGroup::factory()->create();

        $response = $this->putJson('/api/group/type-group/' . $typeGroup->id, $data);

        // Verifica se a solicitação falhou devido à validação
        $response->assertStatus(422);

        // Verifica se o modelo TypeGroup não foi atualizado após a solicitação falhada
        $typeGroup->refresh();
        $this->assertNotEquals($typeGroup->name, $data['name']);
    }

    /**
     * Teste de sucesso: Atualizar o tipo de usuário com um valor válido para o campo "name".
     *
     * @return void
     */
    public function testUpdateSuccess()
    {
        // Cria um tipo de grupo no banco de dados
        $typeGroup = TypeGroup::factory()->create();

        // Dados válidos para o campo "name"
        $data = [
            "name" => "Novo Nome",
        ];

        $response = $this->putJson('/api/group/type-group/' . $typeGroup->id, $data);

        // Verifica se a solicitação foi bem-sucedida
        $response->assertStatus(201);

        // Verifica se o modelo TypeGroup foi atualizado corretamente
        $typeGroup->refresh();
        $this->assertEquals($typeGroup->name, $data['name']);
    }

    /**
     * Teste de erro: Tente atualizar o tipo de grupo sem fornecer o campo "name".
     *
     * @return void
     */
    public function testUpdateFailedMissingName()
    {
        // Cria um tipo de grupo no banco de dados
        $typeGroup = TypeGroup::factory()->create();

        // Tenta atualizar o tipo de grupo sem fornecer o campo "name"
        $response = $this->putJson('/api/group/type-group/' . $typeGroup->id, []);

        // Verifica se a solicitação falhou devido à validação
        $response->assertStatus(422);
    }

    public function testDestroyTypeGroup()
    {
        // Cria um tipo de grupo no banco de dados usando o model factory
        $typeGroup = TypeGroup::factory()->create();

        // Envia uma solicitação para excluir o tipo de grupo criado
        $response = $this->deleteJson('/api/group/type-group/' . $typeGroup->id);

        // Verifica se a solicitação foi bem-sucedida e se a resposta está vazia (204)
        $response->assertStatus(204);

        // Verifica se o tipo de grupo foi removido corretamente do banco de dados
        $this->assertDatabaseMissing('type_groups', ['id' => $typeGroup->id]);
    }

    /**
     * Teste de falha: Verificar se a exclusão de um tipo de grupo inexistente retorna um erro 404.
     *
     * @return void
     */
    public function testDestroyNonExistingTypeGroup()
    {
        // Cria um ID inválido para um tipo de grupo inexistente
        $invalidId = 999;

        // Envia uma solicitação para excluir o tipo de grupo inexistente
        $response = $this->deleteJson('/api/group/type-group/' . $invalidId);

        // Verifica se a solicitação retornou um erro 404
        $response->assertStatus(404);
    }
}

