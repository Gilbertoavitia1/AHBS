<?php

$installer = $this;

$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('storelocator')};
CREATE TABLE {$this->getTable('storelocator')} (
  `storelocator_id` int(11) unsigned NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `address` varchar(255) NOT NULL default '',
  `city` varchar(255) NOT NULL default '',
  `state` varchar(255) NOT NULL default '',
  `state_id` int(11) NULL ,
  `country` varchar(255) NOT NULL default '',
  `zipcode` varchar(255) NOT NULL default '',
  `fax` varchar(25) NULL default '',
  `email` varchar(255) NOT NULL default '',
  `phone` varchar(255) NOT NULL default '',
  `web_url` varchar(255) NOT NULL default '',
  `description` text NOT NULL default '',
  `storeimage` varchar(255) NOT NULL default '',
  `latitude` varchar(255) NOT NULL default '', 
  `longitude` varchar(255) NOT NULL default '',
  `zoom_level` int(11) NULL,
  `hours1` varchar(255) NOT NULL default '',
  `hours2` varchar(255) NOT NULL default '',
  `hours3` varchar(255) NOT NULL default '',
   category varchar(255)  NOT NULL default '',
  `status` smallint(6) NOT NULL default '0',
  `sortorder` int(10) NULL default 0,
  `created_time` datetime NULL,
  `update_time` datetime NULL,
  PRIMARY KEY (`storelocator_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ");

$installer->endSetup(); 