USE taskforce;

TRUNCATE TABLE locations;
INSERT INTO locations (name,latitude,longitude)
 VALUES
('Абакан','53.7223661','91.4437792'),
('Белгород','50.5977351','36.5858236'),
('Воронеж','51.6592378','39.1968284'),
('Екатеринбург','56.8386326','60.6054887'),
('Иваново','56.9994677','40.9728231'),
('Казань','55.7943877','49.1115312'),
('Липецк','52.6103027','39.5946266'),
('Нижний Новгород','56.3242093','44.0053948');


TRUNCATE TABLE specializations;
INSERT INTO specializations (name,icon)
 VALUES
('Курьерские услуги','courier'),
('Уборка','clean'),
('Переезды','cargo'),
('Компьютерная помощь','neo'),
('Ремонт квартирный','flat'),
('Ремонт техники','repair'),
('Красота','beauty'),
('Фото','photo');

TRUNCATE TABLE tasks;
INSERT INTO tasks (name, description, specializing_id, location_id, budget, deadline, customer_id, executor_id, status_id, date)
 VALUES
('Ducimus sapiente ea quisquam et.','Non consequatur molestias necessitatibus magnam. Autem enim dolor occaecati et deleniti consectetur quam dolores.',8,7,7593,'2025-12-13 20:16:58',3,7,1,'2025-09-09 09:58:01'),
('Maxime similique quam officiis beatae ipsa nemo.','Quibusdam molestiae tenetur et expedita cum minima dolorum.',3,4,10680,'2025-10-08 08:17:42',2,6,2,'2025-08-30 15:44:15'),
('Earum ipsam enim corporis.','Repudiandae sint magni dolorum laudantium hic sed. Enim omnis sit est recusandae.',6,8,14900,'2026-03-05 16:44:05',1,6,2,'2025-08-22 18:40:04'),
('Maxime perferendis non voluptas sint veritatis nemo saepe.','Deleniti corrupti aliquam in rerum ut illo nam.',7,1,10500,'2026-03-08 04:48:22',2,NULL,1,'2025-09-03 03:23:24'),
('Fugit qui officiis temporibus in ut.','Pariatur omnis sit eos corporis consequuntur at.',8,2,17400,'2025-10-14 14:26:42',3,8,2,'2025-08-25 07:12:26');
