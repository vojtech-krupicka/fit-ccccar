-- -----------------------------------------------------
-- Které půjčovny mají k dispozici vozy Felicia?
-- -----------------------------------------------------
SELECT DISTINCT(ro.id), ro.name FROM rentalOffice AS ro JOIN vehicle AS v ON (ro.id = v.rentalOffice_id) JOIN vehicleType as vt ON (vt.id = v.vehicleType_id) WHERE vt.brand = 'Felicia';
SELECT ro.id, ro.name, COUNT(ro.id) AS count FROM rentalOffice AS ro JOIN vehicle AS v ON (ro.id = v.rentalOffice_id) JOIN vehicleType as vt ON (vt.id = v.vehicleType_id) WHERE vt.brand = 'Felicia' GROUP BY ro.id ORDER BY count, ro.id;

-- +----+--------------------------+-------+
-- | id | name                     | count |
-- +----+--------------------------+-------+
-- |  5 | Pobočka Pardubice        |     1 |
-- |  1 | Pobočka Praha            |     2 |
-- |  6 | Pobočka České Budějovice |     2 |
-- +----+--------------------------+-------+
-- 3 rows in set (0.00 sec)


-- -----------------------------------------------------
-- Který zákazník si půjčil nejvíce vozů?
-- -----------------------------------------------------
SELECT c.id, c.name, c.surname, COUNT(*) as count FROM loan AS l JOIN customer AS c ON (c.id = l.customer_id) GROUP BY l.customer_id ORDER BY count DESC LIMIT 1;

-- +----+---------+---------+-------+
-- | id | name    | surname | count |
-- +----+---------+---------+-------+
-- |  3 | Hamfast | Gamgee  |    32 |
-- +----+---------+---------+-------+
-- 1 row in set (0.00 sec)


-- -----------------------------------------------------
-- Pivotní tabulka - počet vypůjček každého zákazníka pro každé vozidlo.
-- -----------------------------------------------------
SELECT CONCAT(vt.manufacturer, " ", vt.brand, " ", v.model) AS vehicle, CONCAT(c.name, " ", c.surname) AS customer, COUNT(l.id) AS num_of_loans FROM vehicleType vt JOIN vehicle v ON (vt.id = v.vehicleType_id) CROSS JOIN customer c LEFT JOIN loan l ON (l.vehicle_id = v.id AND l.customer_id = c.id) GROUP BY v.id, c.id ORDER BY v.id, c.id;

-- +-----------------------+-------------------------+--------------+
-- | vehicle               | customer                | num_of_loans |
-- +-----------------------+-------------------------+--------------+
-- | Škoda Felicia 1.1 TDI | Bilbo Baggins           |            1 |
-- | Škoda Felicia 1.1 TDI | Rosie Cotton            |            1 |
-- | Škoda Felicia 1.1 TDI | Hamfast Gamgee          |            4 |
-- | Škoda Felicia 1.1 TDI | Fredegar Bolger         |            2 |
-- | Škoda Felicia 1.1 TDI | Galadriel Lórien        |            0 |
-- | Škoda Felicia 1.1 TDI | Celeborn Lórien         |            0 |
-- | Škoda Felicia 1.1 TDI | Arwen Undómiel          |            0 |
-- | Škoda Felicia 1.1 TDI | Glorfindel Rivendell    |            0 |
-- | Škoda Felicia 1.1 TDI | Haldir Lórien           |            1 |
-- | Škoda Felicia 1.1 TDI | Thranduil Woodland      |            2 |
-- | Škoda Felicia 1.1 TDI | Thorin Oakenshield      |            0 |
-- | Škoda Felicia 1.1 TDI | Balin Fundinson         |            2 |
-- | Škoda Felicia 1.1 TDI | Dwalin Fundinson        |            2 |
-- | Škoda Felicia 1.1 TDI | Glóin Gróinson          |            1 |
-- | Škoda Felicia 1.1 TDI | Óin Gróinson            |            0 |
-- | Škoda Felicia 1.1 TDI | Fíli Dísson             |            0 |
-- | Škoda Felicia 1.1 TDI | Kíli Dísson             |            2 |
-- | Škoda Felicia 1.1 TDI | Bombur Bifurson         |            3 |
-- | Škoda Felicia 1.1 TDI | Bofur Bifurson          |            0 |
-- | Škoda Felicia 1.1 TDI | Saruman White           |            0 |
-- | Škoda Felicia 1.1 TDI | Gollum Sméagol          |            1 |
-- | Škoda Felicia 1.1 TDI | Tom Bombadil            |            1 |
-- | Škoda Felicia 1.1 TDI | Goldberry Riverdaughter |            1 |
-- | Škoda Felicia 1.1 TDI | Treebeard Fangorn       |            1 |
-- | Škoda Felicia 1.1 TDI | Quickbeam Fangorn       |            0 |
-- | Škoda Felicia 1.1 TDI | Bard Bowman             |            0 |
-- | Škoda Felicia 1.1 TDI | Beorn Skinchanger       |            3 |
-- | Škoda Felicia 1.1 TDI | Gríma Wormtongue        |            0 |
-- | Škoda Felicia 1.1 TDI | Halbarad Dúnadan        |            0 |
-- | Škoda Felicia 1.1 TDI | Isildur Elendilson      |            0 |
-- ...
-- ......
-- .........
-- +-----------------------+-------------------------+--------------+
-- 750 rows in set (0.02 sec)
