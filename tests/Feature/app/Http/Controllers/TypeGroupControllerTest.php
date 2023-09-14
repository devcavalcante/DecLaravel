<?php

namespace Tests\Feature\app\Http\Controllers;

use App\Enums\TypeUserEnum;
use App\Models\TypeGroup;
use App\Models\TypeUser;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Laravel\Passport\Passport;
use Tests\Feature\Utils\LoginUsersTrait;
use Tests\TestCase;

class TypeGroupControllerTest extends TestCase
{
    use DatabaseTransactions;
    use LoginUsersTrait;

    public function testIndexTypeGroups()
    {
        $this->login(TypeUserEnum::REPRESENTATIVE);
        // Cria 10 tipos de grupos no banco de dados usando o model factory
        TypeGroup::factory(10)->create();

        // Envia uma solicitação para listar todos os tipos de grupos
        $response = $this->getJson('/api/type-group');
        $actual = json_decode($response->getContent(), true);

        // Verifica se a solicitação foi bem-sucedida e se a resposta contém os tipos de grupos
        $response->assertStatus(200);
        $this->assertEquals(TypeGroup::all()->toArray(), $actual);
    }

    /**
     * Teste de sucesso: Verificar se a listagem de tipos de grupos está vazia quando não há nenhum no banco de dados.
     *
     * @return void
     */
    public function testIndexEmptyTypeGroups()
    {
        $this->login(TypeUserEnum::REPRESENTATIVE);

        // Envia uma solicitação para listar todos os tipos de grupos quando não há nenhum no banco de dados
        $response = $this->getJson('/api/type-group');

        // Verifica se a solicitação foi bem-sucedida e se a resposta está vazia
        $response->assertStatus(200)
            ->assertJson([]);
    }

    public function testShowTypeGroup()
    {
        $this->login(TypeUserEnum::REPRESENTATIVE);

        // Cria um tipo de grupo no banco de dados usando o model factory
        $typeGroup = TypeGroup::factory()->create();

        // Envia uma solicitação para exibir o tipo de grupo criado
        $response = $this->getJson('/api/type-group/' . $typeGroup->id);

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
        $this->login(TypeUserEnum::REPRESENTATIVE);

        // Cria um ID inválido para um tipo de grupo inexistente
        $invalidId = 999;

        // Envia uma solicitação para exibir o tipo de grupo inexistente
        $response = $this->getJson('/api/type-group/' . $invalidId);

        // Verifica se a solicitação retornou um erro 404
        $response->assertStatus(404);
    }

    public function testValidationSuccess()
    {
        $this->login(TypeUserEnum::REPRESENTATIVE);

        $response = $this->postJson('/api/type-group', [
            'name' => 'Comitê', // Valor válido para o campo "name"
            'type_group' => 'Interno', //Valor válido para o campo "type_group"
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'name' => 'Comitê',
                'type_group' => 'Interno',
            ]);
    }

    public function testValidationFailedMissingName()
    {
        $this->login(TypeUserEnum::REPRESENTATIVE);

        // Tenta criar um tipo de grupo sem fornecer o campo "name"
        $response = $this->postJson('/api/type-group', []);

        // Verifica se a solicitação falhou devido à validação
        $response->assertStatus(422);
    }

    public function testValidationFailedInvalidDataType()
    {
        $this->login(TypeUserEnum::REPRESENTATIVE);

        $response = $this->postJson('/api/type-group', [
            "name" => 123,
        ]);

        // Verifica se a resposta JSON contém o fragmento de erro esperado
        $response->assertStatus(422);
    }

    public function testValidationFailedNameTooShort()
    {
        $this->login(TypeUserEnum::REPRESENTATIVE);

        // Dados inválidos para o campo "name" (menos de 4 caracteres)
        $response = $this->postJson('/api/type-group', [
            "name" => "abc",
        ]);

        // Verifica se a resposta JSON contém o fragmento de erro esperado
        $response->assertStatus(422);
    }

    public function testValidationFailedOnUpdate()
    {
        $this->login(TypeUserEnum::REPRESENTATIVE);

        // Dados inválidos para o campo "name" (menos de 4 caracteres)
        $data = [
            "name" => "abc",
        ];

        // Obtenha um tipo de grupo existente do banco de dados
        $typeGroup = TypeGroup::factory()->create();

        $response = $this->putJson('/api/type-group/' . $typeGroup->id, $data);

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
        $this->login(TypeUserEnum::REPRESENTATIVE);

        // Cria um tipo de grupo no banco de dados
        $typeGroup = TypeGroup::factory()->create();

        // Dados válidos para o campo "name"
        $data = [
            "name" => "Novo Nome",
        ];

        $response = $this->putJson('/api/type-group/' . $typeGroup->id, $data);

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
        $this->login(TypeUserEnum::REPRESENTATIVE);

        // Cria um tipo de grupo no banco de dados
        $typeGroup = TypeGroup::factory()->create();

        // Tenta atualizar o tipo de grupo sem fornecer o campo "name"
        $response = $this->putJson('/api/type-group/' . $typeGroup->id, []);

        // Verifica se a solicitação falhou devido à validação
        $response->assertStatus(422);
    }

    public function testDestroyTypeGroup()
    {
        $this->login(TypeUserEnum::REPRESENTATIVE);

        // Cria um tipo de grupo no banco de dados usando o model factory
        $typeGroup = TypeGroup::factory()->create();

        // Envia uma solicitação para excluir o tipo de grupo criado
        $response = $this->deleteJson('/api/type-group/' . $typeGroup->id);

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
    public function testDestroyNotExistingTypeGroup()
    {
        $this->login(TypeUserEnum::REPRESENTATIVE);

        // Cria um ID inválido para um tipo de grupo inexistente
        $invalidId = 999;

        // Envia uma solicitação para excluir o tipo de grupo inexistente
        $response = $this->deleteJson('/api/type-group/' . $invalidId);

        // Verifica se a solicitação retornou um erro 404
        $response->assertStatus(404);
    }

    public function testShouldNotListWithoutPermission()
    {
        $this->login(TypeUserEnum::ADMIN);
        TypeUser::factory(10)->create();

        $response = $this->getJson('/api/type-group');
        $response->assertStatus(403);
    }

    public function testShouldNotListOneWithoutPermission()
    {
        $this->login(TypeUserEnum::ADMIN);
        TypeUser::factory(10)->create();

        $response = $this->getJson(sprintf('/api/type-group/%s', 1));
        $response->assertStatus(403);
    }

    public function testShouldNotUpdateWithoutPermission()
    {
        $data = [
            "name" => "Novo Nome",
        ];

        $this->login(TypeUserEnum::ADMIN);
        TypeUser::factory(10)->create();

        $response = $this->put(sprintf('/api/type-group/%s', 1), $data);
        $response->assertStatus(403);
    }

    public function testShouldNotDestroyWithoutPermission()
    {
        $this->login(TypeUserEnum::ADMIN);
        TypeUser::factory(10)->create();

        $response = $this->getJson(sprintf('/api/type-group/%s', 1));
        $response->assertStatus(403);
    }
}
