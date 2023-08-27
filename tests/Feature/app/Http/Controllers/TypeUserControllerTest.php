<?php

namespace Tests\Feature\app\Http\Controllers;

use App\Models\TypeUser;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class TypeUserControllerTest extends TestCase
{
    use DatabaseMigrations;

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
                                 'id'   => $typeUser1->id,
                                 'name' => $typeUser1->name,
                             ],
                             [
                                 'id'   => $typeUser2->id,
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

    public function testShowTypeUser()
    {
        // Cria um tipo de usuário no banco de dados usando o model factory
        $typeUser = TypeUser::factory()->create();

        // Envia uma solicitação para exibir o tipo de usuário criado
        $response = $this->getJson('/api/group/type-user/' . $typeUser->id);

        // Verifica se a solicitação foi bem-sucedida e se os dados retornados são corretos
        $response->assertStatus(200)
            ->assertJson([
                             'id'   => $typeUser->id,
                             'name' => $typeUser->name,
                         ]);
    }

    /**
     * Teste de falha: Verificar se um tipo de usuário inexistente retorna um erro 404.
     *
     * @return void
     */
    public function testShowNotExistsTypeUser()
    {
        // Cria um ID inválido para um tipo de usuário inexistente
        $invalidId = 999;

        // Envia uma solicitação para exibir o tipo de usuário inexistente
        $response = $this->getJson('/api/group/type-user/' . $invalidId);

        // Verifica se a solicitação retornou um erro 404
        $response->assertStatus(404);
    }

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
            "name" => 123,
        ]);

        // Verifica se a resposta JSON contém o fragmento de erro esperado
        $response->assertStatus(422);
    }

    public function testValidationFailedNameTooShort()
    {
        // Dados inválidos para o campo "name" (menos de 4 caracteres)
        $response = $this->postJson('/api/group/type-user', [
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

        // Obtenha um tipo de usuário existente do banco de dados
        $typeUser = TypeUser::factory()->create();

        $response = $this->putJson('/api/group/type-user/' . $typeUser->id, $data);

        // Verifica se a solicitação falhou devido à validação
        $response->assertStatus(422);

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
            "name" => "Novo Nome",
        ];

        $response = $this->putJson('/api/group/type-user/' . $typeUser->id, $data);

        // Verifica se a solicitação foi bem-sucedida
        $response->assertStatus(201);

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
    }

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
