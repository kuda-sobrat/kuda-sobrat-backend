<?php

namespace App\Repositories;

use App\Models\Community;
use Carbon\Carbon;

class CommunityRepository
{
    public function getCommunitiesToVerify()
    {
        return Community::whereNull('last_checked_at')
            ->orWhere('last_checked_at', '<', Carbon::now()->subDays(7))
            ->get();
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
}
