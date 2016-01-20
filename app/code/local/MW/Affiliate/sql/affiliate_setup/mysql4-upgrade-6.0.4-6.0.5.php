<?php
$installer = $this;
$installer->startSetup();

$installer->run("
DROP TABLE IF EXISTS `mw_affiliate_bonus`;
CREATE TABLE `mw_affiliate_bonus` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `start_day` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `parent_id` int(11) NOT NULL,
  `child_id` int(11) NOT NULL,
  `total` double(15,2) NOT NULL,
  `reference` varchar(255) NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `website_id` int(11) NOT NULL,
  `bonus_id` int(11) NOT NULL,
  `package` varchar(1000) NULL,
  `description` varchar(500) NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

$installer->endSetup(); 