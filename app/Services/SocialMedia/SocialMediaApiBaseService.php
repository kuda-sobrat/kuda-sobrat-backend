<?php

namespace App\Services\SocialMedia;

use App\Contracts\Interfaces\SocialMediaServiceInterface;
use App\Models\ContextPost;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Collection;

class SocialMediaApiBaseService implements SocialMediaServiceInterface
{
    protected $client;
    protected $accessToken;
    protected $apiVersion;
    protected $baseUrl;

    public function __construct()
    {
        $this->client = new Client();
    }

    /**
     * Метод для выполнения запроса к API. Общая реализация для выполнения HTTP-запроса
     *
     * @param $method
     * @param $params
     * @param $httpMethod
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function call($method, $params = [], $httpMethod = 'GET')
    {
        try {
            $url = $this->baseUrl . $method;

            $options = [
                'headers' => [
                    'Accept' => 'application/json',
                ],
            ];

            if ($httpMethod === 'GET') {
                $options['query'] = $params;
            } else {
                $options['form_params'] = $params;
            }

            $options['verify'] = false;

            $response = $this->client->request($httpMethod, $url, $options);

            $body = $response->getBody();
            $data = json_decode($body, true);

            return $data;
        } catch (RequestException $e) {
            throw $e;
        }
    }

    /**
     * @param $communityId
     * @param $limit
     * @param int $offset
     * @param ?\DateTime $since
     * @param ?string $sinceId
     * @param ?array $contextPostData.
     * @return Collection<ContextPost>
     * @throws \Exception
     */
    public function getLatestPosts($communityId, $limit = 10, int $offset = 0, \DateTime $since = null, ?string $sinceId = null, ?array $contextPostData = null): Collection
    {
        throw new \Exception('Метод getLatestPosts не определен.');
    }

    public function getPostsByDate(int|string $communityId, \DateTime $startDate, \DateTime $endDate = null): Collection
    {
        throw new \Exception('Метод getPostsByDate не определен.');
    }

    public function getCommunityInfo(int|string $communityId): array
    {
        throw new \Exception('Метод getCommunityInfo не определен.');
    }
}
