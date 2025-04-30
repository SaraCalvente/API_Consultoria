<?php
declare(strict_types=1);

namespace App\Tests\api\Client;

use App\Tests\api\TestDataFixtures\ClientFixtures;
use App\Tests\api\TestDataFixtures\UserFixtures;
use App\Tests\Support\ApiTester;
use Codeception\Util\HttpCode;
final class ClientGetAPICest
{
    public function _before(ApiTester $I): void
    {
        $I->loadFixtures([UserFixtures::class, ClientFixtures::class]);
    }

    public function tryToGetAllClientsAsAdmin(ApiTester $I): void
    {
        $I->wantTo('Retrieve client information as an authenticated client');

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/login', [
            'email' => 'ana@garcia.com',
            'password' => 'passw',
        ]);
        $I->seeResponseCodeIs(HttpCode::OK);
        $token = $I->grabDataFromResponseByJsonPath('token')[0];

        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $I->sendGET('/client');

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();

        $I->seeResponseMatchesJsonType([
            "client_id" => "integer",
            "user_id" => "integer",
            "email" => "string",
            "name" => "string",
            "surnames" => "string",
            "address" => "string",
            "phone_number" => "string",
            "roles" => "array"
        ]);
    }

    public function failToGetAllClientsAsUnauthorized(ApiTester $I): void
    {
        $I->wantTo('Fail to retrieve client when not authenticated');

        $I->sendGET('/client');

        $I->seeResponseCodeIs(HttpCode::UNAUTHORIZED);
        $I->seeResponseContainsJson([
            'message' => 'JWT Token not found',
        ]);
    }
}