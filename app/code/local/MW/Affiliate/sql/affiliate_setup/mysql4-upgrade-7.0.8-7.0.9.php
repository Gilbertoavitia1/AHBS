<?php
$installer = $this;
$installer->startSetup();

$installer->run("
DROP TABLE IF EXISTS `mw_affiliate_expired`;
CREATE TABLE `mw_affiliate_expired` (
  `customer_id` int(11) unsigned NOT NULL,
  `expired_package` datetime NULL,
  `expired_rank` datetime NULL,
  `historic` text NULL,
  PRIMARY KEY (`customer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

$installer->endSetup(); 