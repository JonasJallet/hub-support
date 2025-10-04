<?php

namespace App\Infrastructure\Controller\Api\SupportCase;

use App\Application\Bus\Command\CommandBus;
use App\Application\Bus\Query\QueryBus;
use App\Application\Command\SupportCase\AddSupportCase\AddSupportCase;
use App\Application\Query\SupportCase\ReadSupportCase\ReadSupportCase;
use App\Infrastructure\Utils\ResponseFormatter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;
use Throwable;

#[Route('support-cases', name: 'api_support_case_')]
#[OA\Tag(name: 'Support Case')]
class SupportCaseController extends AbstractController
{
    public function __construct(
        private readonly CommandBus $commandBus,
        private readonly QueryBus $queryBus,
        private readonly ResponseFormatter $responseFormatter,
    ) {
    }

    #[Route('', name: 'add', methods: ['POST'])]
    #[OA\Post(
        path: '/support-cases',
        description: 'Add a new support case',
        summary: 'Add a new support case'
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'year', type: 'integer', example: 2024),
                new OA\Property(property: 'month', type: 'integer', example: 2),
            ]
        )
    )]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Support Case successfully created',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'message', type: 'string'),
                new OA\Property(property: 'status', type: 'string'),
                new OA\Property(property: 'data', type: 'object')
            ]
        )
    )]
    #[OA\Response(
        response: Response::HTTP_BAD_REQUEST,
        description: 'Invalid input data provided'
    )]
    #[OA\Response(
        response: Response::HTTP_INTERNAL_SERVER_ERROR,
        description: 'Server error occurred while processing the request'
    )]
    public function add(#[MapRequestPayload] AddSupportCase $addForm): JsonResponse
    {
        try {
            $this->commandBus->dispatch($addForm);

            $query = new ReadSupportCase();
            $query->id = $addForm->id;
            $form = $this->queryBus->ask($query);

            return new JsonResponse(
                $this->responseFormatter->formatResponse(
                    "Success.Entity.Added",
                    ['%entity%' => 'Support Case'],
                    'success',
                    $form
                )
                , Response::HTTP_OK
            );
        } catch (Throwable $exception) {
            [$response, $status] = $this->responseFormatter->formatException($exception);


            return new JsonResponse(
                $response,
                $status
            );
        }
    }

}
