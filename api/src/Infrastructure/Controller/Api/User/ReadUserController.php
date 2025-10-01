<?php

namespace App\Infrastructure\Controller\Api\User;

use App\Application\Bus\Query\QueryBus;
use App\Application\Query\User\ReadUser\ReadUser;
use App\Infrastructure\Utils\ResponseFormatter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;
use Throwable;

#[Route('users', name: 'api_user_')]
#[OA\Tag(name: 'User')]
final class ReadUserController extends AbstractController
{
    public function __construct(
        private readonly QueryBus $queryBus,
        private readonly ResponseFormatter $responseFormatter,
    ) {
    }

    #[Route('/{email}', name: 'get', methods: ['GET'])]
    #[OA\Parameter(
        name: 'email',
        description: 'User email',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'string')
    )]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Returns user details'
    )]
    #[OA\Response(
        response: Response::HTTP_NOT_FOUND,
        description: 'User not found'
    )]
    public function read(string $email): JsonResponse
    {
        try {
            $readUser = new ReadUser();
            $readUser->email = $email;
            $user = $this->queryBus->ask($readUser);

            return new JsonResponse(
                $this->responseFormatter->formatResponse(
                    "Success.Entity.Retrieved",
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
