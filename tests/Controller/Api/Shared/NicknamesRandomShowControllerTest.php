<?php

namespace App\Tests\Controller\Api\Shared;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class NicknamesRandomShowControllerTest extends WebTestCase
{
    final public const NICKNAMES_RANDOM = '/api/nicknames/random';

    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    /**
     * @test
     * @dataProvider queryFixtures
     */
    public function it_should_returns_code_200_with_the_random_nickname(?string $baseName): void
    {
        $this->client->request(
            method: 'GET',
            uri: self::NICKNAMES_RANDOM,
            parameters: $baseName ? ['base_name' => $baseName] : []
        );

        $response = json_decode($this->client->getResponse()->getContent(), associative: true);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertArrayHasKey('data', $response);
        $this->assertArrayHasKey('nickname', $response['data']);
        $this->assertNotEmpty($response['data']['nickname']);

        $baseName && $this->assertStringContainsString($baseName, $response['data']['nickname']);
    }

    private function queryFixtures(): array
    {
        return [
            [null],
            ['test']
        ];
    }
}
