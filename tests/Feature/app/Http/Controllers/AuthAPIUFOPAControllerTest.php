<?php

namespace Tests\Feature\app\Http\Controllers;

use App\Enums\TypeUserEnum;
use App\Http\Controllers\Auth\AuthAPIUFOPAController;
use App\Models\TypeUser;
use App\Models\User;
use App\Services\Auth\AuthAPIService;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use InvalidArgumentException;
use Laravel\Passport\Passport;
use Mockery;
use Tests\Feature\Utils\LoginUsersTrait;
use Tests\TestCase;

class AuthAPIUFOPAControllerTest extends TestCase
{
    public function testShouldRedirect()
    {
        $authServiceMock = $this->getMockBuilder(AuthAPIService::class)
            ->disableOriginalConstructor()
            ->getMock();

        // Defina a URL esperada para redirecionamento
        $expectedUrl = 'http://auth-server.com/authorize?client_id=your_client_id&response_type=code&redirect_uri=http://your-redirect-uri.com';

        // Configure o método getAuthorizationUrl no mock para retornar a URL esperada
        $authServiceMock->expects($this->once())
            ->method('getAuthorizationUrl')
            ->willReturn($expectedUrl);

        // Crie uma instância do controlador passando o mock do serviço de autenticação
        $controller = new AuthAPIUFOPAController($authServiceMock);

        // Chame o método redirect
        $response = $controller->redirect();

        // Verifique se a resposta é uma instância de RedirectResponse
        $this->assertInstanceOf(RedirectResponse::class, $response);

        // Verifique se a URL de redirecionamento está correta
        $this->assertEquals($expectedUrl, $response->getTargetUrl());
    }

    /**
     * @throws \Throwable
     * @throws GuzzleException
     */
    public function testSuccesfulCreateUserWithAPI()
    {
        // Mock para o serviço de autenticação
        $authServiceMock = Mockery::mock(AuthAPIService::class);
        $authServiceMock->shouldReceive('loginWithAPIUFOPA')->andReturn(collect(['user' => 'test_user']));

        // Crie uma instância do controlador passando o mock do serviço de autenticação
        $controller = new AuthAPIUFOPAController($authServiceMock);

        // Mock da função request() global
        $requestMock = Mockery::mock('alias:Illuminate\Support\Facades\Request');
        $requestMock->shouldReceive('query')->with('code')->andReturn('example_code');

        // Chame o método handleCallback
        $response = $controller->handleCallback();

        // Verifique se a resposta é uma instância de JsonResponse
        $this->assertInstanceOf(JsonResponse::class, $response);

        // Verifique se a resposta contém os dados esperados
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals(['user' => 'test_user'], $responseData);
    }

    /**
     * @throws \Throwable
     * @throws GuzzleException
     */
    public function testShouldNotCreateWithoutCode()
    {
        $authServiceMock = Mockery::mock(AuthAPIService::class);

        // Ajuste a expectativa do método loginWithAPIUFOPA para lançar a exceção ao receber null
        $authServiceMock->shouldReceive('loginWithAPIUFOPA')->withArgs([null])->andThrow(new InvalidArgumentException('É necessário o código'));

        // Crie uma instância do controlador passando o mock do serviço de autenticação
        $controller = new AuthAPIUFOPAController($authServiceMock);

        // Mock da função request() global com código nulo
        $requestMock = Mockery::mock('alias:Illuminate\Support\Facades\Request');
        $requestMock->shouldReceive('query')->with('code')->andReturnNull();

        // Chame o método handleCallback
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('É necessário o código');
        $response = $controller->handleCallback();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }
}
