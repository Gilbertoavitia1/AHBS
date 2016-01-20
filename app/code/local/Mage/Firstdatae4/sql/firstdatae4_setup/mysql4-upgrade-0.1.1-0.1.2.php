<?php
$installer = $this;
$installer->startSetup();

$installer->run("
     ALTER TABLE firstdata_recurring_detail 
    ADD `recurring_id` int(11) NOT NULL AFTER `detail_id`;

");

$installer->endSetup(); 