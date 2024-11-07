<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class VkService
{
    private Client $client;
    private string $accessToken;
    private string $apiVersion;

    public function __construct()
    {
        $this->client = new Client();
        $this->accessToken = env('VK_ACCESS_TOKEN');
        $this->apiVersion = env('VK_API_VERSION');
    }

    public function getWallPosts($groupId, $count = 10)
    {
        $url = 'https://api.vk.com/method/wall.get';

        try {
            $response = $this->client->post($url, [
                'query' => [
                    'domain' => $groupId, // Указываем отрицательный ID для сообщества
                    'count' => $count,
                    'access_token' => $this->accessToken,
                    'v' => $this->apiVersion,
                ],
                'verify' => false,
            ]);
            return json_decode($response->getBody(), true);
        } catch (GuzzleException $e) {
            // Обработка ошибок
            return null;
        }
    }
}
