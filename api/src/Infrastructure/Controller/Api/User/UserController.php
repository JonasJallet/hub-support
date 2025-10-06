<?php

namespace App\Infrastructure\Controller\Api\User;

use App\Application\Bus\Command\CommandBus;
use App\Application\Bus\Query\QueryBus;
use App\Application\Command\User\AddUser\AddUser;
use App\Application\Command\User\ResetForgottenPassword\ResetForgottenPassword;
use App\Application\Query\User\ReadUser\ReadUser;
use App\Infrastructure\Utils\ResponseFormatter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Throwable;

#[Route('users', name: 'api_user_')]
#[OA\Tag(name: 'User')]
final class UserController extends AbstractController
{
    public function __construct(
        private readonly CommandBus $commandBus,
        private readonly QueryBus $queryBus,
        private readonly ResponseFormatter $responseFormatter,
    ) {
    }

    #[Route('/authenticate', name: 'authenticate', methods: ['GET'])]
    public function me(): JsonResponse
    {
        return $this->json([
            'email' => $this->getUser()?->getUserIdentifier(),
            'roles' => $this->getUser()?->getRoles(),
        ]);
    }

    #[Route('', name: 'add', methods: ['POST'])]
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
    #[IsGranted("PUBLIC_ACCESS")]
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

    #[Route('/{email}', name: 'read', methods: ['GET'])]
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

    #[Route("/reset-password", name: "_forgotten_password", methods: ["PATCH"])]
    #[OA\Patch(
        summary: "Reset forgotten password",
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'email', type: 'string', format: 'email'),
                new OA\Property(property: 'currentPassword', type: 'string'),
                new OA\Property(property: 'newPassword', type: 'string'),
            ]
        )
    )]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Valid change password'
    )]
    #[OA\Response(
        response: Response::HTTP_NOT_FOUND,
        description: 'User not found'
    )]
    public function resetForgottenPassword(#[MapRequestPayload] ResetForgottenPassword $resetForgottenPassword): JsonResponse
    {
        try {
            $this->commandBus->dispatch($resetForgottenPassword);

            return new JsonResponse(
                $this->responseFormatter->formatResponse(
                    "Success.User.ResetPassword",
                    [],
                    'success',
                )
            );

        } catch (Throwable $exception) {
            [$response, $status] = $this->responseFormatter->formatException($exception);

            return new JsonResponse($response, $status);
        }
    }
}
