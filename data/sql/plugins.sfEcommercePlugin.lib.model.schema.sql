
# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

#-----------------------------------------------------------------------------
#-- sale
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `sale`;


CREATE TABLE `sale`
(
	`id` INTEGER  NOT NULL,
	`first_name` VARCHAR(50),
	`last_name` VARCHAR(50),
	`address1` VARCHAR(50),
	`address2` VARCHAR(50),
	`city` VARCHAR(50),
	`country` VARCHAR(2),
	`province` VARCHAR(30),
	`postal_code` VARCHAR(15),
	`email` VARCHAR(50),
	`phone` VARCHAR(30),
	`processing_status` VARCHAR(50),
	`total_amount` VARCHAR(20),
	`paid_at` DATETIME,
	`transaction_id` VARCHAR(50),
	`transaction_fee` VARCHAR(20),
	`transaction_date` VARCHAR(50),
	`created_at` DATETIME  NOT NULL,
	`updated_at` DATETIME  NOT NULL,
	`last_processed_at` DATETIME,
	PRIMARY KEY (`id`),
	CONSTRAINT `sale_FK_1`
		FOREIGN KEY (`id`)
		REFERENCES `object` (`id`)
		ON DELETE CASCADE
)Engine=InnoDB;

#-----------------------------------------------------------------------------
#-- sale_resource
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `sale_resource`;


CREATE TABLE `sale_resource`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`sale_id` INTEGER,
	`resource_id` INTEGER,
	`repository_id` INTEGER,
	`price` VARCHAR(20),
	`tax1_name` VARCHAR(10),
	`tax1_rate` VARCHAR(10),
	`tax2_name` VARCHAR(10),
	`tax2_rate` VARCHAR(10),
	`processing_status` VARCHAR(50),
	`refund_transaction_id` VARCHAR(50),
	`serial_number` INTEGER default 0 NOT NULL,
	PRIMARY KEY (`id`),
	INDEX `sale_resource_FI_1` (`sale_id`),
	CONSTRAINT `sale_resource_FK_1`
		FOREIGN KEY (`sale_id`)
		REFERENCES `sale` (`id`)
		ON DELETE CASCADE,
	INDEX `sale_resource_FI_2` (`resource_id`),
	CONSTRAINT `sale_resource_FK_2`
		FOREIGN KEY (`resource_id`)
		REFERENCES `information_object` (`id`),
	INDEX `sale_resource_FI_3` (`repository_id`),
	CONSTRAINT `sale_resource_FK_3`
		FOREIGN KEY (`repository_id`)
		REFERENCES `repository` (`id`)
)Engine=InnoDB;

#-----------------------------------------------------------------------------
#-- user_ecommerce_settings
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `user_ecommerce_settings`;


CREATE TABLE `user_ecommerce_settings`
(
	`user_id` INTEGER,
	`repository_id` INTEGER,
	`vacation_enabled` TINYINT default 0,
	`vacation_message` TEXT,
	`ecommerce_master` TINYINT default 0,
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`serial_number` INTEGER default 0 NOT NULL,
	PRIMARY KEY (`id`),
	INDEX `user_ecommerce_settings_FI_1` (`user_id`),
	CONSTRAINT `user_ecommerce_settings_FK_1`
		FOREIGN KEY (`user_id`)
		REFERENCES `user` (`id`)
		ON DELETE CASCADE,
	INDEX `user_ecommerce_settings_FI_2` (`repository_id`),
	CONSTRAINT `user_ecommerce_settings_FK_2`
		FOREIGN KEY (`repository_id`)
		REFERENCES `repository` (`id`)
)Engine=InnoDB;

#-----------------------------------------------------------------------------
#-- ecommerce_transaction
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `ecommerce_transaction`;


CREATE TABLE `ecommerce_transaction`
(
	`repository_id` INTEGER,
	`sale_id` INTEGER,
	`amount` DECIMAL(15,2),
	`type` VARCHAR(30),
	`created_at` DATETIME,
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`serial_number` INTEGER default 0 NOT NULL,
	PRIMARY KEY (`id`),
	INDEX `ecommerce_transaction_FI_1` (`repository_id`),
	CONSTRAINT `ecommerce_transaction_FK_1`
		FOREIGN KEY (`repository_id`)
		REFERENCES `repository` (`id`),
	INDEX `ecommerce_transaction_FI_2` (`sale_id`),
	CONSTRAINT `ecommerce_transaction_FK_2`
		FOREIGN KEY (`sale_id`)
		REFERENCES `sale` (`id`)
		ON DELETE CASCADE
)Engine=InnoDB;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
