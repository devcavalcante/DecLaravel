<?php
use Tests\TestCase;
use App\Models\Member;
use App\Http\Controllers\MemberController;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Feature\Utils\LoginUsersTrait;
use App\Enums\TypeUserEnum;
use Illuminate\Support\Facades\Passport;

class MemberControllerTest extends TestCase
{
    use DatabaseTransactions;
    use LoginUsersTrait;

    // Teste de listagem de membros
    public function testIndexMembers()
    {
        $this->login(TypeUserEnum::REPRESENTATIVE); // Faz login como representante
        Member::factory(10)->create(); // Cria 10 membros no banco de dados

        // Envia uma solicitação para listar todos os membros
        $response = $this->get('/api/members');
        $response->assertStatus(200); // Verifica se a resposta é bem-sucedida (status HTTP 200)
    }

    // Teste que um usuário sem permissão não pode listar membros
    public function testShouldNotListMembersWithoutPermission()
    {
        $this->login(TypeUserEnum::VIEWER); // Faz login como visualizador (sem permissão para listar membros)
        Member::factory(10)->create(); // Cria 10 membros no banco de dados

        // Envia uma solicitação para listar membros e espera uma resposta proibida
        $response = $this->get('/api/members');
        $response->assertStatus(403); // Verifica se a resposta é proibida (status HTTP 403)
    }

    // Teste para recuperar um membro que não existe
    public function testIndexNotExistsMember()
    {
        $this->login(TypeUserEnum::REPRESENTATIVE); // Faz login como representante

        $invalidId = 999; // Cria um ID de membro inválido

        // Envia uma solicitação para recuperar um membro que não existe
        $response = $this->getJson('/api/members/' . $invalidId);
        $response->assertStatus(404); // Verifica se a resposta é um erro de não encontrado (status HTTP 404)
    }

    // Teste de exibição de um membro
    public function testShowMember()
    {
        $this->login(TypeUserEnum::REPRESENTATIVE); // Faz login como representante

        // Cria um membro no banco de dados usando a fábrica de modelos
        $member = Member::factory()->create();

        // Envia uma solicitação para exibir o membro
        $response = $this->getJson('/api/members/' . $member->id);
        $response->assertStatus(200); // Verifica se a resposta é bem-sucedida (status HTTP 200)
    }

    // Teste para exibir um membro que não existe
    public function testShowNotExistMember()
    {
        $this->login(TypeUserEnum::REPRESENTATIVE); // Faz login como representante

        $invalidId = 999; // Cria um ID de membro inválido

        // Envia uma solicitação para exibir um membro que não existe
        $response = $this->getJson('/api/members/' . $invalidId);
        $response->assertStatus(404); // Verifica se a resposta é um erro de não encontrado (status HTTP 404)
    }

    // Teste de exclusão de um membro
    public function testDestroyMember()
    {
        $userLogged = $this->login(TypeUserEnum::REPRESENTATIVE); // Faz login como representante
        $member = Member::factory(['creator_user_id' => $userLogged->id])->create(); // Cria um membro com o usuário logado como criador

        // Envia uma solicitação para excluir o membro
        $response = $this->deleteJson('/api/members/' . $member->id);
        $response->assertStatus(204); // Verifica se a resposta é bem-sucedida (status HTTP 204)
        $this->assertSoftDeleted('members', ['id' => $member->id]); // Verifica se o membro foi excluído logicamente no banco de dados
    }

    // Teste que um usuário sem permissão não pode excluir um membro
    public function testShouldNotDestroyUserWithoutPermission()
    {
        $userLogged = $this->login(TypeUserEnum::REPRESENTATIVE); // Faz login como representante
        $member = Member::factory()->create(); // Cria um membro

        // Envia uma solicitação para excluir o membro e espera uma resposta proibida
        $response = $this->deleteJson('/api/members/' . $member->id);
        $response->assertStatus(403); // Verifica se a resposta é proibida (status HTTP 403)
    }

    // Teste de exclusão de um membro que não existe
    public function testDestroyNonExistingMember()
    {
        $this->login(TypeUserEnum::REPRESENTATIVE); // Faz login como representante

        $invalidId = 999; // Cria um ID de membro inválido

        // Envia uma solicitação para excluir um membro que não existe e espera um erro de não encontrado
        $response = $this->deleteJson('/api/members/' . $invalidId);
        $response->assertStatus(404); // Verifica se a resposta é um erro de não encontrado (status HTTP 404)
    }

    // Teste de atualização de um membro
    public function testShouldUpdate()
    {
        $user = $this->login(TypeUserEnum::REPRESENTATIVE); // Faz login como representante

        // Cria um membro com o usuário logado como criador
        $member = Member::factory(['creator_user_id' => $user->id])->create();

        // Envia uma solicitação para atualizar a função do membro
        $response = $this->put(sprintf('api/members/%s', $member->id), ['role' => 'nova_funcao']);

        $response->assertStatus(200); // Verifica se a resposta é bem-sucedida (status HTTP 200)
    }

    // Teste que um usuário sem permissão não pode atualizar um membro
    public function testShouldNotUpdateWithoutPermission()
    {
        $this->login(TypeUserEnum::REPRESENTATIVE); // Faz login como representante
        $member = Member::factory()->create(); // Cria um membro

        // Envia uma solicitação para atualizar a função do membro e espera uma resposta proibida
        $response = $this->put(sprintf('api/members/%s', $member->id), ['role' => 'nova_funcao']);
        $response->assertStatus(403); // Verifica se a resposta é proibida (status HTTP 403)
    }

    // Teste que um usuário não pode atualizar outros membros
    public function testShouldNotUpdateOthersMembers()
    {
        $this->login(TypeUserEnum::REPRESENTATIVE); // Faz login como representante
        $member = Member::factory()->create(); // Cria um membro

        // Envia uma solicitação para atualizar a função de outro membro e espera uma resposta proibida
        $response = $this->put(sprintf('api/members/%s', $member->id), ['role' => 'nova_funcao']);
        $response->assertStatus(403); // Verifica se a resposta é proibida (status HTTP 403)
    }
}
