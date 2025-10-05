USE taskforce;

TRUNCATE TABLE towns;
INSERT INTO towns (name)
 VALUES
('Абакан'),
('Белгород'),
('Воронеж'),
('Екатеринбург'),
('Иваново'),
('Казань'),
('Липецк'),
('Нижний Новгород');

TRUNCATE TABLE locations;
INSERT INTO locations (name,latitude,longitude,town_id)
 VALUES
('Абакан','53.7223661','91.4437792',1),
('Белгород','50.5977351','36.5858236',2),
('Воронеж','51.6592378','39.1968284',3),
('Екатеринбург','56.8386326','60.6054887',4),
('Иваново','56.9994677','40.9728231',5),
('Казань','55.7943877','49.1115312',6),
('Липецк','52.6103027','39.5946266',7),
('Нижний Новгород','56.3242093','44.0053948',8);

TRUNCATE TABLE categories;
INSERT INTO categories (name,rus_name)
 VALUES
('courier','Курьерские услуги'),
('clean','Уборка'),
('cargo','Переезды'),
('neo','Компьютерная помощь'),
('flat','Ремонт квартирный'),
('repair','Ремонт техники'),
('beauty','Красота'),
('photo','Фото');

TRUNCATE TABLE tasks;
INSERT INTO tasks (name, description, category_id, location_id, budget, deadline, customer_id, executor_id, status_id, date)
 VALUES
('Ducimus sapiente ea quisquam et.','Non consequatur molestias necessitatibus magnam. Autem enim dolor occaecati et deleniti consectetur quam dolores.',8,7,7593,'2025-12-13 20:16:58',3,4,1,'2025-09-09 09:58:01'),
('Maxime similique quam officiis beatae ipsa nemo.','Quibusdam molestiae tenetur et expedita cum minima dolorum.',3,4,10680,'2025-10-08 08:17:42',2,NULL,2,'2025-08-30 15:44:15'),
('Earum ipsam enim corporis.','Repudiandae sint magni dolorum laudantium hic sed. Enim omnis sit est recusandae.',6,8,14900,'2026-03-05 16:44:05',1,6,2,'2025-08-22 18:40:04'),
('Maxime perferendis non voluptas sint veritatis nemo saepe.','Deleniti corrupti aliquam in rerum ut illo nam.',7,1,10500,'2026-03-08 04:48:22',2,NULL,1,'2025-09-03 03:23:24'),
('Fugit qui officiis temporibus in ut.','Pariatur omnis sit eos corporis consequuntur at.',8,2,17400,'2025-10-14 14:26:42',3,8,2,'2025-08-25 07:12:26');

-- Гарантируем исполнителя с id=4 у задачи с id=1 после заполнения БД задач (tasks) из сгенерированной фикстуры
UPDATE tasks SET executor_id = null, status_id = 1 WHERE id = 1;

TRUNCATE TABLE task_statuses;
INSERT INTO task_statuses (name,rus_name)
 VALUES
('new','Новое'),
('canceled','Отменено'),
('in_work','В работе'),
('done','Выполнено'),
('failed','Провалено');

TRUNCATE TABLE task_responses;
INSERT INTO task_responses (task_id, executor_id, description, budget, accepted, date)
 VALUES
(1,4,'Всё будет сделано в лучшем виде',2100, false,'2025-09-23 10:00:00'),
(1,5,'Всё будет сделано в наилучшем виде',2090, false,'2025-09-23 11:00:00'),
(1,6,'Всё будет сделано в самом лучшем виде',2080, false,'2025-09-23 12:00:00');

-- Пароли у всех пользователей - 1234
TRUNCATE TABLE users;
INSERT INTO `users` VALUES
(1,'Заказчик 1','c1@example.com','$2y$13$EJG5hcUJ.t98PZC1PlFHDOpDAtiGoP1xNjKjllvql7.nsTAlHx6ba',1,0,'2025-07-31 21:00:00','img/man-blond.jpg','1999-12-31 21:00:00','+71110987654','tg-c1','Заказчик 1 - информация'),
(2,'Заказчик 2','c2@example.com','$2y$13$tS1xQS0REIcEmX517WK7guidyq5zcX4kDhCQGPG6Hhv0VnfBYhU3G',1,0,'2025-07-31 21:00:00','img/man-brune.jpg','1999-12-31 21:00:00','+71110987653','tg-c2','Заказчик 2 - информация'),
(3,'Заказчик 3','c3@example.com','$2y$13$0yoaEGticeOiRQL/APnoXOOdorlOdU.vcTGSB2.ODP42dcE10j18.',1,0,'2025-07-31 21:00:00','img/man-coat.png','1999-12-31 21:00:00','+71110987652','tg-c3','Заказчик 3 - информация'),
(4,'Исполнитель 1','e1@example.com','$2y$13$iWB5oZcnu7CIfn6aUgOqV.QnpyJ6.1bueHCHWXnrHNup5c3pHJCBK',1,1,'2025-07-31 21:00:00','img/man-glasses.png','1999-12-31 21:00:00','+71110987659','tg-e1','Исполнитель 1 - информация'),
(5,'Исполнитель 2','e2@example.com','$2y$13$WfYuI9hPUq/wOmrSKVhrLe0i85Dns7BIuG9sAMAOUjINhojlWqBl6',1,1,'2025-07-31 21:00:00','img/man-hat.png','1999-12-31 21:00:00','+71110987658','tg-e2','Исполнитель 2 - информация'),
(6,'Исполнитель 3','e3@example.com','$2y$13$gQgUs2Rqd2fbAPC4jpyecensLe.y6yF.rfBeIiOOMX5Xkk3dcwQsa',1,1,'2025-07-31 21:00:00','img/man-sweater.png','1999-12-31 21:00:00','+71110987657','tg-e3','Исполнитель 3 - информация'),
(7,'Петр Петров Исполнитель','e4@example.com','$2y$13$G/MA/o45VqVw3ONxZBnQ6u8e.JvBpFjJNKwNZP/RL7bty/pDkwi6y',2,1,'2025-09-26 19:51:58',NULL,NULL,NULL,NULL,NULL),
(8,'Иван Иванов Заказчик','c4@example.com','$2y$13$PQ5M5A/GB9UhfnBgcE1RBeW8VZRqdvfoi6LicnMhkRmf.WuyTwak.',2,0,'2025-09-26 19:54:00',NULL,NULL,NULL,NULL,NULL);

-- Создаёт уникальный индекс на таблицу users по полю email
CREATE UNIQUE INDEX idx_email_unique ON users (email);

-- выгружает дамп таблицы users (запускать не в консоли mysql а в терминале)
-- mysqldump -u sqladmin -p --no-create-info --skip-triggers --compact taskforce users > users_data.sql

TRUNCATE TABLE customer_reviews;
INSERT INTO customer_reviews (customer_id, executor_id, task_id, description, rating, date)
 VALUES
(1,4,12,'Всё было сделано в лучшем виде', 500,'2025-09-23 10:00:00'),
(2,4,1,'Всё было сделано нормально', 400, '2025-09-23 11:00:00'),
(1,5,11,'Всё было сделано в самом лучшем виде', 500,'2025-09-23 12:00:00');

TRUNCATE TABLE executor_category;
INSERT INTO executor_category (user_id, category_id)
 VALUES
(4,1),(4,3),(4,5),
(5,4),(5,6),(5,8),
(6,2),(6,3);
