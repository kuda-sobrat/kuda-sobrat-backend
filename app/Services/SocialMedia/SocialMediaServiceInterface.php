<?php

namespace App\Services\SocialMedia;

use App\Models\ContextPost;
use Illuminate\Support\Collection;

interface SocialMediaServiceInterface
{
    /**
     * Получить последние посты из сообщества.
     *
     * @param int|string $communityId Идентификатор сообщества.
     * @param int $limit Количество записей для получения.
     * @param int $offset Смещение для выборки записей.
     * @return Collection<ContextPost> Массив постов.
     */
    public function getLatestPosts(int|string $communityId, int $limit = 10, int $offset = 0): Collection;

    /**
     * Получить посты из сообщества за определенный период.
     *
     * @param int|string $communityId Идентификатор сообщества.
     * @param \DateTime $startDate Начальная дата периода (в формате 'Y-m-d H:i:s').
     * @param \DateTime|null $endDate Конечная дата периода (если null, используется текущее время).
     * @return Collection<ContextPost> Массив постов.
     */
    public function getPostsByDate(int|string $communityId, \DateTime $startDate, \DateTime $endDate = null): Collection;

    /**
     * Получить информацию о сообществе.
     *
     * @param int|string $communityId Идентификатор сообщества.
     * @return array Информация о сообществе.
     */
    public function getCommunityInfo(int|string $communityId): array;
}
