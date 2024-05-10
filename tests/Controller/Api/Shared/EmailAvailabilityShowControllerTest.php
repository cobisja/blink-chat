<?php

namespace App\Tests\Controller\Api\Shared;

use App\Tests\Controller\Api\ApiWebTestCase;
use Symfony\Component\HttpFoundation\Response;

class EmailAvailabilityShowControllerTest extends ApiWebTestCase
{
    final public const EMAIL_AVAILABILITY_URI = '/api/emails/availability';

    /**
     * @test
     */
    public function it_should_return_a_code_422_when_an_invalid_email_is_provided(): void
    {
        $expectedCode = Response::HTTP_UNPROCESSABLE_ENTITY;
        $expectedErrorPropertyPath = 'email';
        $expectedErrorMessage = 'This value is not a valid email address.';

        $email = 'test';

        $this->client->request(
            method: 'GET',
            uri: self::EMAIL_AVAILABILITY_URI,
            parameters: compact('email')
        );

        $response = json_decode($this->client->getResponse()->getContent(), associative: true);

        $this->assertResponseStatusCodeSame($expectedCode);
        $this->assertArrayHasKey('error', $response);
        $this->assertArrayHasKey('propertyPath', $response['error'][0]);
        $this->assertArrayHasKey('message', $response['error'][0]);
        $this->assertEquals($expectedErrorPropertyPath, $response['error'][0]['propertyPath']);
        $this->assertEquals($expectedErrorMessage, $response['error'][0]['message']);
    }

    /**
     * @test
     * @dataProvider availabilityContent
     */
    public function it_should_returns_a_200_code_indicating_the_email_availability(
        string $email,
        bool $isAvailable
    ): void {
        $userData = [
            'email' => $isAvailable ? 'john.doe@example.org' : $email,
            'password' => 'test-password',
            'name' => 'test',
            'lastname' => 'test',
            'nickname' => 'test-nickname'
        ];

        $this->createTestUser($userData);

        $userData['email'] = $email;

        $this->client->request(
            method: 'GET',
            uri: self::EMAIL_AVAILABILITY_URI,
            parameters: ['email' => $userData['email']]
        );

        $response = json_decode($this->client->getResponse()->getContent(), associative: true);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertArrayHasKey('data', $response);
        $this->assertArrayHasKey('available', $response['data']);
        $this->assertIsBool($response['data']['available']);
        $this->assertEquals($isAvailable, $response['data']['available']);
    }

    private function availabilityContent(): array
    {
        return [
            ['test@test.test', true],
            ['test@test.test', false],
        ];
    }
}
