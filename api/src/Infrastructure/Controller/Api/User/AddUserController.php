<?php

namespace App\Infrastructure\Controller\Api\User;

use App\Application\Bus\Command\CommandBus;
use App\Application\Bus\Query\QueryBus;
use App\Application\Command\User\AddUser\AddUser;
use App\Application\Query\User\ReadUser\ReadUser;
use App\Infrastructure\Utils\ResponseFormatter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;
use Throwable;

#[Route('users', name: 'api_user_')]
#[OA\Tag(name: 'User')]
final class AddUserController extends AbstractController
{
    public function __construct(
        private readonly CommandBus $commandBus,
        private readonly QueryBus $queryBus,
        private readonly ResponseFormatter $responseFormatter,
    ) {
    }

    #[Route('', name: 'post', methods: ['POST'])]
    #[OA\Post(
        path: '/users',
        description: 'Add a new user with the provided details',
        summary: 'Add a new user'
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'email', type: 'string', format: 'email'),
                new OA\Property(property: 'firstName', type: 'string'),
                new OA\Property(property: 'lastName', type: 'string'),
                new OA\Property(property: 'password', type: 'string', format: 'password')
            ]
        )
    )]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'User successfully created',
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
    public function add(#[MapRequestPayload] AddUser $addUser): JsonResponse
    {
        try {
            $this->commandBus->dispatch($addUser);

            $query = new ReadUser();
            $query->email = $addUser->email;
            $user = $this->queryBus->ask($query);

            return new JsonResponse(
                $this->responseFormatter->formatResponse(
                    "Success.Entity.Added",
                    ['%entity%' => 'User'],
                    'success',
                    $user
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
