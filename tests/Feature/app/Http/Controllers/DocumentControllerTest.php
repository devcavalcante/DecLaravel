<?php

namespace Tests\Feature\app\Http\Controllers;

use App\Enums\TypeUserEnum;
use App\Models\Document;
use App\Models\Group;
use App\Models\Representative;
use App\Models\TypeUser;
use App\Models\User;
use Faker\Factory as FakerFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\Feature\Utils\LoginUsersTrait;
use Tests\TestCase;

class DocumentControllerTest extends TestCase
{
    use DatabaseTransactions;
    use LoginUsersTrait;

    const BASE_URL = 'api/groups';

    public function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub
        $this->faker = FakerFactory::create();
        $this->artisan('db:seed --class=TypeGroupSeeder');
        $this->artisan('db:seed --class=TypeUserSeeder');
        $this->artisan('db:seed --class=UserSeeder');
    }

    public function testShouldListAll()
    {
        $group = Group::factory()->create();
        Document::factory(['group_id' => $group->id])->create();
        Document::factory(['group_id' => $group->id])->create();

        $this->login(TypeUserEnum::REPRESENTATIVE);

        $response = $this->get(sprintf('%s/%s/documents', self::BASE_URL, $group->id));

        $response->assertStatus(200);
        $this->assertCount(2, json_decode($response->getContent(), true));
    }

    public function testShouldListOne()
    {
        $group = Group::factory()->create();
        $document = Document::factory(['group_id' => $group->id])->create();
        $this->login(TypeUserEnum::REPRESENTATIVE);

        $response = $this->get(sprintf('%s/%s/documents/%s', self::BASE_URL, $group->id, $document->id));

        $response->assertStatus(200);
        $response->assertJsonStructure($this->getJsonStructure());
    }

    public function testNotShouldListOneWhenNotFoundDocument()
    {
        $group = Group::factory()->create();

        $this->login(TypeUserEnum::REPRESENTATIVE);

        $response = $this->get(sprintf('%s/%s/documents/%s', self::BASE_URL, $group->id, 100));

        $response->assertStatus(404);
        $this->assertEquals('Documento não encontrado', json_decode($response->getContent(), true)['errors']);
    }

    public function testShouldCreateIsRepresentative()
    {
        $userRepresentative = $this->login(TypeUserEnum::REPRESENTATIVE);
        $representative = Representative::factory(['user_id' => $userRepresentative->id])->create();
        $group = Group::factory(['representative_id' => $representative->id])->create();

        $file = UploadedFile::fake()->create('file.pdf');
        $payload = [
            'file'        => $file,
        ];

        $response = $this->post(sprintf('%s/%s/documents', self::BASE_URL, $group->id), $payload);
        $response->assertStatus(201);
        $response->assertJsonStructure(['file']);
        $response = json_decode($response->getContent(), true);
        $this->assertDatabaseHas('documents', $response);
    }

    public function testShouldCreateIsAdmin()
    {
        $userAdmin = $this->login(TypeUserEnum::ADMIN);
        $representative = Representative::factory(['user_id' => $userAdmin->id])->create();
        $group = Group::factory(['representative_id' => $representative->id])->create();

        $file = UploadedFile::fake()->create('file.pdf');
        $payload = [
            'file'        => $file,
        ];

        $response = $this->post(sprintf('%s/%s/documents', self::BASE_URL, $group->id), $payload);
        $response->assertStatus(201);
        $response->assertJsonStructure(['file']);
        $response = json_decode($response->getContent(), true);
        $this->assertDatabaseHas('documents', $response);
    }

    public function testShouldNotCreateWhenGroupNotFound()
    {
        $userRepresentative = $this->login(TypeUserEnum::REPRESENTATIVE);
        $representative = Representative::factory(['user_id' => $userRepresentative->id])->create();
        Group::factory(['representative_id' => $representative->id])->create();

        $payload = [
            'file' => UploadedFile::fake()->create('file.pdf'),
        ];

        $response = $this->post(sprintf('%s/%s/documents', self::BASE_URL, 100), $payload);

        $response->assertStatus(404);
        $this->assertEquals('Grupo não encontrado', json_decode($response->getContent(), true)['errors']);
    }

    public function testShouldNotCreateWhenIsNotTheRepresentativeOfGroup()
    {
        $typeUser = TypeUser::where('name', TypeUserEnum::REPRESENTATIVE)->first();
        $this->login(TypeUserEnum::REPRESENTATIVE);
        $user1 = User::factory(['type_user_id' => $typeUser->id])->create();
        $representative = Representative::factory(['user_id' => $user1->id])->create();
        $group = Group::factory(['representative_id' => $representative->id])->create();

        $payload = [
            'file' => UploadedFile::fake()->create('file.pdf'),
        ];

        $response = $this->post(sprintf('%s/%s/documents', self::BASE_URL, $group->id), $payload);

        $response->assertStatus(403);
        $this->assertEquals('This action is unauthorized.', json_decode($response->getContent(), true)['errors']);
    }

    public function testShouldDeleteIsRepresentative()
    {
        $userRepresentative = $this->login(TypeUserEnum::REPRESENTATIVE);
        $representative = Representative::factory(['user_id' => $userRepresentative->id])->create();
        $group = Group::factory(['representative_id' => $representative->id])->create();
        $document = Document::factory(['group_id' => $group->id])->create();

        $response = $this->delete(sprintf('%s/%s/documents/%s', self::BASE_URL, $group->id, $document->id));

        $response->assertStatus(204);
        $this->assertDatabaseMissing('documents', $document->toArray());
    }

    public function testShouldDeleteIsAdmin()
    {
        $userAdmin = $this->login(TypeUserEnum::ADMIN);
        $representative = Representative::factory(['user_id' => $userAdmin->id])->create();
        $group = Group::factory(['representative_id' => $representative->id])->create();
        $document = Document::factory(['group_id' => $group->id])->create();

        $response = $this->delete(sprintf('%s/%s/documents/%s', self::BASE_URL, $group->id, $document->id));

        $response->assertStatus(204);
        $this->assertDatabaseMissing('documents', $document->toArray());
    }

    public function testShouldNotDeleteWhenGroupNotFound()
    {
        $userRepresentative = $this->login(TypeUserEnum::REPRESENTATIVE);
        User::factory()->create();
        $representative = Representative::factory(['user_id' => $userRepresentative->id])->create();
        $group = Group::factory(['representative_id' => $representative->id])->create();
        $document = Document::factory(['group_id' => $group->id])->create();

        $response = $this->delete(sprintf('%s/%s/documents/%s', self::BASE_URL, 100, $document->id));

        $response->assertStatus(404);
        $this->assertEquals('Grupo não encontrado', json_decode($response->getContent(), true)['errors']);
    }

    public function testShouldNotDeleteWhenDocumentNotFound()
    {
        $userRepresentative = $this->login(TypeUserEnum::REPRESENTATIVE);
        $representative = Representative::factory(['user_id' => $userRepresentative->id])->create();
        $group = Group::factory(['representative_id' => $representative->id])->create();

        $response = $this->delete(sprintf('%s/%s/documents/%s', self::BASE_URL, $group->id, 100));

        $response->assertStatus(404);
        $this->assertEquals('Documento não encontrado', json_decode($response->getContent(), true)['errors']);
    }

    public function testShouldNotDeleteWhenIsNotTheRepresentativeOfGroup()
    {
        $typeUser = TypeUser::where('name', TypeUserEnum::REPRESENTATIVE)->first();
        $this->login(TypeUserEnum::REPRESENTATIVE);
        $user1 = User::factory(['type_user_id' => $typeUser->id])->create();
        $representative = Representative::factory(['user_id' => $user1->id])->create();
        $group = Group::factory(['representative_id' => $representative->id])->create();
        $document = Document::factory(['group_id' => $group->id])->create();

        $response = $this->delete(sprintf('%s/%s/documents/%s', self::BASE_URL, $group->id, $document->id));

        $response->assertStatus(403);
        $this->assertEquals('This action is unauthorized.', json_decode($response->getContent(), true)['errors']);
    }

    public function testShouldDownload()
    {
        $this->login(TypeUserEnum::VIEWER);
        $file = UploadedFile::fake()->create('file.pdf');
        $file =  Storage::disk('local')->put('docs', $file);

        $group = Group::factory()->create();
        $document = Document::factory()->create(['file' => $file, 'group_id' => $group->id]);

        $response = $this->get(sprintf('%s/%s/documents/%s/download', self::BASE_URL, $group->id, $document->id));

        $response->assertStatus(200);
        Storage::disk('local')->delete($file);
    }

    private function getJsonStructure(): array
    {
        return [
            'id',
            'name',
            'file',
            'file_size',
            'created_at',
            'updated_at',
            'group_id',
        ];
    }
}
