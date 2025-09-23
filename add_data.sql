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
INSERT INTO tasks (name, description, specializing_id, location_id, budget, deadline, customer_id, executor_id, status_id, date)
 VALUES
('Ducimus sapiente ea quisquam et.','Non consequatur molestias necessitatibus magnam. Autem enim dolor occaecati et deleniti consectetur quam dolores.',8,7,7593,'2025-12-13 20:16:58',3,7,1,'2025-09-09 09:58:01'),
('Maxime similique quam officiis beatae ipsa nemo.','Quibusdam molestiae tenetur et expedita cum minima dolorum.',3,4,10680,'2025-10-08 08:17:42',2,NULL,2,'2025-08-30 15:44:15'),
('Earum ipsam enim corporis.','Repudiandae sint magni dolorum laudantium hic sed. Enim omnis sit est recusandae.',6,8,14900,'2026-03-05 16:44:05',1,6,2,'2025-08-22 18:40:04'),
('Maxime perferendis non voluptas sint veritatis nemo saepe.','Deleniti corrupti aliquam in rerum ut illo nam.',7,1,10500,'2026-03-08 04:48:22',2,NULL,1,'2025-09-03 03:23:24'),
('Fugit qui officiis temporibus in ut.','Pariatur omnis sit eos corporis consequuntur at.',8,2,17400,'2025-10-14 14:26:42',3,8,2,'2025-08-25 07:12:26');

TRUNCATE TABLE task_statuses;
INSERT INTO task_statuses (name,rus_name)
 VALUES
('new','Новое'),
('canceled','Отменено'),
('in_work','В работе'),
('done','Выполнено'),
('failed','Провалено');
