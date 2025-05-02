<?php
declare(strict_types=1);

namespace App\Tests\api\Client;

use App\Tests\api\TestDataFixtures\ClientFixtures;
use App\Tests\api\TestDataFixtures\UserFixtures;
use App\Tests\Support\ApiTester;
use Codeception\Util\HttpCode;

final class ClientCreateAPICest
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

    public function tryToCreateClientWithValidData(ApiTester $I): void
    {
         $validClientData = [
            'email' => 'unique@example.com',
            'password' => 'ValidPassword123',
            'name' => 'Laura',
            'surnames' => 'López García',
            'address' => 'Calle Falsa 123',
            'phoneNumber' => '600 123 456',
        ];
        $I->wantTo('Create a new client with valid data');

        $I->haveHttpHeader('Content-Type', 'application/json');
        $token = $this->authenticateAsAdmin($I);

        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $I->sendPOST('/create/client', $validClientData);

        $I->seeResponseCodeIs(HttpCode::CREATED);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            'message' => 'Client successfully registered',
            'client' => [
                'email' => $validClientData['email'],
                'name' => $validClientData['name'],
                'surnames' => $validClientData['surnames'],
                'address' => $validClientData['address'],
                'phone_number' => $validClientData['phoneNumber'],
            ],
        ]);
    }

    public function tryToCreateClientThatAlreadyExists(ApiTester $I): void
    {
        $duplicatedClientData = [
            'email' => 'ana@garcia.com',
            'password' => 'SomePassword123',
            'name' => 'Ana',
            'surnames' => 'García García',
            'address' => '456 Elm Street',
            'phoneNumber' => '666 666 666',
        ];
        $I->wantTo('Fail to create a client that already exists (based on fixtures)');

        $I->haveHttpHeader('Content-Type', 'application/json');
        $token = $this->authenticateAsAdmin($I);


        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $I->sendPOST('/create/client', $duplicatedClientData);

        $I->seeResponseCodeIs(HttpCode::CONFLICT);
        $I->seeResponseContainsJson([
            'error' => 'User ' . $duplicatedClientData['email'] . ' already exists.',
        ]);
    }

    public function tryToCreateClientWithMissingFields(ApiTester $I): void
    {
        $invalidClientData = [
            'email' => '',
            'password' => 'Password123',
            'name' => 'Laura',
            'surnames' => 'López García',
            'address' => 'Calle Falsa 123',
            'phoneNumber' => '600 123 456',
        ];
        $I->wantTo('Fail to create a client due to missing required fields');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $token = $this->authenticateAsAdmin($I);


        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $I->sendPOST('/create/client', $invalidClientData);

        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
        $I->seeResponseContainsJson([
            'error' => 'The field email is required.',
        ]);
    }

    public function tryToCreateClientWithInvalidEmail(ApiTester $I): void
    {
        $invalidClientData = [
            'email' => 'not-an-email',
            'password' => 'ValidPassword123',
            'name' => 'Laura',
            'surnames' => 'López García',
            'address' => 'Calle Falsa 123',
            'phoneNumber' => '600 123 456',
        ];
        $I->wantTo('Fail to create a client with invalid email format');

        $I->haveHttpHeader('Content-Type', 'application/json');
        $token = $this->authenticateAsAdmin($I);


        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $I->sendPOST('/create/client', $invalidClientData);

        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
        $I->seeResponseContainsJson([
            'error' => 'The email not-an-email is not correct. Please, insert a valid email.',
        ]);
    }
}
