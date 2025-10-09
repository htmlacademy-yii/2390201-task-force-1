<?php

use yii\db\Migration;
use yii\db\Query;

class m251009_185039_init_taskforce1009_db extends Migration
{
    public function safeUp()
    {
        // Создание таблицы locations
        $this->createTable('{{%locations}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255)->notNull(),
            'latitude' => $this->decimal(10, 8)->notNull(),
            'longitude' => $this->decimal(10, 8)->notNull(),
        ]);

        // Создание таблицы categories
        $this->createTable('{{%categories}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(128)->notNull(),
            'rus_name' => $this->string(128)->notNull(),
        ]);

        // Создание таблицы task_statuses
        $this->createTable('{{%task_statuses}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(128)->notNull(),
            'rus_name' => $this->string(128)->notNull(),
        ]);

        // Создание таблицы users
        $this->createTable('{{%users}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(256)->notNull(),
            'email' => $this->string(128)->notNull()->unique(),
            'password' => $this->string(128)->notNull(),
            'location_id' => $this->integer()->notNull(),
            'is_executor' => $this->boolean()->notNull(),
            'reg_date' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'avatar' => $this->string(256),
            'birth_date' => $this->timestamp(),
            'phone' => $this->string(64),
            'telegram' => $this->string(128),
            'vk_id' => $this->integer()->unique(),
            'information' => $this->text(),
        ]);

        // Создание таблицы executor_category
        $this->createTable('{{%executor_category}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'category_id' => $this->integer()->notNull(),
        ]);

        // Создание таблицы tasks
        $this->createTable('{{%tasks}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(256)->notNull(),
            'description' => $this->text(2048)->notNull(),
            'category_id' => $this->integer()->notNull(),
            'location_id' => $this->integer(),
            'budget' => $this->integer(),
            'deadline' => $this->timestamp(),
            'customer_id' => $this->integer()->notNull(),
            'executor_id' => $this->integer(),
            'status_id' => $this->integer()->notNull(),
            'date' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        // Создание таблицы tasks_files
        $this->createTable('{{%tasks_files}}', [
            'id' => $this->primaryKey(),
            'task_id' => $this->integer()->notNull(),
            'file_path' => $this->string(256)->notNull(),
            'file_size' => $this->integer()->notNull(),
            'user_filename' => $this->string(256)->notNull(),
        ]);

        // Создание таблицы task_responses
        $this->createTable('{{%task_responses}}', [
            'id' => $this->primaryKey(),
            'task_id' => $this->integer()->notNull(),
            'executor_id' => $this->integer()->notNull(),
            'description' => $this->text(1024),
            'budget' => $this->integer()->notNull(),
            'accepted' => $this->boolean()->notNull()->defaultValue(false),
            'declined' => $this->boolean()->notNull()->defaultValue(false),
            'date' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        // Создание таблицы customer_reviews
        $this->createTable('{{%customer_reviews}}', [
            'id' => $this->primaryKey(),
            'customer_id' => $this->integer()->notNull(),
            'executor_id' => $this->integer()->notNull(),
            'task_id' => $this->integer()->notNull(),
            'description' => $this->text(1024),
            'rating' => $this->integer()->notNull(),
            'date' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        // Вставка начальных данных

        // locations
        $this->batchInsert('{{%locations}}', ['name', 'latitude', 'longitude'], [
            ['Абакан', '53.7223661', '91.4437792'],
            ['Белгород', '50.5977351', '36.5858236'],
            ['Воронеж', '51.6592378', '39.1968284'],
            ['Екатеринбург', '56.8386326', '60.6054887'],
            ['Иваново', '56.9994677', '40.9728231'],
            ['Казань', '55.7943877', '49.1115312'],
            ['Липецк', '52.6103027', '39.5946266'],
            ['Нижний Новгород', '56.3242093', '44.0053948'],
        ]);

        // categories
        $this->batchInsert('{{%categories}}', ['name', 'rus_name'], [
            ['courier', 'Курьерские услуги'],
            ['clean', 'Уборка'],
            ['cargo', 'Переезды'],
            ['neo', 'Компьютерная помощь'],
            ['flat', 'Ремонт квартирный'],
            ['repair', 'Ремонт техники'],
            ['beauty', 'Красота'],
            ['photo', 'Фото'],
        ]);

        // task_statuses
        $this->batchInsert('{{%task_statuses}}', ['name', 'rus_name'], [
            ['new', 'Новое'],
            ['canceled', 'Отменено'],
            ['in_work', 'В работе'],
            ['done', 'Выполнено'],
            ['failed', 'Провалено'],
        ]);

        // users (пароли уже хэшированы)
        $this->batchInsert('{{%users}}', [
            'id', 'name', 'email', 'password', 'location_id', 'is_executor',
            'reg_date', 'avatar', 'birth_date', 'phone', 'telegram', 'information', 'vk_id'
        ], [
            [1, 'Заказчик 1', 'c1@example.com', '$2y$13$EJG5hcUJ.t98PZC1PlFHDOpDAtiGoP1xNjKjllvql7.nsTAlHx6ba', 1, 0, '2025-07-31 21:00:00', 'uploads/avatars/avatar_1_1759930418.jpg', '2000-01-01 10:33:38', '+71110987654', 'tg-c1', 'Заказчик 1 - информация', null],
            [2, 'Заказчик 2', 'c2@example.com', '$2y$13$tS1xQS0REIcEmX517WK7guidyq5zcX4kDhCQGPG6Hhv0VnfBYhU3G', 1, 0, '2025-07-31 21:00:00', 'uploads/avatars/avatar_2_1759930502.jpg', '2000-01-01 10:35:02', '+71110987653', 'tg-c2', 'Заказчик 2 - информация', null],
            [3, 'Заказчик 3', 'c3@example.com', '$2y$13$0yoaEGticeOiRQL/APnoXOOdorlOdU.vcTGSB2.ODP42dcE10j18.', 1, 0, '2025-07-31 21:00:00', 'uploads/avatars/avatar_3_1759930538.png', '2000-01-01 10:35:38', '+71110987652', 'tg-c3', 'Заказчик 3 - информация', null],
            [4, 'Исполнитель 1', 'e1@example.com', '$2y$13$iWB5oZcnu7CIfn6aUgOqV.QnpyJ6.1bueHCHWXnrHNup5c3pHJCBK', 1, 1, '2025-07-31 21:00:00', 'uploads/avatars/avatar_4_1759930583.png', '1999-01-01 10:41:46', '+71110987659', 'tg-e1', 'Исполнитель 1 - информация', null],
            [5, 'Исполнитель 2', 'e2@example.com', '$2y$13$WfYuI9hPUq/wOmrSKVhrLe0i85Dns7BIuG9sAMAOUjINhojlWqBl6', 1, 1, '2025-07-31 21:00:00', 'uploads/avatars/avatar_5_1759930625.png', '2000-01-01 10:37:05', '+71110987658', 'tg-e2', 'Исполнитель 2 - информация', null],
            [6, 'Исполнитель 3', 'e3@example.com', '$2y$13$gQgUs2Rqd2fbAPC4jpyecensLe.y6yF.rfBeIiOOMX5Xkk3dcwQsa', 1, 1, '2025-07-31 21:00:00', 'uploads/avatars/avatar_6_1759930659.png', '2000-01-01 10:39:45', '+71110987657', 'tg-e3', 'Исполнитель 3 - информация', null],
        ]);

        // executor_category
        $this->batchInsert('{{%executor_category}}', ['user_id', 'category_id'], [
            [4, 1], [4, 3], [4, 5],
            [5, 4], [5, 6], [5, 8],
            [6, 2], [6, 3],
        ]);

        // tasks
        $this->batchInsert('{{%tasks}}', [
            'name', 'description', 'category_id', 'location_id', 'budget',
            'deadline', 'customer_id', 'executor_id', 'status_id', 'date'
        ], [
            ['Ducimus sapiente ea quisquam et.', 'Non consequatur molestias necessitatibus magnam. Autem enim dolor occaecati et deleniti consectetur quam dolores.', 8, 1, 7593, '2025-12-13 20:16:58', 2, null, 1, '2025-09-19 09:58:01'],
            ['Maxime similique quam officiis beatae ipsa nemo.', 'Quibusdam molestiae tenetur et expedita cum minima dolorum.', 3, 4, 10680, '2025-10-08 08:17:42', 2, null, 2, '2025-08-30 15:44:15'],
            ['Earum ipsam enim corporis.', 'Repudiandae sint magni dolorum laudantium hic sed. Enim omnis sit est recusandae.', 6, 8, 14900, '2026-03-05 16:44:05', 1, 6, 2, '2025-08-22 18:40:04'],
            ['Maxime perferendis non voluptas sint veritatis nemo saepe.', 'Deleniti corrupti aliquam in rerum ut illo nam.', 7, 1, 10500, '2026-03-08 04:48:22', 2, null, 1, '2025-09-03 03:23:24'],
            ['Fugit qui officiis temporibus in ut.', 'Pariatur omnis sit eos corporis consequuntur at.', 8, 2, 17400, '2025-10-14 14:26:42', 3, 8, 2, '2025-08-25 07:12:26'],
        ]);

        // tasks_files
        $this->batchInsert('{{%tasks_files}}', ['task_id', 'file_path', 'file_size', 'user_filename'], [
            [1, 'uploads/tasks/upload68df5e07652gs.jpg', 34600, 'map.jpg'],
            [1, 'uploads/tasks/upload68df5e07663rt.png', 17500, 'polygon.png'],
        ]);

        // task_responses
        $this->batchInsert('{{%task_responses}}', ['task_id', 'executor_id', 'description', 'budget', 'accepted', 'declined', 'date'], [
            [1, 4, 'Всё будет сделано в лучшем виде', 2100, false, false, '2025-09-23 10:00:00'],
            [1, 5, 'Всё будет сделано в наилучшем виде', 2090, false, false, '2025-09-23 11:00:00'],
            [1, 6, 'Всё будет сделано в самом лучшем виде', 2080, false, false, '2025-09-23 12:00:00'],
        ]);

        // customer_reviews
        $this->batchInsert('{{%customer_reviews}}', ['customer_id', 'executor_id', 'task_id', 'description', 'rating', 'date'], [
            [1, 4, 12, 'Всё было сделано в лучшем виде', 500, '2025-09-23 10:00:00'],
            [2, 4, 1, 'Всё было сделано нормально', 400, '2025-09-23 11:00:00'],
            [1, 5, 11, 'Всё было сделано в самом лучшем виде', 500, '2025-09-23 12:00:00'],
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('{{%customer_reviews}}');
        $this->dropTable('{{%task_responses}}');
        $this->dropTable('{{%tasks_files}}');
        $this->dropTable('{{%tasks}}');
        $this->dropTable('{{%executor_category}}');
        $this->dropTable('{{%users}}');
        $this->dropTable('{{%task_statuses}}');
        $this->dropTable('{{%categories}}');
        $this->dropTable('{{%locations}}');
    }
}
