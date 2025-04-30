<?php
declare(strict_types=1);

namespace App\Tests\api\Client;

use App\Tests\api\TestDataFixtures\ClientFixtures;
use App\Tests\api\TestDataFixtures\UserFixtures;
use App\Tests\Support\ApiTester;
use Codeception\Util\HttpCode;
final class ClientUpdateAPICest
{
    public function _before(ApiTester $I): void
    {
        $I->loadFixtures([UserFixtures::class, ClientFixtures::class]);
    }

    public function tryToUpdateClientWithValidData(ApiTester $I): void
    {
        $I->wantTo('Update an authenticated client with valid data');

        $I->haveHttpHeader('Content-Type', 'application/json');

        $I->sendPOST('/login', [
            'email' => 'ana@garcia.com',
            'password' => 'passw',
        ]);
        $I->seeResponseCodeIs(HttpCode::OK);
        $token = $I->grabDataFromResponseByJsonPath('token')[0];

        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);

        $updateData = [
            'address' => 'Nueva Dirección 123',
        ];

        $I->sendPUT('/client/update', $updateData);

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();

        $I->seeResponseContainsJson([
            'message' => 'Client address updated successfully',
            'client' => [
                'email' => 'ana@garcia.com',
                'name' => 'Ana',
                'surnames' => 'García García',
                'address' => 'Nueva Dirección 123',
                'phone_number' => '666 666 666',
                'roles' => ['ROLE_CLIENT']
            ]
        ]);
    }

    public function tryToUpdateClientWithoutToken(ApiTester $I): void
    {
        $I->wantTo('Fail to update client without authentication');

        $I->haveHttpHeader('Content-Type', 'application/json');

        $I->sendPUT('/client/update', [
            'address' => 'Nueva Dirección 456',
            'phone_number' => '622 222 222',
        ]);

        $I->seeResponseCodeIs(HttpCode::UNAUTHORIZED);
    }

    public function tryToUpdateClientWithEmptyData(ApiTester $I): void
    {
        $I->wantTo('Fail to update client with empty data');

        $I->haveHttpHeader('Content-Type', 'application/json');

        $I->sendPOST('/login', [
            'email' => 'ana@garcia.com',
            'password' => 'passw',
        ]);
        $I->seeResponseCodeIs(HttpCode::OK);
        $token = $I->grabDataFromResponseByJsonPath('token')[0];

        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $I->sendPUT('/client/update', []);

        $I->seeResponseCodeIs(HttpCode::UNAUTHORIZED);
        $I->seeResponseContainsJson([
            'error' => 'There is no data to update',
        ]);
    }
}