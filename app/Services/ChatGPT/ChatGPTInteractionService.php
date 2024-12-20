<?php

namespace App\Services\ChatGPT;

use GuzzleHttp\Client;

class ChatGPTInteractionService
{
    protected Client $client;
    protected string $apiKey;
    protected string $baseUrl = 'https://api.rockapi.ru/openai/v1';
    public string $model;

    public function __construct()
    {
        $this->apiKey = env('OPENAI_API_KEY');
        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'headers' => [
                'Authorization' => "Bearer {$this->apiKey}",
                'Content-Type'  => 'application/json',
            ],
            'verify' => false,
        ]);
    }

    public function sendMessage(string $context, string $model = 'gpt-4o-mini', string $role = 'user')
    {
        $this->model = $model;
        try {
            $response = $this->client->post($this->baseUrl . '/chat/completions', [
                'json' => [
                    'model' => $model,
                    'messages' => [
                        ['role' => $role, 'content' => $context],
                    ],
                ],
            ]);

            $content = json_decode($response->getBody()->getContents(), true);
            return $content;
        } catch (\Exception $e) {
            // Логирование ошибки или повторная обработка исключения
            throw $e;
        }
    }

    public function sendMessages(array $messages, string $model = 'gpt-4o-mini')
    {
        $this->model = $model;
        try {
            $response = $this->client->post($this->baseUrl . '/chat/completions', [
                'json' => [
                    'model' => $model,
                    'messages' => $messages,
                ],
            ]);

            $content = json_decode($response->getBody(), true);

            return $content;
        } catch (\Exception $e) {
            // Логирование ошибки или повторная обработка исключения
            throw $e;
        }
    }
}
