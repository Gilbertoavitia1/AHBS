<?php
$installer = $this;
$installer->startSetup();

$installer->run("   
    ALTER TABLE firstdata_recurring_history
    ADD `order_created` VARCHAR(50);

");

$installer->endSetup(); 