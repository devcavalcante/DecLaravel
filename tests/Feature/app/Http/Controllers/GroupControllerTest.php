<?php

namespace Tests\Feature\app\Http\Controllers;

use App\Enums\TypeUserEnum;
use App\Models\Group;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Feature\Utils\LoginUsersTrait;
use Tests\TestCase;

class GroupControllerTest extends TestCase
{
    use DatabaseTransactions;
    use LoginUsersTrait;

    const BASE_URL = 'api/group';

//    public function testShouldListAll()
//    {
//        $this->login(TypeUserEnum::MANAGER);
//        Group::factory(10)->create();
//
//        $response = $this->get(sprintf('%s/1', self::BASE_URL));
//
//        dd($response);
//    }
}
