<?php

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */
/* $installer Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */

$installer->startSetup();

//START Add order attribute by Branko Ajzele
$sql = "SELECT entity_type_id FROM ".$this->getTable('eav_entity_type')." WHERE entity_type_code='order'";
$row = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchRow($sql);
   
$attribute  = array(
	'type'			=> 'text',
	'label'			=> 'Heared4us',
	'visible'		=> false,
	'required'		=> false,
	'user_defined'	=> false,
	'searchable'	=> false,
	'filterable'	=> false,
	'comparable'	=> false,
);

$installer->addAttribute($row['entity_type_id'], 'heared4us', $attribute);
//END Add customer attribute Branko Ajzele

$installer->endSetup();