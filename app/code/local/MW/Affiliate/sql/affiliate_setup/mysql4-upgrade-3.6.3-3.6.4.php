<?php
$installer = $this;
$resource = Mage::getSingleton('core/resource');
$installer->startSetup();

$installer->run("

ALTER TABLE {$resource->getTableName('affiliate/affiliatecustomers')}
ADD COLUMN `link_click_id_pivot` int(11) default 0 AFTER `invitation_type`;
		
");

$installer->endSetup();
