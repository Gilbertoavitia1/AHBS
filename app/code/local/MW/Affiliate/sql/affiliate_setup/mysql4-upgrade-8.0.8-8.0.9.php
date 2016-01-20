<?php
$installer = $this;
$installer->startSetup();

$installer->run("
DROP TABLE IF EXISTS `mw_affiliate_binary_faststart`;
CREATE TABLE `mw_affiliate_binary_faststart` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `customer_id` int(11) unsigned NOT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `increment_id` VARCHAR(50) NOT NULL,
  `order_id` int(11) NOT NULL,
  `qualify` int(11) NOT NULL,
  `total` double NULL,
  `points` double NULL,
  `currency` VARCHAR(20) NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `mw_affiliate_binary_faststart_detail`;
CREATE TABLE `mw_affiliate_binary_faststart_detail` (
  `id_detail` int(11) unsigned NOT NULL auto_increment,
  `id_main` int(11) NOT NULL,
  `customer_id` int(11) NULL,
  `level` int(11) NULL,
  `paid` double NULL,
  `paid_date` datetime NULL,
  `qualify` int(11) NOT NULL,
  PRIMARY KEY (`id_detail`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

$installer->endSetup(); 