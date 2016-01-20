<?php
class MW_Affiliate_Block_Adminhtml_Renderer_ConstructorReport extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
    	if(empty($row['customer_id'])) {
    		return '';
    	}
        
        $today = Mage::helper('affiliate_binary')->getRangeDate();
        $collection = Mage::getModel('affiliate/affiliatebinarycheck')->getCollection()
            ->addFieldToFilter('chain', $row['id'])
            ->addFieldToFilter('start_date', $today[2])
            ->getFirstItem()
            ->getData();
        
        if (sizeof($collection) > 0){
            $url = Mage::helper("adminhtml")->getUrl("affiliate/adminhtml_affiliatebinary/editigualable", array("id"=>$collection['id']));
            return "<a href='{$url}' target='_blank'>Ver Reporte</a>";
        }else{
            return "No se ha actualizado el reporte";
        }
    }

}