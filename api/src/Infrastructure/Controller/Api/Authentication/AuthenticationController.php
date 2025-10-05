<?php

namespace App\Infrastructure\Controller\Api\Authentication;

use App\Application\Bus\Query\QueryBus;
use App\Application\Query\User\ReadUser\ReadUser;
use App\Infrastructure\Persistence\Repository\DoctrineUserRepository;
use App\Infrastructure\Security\Authentication\Authentication;
use App\Infrastructure\Utils\ResponseFormatter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Throwable;

#[OA\Tag(name: 'Authentication')]
class AuthenticationController extends AbstractController
{
    public function __construct(
        private readonly QueryBus $queryBus,
        private readonly Authentication $authentication,
        private readonly ResponseFormatter $responseFormatter,
        private readonly DoctrineUserRepository $doctrineUserRepository
    ) {
    }


    #[Route('/login', name: 'login', methods: ['POST'])]
    #[OA\Post(
        summary: "Login user",
        requestBody: new OA\RequestBody(
            required: true,
            content: [
                new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: "email",
                            description: "User email",
                            type: "varchar(180)"
                        ),
                        new OA\Property(
                            property: "password",
                            description: "User password",
                            type: "varchar(255)"
                        ),
                    ],
                    type: "object",
                    example: [
                        "email" => "mail@support.com",
                        "password" => "example",
                    ]
                ),
            ]
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "User is connected",
            ),
            new OA\Response(
                response: 404,
                description: "User not found",
            ),
            new OA\Response(
                response: 500,
                description: "Incorrectly password",
            ),
        ]
    )]
    #[IsGranted("PUBLIC_ACCESS")]
    public function login(Request $request): JsonResponse
    {
        try {
            $result = json_decode($request->getContent());

            $readUser = new ReadUser();
            $readUser->email = $result->email;
            $user = $this->queryBus->ask($readUser);

            $userEntity = $this->doctrineUserRepository->find($user->id);

            $token = $this->authentication->ensureLoginIsValid($userEntity, $result->password);
            $userEntity->updateLastLogin();

            return new JsonResponse(
                $this->responseFormatter->formatResponse(
                    "Success.Authentication.Login",
                    [],
                    'success',
                    array_merge((array)$user, ['token' => $token])
                ),
                Response::HTTP_OK
            );
        } catch (Throwable $exception) {
            [$response, $status] = $this->responseFormatter->formatException($exception);
            return new JsonResponse($response, $status);
        }
    }
}
