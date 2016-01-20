<?php
$installer = $this;
$installer->startSetup();

$installer->run("
ALTER TABLE `sales_flat_order` ADD COLUMN `affiliate_sale_type` int(11) NULL;
ALTER TABLE `sales_flat_order_grid` ADD COLUMN `affiliate_sale_type` int(11) NULL;
");


$installer->endSetup(); 