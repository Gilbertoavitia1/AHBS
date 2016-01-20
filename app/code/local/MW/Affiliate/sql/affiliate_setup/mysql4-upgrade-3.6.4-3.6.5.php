<?php
$installer = $this;
$resource = Mage::getSingleton('core/resource');
$installer->startSetup();

$installer->run("


ALTER TABLE {$resource->getTableName('affiliate/affiliatetransaction')}

ADD COLUMN `commission_type` int(2) default 7 AFTER `transaction_time`,
ADD COLUMN `customer_id` int(11) default 0 AFTER `order_id`
;
		
");

$installer->endSetup();
