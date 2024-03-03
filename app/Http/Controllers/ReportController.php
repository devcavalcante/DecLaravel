<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReportRequest;
use App\Services\ReportService;
use Illuminate\Support\Arr;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * @OA\Tag(
 *     name="reports",
 *     description="Download de relatórios dos grupos"
 * )
 */
class ReportController extends Controller
{
    public function __construct(
        private readonly ReportService $reportService,
    ) {
    }

    /**
     * @OA\Get(
     *   path="/groups/{groupId}/download/",
     *   tags={"reports"},
     *   summary="Faz download do relatório baseado no ID",
     *   description="faz download do relatório baseado no ID",
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="Id do grupo",
     *     required=true,
     *     @OA\Schema(
     *         type="string"
     *     )
     *   ),
     *    @OA\RequestBody(
     *      @OA\MediaType(
     *          mediaType="application/json",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="filters",
     *                  type="object",
     *                  @OA\Property(
     *                      property="withFiles",
     *                      type="boolean",
     *                      example=true
     *                  )
     *              )
     *          )
     *      )
     *  ),
     *   @OA\Response(
     *     response=404,
     *     description="group not found"
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Ok"
     *   ),
     * )
     */
    public function downloadById(ReportRequest $request, string $id): BinaryFileResponse
    {
        $filename = $this->reportService->uploadById($id, $request->get('filters'));
        $headers = [];

        if (preg_match('/\.zip$/', $filename)) {
            $headers = ["Content-Type" => "application/zip"];
        }

        return response()->download($filename, null, $headers)->deleteFileAfterSend();
    }

    /**
     * @OA\Get(
     *   path="/groups/download/",
     *   tags={"reports"},
     *   summary="Faz download do relatório baseado no ID",
     *   description="faz download do relatório baseado no ID",
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="Id do grupo",
     *     required=true,
     *     @OA\Schema(
     *         type="string"
     *     )
     *   ),
     *    @OA\RequestBody(
     *      @OA\MediaType(
     *          mediaType="application/json",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="filters",
     *                  type="object",
     *                  @OA\Property(
     *                      property="status",
     *                      type="string",
     *                      example="EM ANDAMENTO"
     *                  ),
     *                  @OA\Property(
     *                       property="start_date",
     *                       type="string",
     *                       example="2023-03-03"
     *                  ),
     *                  @OA\Property(
     *                        property="end_date",
     *                        type="string",
     *                        example="2023-03-03"
     *                  )
     *              )
     *          )
     *      )
     *  ),
     *   @OA\Response(
     *     response=404,
     *     description="group not found"
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Ok"
     *   ),
     * )
     */
    public function download(ReportRequest $request): BinaryFileResponse
    {
        $filename = $this->reportService->uploadMany($request->get('filters'));
        return response()->download($filename)->deleteFileAfterSend();
    }
}
