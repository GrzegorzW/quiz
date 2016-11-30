<?php

namespace tests\AppBundleBundle\Controller;

use AppBundle\Controller\Admin\UserController;
use AppBundle\Entity\User;
use Tests\ApiWebTestCase;

class UserControllerTest extends ApiWebTestCase
{
    /**
     * @covers UserController::postAction()
     */
    public function testRegisterUserNotAuthenticated()
    {
        $this->register();
        $this->assertStatusCode(401, $this->getClient());
    }

    public function register($email = null, $plainPassword = 'secret_password')
    {
        $faker = static::getFaker();
        $this->client->request(
            'POST',
            '/api/v1/admin/users',
            [
                'email' => null === $email ? $faker->email : $email,
                'plainPassword' => $plainPassword
            ]
        );
    }

    /**
     * @covers UserController::postAction()
     */
    public function testRegisterUserNotAuthorized()
    {
        $email = $this->getReference('user_1')->getEmail();

        $this->authenticateClient($email);

        $this->register();
        $this->assertStatusCode(403, $this->getClient());
    }

    /**
     * @covers UserController::postAction()
     */
    public function testRegisterUser()
    {
        $this->authenticateClient();

        $email = 'foo@bar.com';

        $this->register($email);
        $this->assertStatusCode(201, $this->getClient());
        $response = $this->getResponseContent();

        $expectedKeys = [
            'id',
            'email',
            'roles',
            'createdAt',
            'updatedAt',
            'enabled'
        ];

        static::assertCount(6, array_keys($response));
        foreach ($expectedKeys as $expectedKey) {
            static::assertArrayHasKey($expectedKey, $response);
        }

        static::assertEmpty($response['roles']);
        static::assertEquals($email, $response['email']);
        static::assertTrue($response['enabled']);
    }

    /**
     * @covers UserController::postAction()
     */
    public function testRegisterUserEmailTwice()
    {
        $this->authenticateClient();

        $email = 'any@foo.bar';
        $this->register($email);
        $this->assertStatusCode(201, $this->getClient());

        $this->register($email);
        $this->assertStatusCode(400, $this->getClient());
    }

    /**
     * @covers UserController::postAction()
     */
    public function testRegisterUserEmailBlank()
    {
        $this->authenticateClient();

        $this->register('');
        $this->assertStatusCode(400, $this->getClient());
    }

    /**
     * @covers UserController::postAction()
     */
    public function testRegisterUserEmailInvalid()
    {
        $this->authenticateClient();

        $invalidEmails = ['asdf@', 'asdf', '@dd.de', 'aa aa@gmail.com'];
        foreach ($invalidEmails as $invalidEmail) {
            $this->register($invalidEmail);
            $this->assertStatusCode(400, $this->getClient());
        }
    }

    /**
     * @covers UserController::postAction()
     */
    public function testRegisterUserPlainPasswordBlank()
    {
        $this->authenticateClient();

        $this->register(null, '');
        $this->assertStatusCode(400, $this->getClient());
    }

    /**
     * @covers UserController::postAction()
     */
    public function testRegisterUserPlainPasswordLength()
    {
        $this->authenticateClient();

        $value = str_repeat('A', 5);
        $this->register(null, $value);
        $this->assertStatusCode(400, $this->getClient());

        $value = str_repeat('A', 6);
        $this->register(null, $value);
        $this->assertStatusCode(201, $this->getClient());

        $value = str_repeat('A', 33);
        $this->register(null, $value);
        $this->assertStatusCode(400, $this->getClient());

        $value = str_repeat('A', 32);
        $this->register(null, $value);
        $this->assertStatusCode(201, $this->getClient());
    }

    /**
     * @covers UserController::getAction()
     */
    public function testGetUser()
    {
        $this->authenticateClient();

        $expectedKeys = [
            'id',
            'email',
            'roles',
            'createdAt',
            'updatedAt',
            'enabled',
            'deleted'
        ];

        /** @var User $user */
        $user = $this->getReference('admin');

        $this->getUser($user->getShortId());

        $this->assertStatusCode(200, $this->getClient());

        $response = $this->getResponseContent();
        static::assertCount(7, array_keys($response));
        foreach ($expectedKeys as $expectedKey) {
            static::assertArrayHasKey($expectedKey, $response);
        }
        static::assertEquals($user->getShortId(), $response['id']);
    }

    public function getUser($userId)
    {
        $this->client->request('GET', '/api/v1/admin/users/' . $userId);
    }

    /**
     * @covers UserController::getAction()
     */
    public function testGetUserDisabled()
    {
        $this->authenticateClient();

        /** @var User $user */
        $user = $this->getReference('user_disabled');

        $this->getUser($user->getShortId());

        $this->assertStatusCode(200, $this->getClient());
        $response = $this->getResponseContent();
        static::assertFalse($response['enabled']);
    }

    /**
     * @covers UserController::getAction()
     */
    public function testGetUserDeleted()
    {
        $this->authenticateClient();

        /** @var User $user */
        $user = $this->getReference('user_deleted');

        $this->getUser($user->getShortId());

        $this->assertStatusCode(404, $this->getClient());
    }

    /**
     * @covers UserController::putAction()
     */
    public function testUpdateUserNotAuthenticated()
    {
        $userId = $this->getReference('user_2')->getShortId();

        $this->putUser($userId);
        $this->assertStatusCode(401, $this->getClient());
    }

    public function putUser(
        $userId = null,
        $email = null,
        $enabled = null
    ) {
        $faker = static::getFaker();

        $parameters = [
            'email' => null === $email ? $faker->email : $email,
            'enabled' => null === $enabled ? '1' : $enabled
        ];

        if ($userId === null) {
            $userId = $this->getReference('user_1')->getShortId();
        }

        $this->client->request(
            'PUT',
            '/api/v1/admin/users/' . $userId,
            $parameters
        );
    }

    /**
     * @covers UserController::putAction()
     */
    public function testUpdateUserNotAuthorized()
    {
        $email = $this->getReference('user_1')->getEmail();

        $this->authenticateClient($email);

        $userId = $this->getReference('user_2')->getShortId();

        $this->putUser($userId);
        $this->assertStatusCode(403, $this->getClient());
    }

    /**
     * @covers UserController::putAction()
     */
    public function testUpdateUserEmailBlank()
    {
        $this->authenticateClient();

        $this->putUser(null, '');
        $this->assertStatusCode(400, $this->getClient());
    }

    /**
     * @covers UserController::putAction()
     */
    public function testUpdateUserEmailAlreadyExists()
    {
        $this->authenticateClient();

        $existingEmail = $this->getReference('user_2')->getEmail();

        $this->putUser(null, $existingEmail);
        $this->assertStatusCode(400, $this->getClient());
    }

    /**
     * @covers UserController::putAction()
     */
    public function testUpdateUserEmailInvalid()
    {
        $this->authenticateClient();

        $invalidEmails = ['asdf@', 'asdf', '@dd.de', 'aa aa@gmail.com'];
        foreach ($invalidEmails as $invalidEmail) {
            $this->putUser(null, $invalidEmail);
            $this->assertStatusCode(400, $this->getClient());
        }
    }

    /**
     * @covers UserController::putAction()
     */
    public function testUpdateStatusDisabled()
    {
        $this->authenticateClient();

        $this->putUser(null, null, 0);
        $this->assertStatusCode(204, $this->getClient());
    }

    /**
     * @covers UserController::putAction()
     */
    public function testUpdateStatusEnableDisabledUser()
    {
        $this->authenticateClient();

        $user = $this->getReference('user_disabled')->getShortId();

        $this->putUser($user, null, 1);
        $this->assertStatusCode(204, $this->getClient());
    }

    /**
     * @covers UserController::putAction()
     */
    public function testUpdateUserEmailStatus()
    {
        $this->authenticateClient();

        $invaludValues = ['true', 'false', 'yes', '2'];
        foreach ($invaludValues as $invalidValue) {
            $this->putUser(null, null, $invalidValue);
            $this->assertStatusCode(400, $this->getClient());
        }
    }

    /**
     * @covers UserController::putAction()
     */
    public function testUpdateStatusWhenDeleted()
    {
        $this->authenticateClient();

        $user = $this->getReference('user_deleted')->getId();

        $this->putUser($user);
        $this->assertStatusCode(404, $this->getClient());
    }

    /**
     * @covers UserController::listAction()
     */
    public function testListUsersAll()
    {
        $this->authenticateClient();

        $this->listUsers();
        $this->assertStatusCode(200, $this->getClient());
        $response = $this->getResponseContent();

        static::assertEquals(12, $response['paging']['totalItems']);
    }

    /**
     * @covers UserController::listAction()
     */
    public function testListUsersEnabled()
    {
        $this->authenticateClient();

        $this->listUsers(null, false);
        $this->assertStatusCode(200, $this->getClient());
        $response = $this->getResponseContent();

        static::assertEquals(11, $response['paging']['totalItems']);
    }

    /**
     * @covers UserController::listAction()
     */
    public function testListUsersDeletedUserByShortEmail()
    {
        $this->authenticateClient();

        $email = $this->getReference('user_deleted')->getEmail();

        $this->listUsers($email);
        $this->assertStatusCode(200, $this->getClient());
        $response = $this->getResponseContent();

        static::assertEquals(0, $response['paging']['totalItems']);
    }

    /**
     * @covers UserController::listAction()
     */
    public function testListUsersEnabledUserByShortEmail()
    {
        $this->authenticateClient();

        $email = $this->getReference('user_1')->getEmail();

        $this->listUsers($email);
        $this->assertStatusCode(200, $this->getClient());
        $response = $this->getResponseContent();

        static::assertEquals(1, $response['paging']['totalItems']);

        $expectedKeys = [
            'id',
            'email',
            'roles',
            'createdAt',
            'updatedAt',
            'enabled'
        ];

        $user = $response['data'][0];
        static::assertCount(6, array_keys($user));
        foreach ($expectedKeys as $expectedKey) {
            static::assertArrayHasKey($expectedKey, $user);
        }
        static::assertEquals($email, $user['email']);
        static::assertTrue($user['enabled']);
    }

    public function listUsers($phrase = null, $allowDisabled = true)
    {
        $this->client->request(
            'GET',
            '/api/v1/admin/users',
            [
                'phrase' => $phrase,
                'allowDisabled' => $allowDisabled
            ]
        );
    }
}
