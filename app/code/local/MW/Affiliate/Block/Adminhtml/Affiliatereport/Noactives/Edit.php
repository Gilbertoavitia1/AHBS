<?php

class MW_Affiliate_Block_Adminhtml_Affiliatereport_Noactives_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $id = $this->getRequest()->getParam('id');
        Mage::app()->getResponse()->setRedirect(Mage::helper("adminhtml")->getUrl("adminhtml/customer/edit/", array("id"=>$id)));
    }
}