CREATE TABLE `department` (
  `id` int(1) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` varchar(30) COLLATE 'utf8_czech_ci' NOT NULL COMMENT 'department name'
) COMMENT='departments' ENGINE='InnoDB' COLLATE 'utf8_czech_ci';

CREATE TABLE `product` (
  `id` int(1) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `department_id` int(1) unsigned NOT NULL COMMENT 'department reference',
  `name` varchar(40) COLLATE 'utf8_czech_ci' NOT NULL COMMENT 'product name',
  `price` float(10) unsigned NOT NULL DEFAULT 0 COMMENT 'product price',
  `stock` int(1) unsigned NOT NULL DEFAULT 0 COMMENT 'count on stock',
  FOREIGN KEY (`department_id`) REFERENCES `department` (`id`) ON DELETE RESTRICT
) COMMENT='products on stock' ENGINE='InnoDB' COLLATE 'utf8_czech_ci';

CREATE TABLE `product_parameter_list` (
	`id` int(1) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`department_id` int(1) unsigned NOT NULL COMMENT 'department related to parameters',
	`parameter_name` varchar(20) COLLATE 'utf8_czech_ci' NOT NULL COMMENT 'parameter name',
	FOREIGN KEY (`department_id`) REFERENCES `department` (`id`) ON DELETE CASCADE
) COMMENT='list of available parameters of product on certain department' ENGINE='InnoDB' COLLATE 'utf8_czech_ci';

CREATE TABLE `product_parameter` (
	`id` int(1) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`product_id` int(1) unsigned NOT NULL COMMENT 'parameter of which product',
	`parameter_id` int(1) unsigned NOT NULL COMMENT 'parameter name',
	`value` varchar(30) COLLATE 'utf8_czech_ci' NULL DEFAULT NULL COMMENT 'value of parameter',
	FOREIGN KEY (`product_id`) REFERENCES `product` (`id`) ON DELETE CASCADE,
	FOREIGN KEY (`parameter_id`) REFERENCES `product_parameter_list` (`id`) ON DELETE CASCADE
) COMMENT='product parameters' ENGINE='InnoDB' COLLATE 'utf8_czech_ci';

CREATE TABLE `seller` (
  `id` int(1) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `department_id` int(1) unsigned NOT NULL COMMENT 'department of seller\'s workplace',
  `login` varchar(20) COLLATE 'utf8_czech_ci' NOT NULL COMMENT 'seller\'s login',
  `password` varchar(40) NULL DEFAULT NULL COMMENT 'seller\'s SHA1 password',
  FOREIGN KEY (`department_id`) REFERENCES `department` (`id`) ON DELETE RESTRICT
) COMMENT='sellers list' ENGINE='InnoDB' COLLATE 'utf8_czech_ci';

CREATE TABLE `sale` (
  `id` int(1) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `product_id` int(1) unsigned NOT NULL COMMENT 'product sold',
  `seller_id` int(1) unsigned NOT NULL COMMENT 'who sold product',
  `price` float(10) unsigned NOT NULL DEFAULT 0 COMMENT 'price of sale',
  `customer_order` int(1) unsigned NOT NULL COMMENT 'customers aggregation',
  FOREIGN KEY (`product_id`) REFERENCES `product` (`id`) ON DELETE RESTRICT,
  FOREIGN KEY (`seller_id`) REFERENCES `seller` (`id`) ON DELETE RESTRICT
) COMMENT='list of sales' ENGINE='InnoDB' COLLATE 'utf8_czech_ci';

INSERT INTO `department` (`id`, `name`) VALUES
	(1, 'tv'),
	(2, 'camera'),
	(3, 'phone');

INSERT INTO `product` (`id`, `department_id`, `name`, `price`, `stock`) VALUES
	(1, 1, 'LGY smart-1 TV', 4200, 3),
	(2, 1, 'LGY smart-1.1 devil edition TV', 6666, 1),
	(3, 1, 'Panavocal COMPACT', 3000, 25),
	(4, 2, 'Panavocal HiCAM 7', 2400, 12),
	(5, 2, 'Panavocal HiCAM 8', 3180, 4),
	(6, 2, 'Panavocal HiCAM 8 improved', 3550, 4),
	(7, 2, 'Panavocal HiCAM 9', 5940, 4),
	(8, 2, 'Nixon G1', 5690, 10),
	(9, 2, 'Nixon G2', 6900, 5),
	(10, 2, 'Nixon E1', 5710, 0),
	(11, 3, 'Nocia M300', 1900, 30),
	(12, 3, 'Nocia M5000', 6999, 54),
	(13, 3, 'Sory Electroson pg90', 5320, 12);

INSERT INTO `product_parameter_list` (`id`, `department_id`, `parameter_name`) VALUES
	(1, 1, 'diagonal'),
	(2, 1, 'color'),
	(3, 1, 'format'),
	(4, 2, 'resolution'),
	(5, 2, 'zoom'),
	(6, 2, 'flash'),
	(7, 2, 'material'),
	(8, 3, 'OS'),
	(9, 3, 'capacity'),
	(10, 3, 'wifi'),
	(11, 3, 'bluetooth'),
	(12, 3, 'camera');

INSERT INTO `product_parameter` (`product_id`, `parameter_id`, `value`) VALUES
	(1, 1, '65'), (1, 2, 'black'), (1, 3, '4:3'),
	(2, 1, '99'), (2, 2, 'red'), (2, 3, '16:9'),
	(3, 1, '52'), (3, 2, 'gray'), (3, 3, '16:9'),
	(4, 4, '7'), (4, 5, '14'), (4, 6, '0'), (4, 7, 'plastic'),
	(5, 4, '9'), (5, 6, '0'), (5, 7, 'plastic'),
	(6, 4, '9'), (6, 5, '18'), (6, 6, '1'), (6, 7, 'plastic'),
	(7, 4, '12'), (7, 5, '30'), (7, 6, '1'), (7, 7, 'metallic'),
	(8, 4, '10'), (8, 5, '24'), (8, 6, '1'), (8, 7, 'aluminium'),
	(9, 4, '10'), (9, 5, '26'), (9, 6, '1'), (9, 7, 'aluminium'),
	(10, 4, '12'), (10, 5, '24'),
	(11, 8, NULL), (11, 9, '100'), (11, 10, '0'), (11, 11, '0'), (11, 12, NULL),
	(12, 8, 'Syndian 60'), (12, 9, '2000'), (12, 10, '1'), (12, 11, '1'), (12, 12, '3'),
	(12, 8, 'Robot 1.15'), (12, 10, '0'), (12, 11, '1'), (12, 12, '8');

INSERT INTO `seller` (`id`, `department_id`, `login`, `password`) VALUES
	(1, 1, 'george', '9fd8de5fc2a7c2c0d469b2fff1afde4e5def37ba'),
	(2, 1, 'jane', '8a8deed44623d4c44268c26652d80945851c4f7f'),
	(3, 2, 'fred', '31017a722665e4afce586950f42944a6d331dabf'),
	(4, 3, 'igor2', '8de587ae7bbf71ae276cc0b4cb3be74c179a0820'),
	(5, 3, 'marry', '1155e8b056c3d1c8887d7809a58e2314b15cf746');

INSERT INTO `sale` (`product_id`, `seller_id`, `price`, `customer_order`) VALUES
	(1, 2, 4200, 1), (1, 2, 4200, 2), (1, 2, 4200, 3), (1, 1, 4000, 4),
	(3, 2, 2800, 5), (3, 2, 2850, 6), (3, 1, 2850, 7), (3, 1, 2950, 8),
	(4, 3, 2400, 9),
	(5, 3, 3180, 10),
	(7, 3, 5920, 3), (7, 3, 5940, 11),
	(8, 3, 5690, 12), (8, 3, 5690, 6), (8, 3, 5690, 13),
	(9, 3, 6400, 14), (9, 3, 6400, 15), (9, 3, 6400, 16), (9, 3, 6400, 16), (9, 3, 6400, 16), (9, 3, 6400, 16), (9, 3, 6400, 17),
	(10, 3, 5710, 18),
	(11, 5, 1900, 19), (11, 5, 1900, 20), (11, 5, 1900, 6), (11, 5, 1900, 17), (11, 5, 1900, 21), (11, 5, 1900, 22), (11, 5, 1900, 23), (11, 5, 1900, 24), (11, 5, 1900, 25),
	(12, 4, 6999, 26), (12, 5, 6999, 27),
	(13, 4, 5320, 27), (13, 4, 5320, 28);
