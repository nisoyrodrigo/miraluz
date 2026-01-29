CREATE TABLE cms_region(
id INT(11) UNSIGNED AUTO_INCREMENT,
name VARCHAR(50) UNIQUE NOT NULL,
user INT(11) UNSIGNED,
created DATETIME NOT NULL DEFAULT  '0000-00-00 00:00:00',
modified TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
CONSTRAINT fk_region_u FOREIGN KEY(user) REFERENCES user(id),
PRIMARY KEY(id)
)Engine=InnoDB;

CREATE TABLE cms_list_data(
id INT(11) UNSIGNED AUTO_INCREMENT,
name VARCHAR(50) UNIQUE NOT NULL,
type TINYINT(1) DEFAULT 0,
file varchar(255),
query LONGTEXT,
template LONGTEXT,
user INT(11) UNSIGNED,
created DATETIME NOT NULL DEFAULT  '0000-00-00 00:00:00',
modified TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
CONSTRAINT fk_ld_u FOREIGN KEY(user) REFERENCES user(id),
PRIMARY KEY(id)
)Engine=InnoDB;


CREATE TABLE cms_block(
id INT(11) UNSIGNED AUTO_INCREMENT,
name VARCHAR(50) UNIQUE NOT NULL,
region INT(11) UNSIGNED,
content longtext,
content_type tinyint(1) DEFAULT 0,
order_block INT,
show_in LONGTEXT,
iquals_show_in TINYINT(1),
list_data INT(11) UNSIGNED,
user INT(11) UNSIGNED,
created DATETIME NOT NULL DEFAULT  '0000-00-00 00:00:00',
modified TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
CONSTRAINT fk_block_region FOREIGN KEY(region) REFERENCES cms_region(id),
CONSTRAINT fk_block_u FOREIGN KEY(user) REFERENCES user(id),
CONSTRAINT fk_block_ld FOREIGN KEY(list_data) REFERENCES cms_list_data(id),
PRIMARY KEY(id)
)Engine=InnoDB;

CREATE TABLE cms_template(
id INT(11) UNSIGNED AUTO_INCREMENT,
name VARCHAR(50) UNIQUE NOT NULL,
content LONGTEXT,
template_type TINYINT(1) NOT NULL DEFAULT 0,
user INT(11) UNSIGNED,
created DATETIME NOT NULL DEFAULT  '0000-00-00 00:00:00',
modified TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
CONSTRAINT fk_template_u FOREIGN KEY(user) REFERENCES user(id),
PRIMARY KEY(id)
)Engine=InnoDB;

CREATE TABLE cms_content_type(
id INT(11) UNSIGNED AUTO_INCREMENT,
name VARCHAR(50) UNIQUE NOT NULL,
template INT(11) UNSIGNED,
user INT(11) UNSIGNED,
created DATETIME NOT NULL DEFAULT  '0000-00-00 00:00:00',
modified TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
CONSTRAINT fk_content_type_u FOREIGN KEY(user) REFERENCES user(id),
CONSTRAINT fk_content_type_t FOREIGN KEY(template) REFERENCES cms_template(id),
PRIMARY KEY(id)
)Engine=InnoDB;

INSERT INTO cms_content_type(name, user, created) VALUES
("Basic Page", 1, now());

CREATE TABLE cms_field_type(
id INT(11) UNSIGNED AUTO_INCREMENT,
name VARCHAR(50) UNIQUE NOT NULL,
PRIMARY KEY(id)
)Engine=InnoDB;

INSERT INTO cms_field_type (name) VALUES
("Text"),
("Long Text"),
("Number"),
("Decimal Number"),
("Radio Button"),
("Combo Box"),
("File"),
("Imagen"),
("Date"),
("Date Time"),
("Bool"),
("Email");

CREATE TABLE cms_content_type_field_type(
id INT(11) UNSIGNED AUTO_INCREMENT,
name varchar(50) NOT NULL,
content_type INT(11) UNSIGNED,
field_type INT(11) UNSIGNED,
user INT(11) UNSIGNED,
created DATETIME NOT NULL DEFAULT  '0000-00-00 00:00:00',
modified TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
CONSTRAINT fk_content_type_field_ct FOREIGN KEY(content_type) REFERENCES cms_content_type(id),
CONSTRAINT fk_content_type_field_tf FOREIGN KEY(field_type) REFERENCES cms_field_type(id),
CONSTRAINT fk_content_type_field_u FOREIGN KEY(user) REFERENCES user(id),
PRIMARY KEY(id)
)Engine=InnoDB;

INSERT INTO cms_content_type_field_type (name, content_type, field_type, user, created) VALUES
("Titulo", 1, 1, 1 , now()),
("Body", 1, 2, 1 , now()),
("Imagen", 1, 8, 1, now());

CREATE TABLE cms_content_field_type_conf(
id INT(11) UNSIGNED AUTO_INCREMENT,
label varchar(50) NOT NULL,
required tinyint(1),
size_field INT DEFAULT NULL,
length_field INT DEFAULT 50,
num_values INT DEFAULT 1,
default_value LONGTEXT DEFAULT NULL,
min_value varchar(20) DEFAULT NULL,
max_value varchar(20) DEFAULT NULL,
allowed_values longtext DEFAULT NULL,
content_type_field_type INT(11) UNSIGNED,
CONSTRAINT fk_content_field_type_conf_cft FOREIGN KEY(content_type_field_type) REFERENCES cms_content_type_field_type(id),
PRIMARY KEY(id)
)Engine=InnoDB;

INSERT INTO cms_content_field_type_conf(label, required, length_field, num_values, content_type_field_type) VALUES
("Titulo", 1, 50, 1, 1),
("Contenido", 0, 0, 1, 2),
("Image", 0, 0, 0, 3 );


CREATE TABLE cms_language(
id INT(11) UNSIGNED AUTO_INCREMENT,
name VARCHAR(50) UNIQUE NOT NULL,
alias VARCHAR(4) UNIQUE NOT NULL,
PRIMARY KEY(id)
)Engine=InnoDB;

INSERT INTO cms_language VALUES
(1, "Español", "es"),
(2, "Ingles", "en");

CREATE TABLE cms_content(
id INT(11) UNSIGNED AUTO_INCREMENT,
name VARCHAR(50) NOT NULL UNIQUE,
published tinyint(1),
url varchar(50),
language_content INT(11) UNSIGNED,
content_type INT(11) UNSIGNED,
template INT(11) UNSIGNED,
user INT(11) UNSIGNED,
created DATETIME NOT NULL DEFAULT  '0000-00-00 00:00:00',
modified TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
CONSTRAINT fk_content_u FOREIGN KEY(user) REFERENCES user(id),
CONSTRAINT fk_content_ct FOREIGN KEY(content_type) REFERENCES cms_content_type(id),
CONSTRAINT fk_content_t FOREIGN KEY(template) REFERENCES cms_template(id),
PRIMARY KEY(id)
)Engine=InnoDB;

CREATE TABLE cms_content_value(
id INT(11) UNSIGNED AUTO_INCREMENT,
content INT(11) UNSIGNED,
content_type_field_type INT(11) UNSIGNED,
value_field longtext,
order_value INT DEFAULT 1,
CONSTRAINT fk_content_value_c FOREIGN KEY(content) REFERENCES cms_content(id),
CONSTRAINT fk_content_value_ctft FOREIGN KEY(content_type_field_type) REFERENCES cms_content_type_field_type(id),
PRIMARY KEY(id)
)Engine=InnoDB;

CREATE TABLE cms_type_multimedia(
id INT(11) UNSIGNED AUTO_INCREMENT,
name VARCHAR(50) NOT NULL UNIQUE,
PRIMARY KEY(id)
)Engine=InnoDB;

INSERT INTO cms_type_multimedia (name) VALUES
('Video'),
('Imagen'),
('Documento');

CREATE TABLE cms_multimedia(
id INT(11) UNSIGNED AUTO_INCREMENT,
name VARCHAR(100),
url VARCHAR(100) NOT NULL,
type INT(11) UNSIGNED, 
user INT(11) UNSIGNED,
created DATETIME NOT NULL DEFAULT  '0000-00-00 00:00:00',
modified TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
CONSTRAINT fk_multimedia_u FOREIGN KEY(user) REFERENCES user(id),
PRIMARY KEY(id)
);


