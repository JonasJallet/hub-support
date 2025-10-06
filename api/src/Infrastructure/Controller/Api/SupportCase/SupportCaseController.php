<?php

namespace App\Infrastructure\Controller\Api\SupportCase;

use App\Application\Bus\Command\CommandBus;
use App\Application\Bus\Query\QueryBus;
use App\Application\Command\SupportCase\AddSupportCase\AddSupportCase;
use App\Application\Query\SupportCase\ReadSupportCase\ReadSupportCase;
use App\Infrastructure\Utils\ResponseFormatter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
        description: 'Create a new support case with optional file attachment',
        summary: 'Add a new support case',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: 'multipart/form-data',
                schema: new OA\Schema(
                    required: ['subject', 'message'],
                    properties: [
                        new OA\Property(
                            property: 'subject',
                            description: 'Subject of the support case',
                            type: 'string',
                            example: 'Problem with my account'
                        ),
                        new OA\Property(
                            property: 'message',
                            description: 'Message content',
                            type: 'string',
                            example: 'I cannot log in to my account.'
                        ),
                        new OA\Property(
                            property: 'file',
                            description: 'Optional file attachment',
                            type: 'string',
                            format: 'binary',
                            nullable: true
                        ),
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: 'Support Case successfully created',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string'),
                        new OA\Property(property: 'status', type: 'string'),
                        new OA\Property(property: 'data', type: 'object')
                    ]
                )
            ),
            new OA\Response(
                response: Response::HTTP_BAD_REQUEST,
                description: 'Invalid input data provided'
            ),
            new OA\Response(
                response: Response::HTTP_INTERNAL_SERVER_ERROR,
                description: 'Server error occurred while processing the request'
            ),
        ]
    )]
    public function add(Request $request): JsonResponse {
        try {
            $addSupportCase = new AddSupportCase();
            $addSupportCase->subject = $request->request->get('subject');
            $addSupportCase->message = $request->request->get('message');
            $addSupportCase->file = $request->files->get('file');

            $this->commandBus->dispatch($addSupportCase);

            $query = new ReadSupportCase();
            $query->id = $addSupportCase->id;
            $supportCase = $this->queryBus->ask($query);

            return new JsonResponse(
                $this->responseFormatter->formatResponse(
                    "Success.Entity.Added",
                    ['%entity%' => 'Support Case'],
                    'success',
                    $supportCase
                ),
                Response::HTTP_OK
            );
        } catch (Throwable $exception) {
            [$response, $status] = $this->responseFormatter->formatException($exception);

            return new JsonResponse($response, $status);
        }
    }
}
