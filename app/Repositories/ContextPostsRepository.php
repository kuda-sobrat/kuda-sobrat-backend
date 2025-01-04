<?php

namespace App\Repositories;

use App\Models\ContextAttachment;
use App\Models\ContextPost;
use Illuminate\Support\Collection;

class ContextPostsRepository
{
    /**
     * Сохраняет посты сообщества
     *
     * @param Collection<ContextPost> $contextPosts
     * @return void
     */
    public function savePosts(Collection $contextPosts)
    {
        foreach ($contextPosts as $contextPostData) {
            /** @var ContextPost $contextPost */
            $contextPost = ContextPost::query()->updateOrCreate(
                [
                    'source_id' => $contextPostData->source_id,
                    'social_link_id' => $contextPostData->social_link_id,
                ],
                $contextPostData->getAttributes()
            );

            $contextPost->attachments()->delete();
            /** @var ContextAttachment $attachment */
            foreach ($contextPostData->attachments as $attachment) {
                $attachment->context_id = $contextPost->id;
                $attachment->save();
            }
        }
    }
}
