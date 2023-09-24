<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
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
 *     type="Authorization",
 *     name="Authorization",
 *     in="header",
 *     securityScheme="Authorization"
 * )
 */
class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}
