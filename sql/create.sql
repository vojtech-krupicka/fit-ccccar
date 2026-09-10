SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

DROP SCHEMA IF EXISTS `ccccar` ;
CREATE SCHEMA IF NOT EXISTS `ccccar` DEFAULT CHARACTER SET utf8 COLLATE utf8_czech_ci ;
USE `ccccar` ;

-- -----------------------------------------------------
-- Table `ccccar`.`employee`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `ccccar`.`employee` ;

CREATE  TABLE IF NOT EXISTS `ccccar`.`employee` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `rentalOffice_id` INT UNSIGNED NOT NULL ,
  `name` VARCHAR(64) NOT NULL DEFAULT '' ,
  `surname` VARCHAR(64) NOT NULL DEFAULT '' ,
  `phone` VARCHAR(9) NOT NULL DEFAULT '' ,
  `email` VARCHAR(96) NOT NULL DEFAULT '' ,
  `password` CHAR(64) NOT NULL DEFAULT '' ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_employee_rentalOffice_idx` (`rentalOffice_id` ASC) ,
  CONSTRAINT `fk_employee_rentalOffice`
    FOREIGN KEY (`rentalOffice_id` )
    REFERENCES `ccccar`.`rentalOffice` (`id` )
    ON DELETE RESTRICT
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `ccccar`.`rentalOffice`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `ccccar`.`rentalOffice` ;

CREATE  TABLE IF NOT EXISTS `ccccar`.`rentalOffice` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `supervisor_id` INT UNSIGNED NULL DEFAULT NULL ,
  `name` VARCHAR(256) NOT NULL DEFAULT '' ,
  `street` VARCHAR(64) NOT NULL DEFAULT '' ,
  `city` VARCHAR(64) NOT NULL DEFAULT '' ,
  `country` VARCHAR(64) NOT NULL DEFAULT '' ,
  `zip` VARCHAR(10) NOT NULL DEFAULT '' ,
  `phone` VARCHAR(9) NOT NULL DEFAULT '' ,
  `email` VARCHAR(96) NOT NULL DEFAULT '' ,
  `lat` DECIMAL(10,6) NOT NULL DEFAULT 0.0,
  `lon` DECIMAL(10,6) NOT NULL DEFAULT 0.0,
  PRIMARY KEY (`id`) ,
  INDEX `fk_rentalOffice_employee1_idx` (`supervisor_id` ASC) ,
  CONSTRAINT `fk_rentalOffice_employee1`
    FOREIGN KEY (`supervisor_id` )
    REFERENCES `ccccar`.`employee` (`id` )
    ON DELETE SET NULL
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `ccccar`.`vehicleType`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `ccccar`.`vehicleType` ;

CREATE  TABLE IF NOT EXISTS `ccccar`.`vehicleType` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `manufacturer` VARCHAR(96) NOT NULL DEFAULT '' ,
  `brand` VARCHAR(96) NOT NULL DEFAULT '' ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `ccccar`.`vehicleColor`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `ccccar`.`vehicleColor` ;

CREATE  TABLE IF NOT EXISTS `ccccar`.`vehicleColor` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(32) NOT NULL DEFAULT '' ,
  `ean` VARCHAR(32) NOT NULL DEFAULT '' ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `ccccar`.`vehicleFuelType`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `ccccar`.`vehicleFuelType` ;

CREATE  TABLE IF NOT EXISTS `ccccar`.`vehicleFuelType` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `fuel` VARCHAR(96) NOT NULL DEFAULT '' ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `ccccar`.`vehicle`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `ccccar`.`vehicle` ;

CREATE  TABLE IF NOT EXISTS `ccccar`.`vehicle` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `vehicleType_id` INT UNSIGNED NOT NULL ,
  `vehicleFuelType_id` INT UNSIGNED NOT NULL ,
  `vehicleColor_id` INT UNSIGNED NOT NULL ,
  `rentalOffice_id` INT UNSIGNED NULL DEFAULT NULL ,
  `model` VARCHAR(32) NOT NULL DEFAULT '' ,
  `plateNumber` VARCHAR(7) NOT NULL DEFAULT '' ,
  `mileage` DECIMAL(10,4) UNSIGNED NOT NULL DEFAULT 0 ,
  `year` YEAR NOT NULL ,
  `costPerDay` DECIMAL(10,4) UNSIGNED NOT NULL DEFAULT 0 ,
  `costPerUnit` DECIMAL(10,4) UNSIGNED NOT NULL DEFAULT 0 ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_vehicle_vehicleType1_idx` (`vehicleType_id` ASC) ,
  INDEX `fk_vehicle_vehicleColor1_idx` (`vehicleColor_id` ASC) ,
  INDEX `fk_vehicle_rentalOffice1_idx` (`rentalOffice_id` ASC) ,
  INDEX `fk_vehicle_vehicleFuelType1_idx` (`vehicleFuelType_id` ASC) ,
  CONSTRAINT `fk_vehicle_vehicleType1`
    FOREIGN KEY (`vehicleType_id` )
    REFERENCES `ccccar`.`vehicleType` (`id` )
    ON DELETE RESTRICT
    ON UPDATE CASCADE,
  CONSTRAINT `fk_vehicle_vehicleColor1`
    FOREIGN KEY (`vehicleColor_id` )
    REFERENCES `ccccar`.`vehicleColor` (`id` )
    ON DELETE RESTRICT
    ON UPDATE CASCADE,
  CONSTRAINT `fk_vehicle_rentalOffice1`
    FOREIGN KEY (`rentalOffice_id` )
    REFERENCES `ccccar`.`rentalOffice` (`id` )
    ON DELETE SET NULL
    ON UPDATE CASCADE,
  CONSTRAINT `fk_vehicle_vehicleFuelType1`
    FOREIGN KEY (`vehicleFuelType_id` )
    REFERENCES `ccccar`.`vehicleFuelType` (`id` )
    ON DELETE RESTRICT
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `ccccar`.`customer`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `ccccar`.`customer` ;

CREATE  TABLE IF NOT EXISTS `ccccar`.`customer` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(64) NOT NULL DEFAULT '' ,
  `surname` VARCHAR(64) NOT NULL DEFAULT '' ,
  `email` VARCHAR(96) NOT NULL DEFAULT '' ,
  `password` CHAR(64) NOT NULL DEFAULT '' ,
  `identityNumber` VARCHAR(32) NOT NULL ,
  `phone` VARCHAR(9) NOT NULL DEFAULT '' ,
  `street` VARCHAR(64) NOT NULL DEFAULT '' ,
  `city` VARCHAR(64) NOT NULL DEFAULT '' ,
  `country` VARCHAR(64) NOT NULL DEFAULT '' ,
  `zip` VARCHAR(10) NOT NULL DEFAULT '' ,
  `createdAt` DATETIME NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `ccccar`.`loan`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `ccccar`.`loan` ;

CREATE  TABLE IF NOT EXISTS `ccccar`.`loan` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `vehicle_id` INT UNSIGNED NOT NULL ,
  `rentalOffice_id` INT UNSIGNED NOT NULL ,
  `customer_id` INT UNSIGNED NOT NULL ,
  `borrower_id` INT UNSIGNED NULL DEFAULT NULL ,
  `recipient_id` INT UNSIGNED NULL DEFAULT NULL ,
  `reservedAt` DATETIME NOT NULL ,
  `reservedFrom` DATE NOT NULL ,
  `reservedTo` DATE NOT NULL ,
  `startAt` DATETIME NULL DEFAULT NULL ,
  `endAt` DATETIME NULL DEFAULT NULL ,
  `distance` DECIMAL(10,4) NULL DEFAULT NULL ,
  `price` DECIMAL(10,4) NULL DEFAULT NULL ,
  `deleted` INT(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`) ,
  INDEX `fk_loan_vehicle1_idx` (`vehicle_id` ASC) ,
  INDEX `fk_loan_customer1_idx` (`customer_id` ASC) ,
  INDEX `fk_loan_employee1_idx` (`borrower_id` ASC) ,
  INDEX `fk_loan_employee2_idx` (`recipient_id` ASC) ,
  CONSTRAINT `fk_loan_vehicle1`
    FOREIGN KEY (`vehicle_id` )
    REFERENCES `ccccar`.`vehicle` (`id` )
    ON DELETE RESTRICT
    ON UPDATE CASCADE,
  CONSTRAINT `fk_loan_customer1`
    FOREIGN KEY (`customer_id` )
    REFERENCES `ccccar`.`customer` (`id` )
    ON DELETE RESTRICT
    ON UPDATE CASCADE,
  CONSTRAINT `fk_loan_employee1`
    FOREIGN KEY (`borrower_id` )
    REFERENCES `ccccar`.`employee` (`id` )
    ON DELETE RESTRICT
    ON UPDATE CASCADE,
  CONSTRAINT `fk_loan_employee2`
    FOREIGN KEY (`recipient_id` )
    REFERENCES `ccccar`.`employee` (`id` )
    ON DELETE RESTRICT
    ON UPDATE CASCADE)
ENGINE = InnoDB;



SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
