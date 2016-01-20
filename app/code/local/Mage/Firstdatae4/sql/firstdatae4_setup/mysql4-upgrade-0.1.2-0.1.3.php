<?php
$installer = $this;
$installer->startSetup();

$installer->run("   
    ALTER TABLE firstdata_recurring
    ADD `next_date` datetime NULL;

");

$installer->endSetup(); 