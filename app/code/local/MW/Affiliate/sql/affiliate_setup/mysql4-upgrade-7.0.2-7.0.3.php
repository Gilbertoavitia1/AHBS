<?php
$installer = $this;
$installer->startSetup();

$installer->run("
DROP TABLE IF EXISTS `mw_affiliate_binary_residual`;
CREATE TABLE `mw_affiliate_binary_residual` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `customer_id` int(11) NOT NULL,
  `vp` double(15,2) NULL,
  `vg` double(15,2) NULL,
  `left` double(15,2) NULL,
  `right` double(15,2) NULL,
  `paid` double(15,2) NULL,
  `hold` double(15,2) NULL,
  `hold_side` int(1) NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

$installer->endSetup(); 