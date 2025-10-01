<?php

namespace App\Infrastructure\Controller\Api\User;

use App\Application\Bus\Query\QueryBus;
use App\Application\Query\User\BrowseUsers\BrowseUsers;
use App\Infrastructure\Utils\ResponseFormatter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;
use Throwable;

#[Route('users', name: 'api_user_')]
#[OA\Tag(name: 'User')]
final class BrowseUserController extends AbstractController
{
    public function __construct(
        private readonly QueryBus          $queryBus,
        private readonly ResponseFormatter $responseFormatter,
    ) {
    }

    #[Route('', name: 'browse', methods: ['GET'])]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Returns users list'
    )]
    public function browse(): JsonResponse
    {
        try {
            $browseUsers = new BrowseUsers();
            $users = $this->queryBus->ask($browseUsers);

            return new JsonResponse(
                $this->responseFormatter->formatResponse(
                    "Success.Entity.RetrievedList",
                    ['%entity%' => 'User'],
                    'success',
                    $users
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
