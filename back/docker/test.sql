-- MySQL Script generated by MySQL Workbench
-- Fri Jan  5 14:38:19 2024
-- Model: New Model    Version: 1.0
-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------
-- Schema artlab
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema artlab
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `artlab` DEFAULT CHARACTER SET utf8 ;
USE `artlab` ;

-- -----------------------------------------------------
-- Table `artlab`.`users_adresses`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `artlab`.`users_adresses` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `street_no` VARCHAR(45) NOT NULL,
  `zip` VARCHAR(45) NOT NULL,
  `city` VARCHAR(45) NOT NULL,
  `country` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `artlab`.`users`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `artlab`.`users` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(45) NOT NULL,
  `email` VARCHAR(45) NOT NULL,
  `password` VARCHAR(45) NOT NULL,
  `phonenumber` VARCHAR(45) NOT NULL,
  `date_of_birth` DATE NOT NULL,
  `profile_picture` VARCHAR(45) NOT NULL,
  `role` ENUM("admin", "user") NOT NULL,
  `order_id` INT NOT NULL,
  `created_at` DATE NOT NULL,
  `users_adresses_id` INT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_users_users_adresses1_idx` (`users_adresses_id` ASC) VISIBLE,
  CONSTRAINT `fk_users_users_adresses1`
    FOREIGN KEY (`users_adresses_id`)
    REFERENCES `artlab`.`users_adresses` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `artlab`.`orders_adresses`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `artlab`.`orders_adresses` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `street_no` VARCHAR(45) NOT NULL,
  `zip` VARCHAR(45) NOT NULL,
  `city` VARCHAR(45) NOT NULL,
  `country` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `artlab`.`orders`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `artlab`.`orders` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `status` VARCHAR(45) NOT NULL,
  `user_id` INT NOT NULL,
  `order_adress_id` INT NOT NULL,
  `created_at` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`id`, `user_id`, `order_adress_id`),
  INDEX `fk_orders_users1_idx` (`user_id` ASC) VISIBLE,
  INDEX `fk_orders_orders_adresses1_idx` (`order_adress_id` ASC) VISIBLE,
  CONSTRAINT `fk_orders_users1`
    FOREIGN KEY (`user_id`)
    REFERENCES `artlab`.`users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_orders_orders_adresses1`
    FOREIGN KEY (`order_adress_id`)
    REFERENCES `artlab`.`orders_adresses` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `artlab`.`auctions`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `artlab`.`auctions` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `end_date` DATE NOT NULL,
  `created_at` DATE NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `artlab`.`artpieces`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `artlab`.`artpieces` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NOT NULL,
  `description` VARCHAR(45) NOT NULL,
  `width_in_cm` INT NOT NULL,
  `height_in_cm` INT NOT NULL,
  `price` INT NOT NULL,
  `type` ENUM("auction", "buynow") NOT NULL,
  `likes` INT NOT NULL,
  `created_at` DATE NOT NULL,
  `user_id` INT NOT NULL,
  `order_id` INT NULL,
  `auction_id` INT NULL,
  PRIMARY KEY (`id`, `user_id`, `order_id`, `auction_id`),
  INDEX `fk_artpieces_users_idx` (`user_id` ASC) VISIBLE,
  INDEX `fk_artpieces_orders1_idx` (`order_id` ASC) VISIBLE,
  INDEX `fk_artpieces_auctions1_idx` (`auction_id` ASC) VISIBLE,
  CONSTRAINT `fk_artpieces_users`
    FOREIGN KEY (`user_id`)
    REFERENCES `artlab`.`users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_artpieces_orders1`
    FOREIGN KEY (`order_id`)
    REFERENCES `artlab`.`orders` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_artpieces_auctions1`
    FOREIGN KEY (`auction_id`)
    REFERENCES `artlab`.`auctions` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `artlab`.`comments`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `artlab`.`comments` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `content` VARCHAR(45) NOT NULL,
  `likes` INT NOT NULL,
  `artpiece_id` INT NOT NULL,
  `created_at` DATE NOT NULL,
  `user_id` INT NOT NULL,
  PRIMARY KEY (`id`, `artpiece_id`, `user_id`),
  INDEX `fk_comments_artpieces1_idx` (`artpiece_id` ASC) VISIBLE,
  INDEX `fk_comments_users1_idx` (`user_id` ASC) VISIBLE,
  CONSTRAINT `fk_comments_artpieces1`
    FOREIGN KEY (`artpiece_id`)
    REFERENCES `artlab`.`artpieces` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_comments_users1`
    FOREIGN KEY (`user_id`)
    REFERENCES `artlab`.`users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `artlab`.`bids`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `artlab`.`bids` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `price` INT NOT NULL,
  `created_at` INT NOT NULL,
  `auction_id` INT NOT NULL,
  `user_id` INT NOT NULL,
  PRIMARY KEY (`id`, `auction_id`, `user_id`),
  INDEX `fk_bids_auctions1_idx` (`auction_id` ASC) VISIBLE,
  INDEX `fk_bids_users1_idx` (`user_id` ASC) VISIBLE,
  CONSTRAINT `fk_bids_auctions1`
    FOREIGN KEY (`auction_id`)
    REFERENCES `artlab`.`auctions` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_bids_users1`
    FOREIGN KEY (`user_id`)
    REFERENCES `artlab`.`users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `artlab`.`tags`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `artlab`.`tags` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `artlab`.`images`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `artlab`.`images` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `url` VARCHAR(45) NOT NULL,
  `artpiece_id` INT NOT NULL,
  PRIMARY KEY (`id`, `artpiece_id`),
  INDEX `fk_images_artpieces1_idx` (`artpiece_id` ASC) VISIBLE,
  CONSTRAINT `fk_images_artpieces1`
    FOREIGN KEY (`artpiece_id`)
    REFERENCES `artlab`.`artpieces` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `artlab`.`artpieces_has_tags`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `artlab`.`artpieces_has_tags` (
  `artpiece_id` INT NOT NULL,
  `tag_id` INT NOT NULL,
  PRIMARY KEY (`artpiece_id`, `tag_id`),
  INDEX `fk_artpieces_has_tags_tags1_idx` (`tag_id` ASC) VISIBLE,
  INDEX `fk_artpieces_has_tags_artpieces1_idx` (`artpiece_id` ASC) VISIBLE,
  CONSTRAINT `fk_artpieces_has_tags_artpieces1`
    FOREIGN KEY (`artpiece_id`)
    REFERENCES `artlab`.`artpieces` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_artpieces_has_tags_tags1`
    FOREIGN KEY (`tag_id`)
    REFERENCES `artlab`.`tags` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
