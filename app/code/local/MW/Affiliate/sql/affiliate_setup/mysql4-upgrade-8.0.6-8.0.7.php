<?php
$installer = $this;
$installer->startSetup();

$installer->run("
DROP TABLE IF EXISTS `mw_affiliate_binary_preferred`;
CREATE TABLE `mw_affiliate_binary_preferred` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `preferred_id` int(11) NOT NULL,
  `profit` double(15,2) NULL,
  `customer_id` int(11) NULL,
  `package` text NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

$installer->endSetup(); 