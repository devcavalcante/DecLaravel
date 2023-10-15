<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use League\Fractal\TransformerAbstract;
use OpenApi\Annotations as OA;

/**
 * @OA\OpenApi(
 *     security={{"Authorization": {}}},
 *     @OA\Info(
 *         version="1.0.0",
 *         title="DEC",
 *     ),
 *     @OA\Server(
 *         description="DEV",
 *         url="http://localhost:8001/api/",
 *     )
 * )
 * @OA\SecurityScheme(
 *     type="apiKey",
 *     name="Authorization",
 *     in="header",
 *     securityScheme="Authorization"
 * )
 */
class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function transform(
        TransformerAbstract $transformerAbstract,
        \Illuminate\Database\Eloquent\Collection|Model $data
    ): ?array
    {
        $manager = new Manager();
        $resource = $this->prepareData($data, $transformerAbstract);
        return $manager->createData($resource)->toArray();
    }

    public function prepareData(mixed $data, TransformerAbstract $transformer): Collection|Item
    {
        if ($data instanceof Model) {
            return new Item($data, $transformer);
        }

        return new Collection($data, $transformer);
    }
}
