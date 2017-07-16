SET NAMES 'utf8';
USE dbcast;
DROP TABLE IF EXISTS auth_rule;
CREATE TABLE auth_rule (
  name VARCHAR(64) NOT NULL,
  data TEXT DEFAULT NULL,
  created_at INT(11) DEFAULT NULL,
  updated_at INT(11) DEFAULT NULL,
  PRIMARY KEY (name)
)
ENGINE = INNODB
CHARACTER SET utf8
COLLATE utf8_unicode_ci;

--
-- Описание для таблицы cms_log_error
--
DROP TABLE IF EXISTS cms_log_error;
CREATE TABLE cms_log_error (
  id INT(11) NOT NULL AUTO_INCREMENT,
  code SMALLINT(6) NOT NULL COMMENT 'Код ошибки',
  message TEXT NOT NULL COMMENT 'Дополнительная информация',
  url TEXT DEFAULT NULL COMMENT 'Страница',
  referer TEXT DEFAULT NULL COMMENT 'Откуда пришел',
  count SMALLINT(6) UNSIGNED NOT NULL COMMENT 'Кол-во',
  time INT(11) UNSIGNED NOT NULL COMMENT 'Время|datetime',
  update_time INT(11) UNSIGNED NOT NULL COMMENT 'Последнее изменение|datetime',
  ip INT(11) UNSIGNED NOT NULL COMMENT 'IP-адрес|text',
  PRIMARY KEY (id)
)
ENGINE = INNODB
AUTO_INCREMENT = 119084
AVG_ROW_LENGTH = 524
CHARACTER SET utf8
COLLATE utf8_general_ci
COMMENT = 'Журнал ошибок';

--
-- Описание для таблицы gallery_image
--
DROP TABLE IF EXISTS gallery_image;
CREATE TABLE gallery_image (
  id INT(11) NOT NULL AUTO_INCREMENT,
  type VARCHAR(255) DEFAULT NULL,
  ownerId VARCHAR(255) NOT NULL,
  rank INT(11) NOT NULL DEFAULT 0,
  name VARCHAR(255) DEFAULT NULL,
  description TEXT DEFAULT NULL,
  PRIMARY KEY (id)
)
ENGINE = INNODB
AUTO_INCREMENT = 16865
AVG_ROW_LENGTH = 103
CHARACTER SET utf8
COLLATE utf8_general_ci;

--
-- Описание для таблицы migration
--
DROP TABLE IF EXISTS migration;
CREATE TABLE migration (
  version VARCHAR(180) NOT NULL,
  apply_time INT(11) DEFAULT NULL,
  PRIMARY KEY (version)
)
ENGINE = INNODB
AVG_ROW_LENGTH = 273
CHARACTER SET utf8
COLLATE utf8_general_ci;

--
-- Описание для таблицы module_digest
--
DROP TABLE IF EXISTS module_digest;
CREATE TABLE module_digest (
  id INT(11) NOT NULL AUTO_INCREMENT,
  title VARCHAR(255) DEFAULT NULL COMMENT 'Название|title',
  is_active SMALLINT(1) DEFAULT 0 COMMENT 'Активность|checkbox',
  description TEXT DEFAULT NULL COMMENT 'Описание|wysiwyg',
  image VARCHAR(255) DEFAULT NULL COMMENT 'Изображение|image',
  date_in INT(11) NOT NULL DEFAULT 0 COMMENT 'Дата начала|date',
  date_out_new INT(11) NOT NULL DEFAULT 0 COMMENT 'Дата окончания|date',
  subtitle VARCHAR(255) DEFAULT NULL COMMENT 'Подпись на картинке|text',
  subtitle_color VARCHAR(8) DEFAULT NULL COMMENT 'Цвет подписи|color',
  on_main TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'На главной|checkbox',
  rank INT(11) DEFAULT NULL COMMENT 'Сортировка|int',
  seo_title VARCHAR(255) DEFAULT NULL COMMENT 'Title страницы|text',
  PRIMARY KEY (id),
  INDEX IDX_module_digest (date_in, date_out_new),
  INDEX IDX_module_digest_date_in (date_in),
  INDEX IDX_module_digest_date_out (date_out_new)
)
ENGINE = INNODB
AUTO_INCREMENT = 189
AVG_ROW_LENGTH = 1352
CHARACTER SET utf8
COLLATE utf8_general_ci
COMMENT = 'Дайджесты';

--
-- Описание для таблицы module_event_request
--
DROP TABLE IF EXISTS module_event_request;
CREATE TABLE module_event_request (
  id INT(11) NOT NULL AUTO_INCREMENT,
  title VARCHAR(128) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT 'Название события|text',
  name VARCHAR(128) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT 'ФИО|text',
  email VARCHAR(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'E-mail|email',
  phone VARCHAR(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'Телефон|text',
  description TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'Описание|textarea',
  create_time INT(11) DEFAULT NULL COMMENT 'Дата создания|datetime',
  date VARCHAR(128) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT 'Дата проведения события|text',
  time VARCHAR(128) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT 'Время проведения события|text',
  cost VARCHAR(128) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT 'Стоимость|text',
  address VARCHAR(128) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT 'Адрес проведения|text',
  location_name VARCHAR(128) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT 'Название локации|text',
  how_drive TEXT CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT 'Как доехать|textarea',
  program TEXT CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT 'Программа или расписание|textarea',
  contacts_on_page VARCHAR(256) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT 'Контакты для размещения на сайте|text',
  ticket_link VARCHAR(256) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT 'Ссылка на билеты|text',
  site_link VARCHAR(256) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT 'Ссылка на ваш сайт|text',
  social_links TEXT CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT 'Ссылки на событие в соц. сетях|text',
  photos TEXT CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT 'Фотографии для анонсирования события|images',
  PRIMARY KEY (id),
  INDEX UK_module_event_request (email)
)
ENGINE = INNODB
AUTO_INCREMENT = 365
AVG_ROW_LENGTH = 4392
CHARACTER SET utf32
COLLATE utf32_general_ci
COMMENT = 'Предложенные события';

--
-- Описание для таблицы module_event_request_duplicate
--
DROP TABLE IF EXISTS module_event_request_duplicate;
CREATE TABLE module_event_request_duplicate (
  id INT(11) NOT NULL AUTO_INCREMENT,
  title VARCHAR(128) NOT NULL COMMENT 'Название события|text',
  name VARCHAR(128) NOT NULL COMMENT 'ФИО|text',
  email VARCHAR(32) NOT NULL COMMENT 'E-mail|email',
  phone VARCHAR(32) NOT NULL COMMENT 'Телефон|text',
  description TEXT NOT NULL COMMENT 'Описание|textarea',
  create_time INT(11) DEFAULT NULL COMMENT 'Дата создания|datetime',
  date VARCHAR(128) DEFAULT NULL COMMENT 'Дата проведения события|text',
  time VARCHAR(128) DEFAULT NULL COMMENT 'Время проведения события|text',
  cost VARCHAR(128) DEFAULT NULL COMMENT 'Стоимость|text',
  address VARCHAR(128) DEFAULT NULL COMMENT 'Адрес проведения|text',
  location_name VARCHAR(128) DEFAULT NULL COMMENT 'Название локации|text',
  how_drive TEXT DEFAULT NULL COMMENT 'Как доехать|textarea',
  program TEXT DEFAULT NULL COMMENT 'Программа или расписание|textarea',
  contacts_on_page VARCHAR(256) DEFAULT NULL COMMENT 'Контакты для размещения на сайте|text',
  ticket_link VARCHAR(256) DEFAULT NULL COMMENT 'Ссылка на билеты|text',
  site_link VARCHAR(256) DEFAULT NULL COMMENT 'Ссылка на ваш сайт|text',
  social_links TEXT DEFAULT NULL COMMENT 'Ссылки на событие в соц. сетях|text',
  photos TEXT DEFAULT NULL COMMENT 'Фотографии для анонсирования события|images',
  PRIMARY KEY (id)
)
ENGINE = INNODB
AUTO_INCREMENT = 1
CHARACTER SET utf8
COLLATE utf8_general_ci
COMMENT = 'Предложенные события';

--
-- Описание для таблицы module_feedback
--
DROP TABLE IF EXISTS module_feedback;
CREATE TABLE module_feedback (
  id INT(11) NOT NULL AUTO_INCREMENT,
  name VARCHAR(64) NOT NULL COMMENT 'Имя|text',
  email VARCHAR(50) NOT NULL COMMENT 'E-mail|email',
  message TEXT NOT NULL COMMENT 'Cообщение|textarea',
  create_time INT(11) DEFAULT NULL COMMENT 'Дата создания|datetime',
  PRIMARY KEY (id)
)
ENGINE = INNODB
AUTO_INCREMENT = 607
AVG_ROW_LENGTH = 1417
CHARACTER SET utf8
COLLATE utf8_general_ci
COMMENT = 'Письма с контактов';

--
-- Описание для таблицы module_partner
--
DROP TABLE IF EXISTS module_partner;
CREATE TABLE module_partner (
  id INT(11) NOT NULL AUTO_INCREMENT,
  title_new VARCHAR(255) NOT NULL COMMENT 'Название|text',
  name INT(10) UNSIGNED NOT NULL COMMENT 'Контактное лицо|text',
  rrrrrr VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (id)
)
ENGINE = INNODB
AUTO_INCREMENT = 5
AVG_ROW_LENGTH = 5461
CHARACTER SET utf8
COLLATE utf8_general_ci
COMMENT = 'Партнёры';

--
-- Описание для таблицы module_subscribe
--
DROP TABLE IF EXISTS module_subscribe;
CREATE TABLE module_subscribe (
  id INT(11) NOT NULL AUTO_INCREMENT,
  create_time INT(11) NOT NULL DEFAULT 0 COMMENT 'Дата создания|datetime',
  first_name VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Имя|text',
  email VARCHAR(127) NOT NULL DEFAULT '' COMMENT 'E-mail|email',
  status TINYINT(1) NOT NULL DEFAULT 1 COMMENT 'Активен|checkbox',
  errors VARCHAR(500) NOT NULL COMMENT 'Ошибки|text',
  initialized TINYINT(4) NOT NULL DEFAULT 0 COMMENT 'отправлен на MailChimp',
  PRIMARY KEY (id),
  INDEX CreateDateIndex (create_time),
  UNIQUE INDEX email (email)
)
ENGINE = INNODB
AUTO_INCREMENT = 34085
AVG_ROW_LENGTH = 222
CHARACTER SET utf8
COLLATE utf8_general_ci
COMMENT = 'Подписчики'
ROW_FORMAT = COMPRESSED;

--
-- Описание для таблицы module_tag
--
DROP TABLE IF EXISTS module_tag;
CREATE TABLE module_tag (
  id INT(11) NOT NULL AUTO_INCREMENT,
  title VARCHAR(64) NOT NULL COMMENT 'Название|text',
  alias VARCHAR(64) NOT NULL COMMENT 'Алиас|alias',
  is_popular TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Популярный|checkbox',
  on_main TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'На главной|checkbox',
  order_rank INT(11) NOT NULL DEFAULT 0 COMMENT 'Сортировка|int',
  related_keys VARCHAR(500) DEFAULT NULL COMMENT 'Ключи для поиска|tags',
  PRIMARY KEY (id),
  INDEX IDX_module_tag_alias (alias)
)
ENGINE = INNODB
AUTO_INCREMENT = 36
AVG_ROW_LENGTH = 744
CHARACTER SET utf8
COLLATE utf8_general_ci
COMMENT = 'Теги';

--
-- Описание для таблицы module_test
--
DROP TABLE IF EXISTS module_test;
CREATE TABLE module_test (
  id INT(11) NOT NULL AUTO_INCREMENT COMMENT 'Comment',
  name VARCHAR(50) DEFAULT NULL,
  alias VARCHAR(255) DEFAULT '12',
  PRIMARY KEY (id)
)
ENGINE = INNODB
AUTO_INCREMENT = 1
CHARACTER SET utf8
COLLATE utf8_general_ci;

--
-- Описание для таблицы module_user
--
DROP TABLE IF EXISTS module_user;
CREATE TABLE module_user (
  id INT(11) DEFAULT NULL AUTO_INCREMENT,
  first_name VARCHAR(32) NOT NULL COMMENT 'Имя|text',
  last_name VARCHAR(32) DEFAULT NULL COMMENT 'Фамилия|text',
  email VARCHAR(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'E-mail|email',
  photo VARCHAR(255) DEFAULT NULL COMMENT 'Фотография|image',
  created_at INT(11) NOT NULL COMMENT 'Дата создания|datetime',
  updated_at INT(11) NOT NULL COMMENT 'Дата изменения|datetime',
  is_subscribed TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Подписан|checkbox',
  status INT(1) NOT NULL DEFAULT 0 COMMENT 'Статус|radiolist',
  active_key VARCHAR(64) DEFAULT NULL COMMENT 'Ключ активации|text',
  sex TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Пол|radiolist',
  auth_key VARCHAR(32) NOT NULL COMMENT 'Auth_key|text',
  password_hash VARCHAR(255) DEFAULT NULL COMMENT 'Password hash|text',
  password_reset_token VARCHAR(255) DEFAULT NULL COMMENT 'Password reset token hash|text',
  vkontakte_id INT(11) UNSIGNED DEFAULT NULL COMMENT 'Id в контакте|int',
  twitter_id VARCHAR(32) DEFAULT NULL COMMENT 'Id в twitter|int',
  facebook_id VARCHAR(32) DEFAULT NULL COMMENT 'Id в facebook|int',
  google_id VARCHAR(32) DEFAULT NULL COMMENT 'Id в google plus|int',
  odnoklassniki_id VARCHAR(32) DEFAULT NULL COMMENT 'Id в однокласниках|int',
  verified_email TINYINT(1) DEFAULT 0,
  INDEX IDX_module_user_email (email),
  INDEX IDX_module_user_facebook_id (facebook_id),
  INDEX IDX_module_user_google_id (google_id),
  INDEX IDX_module_user_odnoklassniki_id (odnoklassniki_id),
  INDEX IDX_module_user_twitter_id (twitter_id),
  INDEX IDX_module_user_vkontakte_id (vkontakte_id),
  UNIQUE INDEX module_user_password_hash_uindex (password_reset_token),
  PRIMARY KEY (id)
)
ENGINE = INNODB
AVG_ROW_LENGTH = 273
CHARACTER SET utf8
COLLATE utf8_general_ci
COMMENT = 'Пользователи';

--
-- Описание для таблицы module_with_who
--
DROP TABLE IF EXISTS module_with_who;
CREATE TABLE module_with_who (
  id INT(11) NOT NULL AUTO_INCREMENT,
  title VARCHAR(32) NOT NULL COMMENT 'Название|text',
  title_p VARCHAR(32) DEFAULT NULL COMMENT 'Склоненное название|text',
  image VARCHAR(255) DEFAULT NULL COMMENT 'Изображение|image',
  alias VARCHAR(32) DEFAULT NULL COMMENT 'Алиас|alias',
  is_active TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Активен|checkbox',
  PRIMARY KEY (id)
)
ENGINE = INNODB
AUTO_INCREMENT = 7
AVG_ROW_LENGTH = 4096
CHARACTER SET utf8
COLLATE utf8_general_ci
COMMENT = 'С кем пойти';

--
-- Описание для таблицы user
--
DROP TABLE IF EXISTS user;
CREATE TABLE user (
  id INT(11) NOT NULL AUTO_INCREMENT,
  username VARCHAR(255) NOT NULL,
  auth_key VARCHAR(32) NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  password_reset_token VARCHAR(255) DEFAULT NULL,
  email VARCHAR(255) NOT NULL,
  status SMALLINT(6) NOT NULL DEFAULT 10,
  created_at INT(11) NOT NULL,
  updated_at INT(11) NOT NULL,
  image VARCHAR(255) DEFAULT NULL COMMENT 'Изображение пользователя|image',
  PRIMARY KEY (id),
  UNIQUE INDEX email (email),
  UNIQUE INDEX password_reset_token (password_reset_token),
  UNIQUE INDEX username (username)
)
ENGINE = INNODB
AUTO_INCREMENT = 50
AVG_ROW_LENGTH = 16384
CHARACTER SET utf8
COLLATE utf8_general_ci;

--
-- Описание для таблицы auth_item
--
DROP TABLE IF EXISTS auth_item;
CREATE TABLE auth_item (
  name VARCHAR(64) NOT NULL,
  type INT(11) NOT NULL,
  description TEXT DEFAULT NULL,
  rule_name VARCHAR(64) DEFAULT NULL,
  data TEXT DEFAULT NULL,
  created_at INT(11) DEFAULT NULL,
  updated_at INT(11) DEFAULT NULL,
  PRIMARY KEY (name),
  INDEX `idx-auth_item-type` (type),
  INDEX rule_name (rule_name),
  CONSTRAINT auth_item_ibfk_1 FOREIGN KEY (rule_name)
    REFERENCES auth_rule(name) ON DELETE SET NULL ON UPDATE CASCADE
)
ENGINE = INNODB
AVG_ROW_LENGTH = 712
CHARACTER SET utf8
COLLATE utf8_unicode_ci;

--
-- Описание для таблицы cms_log_action
--
DROP TABLE IF EXISTS cms_log_action;
CREATE TABLE cms_log_action (
  id INT(11) NOT NULL AUTO_INCREMENT,
  user_id INT(11) DEFAULT NULL COMMENT 'Пользователь|select',
  action_id TINYINT(3) UNSIGNED NOT NULL COMMENT 'Действие|radiolist',
  data TEXT DEFAULT NULL COMMENT 'Данные|text',
  model_name VARCHAR(255) DEFAULT NULL COMMENT 'Название модели|text',
  model_id INT(11) UNSIGNED NOT NULL COMMENT 'ID модели|int',
  create_time INT(11) UNSIGNED NOT NULL COMMENT 'Время|datetime',
  ip INT(11) UNSIGNED NOT NULL COMMENT 'IP-адрес|text',
  PRIMARY KEY (id),
  INDEX cms_log_action_model (model_name, model_id),
  CONSTRAINT cms_log_action_user_id_fk FOREIGN KEY (user_id)
    REFERENCES user(id) ON DELETE RESTRICT ON UPDATE CASCADE
)
ENGINE = INNODB
AUTO_INCREMENT = 48793
AVG_ROW_LENGTH = 5938
CHARACTER SET utf8
COLLATE utf8_general_ci
COMMENT = 'Журнал действий';

--
-- Описание для таблицы module_category
--
DROP TABLE IF EXISTS module_category;
CREATE TABLE module_category (
  id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  parent_id INT(11) UNSIGNED DEFAULT NULL COMMENT 'Родитель|select',
  alias VARCHAR(50) NOT NULL COMMENT 'Алиас|alias',
  path VARCHAR(200) NOT NULL COMMENT 'Абсолютный путь|text',
  `position` SMALLINT(6) NOT NULL DEFAULT 0 COMMENT 'Позиция|int',
  level TINYINT(4) NOT NULL DEFAULT 0 COMMENT 'Уровень вложенности|int',
  title VARCHAR(255) NOT NULL COMMENT 'Title|text',
  h1 VARCHAR(255) NOT NULL COMMENT 'H1|text',
  seo_text TEXT DEFAULT NULL COMMENT 'Seo text|wysiwyg',
  rating DECIMAL(2, 1) NOT NULL DEFAULT 4.5 COMMENT 'Рейтинг|rating',
  author_id INT(11) DEFAULT NULL COMMENT 'Автор|select',
  type TINYINT(1) DEFAULT 0 COMMENT 'Тип|select',
  image_background VARCHAR(255) NOT NULL COMMENT 'Фоновая картинка|image',
  seo_title VARCHAR(255) DEFAULT NULL COMMENT 'SEO title|text',
  seo_description VARCHAR(255) DEFAULT NULL COMMENT 'SEO description|text',
  is_active TINYINT(1) NOT NULL DEFAULT 1 COMMENT 'активность|checkbox',
  PRIMARY KEY (id),
  INDEX category_level (level),
  INDEX UK_module_category_path (path),
  CONSTRAINT FK_module_category_module_category_id FOREIGN KEY (parent_id)
    REFERENCES module_category(id) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT FK_module_category_user_id FOREIGN KEY (author_id)
    REFERENCES user(id) ON DELETE SET NULL ON UPDATE CASCADE
)
ENGINE = INNODB
AUTO_INCREMENT = 188
AVG_ROW_LENGTH = 4096
CHARACTER SET utf8
COLLATE utf8_general_ci;

--
-- Описание для таблицы auth_assignment
--
DROP TABLE IF EXISTS auth_assignment;
CREATE TABLE auth_assignment (
  item_name VARCHAR(64) NOT NULL,
  user_id VARCHAR(64) NOT NULL,
  created_at INT(11) DEFAULT NULL,
  PRIMARY KEY (item_name, user_id),
  CONSTRAINT auth_assignment_ibfk_1 FOREIGN KEY (item_name)
    REFERENCES auth_item(name) ON DELETE CASCADE ON UPDATE CASCADE
)
ENGINE = INNODB
AVG_ROW_LENGTH = 8192
CHARACTER SET utf8
COLLATE utf8_unicode_ci;

--
-- Описание для таблицы auth_item_child
--
DROP TABLE IF EXISTS auth_item_child;
CREATE TABLE auth_item_child (
  parent VARCHAR(64) NOT NULL,
  child VARCHAR(64) NOT NULL,
  PRIMARY KEY (parent, child),
  INDEX child (child),
  CONSTRAINT auth_item_child_ibfk_1 FOREIGN KEY (parent)
    REFERENCES auth_item(name) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT auth_item_child_ibfk_2 FOREIGN KEY (child)
    REFERENCES auth_item(name) ON DELETE CASCADE ON UPDATE CASCADE
)
ENGINE = INNODB
AVG_ROW_LENGTH = 292
CHARACTER SET utf8
COLLATE utf8_unicode_ci;

--
-- Описание для таблицы module_category_vs_redirect
--
DROP TABLE IF EXISTS module_category_vs_redirect;
CREATE TABLE module_category_vs_redirect (
  id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  old_path VARCHAR(200) NOT NULL COMMENT 'Старый путь|text',
  category_id INT(11) UNSIGNED DEFAULT NULL COMMENT 'Категория|select',
  PRIMARY KEY (id),
  INDEX IDX_module_category_vs_redirect_old_path (old_path),
  CONSTRAINT FK_module_category_vs_redirect_module_category_id FOREIGN KEY (category_id)
    REFERENCES module_category(id) ON DELETE CASCADE ON UPDATE CASCADE
)
ENGINE = INNODB
AUTO_INCREMENT = 1
CHARACTER SET utf8
COLLATE utf8_general_ci
COMMENT = 'редиректы для категорий';

--
-- Описание для таблицы module_event
--
DROP TABLE IF EXISTS module_event;
CREATE TABLE module_event (
  id INT(11) NOT NULL AUTO_INCREMENT,
  type TINYINT(4) NOT NULL DEFAULT 1 COMMENT 'Тип|select',
  title VARCHAR(128) NOT NULL COMMENT 'Название|text',
  alias VARCHAR(128) NOT NULL COMMENT 'Алиас|text',
  is_active TINYINT(1) NOT NULL DEFAULT 1 COMMENT 'Активен|checkbox',
  category_id INT(11) UNSIGNED DEFAULT NULL COMMENT 'Категория|select',
  image VARCHAR(255) NOT NULL COMMENT 'Изображение|image',
  image_large VARCHAR(255) NOT NULL COMMENT 'Большое изоб-ие|image',
  announce TEXT DEFAULT NULL COMMENT 'Анонс|text',
  time_title TEXT DEFAULT NULL COMMENT 'Время события|text',
  description MEDIUMTEXT DEFAULT NULL COMMENT 'Описание|wysiwyg',
  rating_up SMALLINT(6) NOT NULL DEFAULT 0 COMMENT 'Рейтинг +|int',
  rating_down SMALLINT(6) NOT NULL DEFAULT 0 COMMENT 'Рейтинг -|int',
  schedule TEXT DEFAULT NULL COMMENT 'График|text',
  address TEXT DEFAULT NULL COMMENT 'Адрес|text',
  fee TEXT DEFAULT NULL COMMENT 'Вход|text',
  age TEXT DEFAULT NULL COMMENT 'Возраст|text',
  map TEXT DEFAULT NULL COMMENT 'Координаты|map',
  map_image VARCHAR(255) DEFAULT NULL COMMENT 'Кеш мапи|image',
  how_to_get TEXT DEFAULT NULL COMMENT 'Как добратся|text',
  youtube_id VARCHAR(255) DEFAULT NULL COMMENT 'Youtube видео (id)|tags',
  participants_count INT(11) NOT NULL DEFAULT 0 COMMENT 'Кол - во участников|int',
  comments_count INT(11) NOT NULL COMMENT 'Кол-во отзывов|int',
  is_permanent TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Постоянное|checkbox',
  is_actual TINYINT(1) NOT NULL DEFAULT 1 COMMENT 'Актуальное|checkbox',
  is_bodo_gift TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Бодо впечатление|checkbox',
  sign_up TEXT DEFAULT NULL COMMENT 'Записаться|text',
  sign_up_type SMALLINT(6) NOT NULL DEFAULT 0 COMMENT 'Вид кнопки|select',
  seo_title VARCHAR(128) DEFAULT NULL COMMENT 'SEO title|text',
  seo_description VARCHAR(255) DEFAULT NULL COMMENT 'SEO description|textarea',
  seo_text TEXT DEFAULT NULL COMMENT 'SEO text|wysiwyg',
  seo_keywords VARCHAR(255) NOT NULL COMMENT 'Ключевые слова',
  detail_info TEXT DEFAULT NULL COMMENT 'Детальная информация',
  author_id INT(11) DEFAULT NULL COMMENT 'Автор ID|select',
  gallery SMALLINT(6) NOT NULL DEFAULT 0 COMMENT 'Галерея|imagesgallery',
  parent_event_id INT(11) DEFAULT NULL COMMENT 'Куда ссылается новость|select',
  buy_ticket_url VARCHAR(255) DEFAULT NULL COMMENT 'Ссылка на покупку билета',
  hide_on_poster TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Скрывать в афише на главной',
  sort_position INT(11) DEFAULT NULL COMMENT 'Позиция в сортировке',
  max_date_out INT(11) NOT NULL DEFAULT 0 COMMENT 'Дата окончания(заполняеться автоматически)|datetime',
  create_time INT(11) DEFAULT NULL COMMENT 'Дата создания|datetime',
  schema_type TINYINT(4) NOT NULL DEFAULT 0 COMMENT 'Schema type|select',
  partner_id INT(11) DEFAULT NULL COMMENT 'Партнёр (собираем заявки)|select',
  remote_id INT(11) DEFAULT NULL COMMENT 'Удаленный id(парсинг)|int',
  remote_type SMALLINT(6) DEFAULT NULL COMMENT 'Удаленный тип(парсинг)int',
  PRIMARY KEY (id),
  INDEX author_id (author_id),
  INDEX category_id (category_id),
  INDEX event_active_actual_category (is_actual, is_active, category_id),
  INDEX event_active_actual_type (type, is_actual, is_active),
  INDEX FK_module_event_module_event_id (parent_event_id),
  INDEX IDX_module_event_max_date_out (max_date_out),
  INDEX module_event_id (gallery),
  INDEX UK_module_event (alias),
  INDEX UK_module_event_alias (alias),
  CONSTRAINT author_id_fk FOREIGN KEY (author_id)
    REFERENCES user(id) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT category_id_fk FOREIGN KEY (category_id)
    REFERENCES module_category(id) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT FK_module_event_module_partner_id FOREIGN KEY (partner_id)
    REFERENCES module_partner(id) ON DELETE CASCADE ON UPDATE CASCADE
)
ENGINE = INNODB
AUTO_INCREMENT = 7224
AVG_ROW_LENGTH = 6057
CHARACTER SET utf8
COLLATE utf8_general_ci
COMMENT = 'Событие';

--
-- Описание для таблицы module_digest_vs_event
--
DROP TABLE IF EXISTS module_digest_vs_event;
CREATE TABLE module_digest_vs_event (
  digest_id INT(11) NOT NULL COMMENT 'Дайджест|select',
  event_id INT(11) NOT NULL COMMENT 'Событие|select',
  PRIMARY KEY (digest_id, event_id),
  INDEX digest_id (digest_id),
  INDEX event_id (event_id),
  INDEX IDX_module_digest_vs_event_digest_id (digest_id),
  INDEX IDX_module_digest_vs_event_event_id (event_id),
  UNIQUE INDEX UK_module_digest_vs_event (digest_id, event_id),
  CONSTRAINT digest_id_fk FOREIGN KEY (digest_id)
    REFERENCES module_digest(id) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT event_id_fk FOREIGN KEY (event_id)
    REFERENCES module_event(id) ON DELETE CASCADE ON UPDATE CASCADE
)
ENGINE = INNODB
AVG_ROW_LENGTH = 35
CHARACTER SET utf8
COLLATE utf8_general_ci
COMMENT = 'Связь дайджест - событие';

--
-- Описание для таблицы module_event_date
--
DROP TABLE IF EXISTS module_event_date;
CREATE TABLE module_event_date (
  id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  date_out INT(11) NOT NULL COMMENT 'Дата окончания|date',
  event_id INT(11) NOT NULL COMMENT 'Событие|int',
  hours VARCHAR(1000) NOT NULL COMMENT 'Время и ссылки|textarea',
  test_id INT(11) UNSIGNED DEFAULT NULL COMMENT 'Тестовый id',
  test_for_index TIMESTAMP NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Тестовое поле для индекса',
  PRIMARY KEY (id),
  INDEX IDX_module_event_vs_date_date_out (date_out),
  INDEX IDX_module_event_vs_date_event_id (event_id),
  UNIQUE INDEX UK_module_event_date (test_for_index),
  CONSTRAINT FK_module_event_date_module_event_id FOREIGN KEY (event_id)
    REFERENCES module_event(id) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT FK_module_event_date_test_id FOREIGN KEY (test_id)
    REFERENCES module_event_date(id) ON DELETE CASCADE ON UPDATE CASCADE
)
ENGINE = INNODB
AUTO_INCREMENT = 47801
AVG_ROW_LENGTH = 17
CHARACTER SET utf8
COLLATE utf8_general_ci
COMMENT = 'Даты события';

--
-- Описание для таблицы module_event_vs_place_event
--
DROP TABLE IF EXISTS module_event_vs_place_event;
CREATE TABLE module_event_vs_place_event (
  place_event_id INT(11) NOT NULL COMMENT 'Место|select',
  event_id INT(11) NOT NULL COMMENT 'Событие|select',
  announce TEXT CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT 'Анонс в сборной статье|textarea',
  PRIMARY KEY (event_id, place_event_id),
  INDEX IDX_module_type_vs_event_event_id (event_id),
  INDEX IDX_module_type_vs_event_type_id (place_event_id),
  CONSTRAINT FK_module_event_vs_place_event_module_event_id FOREIGN KEY (place_event_id)
    REFERENCES module_event(id) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT FK_module_event_vs_place_event_module_event_id_1 FOREIGN KEY (event_id)
    REFERENCES module_event(id) ON DELETE CASCADE ON UPDATE CASCADE
)
ENGINE = INNODB
AVG_ROW_LENGTH = 167
CHARACTER SET latin1
COLLATE latin1_swedish_ci
COMMENT = 'Связь места и события';

--
-- Описание для таблицы module_event_vs_similar_event
--
DROP TABLE IF EXISTS module_event_vs_similar_event;
CREATE TABLE module_event_vs_similar_event (
  event_id INT(11) NOT NULL COMMENT 'Событие|select',
  similar_event_id INT(11) NOT NULL COMMENT 'Похожее событие|select',
  type INT(11) NOT NULL DEFAULT 0 COMMENT 'Тип|select',
  PRIMARY KEY (event_id, similar_event_id),
  INDEX IDX_module_event_vs_similar_event_event_id (event_id),
  INDEX IDX_module_event_vs_similar_event_similar_event_id (similar_event_id),
  UNIQUE INDEX UK_module_event_vs_similar_eve (event_id, similar_event_id),
  CONSTRAINT module_event_vs_similar_event_ibfk_1 FOREIGN KEY (event_id)
    REFERENCES module_event(id) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT module_event_vs_similar_event_ibfk_2 FOREIGN KEY (similar_event_id)
    REFERENCES module_event(id) ON DELETE CASCADE ON UPDATE CASCADE
)
ENGINE = INNODB
AVG_ROW_LENGTH = 34
CHARACTER SET utf8
COLLATE utf8_general_ci
COMMENT = 'Похожие события';

--
-- Описание для таблицы module_event_vs_tag
--
DROP TABLE IF EXISTS module_event_vs_tag;
CREATE TABLE module_event_vs_tag (
  event_id INT(11) NOT NULL COMMENT 'Событие|select',
  tag_id INT(11) NOT NULL COMMENT 'Тег|select',
  PRIMARY KEY (event_id, tag_id),
  INDEX IDX_module_event_vs_tag_tag_id (tag_id),
  CONSTRAINT FK_module_event_vs_tag_module_event_id FOREIGN KEY (event_id)
    REFERENCES module_event(id) ON DELETE CASCADE ON UPDATE NO ACTION
)
ENGINE = INNODB
AVG_ROW_LENGTH = 34
CHARACTER SET utf8
COLLATE utf8_general_ci
COMMENT = 'Связь событие + тег';

--
-- Описание для таблицы module_event_vs_with_who
--
DROP TABLE IF EXISTS module_event_vs_with_who;
CREATE TABLE module_event_vs_with_who (
  event_id INT(11) NOT NULL COMMENT 'Событие|select',
  with_who_id INT(11) NOT NULL COMMENT 'С кем пойти|select',
  PRIMARY KEY (event_id, with_who_id),
  INDEX IDX_module_event_vs_with_who_event_id (event_id),
  INDEX IDX_module_event_vs_with_who_with_who_id (with_who_id),
  UNIQUE INDEX UK_module_event_vs_with_who (event_id, with_who_id),
  CONSTRAINT FK_module_event_vs_with_who_module_event_id FOREIGN KEY (event_id)
    REFERENCES module_event(id) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT FK_module_event_vs_with_who_module_with_who_id FOREIGN KEY (with_who_id)
    REFERENCES module_with_who(id) ON DELETE CASCADE ON UPDATE CASCADE
)
ENGINE = INNODB
AVG_ROW_LENGTH = 35
CHARACTER SET utf8
COLLATE utf8_general_ci
COMMENT = 'Связь событие - с кем пойти';

--
-- Описание для таблицы module_partner_lead
--
DROP TABLE IF EXISTS module_partner_lead;
CREATE TABLE module_partner_lead (
  id INT(11) NOT NULL AUTO_INCREMENT,
  event_id INT(11) NOT NULL COMMENT 'События|select',
  partner_id INT(11) NOT NULL COMMENT 'Партнер|select',
  name VARCHAR(50) NOT NULL COMMENT 'Имя|text',
  phone VARCHAR(50) NOT NULL COMMENT 'Телефон|phone',
  email VARCHAR(50) NOT NULL COMMENT 'Email|email',
  date INT(11) NOT NULL COMMENT 'Дата|datetime',
  count SMALLINT(6) NOT NULL DEFAULT 0 COMMENT 'Количество участников|int',
  status TINYINT(4) NOT NULL DEFAULT 0 COMMENT 'Статус|select',
  create_time INT(11) NOT NULL COMMENT 'Создано|datetime',
  ip VARCHAR(60) NOT NULL COMMENT 'Ip|ip',
  PRIMARY KEY (id),
  INDEX IDX_module_partner_lead_status (status),
  CONSTRAINT FK_module_partner_lead_module_event_id FOREIGN KEY (event_id)
    REFERENCES module_event(id) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT FK_module_partner_lead_module_partner_id FOREIGN KEY (partner_id)
    REFERENCES module_partner(id) ON DELETE CASCADE ON UPDATE CASCADE
)
ENGINE = INNODB
AUTO_INCREMENT = 174
AVG_ROW_LENGTH = 105
CHARACTER SET utf8
COLLATE utf8_general_ci
COMMENT = 'Заявки';