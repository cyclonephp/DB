drop table if exists cy_user_email;

DROP TABLE IF EXISTS t_posts;

DROP TABLE IF EXISTS cy_user;

CREATE TABLE cy_user (
  id int(11) NOT NULL AUTO_INCREMENT,
  name varchar(32) NOT NULL,
  email varchar(32) DEFAULT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

create table cy_user_email(
  user_fk int primary key references cy_user(id),
  email varchar(128) unique
);

CREATE TABLE t_posts (
  id int primary key auto_increment,
  user_fk int,
  foreign key (user_fk) references cy_user(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS tmp;
CREATE TABLE tmp (
  id int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS tmp2;
CREATE TABLE tmp2 (
  x int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
