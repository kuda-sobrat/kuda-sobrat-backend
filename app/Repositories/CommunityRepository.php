<?php

namespace App\Repositories;

use App\Enums\ProcessStatusEnum;
use App\Models\Community;
use App\Models\ContextPost;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class CommunityRepository
{
    // TODO: Пересмотреть
    public function getCommunitiesToVerify()
    {
        return Community::where('verification_status', '=', ProcessStatusEnum::Created)
            ->where('is_verified', '=', false)
            ->get();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function getCommunitiesToCheck()
    {
        return Community::query()
            ->where('is_verified', '=', true)
            ->where(function($query) {
                $query->where('last_checked_at', '>=', Carbon::now()->subDay())
                    ->orWhereNull('last_checked_at');
            });
    }

    public function updateCommunityVerificationStatus(Community $community, bool $isVerified)
    {
        $community->is_verified = $isVerified;
        $community->last_checked_at = Carbon::now();
        $community->save();
    }

    public function updateLastCheckedAt(Community $community)
    {
        $community->last_checked_at = Carbon::now();
        $community->save();
    }

    /**
     * @param $communityId
     * @return Community|null
     */
    public function get($communityId): Community|null
    {
        /** @phpstan-ignore-next-line */
        return Community::query()->where('id', '=', $communityId)->first();
    }

    /**
     * @param int|Community $communityId
     * @return void
     */
    public function getDescription($communityId)
    {
        $community = $communityId instanceof Community ? $communityId : $this->get($communityId);
        return $community->description;
    }

    /**
     * @param Community $community
     * @return ContextPost|null
     */
    public function getLatestPost(Community $community)
    {
        return $community->contextPosts()->orderBy('created_at', 'desc')->first();
    }

    /**
     * Получает список постов с мероприятиями сообщества
     *
     * @param Community $community
     * @return Collection<ContextPost>
     */
    public function getContextPostsWithEvents(Community $community): Collection
    {
        return $community->contextPosts()->with('event')->get();
    }

    /**
     * Получает список верифицированных сообществ
     *
     * @return Collection<Community>
     */
    public function getVerifiedCommunities(): Collection
    {
        return Community::query()->where('is_verified', '=', true)->get();
    }
}
