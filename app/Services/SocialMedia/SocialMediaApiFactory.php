<?php

namespace App\Services\SocialMedia;

class SocialMediaApiFactory
{
    public static function getService($sourceType)
    {
        return match ($sourceType) {
            'vk' => new VkApiService(),
            default => throw new \Exception('Unsupported social media source type: ' . $sourceType),
        };
    }
}
