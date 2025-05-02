<?php
declare(strict_types=1);

namespace App\Tests\api\Client;

use App\Tests\api\TestDataFixtures\ClientFixtures;
use App\Tests\api\TestDataFixtures\UserFixtures;
use App\Tests\Support\ApiTester;
use Codeception\Util\HttpCode;
final class ClientAdminDeleteAPICest
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


    public function tryToDeleteClientWithValidEmail(ApiTester $I): void
    {
        $I->wantTo('Delete a client as an authenticated admin with valid email');

        $I->haveHttpHeader('Content-Type', 'application/json');

        $token = $this->authenticateAsAdmin($I);

        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);

        $I->sendDELETE('/admin/delete/client?email=ana@garcia.com');

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseContainsJson([
            'success' => 'Client and associated user deleted successfully'
        ]);
    }

    public function tryToDeleteClientWithoutToken(ApiTester $I): void
    {
        $I->wantTo('Fail to delete client without authentication');

        $I->haveHttpHeader('Content-Type', 'application/json');

        $I->sendDELETE('/admin/delete/client?email=ana@garcia.com');


        $I->seeResponseCodeIs(HttpCode::UNAUTHORIZED);
    }

    public function tryToDeleteClientWithNonExistentEmail(ApiTester $I): void
    {
        $I->wantTo('Fail to delete client with non-existent email');

        $I->haveHttpHeader('Content-Type', 'application/json');

        $token = $this->authenticateAsAdmin($I);

        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);

        $I->sendDELETE('/admin/delete/client?email=noana@garcia.com');


        $I->seeResponseCodeIs(HttpCode::NOT_FOUND);
        $I->seeResponseContainsJson([
            'message' => 'User not found'
        ]);
    }

    public function tryToDeleteClientWithoutEmail(ApiTester $I): void
    {
        $I->wantTo('Fail to delete client when no email is provided');

        $I->haveHttpHeader('Content-Type', 'application/json');

        $token = $this->authenticateAsAdmin($I);

        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);

        $I->sendDELETE('/admin/delete/client');

        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
        $I->seeResponseContainsJson([
            'error' => 'No email provided.'
        ]);
    }
}