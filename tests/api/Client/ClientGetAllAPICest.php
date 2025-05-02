<?php
declare(strict_types=1);

namespace App\Tests\api\Client;

use App\Tests\api\TestDataFixtures\ClientFixtures;
use App\Tests\api\TestDataFixtures\UserFixtures;
use App\Tests\Support\ApiTester;
use Codeception\Util\HttpCode;
class ClientGetAllAPICest
{
    public function _before(ApiTester $I): void
    {
        $I->loadFixtures([UserFixtures::class, ClientFixtures::class]);
    }
    private function authenticateAsAdmin(ApiTester $I): string
    {
        $I->sendPOST('/login', [
            'email' => 'admin@example.com',
            'password' => 'passw',
        ]);
        $I->seeResponseCodeIs(HttpCode::OK);
        return $I->grabDataFromResponseByJsonPath('token')[0];
    }

    public function tryToGetAllClientsAsAdmin(ApiTester $I): void
    {
        $I->wantTo('Retrieve all clients as an authenticated admin');

        $I->haveHttpHeader('Content-Type', 'application/json');
        $token = $this->authenticateAsAdmin($I);
        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $I->sendGET('/admin/clients');

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
        ], '$[*]');
    }

    public function failToGetAllClientsAsUnauthorized(ApiTester $I): void
    {
        $I->wantTo('Fail to retrieve clients when not authenticated');

        $I->sendGET('/admin/clients');

        $I->seeResponseCodeIs(HttpCode::UNAUTHORIZED);
        $I->seeResponseContainsJson([
            'message' => 'JWT Token not found',
        ]);
    }
}