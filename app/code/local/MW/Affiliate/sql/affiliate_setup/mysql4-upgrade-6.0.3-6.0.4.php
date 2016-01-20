<?php
$installer = $this;
$installer->startSetup();

$installer->run("
DROP TABLE IF EXISTS `mw_affiliate_volume`;
CREATE TABLE `mw_affiliate_volume` (
  `volume_id` int(11) unsigned NOT NULL auto_increment,
  `customer_id` int(11) NOT NULL,
  `vg` int(11) NOT NULL default '0',
  `vp` int(11) NOT NULL default '0',
  `month_date` varchar(255) NOT NULL,
  PRIMARY KEY (`volume_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

$installer->endSetup(); 