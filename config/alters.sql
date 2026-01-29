CREATE TABLE rol(
id INT(11) UNSIGNED AUTO_INCREMENT,
name VARCHAR(50) UNIQUE,
created DATETIME NOT NULL DEFAULT  '0000-00-00 00:00:00',
modified TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP ,
PRIMARY KEY (id)
)Engine=InnoDB;

INSERT INTO rol (name, created) values ('Super Admin', '2017-01-01 00:00:00');

CREATE TABLE user(
id INT(11) UNSIGNED AUTO_INCREMENT,
username VARCHAR(50) UNIQUE NOT NULL,
password varchar(50) NOT NULL,
rol INT(11) UNSIGNED,
status ENUM('Active', 'Suspend', 'Eliminate') DEFAULT 'Active',
created DATETIME NOT NULL DEFAULT  '0000-00-00 00:00:00',
modified TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
CONSTRAINT fk_user_rol FOREIGN KEY(rol) REFERENCES rol(id),
PRIMARY KEY(id)
)Engine=InnoDB;

INSERT INTO user (username, password, rol) values ('linkkurt', 'slipkorn', 1);

CREATE TABLE section(
id INT(11) UNSIGNED AUTO_INCREMENT,
name VARCHAR(50),
grupo TINYINT(1),
action VARCHAR(50),
status ENUM('Active', 'Suspend', 'Eliminate') DEFAULT 'Active',
created DATETIME NOT NULL DEFAULT  '0000-00-00 00:00:00',
modified TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP ,
PRIMARY KEY(id)
)Engine=InnoDB;

CREATE TABLE user_section(
id INT(11) UNSIGNED AUTO_INCREMENT,
section INT(11) UNSIGNED,
user INT(11) UNSIGNED,
rol INT(11) UNSIGNED,
permiso TINYINT(1) DEFAULT 0,
created DATETIME NOT NULL DEFAULT  '0000-00-00 00:00:00',
modified TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP ,
CONSTRAINT fk_user_section_section FOREIGN KEY(section) REFERENCES section(id),
CONSTRAINT fk_user_section_user FOREIGN KEY(user) REFERENCES user(id),
CONSTRAINT fk_user_section_rol FOREIGN KEY(rol) REFERENCES rol(id),
PRIMARY KEY(id)
)Engine=InnoDB;