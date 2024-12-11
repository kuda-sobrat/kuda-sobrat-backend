<?php

namespace App\Services\SocialMedia;

use App\Models\ContextAttachment;
use App\Models\ContextPost;
use Carbon\Carbon;
use DateTime;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Collection;

class VkApiService extends SocialMediaApiBaseService
{
    protected $client;
    protected $accessToken;
    protected $apiVersion;
    protected $baseUrl = 'https://api.vk.com/method/';

    public function __construct()
    {
        $this->apiVersion = env('VK_API_VERSION');
        $this->accessToken = env('VK_ACCESS_TOKEN');
        parent::__construct();
    }

    /**
     * Метод для выполнения API запроса к VK
     *
     * @param $method
     * @param $params
     * @param $httpMethod
     * @return mixed
     * @throws GuzzleException
     */
    protected function call($method, $params = [], $httpMethod = 'GET')
    {
        $params['access_token'] = $this->accessToken;
        $params['v'] = $this->apiVersion;

        return parent::call($method, $params, $httpMethod);
    }

    /**
     * Получает список записей сообщества, ориентируясь на переданные параметры
     *
     * @param $communityId
     * @param int $limit
     * @param int $offset
     * @param DateTime|null $since
     * @param string|null $sinceId
     * @param ?array $contextPostData.
     * @return Collection<ContextPost>
     * @throws GuzzleException
     */
    public function getLatestPosts($communityId, $limit = 10, int $offset = 0, DateTime $since = null, ?string $sinceId = null, ?array $contextPostData = null): Collection
    {
        $maxLimit = 100;
        $params = [
            'owner_id' => -1 * $communityId, // Отрицательное значение для групп
            'count' => min($limit, $maxLimit),
            'offset' => $offset,
        ];

        $response = $this->call('wall.get', $params);

        if (isset($response['error'])) {
            throw new \Exception('Ошибка VK API: ' . $response['error']['error_msg']);
        }

        // Обработка полученных данных и приведение к общему виду
        $contextPosts = new Collection();

        foreach ($response['response']['items'] as $item) {
            $date = Carbon::parse($item['date']);
            if ($since && $date->lte($since)) {
                continue;
            }

            $contextPost = new ContextPost([
                'source_id' => $item['id'],
                'social_link_id' => $contextPostData['social_link_id'],
                'text' => $item['text'],
                'unique_hash' => $item['hash'],
                'created_at' => date('Y-m-d H:i:s', $item['date']),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            if (isset($item['attachments']) && is_array($item['attachments'])) {
                $contextPost->setRelation('attachments', collect($this->processAttachments($item['attachments'], $contextPost)));
            }

            $contextPosts->push($contextPost);
        }

        return $contextPosts;
    }

    /**
     * Получить посты из сообщества за определенный период.
     *
     * @param int|string $communityId Идентификатор сообщества.
     * @param DateTime $startDate Начальная дата периода.
     * @param DateTime|null $endDate Конечная дата периода (если null, используется текущее время).
     * @return Collection<ContextPost>
     * @throws GuzzleException
     */
    public function getPostsByDate(int|string $communityId, DateTime $startDate, ?DateTime $endDate = null): Collection
    {
        $startTimestamp = $startDate->getTimestamp();

        if ($endDate) {
            $endTimestamp = $endDate->getTimestamp();
        } else {
            $endTimestamp = time();
        }

        $offset = 0;
        $count = 100; // Максимальное значение для VK API
        $posts = new Collection();

        do {
            $params = [
                'owner_id' => -1 * $communityId,
                'count' => $count,
                'offset' => $offset,
                'filter' => 'owner',
            ];

            $response = $this->call('wall.get', $params);

            if (isset($response['error'])) {
                throw new \Exception('VK API error: ' . $response['error']['error_msg']);
            }

            $items = $response['response']['items'];
            $totalCount = $response['response']['count'];

            if (empty($items)) {
                // Больше нет записей для обработки
                break;
            }

            foreach ($items as $item) {
                $postDate = $item['date'];
                $isPinned = $item['is_pinned'] ?? false;

                // Если пост старше начальной даты и не закреплен, прекращаем цикл
                if ($postDate < $startTimestamp && !$isPinned) {
                    break 2;
                }

                // Обрабатываем посты, которые находятся в заданном диапазоне дат
                if ($postDate >= $startTimestamp && $postDate <= $endTimestamp) {
                    $contextPost = $this->mapPostItemToContextPost($item, $communityId);
                    $posts->push($contextPost);
                }

                // Иначе продолжаем обработку следующего поста
            }

            $offset += $count;

            if ($offset >= $totalCount) {
                // Все посты обработаны
                break;
            }

            // Задержка для соблюдения ограничений API
            usleep(500000); // 0.5 секунды
        } while (true);

        return $posts;
    }

    /**
     * Получить информацию о сообществе.
     *
     * @param int|string $communityId Идентификатор сообщества.
     * @return array Информация о сообществе.
     * @throws Exception|GuzzleException
     */
    public function getCommunityInfo(int|string $communityId): array
    {
        $params = [
            'group_id' => abs($communityId),
            'fields' => 'activity,description,status,interests,links',
        ];

        $response = $this->call('groups.getById', $params, 'POST');

        if (isset($response['error'])) {
            throw new Exception('VK API error: ' . $response['error']['error_msg']);
        }

        return $response['response'] ?? [];
    }

    /**
     * Преобразование данных поста из VK API в объект `ContextPost`.
     *
     * @param array $item Данные поста из VK API.
     * @param int|string $communityId Идентификатор сообщества.
     * @return ContextPost
     */
    protected function mapPostItemToContextPost(array $item, int|string $communityId): ContextPost
    {
        $contextPost = new ContextPost([
            'source_id' => $item['id'],
            'community_id' => $communityId,
            'text' => $item['text'],
            'unique_hash' => md5($item['text'] ?? ''), // TODO: Определить алгоритм генерации уникального хэша
            'created_at' => date('Y-m-d H:i:s', $item['date']),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        if (isset($item['attachments']) && is_array($item['attachments'])) {
            $attachments = $this->processAttachments($item['attachments'], $contextPost);
            $contextPost->setRelation('attachments', $attachments);
        }

        return $contextPost;
    }

    /**
     * Обработка вложений поста и получение коллекции `ContextAttachment`.
     *
     * @param array $attachmentsData Массив данных вложений из VK API.
     * @param ContextPost $contextPost Объект `ContextPost`, к которому относятся вложения.
     * @return Collection<ContextAttachment>
     */
    protected function processAttachments(array $attachmentsData, ContextPost $contextPost): Collection
    {
        $attachments = new Collection();
        foreach ($attachmentsData as $attachment) {
            $contextAttachment = new ContextAttachment([
                'type' => $attachment['type'],
                'created_at' => $contextPost->created_at,
                'updated_at' => $contextPost->updated_at,
            ]);

            // Обработка типов вложений
            switch ($attachment['type']) {
                case 'photo':
                    $photo = $attachment['photo'];
                    $sizes = $photo['sizes'];
                    // Сортируем размеры по площади и выбираем самое большое изображение
                    usort($sizes, function ($a, $b) {
                        return ($b['width'] * $b['height']) <=> ($a['width'] * $a['height']);
                    });
                    $largestPhoto = $sizes[0];
                    $contextAttachment->url = $largestPhoto['url'];
                    $contextAttachment->title = 'Photo';
                    break;

                case 'link':
                    $link = $attachment['link'];
                    $contextAttachment->url = $link['url'];
                    $contextAttachment->title = $link['title'] ?? '';
                    $contextAttachment->text = $link['description'] ?? '';
                    break;

                // TODO: Добавить обработку других типов вложений при необходимости

                default:
                    // Пропустить неизвестные типы вложений
                    continue 2;
            }

            $attachments->push($contextAttachment);
        }
        return $attachments;
    }
}
