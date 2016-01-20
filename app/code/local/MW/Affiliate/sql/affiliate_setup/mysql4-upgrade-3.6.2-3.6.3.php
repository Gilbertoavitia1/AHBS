<?php
$installer = $this;
$resource = Mage::getSingleton('core/resource');
$installer->startSetup();

$installer->run("

ALTER TABLE {$resource->getTableName('affiliate/affiliateinvitation')}
ADD COLUMN `commission` double(15,2) NOT NULL default '0.00';

ALTER TABLE {$resource->getTableName('affiliate/affiliateinvitation')}
ADD COLUMN `count_subscribe` int(11) NOT NULL default '0';

DROP TABLE IF EXISTS {$resource->getTableName('affiliate/affiliatewebsitemember')};
CREATE TABLE {$resource->getTableName('affiliate/affiliatewebsitemember')} (
	`affiliate_website_id` int(11) unsigned not null auto_increment,
	`customer_id` int(11) unsigned not null,
	`domain_name` varchar(255) NOT NULL DEFAULT '',
	`verified_key` varchar(255) NOT NULL DEFAULT '',
	`is_verified` int(2) NOT NULL DEFAULT 0,
	`status` int(2) NOT NULL DEFAULT 1,
	`created_time` timestamp DEFAULT CURRENT_TIMESTAMP,

	PRIMARY KEY(`affiliate_website_id`),
	UNIQUE KEY (`domain_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		
");

$installer->endSetup();
