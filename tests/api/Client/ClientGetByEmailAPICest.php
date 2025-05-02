<?php
declare(strict_types=1);

namespace App\Tests\api\Client;

use App\Tests\api\TestDataFixtures\ClientFixtures;
use App\Tests\api\TestDataFixtures\UserFixtures;
use App\Tests\Support\ApiTester;
use Codeception\Util\HttpCode;

final class ClientGetByEmailAPICest
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

    /**
     * @throws \Exception
     */
    public function tryToGetClientSuccessfully(ApiTester $I): void
    {
        $I->wantTo('Get a client as an authenticated admin using email as query param');

        $I->haveHttpHeader('Content-Type', 'application/json');

        $token = $this->authenticateAsAdmin($I);

        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);

        $I->sendGET('/admin/client?email=ana@garcia.com');

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();

        $I->seeResponseContainsJson([
            'email' => 'ana@garcia.com',
            'name' => 'Ana',
            'address' => '456 Elm Street',
            'phone_number' => '666 666 666',
            'roles' => ['ROLE_CLIENT'],
            'surnames' => "García García"
        ]);
    }

    public function tryToGetClientWithoutToken(ApiTester $I): void
    {
        $I->wantTo('Fail to get client data without authentication');

        $I->sendGET('/admin/client?email=ana@garcia.com');

        $I->seeResponseCodeIs(HttpCode::UNAUTHORIZED);
    }

    /**
     * @throws \Exception
     */
    public function tryToGetClientWithoutEmail(ApiTester $I): void
    {
        $I->wantTo('Fail to get client data when email query param is missing');

        $I->haveHttpHeader('Content-Type', 'application/json');

        $token = $this->authenticateAsAdmin($I);

        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);

        $I->sendGET('/admin/client');

        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
        $I->seeResponseContainsJson([
            'error' => 'Missing email parameter',
        ]);
    }
    /**
     * @throws \Exception
     */
    public function tryToGetNonExistingClient(ApiTester $I): void
    {
        $I->wantTo('Get a not existing client error');

        $I->haveHttpHeader('Content-Type', 'application/json');

        $token = $this->authenticateAsAdmin($I);


        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);

        $I->sendGET('/admin/client?email=noexisting@garcia.com');

        $I->seeResponseCodeIs(HttpCode::NOT_FOUND);
        $I->seeResponseIsJson();

        $I->seeResponseContainsJson([
            'error' => 'No user found'
        ]);
    }


}