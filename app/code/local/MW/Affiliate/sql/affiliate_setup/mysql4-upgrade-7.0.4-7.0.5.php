<?php
$installer = $this;
$installer->startSetup();

$installer->run("
ALTER TABLE `mw_affiliate_binary_residual` ADD COLUMN `cp` int(11) NULL;
");

$installer->endSetup(); 