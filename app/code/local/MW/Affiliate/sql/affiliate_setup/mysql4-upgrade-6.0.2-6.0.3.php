<?php
$installer = $this;
$installer->startSetup();

$installer->run("
DROP TABLE IF EXISTS `mw_affiliate_code`;
CREATE TABLE `mw_affiliate_code` (
  `code_id` int(11) unsigned NOT NULL auto_increment,
  `store_id` int(11) NOT NULL default '0',
  `referral_code` int(11) NOT NULL default '0',
  PRIMARY KEY (`code_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

$installer->endSetup(); 