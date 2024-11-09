<?php

namespace App\Services;

use App\Models\Community;
use App\Models\ContextPost;
use App\Repositories\CommunityRepository;
use App\Services\SocialMedia\SocialMediaApiFactory;
use App\Services\SocialMedia\VkApiService;
use GuzzleHttp\Exception\GuzzleException;

/**
 * Сервис верификации сообществ.
 */
class CommunityVerificationService
{
    public function __construct(
        protected CommunityRepository $communityRepository,
        protected VkApiService $vkApiService,
    ) {
    }

    /**
     * Верифицирует сообщество.
     *
     * @param Community $community
     * @return void
     * @throws GuzzleException
     */
    public function verifyCommunity(Community $community)
    {
        try {
            foreach ($community->socialNetworks as $socialNetwork) {
                $posts = SocialMediaApiFactory::getService($socialNetwork->name)
                    ->getLatestPosts($socialNetwork->pivot->social_network_community_id);

                /** @var ContextPost $post */
                foreach ($posts as $post) {
                    /** @var ContextPost $contextPost */
                    $contextPost = ContextPost::query()->updateOrCreate(
                        [
                            'source_id' => $post->source_id,
                            'social_link_id' => $community->socialLinkBySocialNetwork($socialNetwork)->id
                        ],
                        $post->getAttributes()
                    );
                    $contextPost->attachments()->delete();
                    foreach ($post->attachments as $attachment) {
                        $attachment->context_id = $contextPost->id;
                        $attachment->save();
                    }
                }
            }
        } catch (\Exception $e) {
            dump($e->getMessage());
        }
    }

    /**
     * Верифицирует сообщества.
     *
     * @return void
     * @throws GuzzleException
     */
    public function verifyCommunities()
    {
        $communities = $this->communityRepository->getCommunitiesToVerify();

        foreach ($communities as $community) {
            $this->verifyCommunity($community);
        }
    }
}
