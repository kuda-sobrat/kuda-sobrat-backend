<?php

namespace App\Services;

use App\Enums\ProcessStatusEnum;
use App\Events\CommunitiesVerifiedEvent;
use App\Jobs\VerifyCommunityJob;
use App\Models\Community;
use App\Repositories\CommunityRepository;
use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Сервис верификации сообществ.
 */
class CommunityVerificationService
{
    public function __construct(
        protected CommunityRepository $communityRepository,
    ) {
    }

    /**
     * Верифицирует сообщество.
     *
     * @param Community $community
     * @return bool
     */
    public function verifyCommunity(Community $community): bool
    {
        $hasEventPosts = $community->contextPosts()
            ->whereNotNull('event_id')
            ->where('status', '=', ProcessStatusEnum::Completed)
            ->exists();

        if ($hasEventPosts) {
            $community->verification_status = ProcessStatusEnum::Completed;
            $community->is_verified = true;
            $community->save();

            Log::info("Сообщество {$community->id} успешно верифицировано (найдены мероприятия).");
            return true;
        } else {
            $community->verification_status = ProcessStatusEnum::Failed;
            $community->is_verified = false;
            $community->save();

            Log::info("В сообществе {$community->id} не обнаружены посты с мероприятиями.");
            return false;
        }
    }

    /**
     * Верифицирует сообщества.
     *
     * @return void
     * @throws Throwable
     */
    public function verifyCommunities()
    {
        $communities = $this->communityRepository->getCommunitiesToVerify();

        $jobs = [];

        foreach ($communities as $community) {
            $jobs[] = new VerifyCommunityJob($community->id);
        }

        Bus::batch($jobs)
            ->name('Пакет верификации сообществ')
            ->then(function () {
                event(new CommunitiesVerifiedEvent());
            })
            ->catch(function (Batch $batch, Throwable $e) {
                // TODO: Логирование
                dump($e->getMessage());
//                \Log::error('Ошибка в пакете задач: ' . $e->getMessage());
            })
            ->finally(function () {
                // Этот метод вызывается после завершения всех задач, независимо от их статусов
            })
            ->dispatch();
    }
}
