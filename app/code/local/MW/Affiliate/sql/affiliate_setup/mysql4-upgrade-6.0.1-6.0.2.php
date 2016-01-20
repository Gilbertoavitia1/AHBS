<?php
$installer = $this;
$resource = Mage::getSingleton('core/resource');
$installer->startSetup();

$installer->run("


ALTER TABLE {$resource->getTableName('affiliate/affiliatecustomers')}

ADD COLUMN `autoapprove` int(2) default 7 AFTER `customer_url`;
		
");

$installer->endSetup();
