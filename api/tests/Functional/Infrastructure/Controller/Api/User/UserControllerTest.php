<?php

namespace App\Tests\Functional\Infrastructure\Controller\Api\User;

use App\Tests\Functional\ApiTestCase;
use Zenstruck\Browser\Json;
use Zenstruck\Foundry\Test\Factories;
use Symfony\Component\HttpFoundation\Response;

class UserControllerTest extends ApiTestCase
{
    use Factories;

    public function testAuthenticateUser(): void
    {
        $loggedUser = $this->loginAsUser();

        $response = $this->browser()->get('/api/users/authenticate', [
            'headers' => ['Authorization' => 'Bearer ' . $loggedUser['token']],
        ])
            ->assertStatus(Response::HTTP_OK);

        $response->use(function (Json $response) use ($loggedUser) {
            $response->assertHas('email');
            $response->assertThat('email', fn(Json $email) => $email->equals($loggedUser['email']));
        });
    }

    public function testAddUser(): void
    {
        $userToCreate = [
            'email' => 'jonas.j@decq.com',
            'firstName' => 'Jonas',
            'lastName' => 'J',
            'password' => 'Password1234!',
        ];

        $response = $this->browser()->post('/api/users', [
            'json' => $userToCreate,
        ])
            ->assertStatus(Response::HTTP_OK);

        $response->use(function (Json $response) {
            $response->assertThat(
                'message',
                fn(Json $message) => $message->equals(
                    $this->translateSuccessMessage(
                        'Success.Entity.Added',
                        ['%entity%' => 'User'],
                        'fr'
                    )
                )
            );

            $response->assertHas('data');
            $response->assertThat('data', function (Json $data) {
                $data->assertHas('id');
                $data->assertHas('email');
                $data->assertHas('firstName');
                $data->assertHas('lastName');
                $data->assertThat('email', fn(Json $email) => $email->equals('jonas.j@decq.com'));
                $data->assertThat('firstName', fn(Json $firstName) => $firstName->equals('Jonas'));
                $data->assertThat('lastName', fn(Json $lastName) => $lastName->equals('J'));
            });
        });
    }

    public function testReadUser(): void
    {
        $loggedUser = $this->loginAsUser();

        $response = $this->browser()->get('/api/users/' . $loggedUser['email'], [
            'headers' => ['Authorization' => 'Bearer ' . $loggedUser['token']],
        ])
            ->assertStatus(Response::HTTP_OK);

        $response->use(function (Json $response) use ($loggedUser) {
            $response->assertHas('data');

            $response->assertThat('data', function (Json $data) use ($loggedUser) {
                $data->assertHas('id');
                $data->assertHas('email');
                $data->assertHas('firstName');
                $data->assertHas('lastName');

                $data->assertThat('email', fn(Json $email) => $email->equals($loggedUser['email']));
            });
        });
    }

    public function testResetForgottenPassword(): void
    {
        $loggedUser = $this->loginAsUser();

        $response = $this->browser()->patch('/api/users/reset-password', [
            'json' => [
                'email' => $loggedUser['email'],
                'password' => 'NewPassword123!',
            ],
            'headers' => ['Authorization' => 'Bearer ' . $loggedUser['token']],
        ])
            ->assertStatus(Response::HTTP_OK);

        $response->use(function (Json $response) {
            $response->assertThat(
                'message',
                fn(Json $message) => $message->equals(
                    $this->translateSuccessMessage(
                        'Success.User.ResetPassword',
                        [],
                        'fr'
                    )
                )
            );
        });
    }
}
