-- Initial schema. rental_office.supervisor_id and employee.rental_office_id reference each other,
-- so the supervisor foreign key is added after both tables exist.

CREATE TABLE `rental_office` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `supervisor_id` INT UNSIGNED NULL DEFAULT NULL,
  `name` VARCHAR(256) NOT NULL DEFAULT '',
  `street` VARCHAR(64) NOT NULL DEFAULT '',
  `city` VARCHAR(64) NOT NULL DEFAULT '',
  `country` CHAR(2) NOT NULL,
  `zip` VARCHAR(10) NOT NULL DEFAULT '',
  `phone` VARCHAR(13) NOT NULL DEFAULT '',
  `email` VARCHAR(96) NOT NULL DEFAULT '',
  `lat` DECIMAL(10,6) NOT NULL DEFAULT 0,
  `lon` DECIMAL(10,6) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  INDEX `fk_rental_office_employee1_idx` (`supervisor_id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_czech_ci;

CREATE TABLE `employee` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `rental_office_id` INT UNSIGNED NOT NULL,
  `name` VARCHAR(64) NOT NULL DEFAULT '',
  `surname` VARCHAR(64) NOT NULL DEFAULT '',
  `phone` VARCHAR(13) NOT NULL DEFAULT '',
  `email` VARCHAR(96) NOT NULL DEFAULT '',
  `password` CHAR(64) NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_employee_rental_office_idx` (`rental_office_id`),
  CONSTRAINT `fk_employee_rental_office`
    FOREIGN KEY (`rental_office_id`)
    REFERENCES `rental_office` (`id`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_czech_ci;

ALTER TABLE `rental_office`
  ADD CONSTRAINT `fk_rental_office_employee1`
    FOREIGN KEY (`supervisor_id`)
    REFERENCES `employee` (`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE;

CREATE TABLE `vehicle_type` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `manufacturer` VARCHAR(96) NOT NULL DEFAULT '',
  `brand` VARCHAR(96) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_czech_ci;

CREATE TABLE `vehicle_color` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(32) NOT NULL DEFAULT '',
  `ean` VARCHAR(32) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_czech_ci;

CREATE TABLE `vehicle_fuel_type` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `fuel` VARCHAR(96) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_czech_ci;

CREATE TABLE `vehicle` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `vehicle_type_id` INT UNSIGNED NOT NULL,
  `vehicle_fuel_type_id` INT UNSIGNED NOT NULL,
  `vehicle_color_id` INT UNSIGNED NOT NULL,
  `rental_office_id` INT UNSIGNED NULL DEFAULT NULL,
  `model` VARCHAR(32) NOT NULL DEFAULT '',
  `plate_number` VARCHAR(7) NOT NULL DEFAULT '',
  `mileage` DECIMAL(10,4) UNSIGNED NOT NULL DEFAULT 0,
  `year` YEAR NOT NULL,
  `cost_per_day` DECIMAL(10,4) UNSIGNED NOT NULL DEFAULT 0,
  `cost_per_unit` DECIMAL(10,4) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  INDEX `fk_vehicle_vehicle_type1_idx` (`vehicle_type_id`),
  INDEX `fk_vehicle_vehicle_color1_idx` (`vehicle_color_id`),
  INDEX `fk_vehicle_rental_office1_idx` (`rental_office_id`),
  INDEX `fk_vehicle_vehicle_fuel_type1_idx` (`vehicle_fuel_type_id`),
  CONSTRAINT `fk_vehicle_vehicle_type1`
    FOREIGN KEY (`vehicle_type_id`)
    REFERENCES `vehicle_type` (`id`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE,
  CONSTRAINT `fk_vehicle_vehicle_color1`
    FOREIGN KEY (`vehicle_color_id`)
    REFERENCES `vehicle_color` (`id`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE,
  CONSTRAINT `fk_vehicle_rental_office1`
    FOREIGN KEY (`rental_office_id`)
    REFERENCES `rental_office` (`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE,
  CONSTRAINT `fk_vehicle_vehicle_fuel_type1`
    FOREIGN KEY (`vehicle_fuel_type_id`)
    REFERENCES `vehicle_fuel_type` (`id`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_czech_ci;

CREATE TABLE `customer` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(64) NOT NULL DEFAULT '',
  `surname` VARCHAR(64) NOT NULL DEFAULT '',
  `email` VARCHAR(96) NOT NULL DEFAULT '',
  `password` CHAR(64) NOT NULL DEFAULT '',
  `identity_number` VARCHAR(32) NOT NULL,
  `phone` VARCHAR(13) NOT NULL DEFAULT '',
  `street` VARCHAR(64) NOT NULL DEFAULT '',
  `city` VARCHAR(64) NOT NULL DEFAULT '',
  `country` VARCHAR(64) NOT NULL DEFAULT '',
  `zip` VARCHAR(10) NOT NULL DEFAULT '',
  `created_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_czech_ci;

CREATE TABLE `car_rental` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `vehicle_id` INT UNSIGNED NOT NULL,
  `rental_office_id` INT UNSIGNED NOT NULL,
  `customer_id` INT UNSIGNED NOT NULL,
  `borrower_id` INT UNSIGNED NULL DEFAULT NULL,
  `recipient_id` INT UNSIGNED NULL DEFAULT NULL,
  `reserved_at` DATETIME NOT NULL,
  `reserved_from` DATE NOT NULL,
  `reserved_to` DATE NOT NULL,
  `start_at` DATETIME NULL DEFAULT NULL,
  `end_at` DATETIME NULL DEFAULT NULL,
  `distance` DECIMAL(10,4) NULL DEFAULT NULL,
  `price` DECIMAL(10,4) NULL DEFAULT NULL,
  `deleted` TINYINT(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  INDEX `fk_car_rental_vehicle1_idx` (`vehicle_id`),
  INDEX `fk_car_rental_customer1_idx` (`customer_id`),
  INDEX `fk_car_rental_employee1_idx` (`borrower_id`),
  INDEX `fk_car_rental_employee2_idx` (`recipient_id`),
  CONSTRAINT `fk_car_rental_vehicle1`
    FOREIGN KEY (`vehicle_id`)
    REFERENCES `vehicle` (`id`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE,
  CONSTRAINT `fk_car_rental_customer1`
    FOREIGN KEY (`customer_id`)
    REFERENCES `customer` (`id`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE,
  CONSTRAINT `fk_car_rental_employee1`
    FOREIGN KEY (`borrower_id`)
    REFERENCES `employee` (`id`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE,
  CONSTRAINT `fk_car_rental_employee2`
    FOREIGN KEY (`recipient_id`)
    REFERENCES `employee` (`id`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_czech_ci;
