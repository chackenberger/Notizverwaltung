
# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

-- ---------------------------------------------------------------------
-- notiz
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `notiz`;

CREATE TABLE `notiz`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `besitzer_id` INTEGER NOT NULL,
    `projekt_id` INTEGER,
    `betreff` VARCHAR(100) NOT NULL,
    `text` LONGTEXT NOT NULL,
    `created_at` DATETIME,
    `updated_at` DATETIME,
    PRIMARY KEY (`id`),
    INDEX `notiz_fi_b10ca2` (`besitzer_id`),
    INDEX `notiz_fi_c4457c` (`projekt_id`),
    CONSTRAINT `notiz_fk_b10ca2`
        FOREIGN KEY (`besitzer_id`)
        REFERENCES `person` (`id`),
    CONSTRAINT `notiz_fk_c4457c`
        FOREIGN KEY (`projekt_id`)
        REFERENCES `projekt` (`id`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- projekt
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `projekt`;

CREATE TABLE `projekt`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `sdate` DATE NOT NULL,
    `edate` DATE NOT NULL,
    `created_at` DATETIME,
    `updated_at` DATETIME,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- person
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `person`;

CREATE TABLE `person`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(150) NOT NULL,
    `desc` VARCHAR(255) NOT NULL,
    `created_at` DATETIME,
    `updated_at` DATETIME,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- todo_notiz
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `todo_notiz`;

CREATE TABLE `todo_notiz`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `notiz_id` INTEGER NOT NULL,
    `status` TINYINT NOT NULL,
    `prior` INTEGER NOT NULL,
    `created_at` DATETIME,
    `updated_at` DATETIME,
    PRIMARY KEY (`id`),
    INDEX `todo_notiz_fi_23e2e9` (`notiz_id`),
    CONSTRAINT `todo_notiz_fk_23e2e9`
        FOREIGN KEY (`notiz_id`)
        REFERENCES `notiz` (`id`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- rezept
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `rezept`;

CREATE TABLE `rezept`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `notiz_id` INTEGER NOT NULL,
    `created_at` DATETIME,
    `updated_at` DATETIME,
    PRIMARY KEY (`id`),
    INDEX `rezept_fi_23e2e9` (`notiz_id`),
    CONSTRAINT `rezept_fk_23e2e9`
        FOREIGN KEY (`notiz_id`)
        REFERENCES `notiz` (`id`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- person_projekt
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `person_projekt`;

CREATE TABLE `person_projekt`
(
    `person_id` INTEGER NOT NULL,
    `projekt_id` INTEGER NOT NULL,
    `created_at` DATETIME,
    `updated_at` DATETIME,
    PRIMARY KEY (`person_id`,`projekt_id`),
    INDEX `person_projekt_fi_c4457c` (`projekt_id`),
    CONSTRAINT `person_projekt_fk_ee5b00`
        FOREIGN KEY (`person_id`)
        REFERENCES `person` (`id`),
    CONSTRAINT `person_projekt_fk_c4457c`
        FOREIGN KEY (`projekt_id`)
        REFERENCES `projekt` (`id`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- rezept_notiz
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `rezept_notiz`;

CREATE TABLE `rezept_notiz`
(
    `rezept_id` INTEGER NOT NULL,
    `notiz_id` INTEGER NOT NULL,
    `created_at` DATETIME,
    `updated_at` DATETIME,
    PRIMARY KEY (`rezept_id`,`notiz_id`),
    INDEX `rezept_notiz_fi_23e2e9` (`notiz_id`),
    CONSTRAINT `rezept_notiz_fk_c330c8`
        FOREIGN KEY (`rezept_id`)
        REFERENCES `rezept` (`id`),
    CONSTRAINT `rezept_notiz_fk_23e2e9`
        FOREIGN KEY (`notiz_id`)
        REFERENCES `notiz` (`id`)
) ENGINE=InnoDB;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
