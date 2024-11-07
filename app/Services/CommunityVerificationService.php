<?php

namespace App\Services;

use App\Models\Community;
use App\Models\ContextPost;
use App\Repositories\CommunityRepository;
use App\Services\SocialMedia\SocialMediaApiFactory;
use App\Services\SocialMedia\VkApiService;

class CommunityVerificationService
{
    public function __construct(
        protected CommunityRepository $communityRepository,
        protected VkApiService $vkApiService,
    ) {
    }

    public function verifyCommunity(Community $community)
    {
        try {
            foreach ($community->socialNetworks as $socialNetwork) {
                $posts = SocialMediaApiFactory::getService($socialNetwork->name)
                    ->getLatestPosts($socialNetwork->pivot->social_network_community_id);

                /** @var ContextPost $post */
                foreach ($posts as $post) {
                    $post->source_type = $socialNetwork->name;
                    /** @var ContextPost $contextPost */
                    $contextPost = ContextPost::query()->updateOrCreate(
                        [
                            'source_id' => $post->source_id,
                            'source_type' => $post->source_type,
                            'community_id' => $post->community_id,
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

    public function verifyCommunities()
    {
        $communities = $this->communityRepository->getCommunitiesToVerify();

        foreach ($communities as $community) {
            $this->verifyCommunity($community);
        }
    }
}
