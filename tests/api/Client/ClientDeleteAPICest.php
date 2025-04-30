<?php
declare(strict_types=1);

namespace App\Tests\api\Client;
use App\Tests\api\TestDataFixtures\ClientFixtures;
use App\Tests\api\TestDataFixtures\UserFixtures;
use App\Tests\Support\ApiTester;
use Codeception\Util\HttpCode;
final class ClientDeleteAPICest
{
    public function _before(ApiTester $I): void
    {
        $I->loadFixtures([UserFixtures::class, ClientFixtures::class]);
    }

    public function tryToDeleteClientSuccessfully(ApiTester $I): void
    {
        $I->wantTo('Delete a client successfully when authenticated');

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/login', [
            'email' => 'ana@garcia.com',
            'password' => 'passw',
        ]);
        $I->seeResponseCodeIs(HttpCode::OK);
        $token = $I->grabDataFromResponseByJsonPath('token')[0];

        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $I->sendDELETE('/client/delete');

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            'success' => 'Client and associated user deleted successfully',
        ]);
    }

    public function tryToDeleteClientWithoutAuthentication(ApiTester $I): void
    {
        $I->wantTo('Fail to delete a client without being authenticated');

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendDELETE('/client/delete');

        $I->seeResponseCodeIs(HttpCode::UNAUTHORIZED);
    }
}