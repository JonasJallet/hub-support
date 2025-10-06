<?php

namespace App\Tests\Functional\Infrastructure\Controller\Api\SupportCase;

use App\Tests\Functional\ApiTestCase;
use Zenstruck\Browser\Json;
use Zenstruck\Foundry\Test\Factories;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class SupportCaseControllerTest extends ApiTestCase
{
    use Factories;

    public function testAddSupportCase(): void
    {
        $loggedUser = $this->loginAsUser();

        $pdfContent = '%PDF-1.4\nSimulate attached log file content.\n%%EOF';
        $tempFile = tempnam(sys_get_temp_dir(), 'test_upload_');
        file_put_contents($tempFile, $pdfContent);

        $uploadedFile = new UploadedFile(
            $tempFile,
            'test_document.pdf',
            'application/pdf',
            null,
            true
        );

        $supportCaseToCreate = [
            'subject' => 'Critical issue server down',
            'message' => 'I cannot access my file.',
        ];

        $response = $this->browser()->post(
            '/api/support-cases',
            [
                'headers' => [
                    'Authorization' => 'Bearer ' . $loggedUser['token'],
                ],
                'files' => [
                    'file' => $uploadedFile,
                ],
                'body' => [
                    'subject' => $supportCaseToCreate['subject'],
                    'message' => $supportCaseToCreate['message'],
                ],
            ]
        );

        if (file_exists($tempFile)) {
            unlink($tempFile);
        }

        $response->use(function (Json $response) use ($supportCaseToCreate) {
            $response->assertThat(
                'message',
                fn(Json $message) => $message->equals(
                    $this->translateSuccessMessage(
                        'Success.Entity.Added',
                        ['%entity%' => 'Support Case'],
                        'fr'
                    )
                )
            );

            $response->assertHas('data');
            $response->assertThat('data', function (Json $data) use ($supportCaseToCreate) {
                $data->assertHas('id');
                $data->assertHas('subject');
                $data->assertHas('message');
                $data->assertThat('subject', fn(Json $subject) => $subject->equals($supportCaseToCreate['subject']));
                $data->assertThat('message', fn(Json $message) => $message->equals($supportCaseToCreate['message']));
            });
        });
    }

    public function testAddSupportCaseWithoutFile(): void
    {
        $loggedUser = $this->loginAsUser();

        $supportCaseToCreate = [
            'subject' => 'General inquiry about features',
            'message' => 'Can you explain how the premium plan works?',
        ];

        $response = $this->browser()->post(
            '/api/support-cases',
            [
                'headers' => [
                    'Authorization' => 'Bearer ' . $loggedUser['token'],
                ],
                'body' => [
                    'subject' => $supportCaseToCreate['subject'],
                    'message' => $supportCaseToCreate['message'],
                ],
            ]
        );

        $response->use(function (Json $response) use ($supportCaseToCreate) {
            $response->assertThat(
                'message',
                fn(Json $message) => $message->equals(
                    $this->translateSuccessMessage(
                        'Success.Entity.Added',
                        ['%entity%' => 'Support Case'],
                        'fr'
                    )
                )
            );

            $response->assertHas('data');
            $response->assertThat('data', function (Json $data) use ($supportCaseToCreate) {
                $data->assertHas('id');
                $data->assertHas('subject');
                $data->assertHas('message');
                $data->assertThat('subject', fn(Json $subject) => $subject->equals($supportCaseToCreate['subject']));
                $data->assertThat('message', fn(Json $message) => $message->equals($supportCaseToCreate['message']));
            });
        });
    }
}
