<?php

namespace App\Tests\Controller\Api\Chat\User;

use App\Controller\Api\Transformer\User\UserTransformer;
use App\Tests\Controller\Api\ProtectedApiWebTestCase;
use Symfony\Component\HttpFoundation\Response;

class PaginatedUsersIndexControllerTest extends ProtectedApiWebTestCase
{
    final public const SIGN_UP_URI = '/api/users';

    private UserTransformer $userTransformer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userTransformer = self::getContainer()->get(UserTransformer::class);;
    }

    /**
     * @test
     */
    public function it_should_returns_code_401_when_no_jwt_is_provided(): void
    {
        $expectedCode = Response::HTTP_UNAUTHORIZED;
        $expectedMessage = 'JWT Token not found';

        $this->client->request(
            method: 'GET',
            uri: self::SIGN_UP_URI,
        );

        $response = json_decode($this->client->getResponse()->getContent(), associative: true);

        $this->assertResponseStatusCodeSame($expectedCode);
        $this->assertArrayHasKey('code', $response);
        $this->assertArrayHasKey('message', $response);
        $this->assertEquals($expectedCode, $response['code']);
        $this->assertEquals($expectedMessage, $response['message']);
    }

    /**
     * @test
     * @dataProvider invalidPageAndLimitQueryParamsFixtures
     */
    public function it_should_returns_code_200_and_empty_paginated_users_with_invalid_values_for_page_and_limit(
        $page,
        $limit
    ): void {
        $expectedCode = Response::HTTP_OK;
        $expectedTotal = 0;
        $expectedCount = 0;

        $user = $this->createTestUser();
        $authToken = $this->authenticateUser($user);

        $this->client->request(
            method: 'GET',
            uri: self::SIGN_UP_URI,
            parameters: compact('page', 'limit'),
            server: ['HTTP_AUTHORIZATION' => sprintf('Bearer %s', $authToken)]
        );

        $response = json_decode($this->client->getResponse()->getContent(), associative: true);

        $this->assertResponseStatusCodeSame($expectedCode);
        $this->assertArrayHasKey('data', $response);
        $this->assertArrayHasKey('meta', $response);
        $this->assertArrayHasKey('_links', $response['meta']);
        $this->assertArrayHasKey('total', $response['meta']);
        $this->assertArrayHasKey('count', $response['meta']);
        $this->assertArrayHasKey('self', $response['meta']['_links']);
        $this->assertArrayHasKey('first', $response['meta']['_links']);
        $this->assertArrayHasKey('last', $response['meta']['_links']);
        $this->assertArrayNotHasKey('next', $response['meta']['_links']);
        $this->assertArrayNotHasKey('prev', $response['meta']['_links']);

        $this->assertEmpty($response['data']);
        $this->assertSame($expectedTotal, $response['meta']['total']);
        $this->assertSame($expectedCount, $response['meta']['count']);
    }

    /**
     * @test
     * @dataProvider queryParamsFixtures
     */
    public function it_should_returns_code_200_and_paginated_users(array $queryParams): void
    {
        $expectedCode = Response::HTTP_OK;
        $expectedTotal = 1;
        $expectedCount = 1;

        $user = $this->createTestUser();
        $authToken = $this->authenticateUser($user);

        $this->client->request(
            method: 'GET',
            uri: self::SIGN_UP_URI,
            parameters: $queryParams,
            server: ['HTTP_AUTHORIZATION' => sprintf('Bearer %s', $authToken)]
        );

        $response = json_decode($this->client->getResponse()->getContent(), associative: true);

        $this->assertResponseStatusCodeSame($expectedCode);
        $this->assertArrayHasKey('data', $response);
        $this->assertArrayHasKey('meta', $response);
        $this->assertArrayHasKey('_links', $response['meta']);
        $this->assertArrayHasKey('total', $response['meta']);
        $this->assertArrayHasKey('count', $response['meta']);
        $this->assertArrayHasKey('self', $response['meta']['_links']);
        $this->assertArrayHasKey('first', $response['meta']['_links']);
        $this->assertArrayHasKey('last', $response['meta']['_links']);
        $this->assertArrayNotHasKey('next', $response['meta']['_links']);
        $this->assertArrayNotHasKey('prev', $response['meta']['_links']);

        $this->assertSame($this->userTransformer->transform($user), $response['data'][0]);
        $this->assertSame($expectedTotal, $response['meta']['total']);
        $this->assertSame($expectedCount, $response['meta']['count']);
    }

    /**
     * @test
     * @depends it_should_returns_code_200_and_paginated_users
     */
    public function it_should_returns_code_200_and_paginated_users_with_the_query_set(): void
    {
        $expectedCode = Response::HTTP_OK;
        $queryPattern = 'foo';
        $expectedTotal = 2;
        $expectedCount = 2;

        /**
         * Pattern in email and name
         */
        $userDataset1 = [
            'email' => 'test@foo.test',
            'password' => 'test-test',
            'name' => 'foo',
            'lastname' => 'test',
            'nickname' => 'foo-test'
        ];

        /**
         * Pattern only in lastname
         */
        $userDataset2 = [
            'email' => 'test@bar.test',
            'password' => 'test-test',
            'name' => 'test',
            'lastname' => 'seafood',
            'nickname' => 'seafood-test'
        ];

        /**
         * No fields match the pattern
         */
        $userDataset3 = [
            'email' => 'test@baz.test',
            'password' => 'test-test',
            'name' => 'baz',
            'lastname' => 'test',
            'nickname' => 'bar-test'
        ];

        $authenticatedUser = $this->createTestUser();
        $this->createTestUser($userDataset1);
        $this->createTestUser($userDataset2);
        $this->createTestUser($userDataset3);

        $authToken = $this->authenticateUser($authenticatedUser);

        $this->client->request(
            method: 'GET',
            uri: self::SIGN_UP_URI,
            parameters: [
                'query' => $queryPattern,
                /** default values: $page = 1, $limit = 10 */
            ],
            server: ['HTTP_AUTHORIZATION' => sprintf('Bearer %s', $authToken)]
        );

        $response = json_decode($this->client->getResponse()->getContent(), associative: true);

        $this->assertResponseStatusCodeSame($expectedCode);
        $this->assertSame($expectedCount, count($response['data']));
        $this->assertSame($expectedTotal, count($response['data']));
        $this->assertStringContainsString(
            $queryPattern,
            $response['data'][0]['email'] . $response['data'][0]['name'] . $response['data'][0]['lastname']
        );
        $this->assertStringContainsString(
            $queryPattern,
            $response['data'][1]['email'] . $response['data'][1]['name'] . $response['data'][1]['lastname']
        );
    }

    /**
     * @test
     * @dataProvider queryParamsForPrevAndNextLinksFixtures
     * @depends      it_should_returns_code_200_and_paginated_users
     */
    public function it_should_returns_code_200_and_paginated_users_with_prev_and_next_links(
        int $page,
        bool $hasNext,
        bool $hasPrev
    ): void {
        $expectedCode = Response::HTTP_OK;

        $userDataset1 = [
            'email' => 'test@foo.test',
            'password' => 'test-test',
            'name' => 'foo',
            'lastname' => 'test',
            'nickname' => 'foo-test'
        ];

        $userDataset2 = [
            'email' => 'test@bar.test',
            'password' => 'test-test',
            'name' => 'test',
            'lastname' => 'seafood',
            'nickname' => 'seafood-test'
        ];

        $authenticatedUser = $this->createTestUser();
        $this->createTestUser($userDataset1);
        $this->createTestUser($userDataset2);

        $authToken = $this->authenticateUser($authenticatedUser);

        $this->client->request(
            method: 'GET',
            uri: self::SIGN_UP_URI,
            parameters: [
                'page' => $page,
                'limit' => 1
            ],
            server: ['HTTP_AUTHORIZATION' => sprintf('Bearer %s', $authToken)]
        );

        $response = json_decode($this->client->getResponse()->getContent(), associative: true);

        $this->assertResponseStatusCodeSame($expectedCode);

        if ($hasNext && $hasPrev) {
            $this->assertArrayHasKey('next', $response['meta']['_links']);
            $this->assertArrayHasKey('prev', $response['meta']['_links']);
        } elseif ($hasPrev) {
            $this->assertArrayHasKey('prev', $response['meta']['_links']);
            $this->assertArrayNotHasKey('next', $response['meta']['_links']);
        } else {
            $this->assertArrayHasKey('next', $response['meta']['_links']);
            $this->assertArrayNotHasKey('prev', $response['meta']['_links']);
        }
    }

    public
    function invalidPageAndLimitQueryParamsFixtures(): array
    {
        return [
            ['page' => -1, 'limit' => -1],
            ['page' => 0, 'limit' => -1],
            ['page' => -1, 'limit' => 0],
            ['page' => 0, 'limit' => 0],
        ];
    }

    private
    function queryParamsFixtures(): array
    {
        return [
            [[]],
            [['query' => '']],
            [['query' => 'test']],
            [['query' => 'test', 'page' => 1]],
            [['query' => 'test', 'limit' => 1]],
            [['query' => 'test', 'page' => 1, 'limit' => 1]],
            [['page' => 1]],
            [['page' => 1, 'limit' => 1]],
            [['limit' => 1]],
        ];
    }

    private
    function queryParamsForPrevAndNextLinksFixtures(): array
    {
        return [
            [1, 'has_next_link' => true, 'has_prev_link' => false],
            [2, 'has_next_link' => true, 'has_prev_link' => true],
            [3, 'has_next_link' => false, 'has_prev_link' => true],
        ];
    }
}
