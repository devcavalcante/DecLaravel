<?php

namespace Tests\Feature\app\Http\Controllers;

use App\Models\Meeting;
use App\Models\Group;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Arr;
use Tests\Feature\Utils\LoginUsersTrait;
use Tests\TestCase;

class MeetingControllerTest extends TestCase
{
    use DatabaseTransactions;
    use LoginUsersTrait;

    public function testShouldListAllMeetings()
    {
        $group = Group::factory()->create();
        Meeting::factory(['group_id' => $group->id])->create();

        $this->login();

        $response = $this->get("api/group/{$group->id}/meeting-history");

        $response->assertStatus(200);
        $this->assertCount(1, json_decode($response->getContent(), true)['data']);
    }

    public function testShouldListOneMeeting()
    {
        $group = Group::factory()->create();
        $meeting = Meeting::factory(['group_id' => $group->id])->create();

        $this->login();

        $response = $this->get("api/group/{$group->id}/meeting-history/{$meeting->id}");

        $response->assertStatus(200);
        $response->assertJsonStructure($this->getJsonStructure());
    }

    public function testNotShouldListOneMeetingWhenNotFoundMeeting()
    {
        $this->login();

        $response = $this->get("api/group/1/meeting-history/100");

        $response->assertStatus(404);
        $this->assertEquals('Reunião não encontrada', json_decode($response->getContent(), true)['errors']);
    }

    public function testShouldCreateMeeting()
    {
        $group = Group::factory()->create();
        $this->login();

        $payload = [
            'content' => 'Esta é uma reunião de teste',
            'summary' => 'Este é um resumo da reunião de teste',
            'ata'     => 'Esta é a ata da reunião de teste',
        ];

        $response = $this->post("api/group/{$group->id}/meeting-history", $payload);

        $response->assertStatus(201);
        $this->assertDatabaseHas('meetings', $payload);
    }

    public function testShouldUpdateMeeting()
    {
        $group = Group::factory()->create();
        $meeting = Meeting::factory(['group_id' => $group->id])->create();

        $this->login();

        $payload = [
            'content' => 'Esta é uma reunião de teste atualizada',
        ];

        $response = $this->put("api/group/{$group->id}/meeting-history/{$meeting->id}", $payload);

        $response->assertStatus(200);
        $this->assertDatabaseHas('meetings', Arr::merge($payload, ['id' => $meeting->id]));
    }

    public function testShouldDeleteMeeting()
    {
        $group = Group::factory()->create();
        $meeting = Meeting::factory(['group_id' => $group->id])->create();

        $this->login();

        $response = $this->delete("api/group/{$group->id}/meeting-history/{$meeting->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('meetings', $meeting->toArray());
    }

    private function getJsonStructure(): array
    {
        return [
            'data' => [
                'id',
                'content',
                'summary',
                'ata',
                'group_id',
                'created_at',
                'updated_at',
            ],
        ];
    }
}
