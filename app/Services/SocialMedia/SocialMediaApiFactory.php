<?php

namespace App\Services\SocialMedia;

use App\Contracts\Interfaces\SocialMediaServiceInterface;

class SocialMediaApiFactory
{
    /**
     * @param $sourceType
     * @return VkApiService
     * @throws \Exception
     */
    public static function getService($sourceType): SocialMediaServiceInterface
    {
        return match ($sourceType) {
            'vk' => new VkApiService(),
            default => throw new \Exception('Unsupported social media source type: ' . $sourceType),
        };
    }
}
