<?php
$installer = $this;
$installer->startSetup();

$installer->run("
DROP TABLE IF EXISTS `firstdata_recurring`;
CREATE TABLE `firstdata_recurring` (
  `recurring_id` int(11) unsigned NOT NULL auto_increment,
  `order_id` int(11) NOT NULL default '0',
  `customer_id` int(11) NOT NULL default '0',
  `address_id` int(11) NOT NULL default '0',
  `type` varchar(255) NULL,
  `status` varchar(255) NULL default 'CREATED',
  `total` decimal(12,4) NULL default '0.00',
  `created_date` datetime NULL,
  `updated_date` datetime NULL,
  PRIMARY KEY (`recurring_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `firstdata_recurring_detail`;
CREATE TABLE `firstdata_recurring_detail` (
  `detail_id` int(11) unsigned NOT NULL auto_increment,
  `product_id` int(11) NULL default '0',
  `quantity` int(11) NULL default '0',
  PRIMARY KEY (`detail_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `firstdata_recurring_history`;
CREATE TABLE `firstdata_recurring_history` (
  `history_id` int(11) unsigned NOT NULL auto_increment,
  `recurring_id` int(11) NOT NULL default '0',
  `date_of_run` datetime NULL,
  PRIMARY KEY (`history_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

$installer->endSetup(); 