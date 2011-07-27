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
  `id` INT NOT NULL AUTO_INCREMENT ,
  `description` VARCHAR(45) NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `w3tdb`.`user`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `w3tdb`.`user` ;

CREATE  TABLE IF NOT EXISTS `w3tdb`.`user` (
  `user_account` VARCHAR(50) NOT NULL ,
  `first_name` VARCHAR(45) NOT NULL ,
  `age_range_id` INT NULL DEFAULT 1 ,
  `ethnicity` VARCHAR(45) NULL DEFAULT 'N/A' ,
  `pwd` VARCHAR(50) NOT NULL ,
  `register_time` TIMESTAMP NOT NULL ,
  `opt_in` TINYINT(1)  NULL DEFAULT true ,
  `verified` TINYINT(1)  NULL DEFAULT false ,
  `deleted` TINYINT(1)  NULL DEFAULT false ,
  PRIMARY KEY (`user_account`) ,
  INDEX `fk_user_age_range1` (`age_range_id` ASC) ,
  CONSTRAINT `fk_user_age_range1`
    FOREIGN KEY (`age_range_id` )
    REFERENCES `w3tdb`.`age_range` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
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
  `store_account` VARCHAR(50) NOT NULL ,
  `pwd` VARCHAR(50) NOT NULL ,
  `store_name` VARCHAR(50) NOT NULL ,
  `parent_store_account` VARCHAR(50) NULL ,
  `store_type` VARCHAR(50) NULL DEFAULT 'normal' ,
  `register_time` TIMESTAMP NOT NULL ,
  `verified` TINYINT(1)  NULL DEFAULT false ,
  PRIMARY KEY (`store_account`) ,
  INDEX `fk_store_store` (`parent_store_account` ASC) ,
  INDEX `fk_store_store_type1` (`store_type` ASC) ,
  CONSTRAINT `fk_store_store`
    FOREIGN KEY (`parent_store_account` )
    REFERENCES `w3tdb`.`store` (`store_account` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_store_store_type1`
    FOREIGN KEY (`store_type` )
    REFERENCES `w3tdb`.`store_type` (`store_type` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `w3tdb`.`receipt_source`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `w3tdb`.`receipt_source` ;

CREATE  TABLE IF NOT EXISTS `w3tdb`.`receipt_source` (
  `source` VARCHAR(20) NOT NULL ,
  PRIMARY KEY (`source`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `w3tdb`.`receipt`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `w3tdb`.`receipt` ;

CREATE  TABLE IF NOT EXISTS `w3tdb`.`receipt` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `store_account` VARCHAR(50) NOT NULL ,
  `user_account` VARCHAR(50) NOT NULL ,
  `receipt_time` TIMESTAMP NOT NULL ,
  `tax` DECIMAL(10,2) NOT NULL ,
  `total_cost` DECIMAL(10,2) NOT NULL ,
  `source` VARCHAR(20) NOT NULL ,
  `img` BLOB NULL ,
  `deleted` TINYINT(1)  NULL DEFAULT false ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_receipt_store1` (`store_account` ASC) ,
  INDEX `fk_receipt_user1` (`user_account` ASC) ,
  INDEX `fk_receipt_receipt_source1` (`source` ASC) ,
  CONSTRAINT `fk_receipt_store1`
    FOREIGN KEY (`store_account` )
    REFERENCES `w3tdb`.`store` (`store_account` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_receipt_user1`
    FOREIGN KEY (`user_account` )
    REFERENCES `w3tdb`.`user` (`user_account` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_receipt_receipt_source1`
    FOREIGN KEY (`source` )
    REFERENCES `w3tdb`.`receipt_source` (`source` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `w3tdb`.`receipt_item`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `w3tdb`.`receipt_item` ;

CREATE  TABLE IF NOT EXISTS `w3tdb`.`receipt_item` (
  `item_id` INT NOT NULL ,
  `receipt_id` INT NOT NULL ,
  `item_name` VARCHAR(45) NOT NULL ,
  `item_qty` INT NOT NULL ,
  `item_discount` DECIMAL(10,2) NOT NULL ,
  `item_price` DECIMAL(10,2) NOT NULL ,
  `deleted` TINYINT(1)  NULL DEFAULT false ,
  PRIMARY KEY (`item_id`, `receipt_id`) ,
  INDEX `fk_receipt_item_receipt1` (`receipt_id` ASC) ,
  CONSTRAINT `fk_receipt_item_receipt1`
    FOREIGN KEY (`receipt_id` )
    REFERENCES `w3tdb`.`receipt` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
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
  `value` VARCHAR(100) NOT NULL ,
  `store_account` VARCHAR(50) NULL ,
  `user_account` VARCHAR(50) NULL ,
  `contact_type` VARCHAR(50) NOT NULL ,
  INDEX `fk_contact_contact_type1` (`contact_type` ASC) ,
  INDEX `fk_contact_store1` (`store_account` ASC) ,
  PRIMARY KEY (`value`) ,
  INDEX `fk_contact_user1` (`user_account` ASC) ,
  CONSTRAINT `fk_contact_contact_type1`
    FOREIGN KEY (`contact_type` )
    REFERENCES `w3tdb`.`contact_type` (`contact_type` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_contact_store1`
    FOREIGN KEY (`store_account` )
    REFERENCES `w3tdb`.`store` (`store_account` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_contact_user1`
    FOREIGN KEY (`user_account` )
    REFERENCES `w3tdb`.`user` (`user_account` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `w3tdb`.`address`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `w3tdb`.`address` ;

CREATE  TABLE IF NOT EXISTS `w3tdb`.`address` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `add1` VARCHAR(100) NULL ,
  `add2` VARCHAR(100) NULL ,
  `zipcode` VARCHAR(50) NOT NULL ,
  `city` VARCHAR(100) NULL ,
  `state` VARCHAR(100) NULL ,
  `country` VARCHAR(100) NOT NULL ,
  `store_account` VARCHAR(50) NULL ,
  `user_account` VARCHAR(50) NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_address_store1` (`store_account` ASC) ,
  INDEX `fk_address_user1` (`user_account` ASC) ,
  CONSTRAINT `fk_address_store1`
    FOREIGN KEY (`store_account` )
    REFERENCES `w3tdb`.`store` (`store_account` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_address_user1`
    FOREIGN KEY (`user_account` )
    REFERENCES `w3tdb`.`user` (`user_account` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `w3tdb`.`service`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `w3tdb`.`service` ;

CREATE  TABLE IF NOT EXISTS `w3tdb`.`service` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `service_name` VARCHAR(50) NULL ,
  `discription` TEXT NULL ,
  `price` VARCHAR(45) NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `w3tdb`.`store_service`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `w3tdb`.`store_service` ;

CREATE  TABLE IF NOT EXISTS `w3tdb`.`store_service` (
  `store_account` VARCHAR(50) NOT NULL ,
  `service_id` INT NOT NULL ,
  PRIMARY KEY (`store_account`, `service_id`) ,
  INDEX `fk_store_has_service_service1` (`service_id` ASC) ,
  INDEX `fk_store_has_service_store1` (`store_account` ASC) ,
  CONSTRAINT `fk_store_has_service_store1`
    FOREIGN KEY (`store_account` )
    REFERENCES `w3tdb`.`store` (`store_account` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_store_has_service_service1`
    FOREIGN KEY (`service_id` )
    REFERENCES `w3tdb`.`service` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `w3tdb`.`tag`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `w3tdb`.`tag` ;

CREATE  TABLE IF NOT EXISTS `w3tdb`.`tag` (
  `tag` VARCHAR(20) NOT NULL ,
  `user_account` VARCHAR(50) NOT NULL ,
  PRIMARY KEY (`tag`, `user_account`) ,
  INDEX `fk_tag_user1` (`user_account` ASC) ,
  CONSTRAINT `fk_tag_user1`
    FOREIGN KEY (`user_account` )
    REFERENCES `w3tdb`.`user` (`user_account` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `w3tdb`.`receipt_tag`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `w3tdb`.`receipt_tag` ;

CREATE  TABLE IF NOT EXISTS `w3tdb`.`receipt_tag` (
  `tag` VARCHAR(20) NOT NULL ,
  `user_account` VARCHAR(50) NOT NULL ,
  `receipt_id` INT NOT NULL ,
  PRIMARY KEY (`tag`, `user_account`, `receipt_id`) ,
  INDEX `fk_tag_has_receipt_receipt1` (`receipt_id` ASC) ,
  INDEX `fk_tag_has_receipt_tag1` (`tag` ASC, `user_account` ASC) ,
  CONSTRAINT `fk_tag_has_receipt_tag1`
    FOREIGN KEY (`tag` , `user_account` )
    REFERENCES `w3tdb`.`tag` (`tag` , `user_account` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_tag_has_receipt_receipt1`
    FOREIGN KEY (`receipt_id` )
    REFERENCES `w3tdb`.`receipt` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;



SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
