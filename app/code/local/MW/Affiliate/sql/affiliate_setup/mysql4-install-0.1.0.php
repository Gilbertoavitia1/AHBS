<?php

$installer = $this;
$collection = Mage::getModel('affiliate/affiliatebanner')->getCollection();
$installer->startSetup();

$installer->run("

DROP TABLE IF EXISTS {$collection->getTable('affiliatebanner')};
CREATE TABLE {$collection->getTable('affiliatebanner')} (
  `banner_id` int(11) unsigned NOT NULL auto_increment,
  `title_banner` varchar(255) NOT NULL default '',
  `link_banner` varchar(255) NOT NULL default '',
  `width` int(11) NOT NULL default '0',
  `height` int(11) NOT NULL default '0',
  `image_name` varchar(255) NOT NULL default '',
  `status` INT(2) NOT NULL,
  PRIMARY KEY (`banner_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS {$collection->getTable('affiliatecategoryprogram')};
CREATE TABLE {$collection->getTable('affiliatecategoryprogram')} (
  `id` int(11) unsigned NOT NULL auto_increment,
  `category_id` int(11) NOT NULL default '0',
  `program_id` int(11) NOT NULL default '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS {$collection->getTable('affiliatecustomers')};
CREATE TABLE {$collection->getTable('affiliatecustomers')} (
  `customer_id` int(11) NOT NULL default '0',
  `active` int(2) unsigned NOT NULL default '0',
  `payment_gateway` varchar(255) NOT NULL default '',
  `payment_email` varchar(255) NOT NULL default '',
  `auto_withdrawn` int(2) unsigned NOT NULL default '0',
  `withdrawn_level` double(15,2) NOT NULL default '0.00',
  `reserve_level` double(15,2) NOT NULL default '0.00',
  `total_commission` double(15,2) NOT NULL default '0.00',
  `total_paid` double(15,2) NOT NULL default '0.00',
  `customer_invited` int(11) NOT NULL default '0',
  `status` INT(2) NOT NULL,
  PRIMARY KEY (`customer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS {$collection->getTable('affiliatecustomerprogram')};
CREATE TABLE {$collection->getTable('affiliatecustomerprogram')} (
  `id` int(11) unsigned NOT NULL auto_increment,
  `customer_id` int(11) NOT NULL default '0',
  `program_id` int(11) NOT NULL default '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS {$collection->getTable('affiliatehistory')};
CREATE TABLE {$collection->getTable('affiliatehistory')} (
  `history_id` int(11) unsigned NOT NULL auto_increment,
  `customer_id` int(11) NOT NULL default '0',
  `product_id` int(11) NOT NULL default '0',
  `program_id` int(11) NOT NULL default '0',
  `order_id` varchar(255) NOT NULL default '',
  `total_amount` double(15,2) NOT NULL default '0.00',
  `history_commission` double(15,2) NOT NULL default '0.00',
  `history_discount` double(15,2) NOT NULL default '0.00',
  `transaction_time` datetime NULL,
  `customer_invited` int(11) NOT NULL default '0',
  `status` INT(2) NOT NULL,
  PRIMARY KEY (`history_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS {$collection->getTable('affiliateinvitation')};
CREATE TABLE {$collection->getTable('affiliateinvitation')} (
  `invitation_id` int(11) unsigned NOT NULL auto_increment,
  `customer_id` int(11) NOT NULL default '0',
  `email` varchar(255) NOT NULL default '',
  `ip` varchar(255) NOT NULL default '',
  `invitation_time` datetime NULL,
  `status` INT(2) NOT NULL,
  PRIMARY KEY (`invitation_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS {$collection->getTable('affiliateproductprogram')};
CREATE TABLE {$collection->getTable('affiliateproductprogram')} (
  `id` int(11) unsigned NOT NULL auto_increment,
  `product_id` int(11) NOT NULL default '0',
  `program_id` int(11) NOT NULL default '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS {$collection->getTable('affiliateprogram')};
CREATE TABLE {$collection->getTable('affiliateprogram')} (
  `program_id` int(11) unsigned NOT NULL auto_increment,
  `program_name` varchar(255) NOT NULL default '',
  `start_date` varchar(255) NOT NULL default '',
  `end_date` varchar(255) NOT NULL default '',
  `commission` varchar(255) NOT NULL default '',
  `discount` varchar(255) NOT NULL default '',
  `total_members` int(11) NOT NULL default '0',
  `total_commission` double(15,2) NOT NULL default '0.00',
  `program_position` int(11) NOT NULL default '0',
  `status` INT(2) NOT NULL,
  PRIMARY KEY (`program_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS {$collection->getTable('affiliatetransaction')};
CREATE TABLE {$collection->getTable('affiliatetransaction')} (
  `history_id` int(11) unsigned NOT NULL auto_increment,
  `order_id` varchar(255) NOT NULL default '',
  `total_commission` double(15,2) NOT NULL default '0.00',
  `total_discount` double(15,2) NOT NULL default '0.00',
  `transaction_time` datetime NULL,
  `customer_invited` int(11) NOT NULL default '0',
  `status` INT(2) NOT NULL,
  PRIMARY KEY (`history_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS {$collection->getTable('affiliatewithdrawn')};
CREATE TABLE {$collection->getTable('affiliatewithdrawn')} (
  `withdrawn_id` int(11) unsigned NOT NULL auto_increment,
  `customer_id` int(11) NOT NULL default '0',
  `withdrawn_amount` double(15,2) NOT NULL default '0.00',
  `fee` double(15,2) NOT NULL default '0.00',
  `amount_receive` double(15,2) NOT NULL default '0.00',
  `withdrawn_time` datetime NULL,
  `status` INT(2) NOT NULL,
  PRIMARY KEY (`withdrawn_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

 ");
/*
$customers = Mage::getModel('customer/customer')->getCollection();

$sql ="";
foreach($customers as $customer)
{
	$sql .="INSERT INTO {$collection->getTable('affiliatecustomers')} 
				VALUES(".$customer->getId().",3,'','',1,0,0,0,0,0,1);";
}
$installer->run($sql);*/

$installer->endSetup(); 