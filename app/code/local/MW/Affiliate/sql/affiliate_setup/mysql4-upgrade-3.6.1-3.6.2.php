<?php
$installer = $this;
$resource = Mage::getSingleton('core/resource');
$installer->startSetup();
$conn = $installer->getConnection();

if(!$conn->showTableStatus($resource->getTableName('affiliate/affiliateprogram'))){
	$installer->run("
			CREATE TABLE {$resource->getTableName('affiliate/affiliateprogram')} (
			`program_id` int(11) unsigned NOT NULL auto_increment,
			`program_name` varchar(255) NOT NULL default '',
			`description` text NOT NULL DEFAULT '',
			`conditions_serialized` mediumtext NOT NULL DEFAULT '',
			`actions_serialized` mediumtext NOT NULL DEFAULT '',	
			`start_date` varchar(255) NOT NULL default '',
			`end_date` varchar(255) NOT NULL default '',
			`commission` varchar(255) NOT NULL default '',
			`discount` varchar(255) NOT NULL default '',
			`total_members` int(11) NOT NULL default '0',
			`total_commission` double(15,2) NOT NULL default '0.00',
			`program_position` int(11) NOT NULL default '0',
			`store_view` varchar(255) NOT NULL DEFAULT '0',
			`status` INT(2) NOT NULL,
			PRIMARY KEY (`program_id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;"
	);
}

if(!$conn->showTableStatus($resource->getTableName('affiliate/affiliategroupprogram'))){
	$installer->run("
		CREATE TABLE {$resource->getTableName('affiliate/affiliategroupprogram')} (
		`id` int(11) unsigned NOT NULL auto_increment,
		`group_id` int(11) NOT NULL DEFAULT '0',
		`program_id` int(11) NOT NULL DEFAULT '0',
		PRIMARY KEY (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;"
	);
}

$installer->endSetup();
