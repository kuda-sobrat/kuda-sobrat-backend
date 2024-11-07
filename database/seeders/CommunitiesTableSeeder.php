<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CommunitiesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $communities = [
            [
                'id' => 1,
                'name' => 'Куда пойти в Воронеже | Афиша мероприятий',
                'description' => 'Присылайте в "Предложить новость" информацию о предстоящих мероприятиях в Воронеже. Это будет размещено на стене группы. По вопросам сотрудничества обращаться через сообщения нашему сообществу.',
            ],
            [
                'id' => 2,
                'name' => 'Не Школа Вокала Воронеж',
                'description' => 'Не Школа - это твои вокальные навыки, эмоции, возможность выступить на сцене с любимой песней, и, конечно же, результат! https://vk.com/app6013442_-219904664?form_id=13#form_..

🏆 ТОПОВАЯ СЕТЬ музыкальных школ в России и странах СНГ.

● Обучение вокалу по авторской методике ДЛЯ ВЗРОСЛЫХ независимо от исходных вокальных данных;
● Лучшие преподаватели ВОРОНЕЖА;
● Самые громкие ПАТИ, квартирники, отчетные концерты и профессиональные мастер-классы;
● Расположение в удобном районе города;
● Гибкий график занятий (можно заниматься утром, днем и вечером, в будни и выходные).

Оставляй заявку: https://vk.com/app6013442_-219904664?form_id=13#form_..',
            ],
            [
                'id' => 3,
                'name' => 'Новый театр | Воронеж',
                'description' => 'Независимый профессиональный театр города Воронежа. В репертуаре - спектакли самой разной направленности: современная драматургия, документальный театр, актуальная классика, детские, музыкальные и пластические спектакли.

Инстаграм театра:
https://www.instagram.com/newteatrvrn

Телеграм театра:
https://t.me/newteatr',
            ],
            [
                'id' => 4,
                'name' => 'Настольные игры | ПараDice | Воронеж',
                'description' => 'Сообщество ПараDice - место, предоставляющее опции:
- Прокат настольных игр
- Проведение игровечеров
- Помощь в разборе правил

Находимся по адресу: Фридриха Энгельса, 24б, офис 50, домофон 50., жилой подъезд 2 этаж, правая дверь.

Телефон +7 (900) 299-12-93',
            ],
            [
                'id' => 5,
                'name' => 'Никитинский театр',
                'description' => 'Независимый театр под руководством Бориса Алексеева.
Фойе театра - территория выставок и спецпроектов.

Воронеж, ул. Бакунина, 2а

Как нас найти: https://nikitincenter.ru/#contacts

тел. +7 473 228 73 82

Анкета зрителя: goo.gl/41tufE
Заявки на прослушивание: goo.gl/Ac4Xmp

Сайт: nikitincenter.ru

Билеты:
http://nikitincenter.ru',
            ],
            [
                'id' => 6,
                'name' => 'REDSUN | Театр Красного Солнца',
                'description' => 'этюды не этюды

Театр Красного Солнца - он же REDSUN - некоммерческий любительский экспериментальный театр
Работаем в жанрах: театр художника, иммерсивный театр, документальный театр, пластический',
            ],
        ];

        $communitySocialLinks = [
            [
                'community_id' => 1,
                'social_network_id' => 1,
                'social_network_community_id' => '22312958',
                'path' => '/mtvgo',
            ],
            [
                'community_id' => 1,
                'social_network_id' => 2,
                'social_network_community_id' => '-1002151070939',
                'path' => '/mtv36go',
            ],
            [
                'community_id' => 2,
                'social_network_id' => 1,
                'social_network_community_id' => '219904664',
                'path' => '/vokal_voronezh',
            ],
            [
                'community_id' => 2,
                'social_network_id' => 2,
                'social_network_community_id' => '-1002389406953',
                'path' => '/neschoolbuteroff',
            ],
            [
                'community_id' => 3,
                'social_network_id' => 1,
                'social_network_community_id' => '111403571',
                'path' => '/newteatr',
            ],
            [
                'community_id' => 3,
                'social_network_id' => 2,
                'social_network_community_id' => '-1001618158821',
                'path' => '/newteatr',
            ],
            [
                'community_id' => 4,
                'social_network_id' => 1,
                'social_network_community_id' => '208711281',
                'path' => '/para_dice_vrn',
            ],
            [
                'community_id' => 5,
                'social_network_id' => 1,
                'social_network_community_id' => '30245777',
                'path' => '/nikitincenter',
            ],
            [
                'community_id' => 6,
                'social_network_id' => 1,
                'social_network_community_id' => '220086955',
                'path' => '/redsuntheatre',
            ],
        ];

        foreach ($communities as $community) {
            DB::table('communities')->updateOrInsert(
                ['id' => $community['id']], // Уникальный ключ для проверки
                $community // Данные для вставки
            );
        }

        DB::table('community_social_links')->insert($communitySocialLinks);
    }
}
