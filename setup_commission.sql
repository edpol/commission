create database commission;
use commission;

CREATE USER 'payroll'@'localhost' IDENTIFIED BY 'Mdr33325';
GRANT ALL PRIVILEGES ON commission.* TO 'payroll'@'localhost';
ALTER  USER 'payroll'@'localhost' IDENTIFIED WITH mysql_native_password BY 'Mdr33325';
FLUSH PRIVILEGES;

--
-- Table structure for table admins
--
DROP TABLE IF EXISTS admins;
CREATE TABLE admins (
id               int(11)        NOT NULL AUTO_INCREMENT,
username        varchar(50)    NOT NULL,
hashed_password    varchar(60)    NOT NULL,
PRIMARY KEY (id)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

--
-- Dumping data for table admins
--
INSERT INTO admins VALUES
(1,'edpol','$2y$10$YjBjYTk3YTI1YWFhOWVkYOtRIQwiAUw5r34jgnjc76eAi6a2JZx1u');

-- All of the other data is in sqlsvr
