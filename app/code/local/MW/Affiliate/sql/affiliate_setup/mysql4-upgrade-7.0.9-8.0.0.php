<?php
$installer = $this;
$installer->startSetup();

$installer->run("
ALTER TABLE `mw_affiliate_binary_residual` ADD COLUMN `package_cp` VARCHAR(5000) NULL;
");

$installer->endSetup(); 