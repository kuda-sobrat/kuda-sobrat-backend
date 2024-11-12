<?php

namespace App\Services;

use App\Contracts\Interfaces\SocialMediaServiceInterface;
use App\Models\Community;
use App\Models\ContextAttachment;
use App\Models\ContextPost;
use App\Models\SocialNetwork;
use App\Services\SocialMedia\SocialMediaApiFactory;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Collection;

class CommunityService
{
    /**
     * Получает последние посты сообщества
     *
     * @param Community $community
     * @param int $limit
     * @param int $offset
     * @param null $since
     * @param null $sinceId
     * @return Collection<ContextPost>
     * @throws GuzzleException
     */
    public function getLatestPosts(Community $community, int $limit = 10, int $offset = 0, $since = null, $sinceId = null): Collection
    {
        // Итерируем по социальным сетям, связанным с сообществом
        $allContextPosts = new Collection();

        /** @var SocialNetwork $socialNetwork */
        foreach ($community->socialNetworks as $socialNetwork) {
            try {
                $socialNetworkService = SocialMediaApiFactory::getService($socialNetwork->name);
                // Предполагаем, что у нас есть идентификатор сообщества в социальной сети
                $socialNetworkCommunityId = $socialNetwork->pivot->social_network_community_id;

                // Получаем последние посты из социальной сети
                $contextPosts = $socialNetworkService->getLatestPosts($socialNetworkCommunityId, $limit, $offset, $since, $sinceId, ['social_link_id' => $community->socialLinks()->where('social_network_id', $socialNetwork->pivot->social_network_id)->first()->id]);

                $allContextPosts = $allContextPosts->merge($contextPosts);
            } catch (\Exception $exception) {
                dump($exception->getMessage(), "ID сообщества: " . $community->id);
                continue;
            }
        }

        return $allContextPosts;
    }

    /**
     * Обновляет актуальную информацию о сообществе.
     *
     * @param Community $community
     * @return void
     */
    public function updateCommunityInfo(Community $community)
    {
        // Добавьте здесь логику для обновления информации о сообществе
        // Например, запрос к API для получения новых данных

        // Пример:
        // $newData = $this->apiClient->getCommunityData($community->external_id);
        // $community->update($newData);
    }

    /**
     * Проводит верификацию сообщества.
     *
     * @param Community $community
     * @return void
     */
    public function verifyCommunity(Community $community)
    {
        // Добавьте здесь логику для верификации сообщества

        // Пример:
        // $isVerified = $this->verificationService->verify($community);
        // $community->is_verified = $isVerified;
        // $community->save();
    }
}
