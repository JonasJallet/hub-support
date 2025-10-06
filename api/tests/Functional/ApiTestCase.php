<?php

namespace App\Tests\Functional;

use App\Domain\Entity\User;
use App\Infrastructure\Persistence\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Contracts\Translation\TranslatorInterface;
use Zenstruck\Browser\HttpOptions;
use Zenstruck\Browser\KernelBrowser;
use Zenstruck\Browser\Test\HasBrowser;
use Zenstruck\Foundry\Test\ResetDatabase;

abstract class ApiTestCase extends WebTestCase
{
    use HasBrowser {
        browser as baseKernelBrowser;
    }

    protected ?TranslatorInterface $translator;

    use ResetDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->translator = $this->getContainer()->get('translator');
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->translator = null;
    }

    protected function browser(array $options = [], array $server = []): KernelBrowser
    {
        return $this->baseKernelBrowser($options, $server)
            ->setDefaultHttpOptions(
                HttpOptions::create()
                    ->withHeader('Accept', 'application/json')
            );
    }

    protected function loginAsUser(): array
    {
        $user = UserFactory::new()->create(['email' => 'user@mail.com']);

        $responseLogin = $this->browser()
            ->post('/api/login', [
                'json' => [
                    'email' => $user->getEmail(),
                    'password' => UserFactory::PASSWORD,
                ],
            ])->assertStatus(200);


        $responseDecoded = $responseLogin->json()->decoded();

        return $responseDecoded['data'];
    }

    protected function translateExceptionMessage(string $translationKey, array $payload): string
    {
        return $this->translator->trans($translationKey, $payload, 'exceptions', 'en');
    }

    protected function translateSuccessMessage(string $translationKey, array $payload): string
    {
        return $this->translator->trans($translationKey, $payload, 'success', 'en');
    }
}
