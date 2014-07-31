
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

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
