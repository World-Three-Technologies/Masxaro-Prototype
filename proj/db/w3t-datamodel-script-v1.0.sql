SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

DROP DATABASE IF EXISTS `w3tdb` ;
CREATE DATABASE IF NOT EXISTS `w3tdb` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ;
USE `w3tdb` ;

-- -----------------------------------------------------
-- Table `w3tdb`.`age_range`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `w3tdb`.`age_range` ;

CREATE  TABLE IF NOT EXISTS `w3tdb`.`age_range` (
  `age_range_id` INT NOT NULL AUTO_INCREMENT ,
  `description` VARCHAR(45) NULL ,
  PRIMARY KEY (`age_range_id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `w3tdb`.`user`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `w3tdb`.`user` ;

CREATE  TABLE IF NOT EXISTS `w3tdb`.`user` (
  `user_account` VARCHAR(50) NOT NULL ,
  `first_name` VARCHAR(45) NOT NULL ,
  `age_range_id` INT NULL ,
  `ethnicity` VARCHAR(45) NULL ,
  `pwd` VARCHAR(50) NOT NULL ,
  `opt_in` TINYINT(1)  NULL DEFAULT true ,
  `deleted` TINYINT(1)  NULL DEFAULT false ,
  PRIMARY KEY (`user_account`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `w3tdb`.`store_type`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `w3tdb`.`store_type` ;

CREATE  TABLE IF NOT EXISTS `w3tdb`.`store_type` (
  `store_type` VARCHAR(50) NOT NULL ,
  PRIMARY KEY (`store_type`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `w3tdb`.`store`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `w3tdb`.`store` ;

CREATE  TABLE IF NOT EXISTS `w3tdb`.`store` (
  `store_id` INT NOT NULL AUTO_INCREMENT ,
  `pwd` VARCHAR(50) NOT NULL ,
  `store_name` VARCHAR(50) NOT NULL ,
  `parent_store_id` INT NULL ,
  `store_type` VARCHAR(50) NOT NULL ,
  PRIMARY KEY (`store_id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `w3tdb`.`receipt`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `w3tdb`.`receipt` ;

CREATE  TABLE IF NOT EXISTS `w3tdb`.`receipt` (
  `receipt_id` VARCHAR(200) NOT NULL ,
  `store_id` INT NOT NULL ,
  `user_account` VARCHAR(50) NOT NULL ,
  `receipt_time` DATETIME NOT NULL ,
  `tax` DOUBLE NOT NULL ,
  `total_cost` DOUBLE NOT NULL ,
  `img` BLOB NULL ,
  `deleted` TINYINT(1)  NULL DEFAULT false ,
  PRIMARY KEY (`receipt_id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `w3tdb`.`receipt_item`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `w3tdb`.`receipt_item` ;

CREATE  TABLE IF NOT EXISTS `w3tdb`.`receipt_item` (
  `receipt_id` VARCHAR(200) NOT NULL ,
  `item_id` INT NOT NULL ,
  `item_name` VARCHAR(45) NOT NULL ,
  `item_qty` INT NOT NULL ,
  `item_discount` DOUBLE NULL ,
  `item_price` DOUBLE NOT NULL ,
  `deleted` TINYINT(1)  NULL DEFAULT false ,
  PRIMARY KEY (`receipt_id`, `item_id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `w3tdb`.`contact_type`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `w3tdb`.`contact_type` ;

CREATE  TABLE IF NOT EXISTS `w3tdb`.`contact_type` (
  `contact_type` VARCHAR(50) NOT NULL ,
  PRIMARY KEY (`contact_type`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `w3tdb`.`contact`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `w3tdb`.`contact` ;

CREATE  TABLE IF NOT EXISTS `w3tdb`.`contact` (
  `contact_id` INT NOT NULL AUTO_INCREMENT ,
  `store_id` INT NULL ,
  `user_account` VARCHAR(50) NULL ,
  `contact_type` VARCHAR(50) NOT NULL ,
  `value` VARCHAR(100) NOT NULL ,
  PRIMARY KEY (`contact_id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `w3tdb`.`address`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `w3tdb`.`address` ;

CREATE  TABLE IF NOT EXISTS `w3tdb`.`address` (
  `address_id` INT NOT NULL AUTO_INCREMENT ,
  `add1` VARCHAR(100) NULL ,
  `add2` VARCHAR(100) NULL ,
  `zipcode` VARCHAR(50) NOT NULL ,
  `city` VARCHAR(100) NULL ,
  `state` VARCHAR(100) NULL ,
  `country` VARCHAR(100) NOT NULL ,
  `store_id` INT NULL ,
  `user_account` VARCHAR(50) NULL ,
  PRIMARY KEY (`address_id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `w3tdb`.`service`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `w3tdb`.`service` ;

CREATE  TABLE IF NOT EXISTS `w3tdb`.`service` (
  `service_id` INT NOT NULL AUTO_INCREMENT ,
  `service_name` VARCHAR(50) NULL ,
  `discription` TEXT NULL ,
  `price` VARCHAR(45) NULL ,
  PRIMARY KEY (`service_id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `w3tdb`.`store_service`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `w3tdb`.`store_service` ;

CREATE  TABLE IF NOT EXISTS `w3tdb`.`store_service` (
  `store_id` INT NOT NULL ,
  `service_id` INT NOT NULL ,
  PRIMARY KEY (`store_id`, `service_id`) )
ENGINE = InnoDB;



SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
