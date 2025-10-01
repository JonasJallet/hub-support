<?php

namespace App\Tests\Functional\Infrastructure\Controller\Api\Form;

use App\Domain\Factory\WorkingDayFactory;
use App\Tests\Functional\ApiTestCase;
use Zenstruck\Foundry\Test\Factories;

class AddSupportCaseControllerTest extends ApiTestCase
{
    use Factories;

    public function testAddFormSupport(): void
    {
        $response = $this->browser()
            ->post('/api/forms', [
                'json' => [

                    'year' => 2001,
                    'month' => 11,
                    'workingDays' => $workingDayIds,
                ],
            ])
            ->json();

        dd($response->decoded());
    }
}
