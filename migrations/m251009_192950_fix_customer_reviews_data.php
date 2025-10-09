<?php

use yii\db\Migration;

class m251009_192950_fix_customer_reviews_data extends Migration
{
    public function safeUp()
    {
        $this->truncateTable('{{%customer_reviews}}');
        $this->truncateTable('{{%tasks}}');

        $this->batchInsert('{{%tasks}}', [
            'name', 'description', 'category_id', 'location_id', 'budget',
            'deadline', 'customer_id', 'executor_id', 'status_id', 'date'
        ], [
            ['Ducimus sapiente ea quisquam et.', 'Non consequatur molestias necessitatibus magnam. Autem enim dolor occaecati et deleniti consectetur quam dolores.', 8, 1, 7593, '2025-12-13 20:16:58', 2, null, 1, '2025-09-19 09:58:01'],
            ['Maxime similique quam officiis beatae ipsa nemo.', 'Quibusdam molestiae tenetur et expedita cum minima dolorum.', 3, 4, 10680, '2025-10-08 08:17:42', 2, null, 2, '2025-08-30 15:44:15'],
            ['Earum ipsam enim corporis.', 'Repudiandae sint magni dolorum laudantium hic sed. Enim omnis sit est recusandae.', 6, 8, 14900, '2026-03-05 16:44:05', 1, 6, 2, '2025-08-22 18:40:04'],
            ['Maxime perferendis non voluptas sint veritatis nemo saepe.', 'Deleniti corrupti aliquam in rerum ut illo nam.', 7, 1, 10500, '2026-03-08 04:48:22', 2, null, 1, '2025-09-03 03:23:24'],
            ['Fugit qui officiis temporibus in ut.', 'Pariatur omnis sit eos corporis consequuntur at.', 8, 2, 17400, '2025-10-14 14:26:42', 3, 8, 2, '2025-08-25 07:12:26'],
            ['Задача 6', 'Заказчик 1; Исполнитель 1 - завершена', 8, 1, 7400, '2025-10-14 14:26:42', 1, 4, 4, '2025-08-25 07:12:26'],
            ['Задача 7', 'Заказчик 2; Исполнитель 1 - завершена', 8, 1, 5500, '2025-10-16 14:26:42', 2, 4, 4, '2025-09-23 07:12:26'],
            ['Задача 8', 'Заказчик 1; Исполнитель 2 - завершена', 8, 1, 3200, '2025-10-25 14:26:42', 1, 5, 4, '2025-09-01 07:12:26'],
        ]);

        $this->batchInsert('{{%customer_reviews}}', [
            'customer_id', 'executor_id', 'task_id', 'description', 'rating', 'date'
        ], [
            [1, 4, 6, 'Всё было сделано в лучшем виде', 500, '2025-09-23 10:00:00'],
            [2, 4, 7, 'Всё было сделано нормально', 400, '2025-09-23 11:00:00'],
            [1, 5, 8, 'Всё было сделано в самом лучшем виде', 500, '2025-09-23 12:00:00'],
        ]);
    }

    public function safeDown()
    {
        $this->truncateTable('{{%customer_reviews}}');
        $this->truncateTable('{{%tasks}}');
    }
}
