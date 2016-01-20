<?php
class MW_Affiliate_Adminhtml_AffiliatebinaryController extends Mage_Adminhtml_Controller_Action

{  protected function _initAction() {
        $this->loadLayout()->_setActiveMenu('affiliate/binary');
        return $this;
    }
 
    public function indexAction() {       
        $this->_redirect('*/*/residual');
    }
    
    public function residualAction(){ 
        $this->loadLayout();
        $this->renderLayout();
    }
    
    public function igualableAction(){
        $this->loadLayout();
        $this->renderLayout();
    }
    
    public function constructorAction(){
        $this->loadLayout();
        $this->renderLayout();
    }
    
    public function preferredAction(){
        $this->loadLayout();
        $this->renderLayout();
    }
    
    public function faststartAction(){
        $this->loadLayout();
        $this->renderLayout();
    }
    
    public function expiredAction(){
        $this->loadLayout();
        $this->renderLayout();
    }
    
    public function saveexpiredAction(){
        $params = $this->getRequest()->getParams();        
        $id = $params['id'];
        $order = $params['recompra'];
                
        $historial = Mage::getResourceModel('sales/order_status_history_collection')
            ->addAttributeToFilter('main_table.status', array('eq'=>'complete'))
            ->addAttributeToFilter('main_table.entity_name', array('in'=>array('invoice','shipment')))
            ->addAttributeToFilter('main_table.parent_id', array('in'=>array($order)))
            ->getFirstItem();
        
        $expired = date("Y-m-d 23:59:59",strtotime($historial->getData('created_at'). ' +1 Month'));
        $historic = json_encode(array(array('order' => $order)));
        
        
        $write = Mage::getSingleton('core/resource')->getConnection('core_write');
        $sql = "UPDATE mw_affiliate_expired SET expired_package = '{$expired}', historic='{$historic}' WHERE customer_id = {$id}";         
        $write->query($sql);   
        
        Mage::app()->getResponse()->setRedirect(Mage::helper("adminhtml")->getUrl("affiliate/adminhtml_affiliatebinary/editexpired", array("id"=>$id)));                    
    }
    
    public function editexpiredAction(){ 
        $params = $this->getRequest()->getParams();
        
        $id = $params['id'];
        
        $collection = Mage::getModel('affiliate/affiliateexpired')->getCollection()
            ->addFieldToFilter('customer_id',$id)
                ->getFirstItem();
        
        Mage::register('collection', $collection->getData());
        Mage::register('id', $id);
        $this->loadLayout();
        $this->renderLayout();
    }     
    
    public function residualeditAction(){  
        $params = $this->getRequest()->getParams();
        $id = $params['id'];
        
        $collection = Mage::getModel('affiliate/affiliatebinaryresidual')->load($id);
        Mage::register('binary', $collection->getData());
        Mage::register('id', $id);
        $this->loadLayout();
        $this->renderLayout();
    }     
    
    public function editresidualAction(){  
        $params = $this->getRequest()->getParams();
        $id = $params['id'];
        
        $collection = Mage::getModel('affiliate/affiliatebinaryresidual')->load($id);
        Mage::register('binary', $collection->getData());
        Mage::register('id', $id);
        $this->loadLayout();
        $this->renderLayout();
    }     
    
    public function editigualableAction(){  
        $params = $this->getRequest()->getParams();
        $id = $params['id'];
        
        $collection = Mage::getModel('affiliate/affiliatebinarycheck')->load($id);
        Mage::register('check', $collection->getData());
        Mage::register('id', $id);
        $this->loadLayout();
        $this->renderLayout();
    } 
    
    public function editpreferredAction(){  
        $params = $this->getRequest()->getParams();
        $id = $params['id'];
        $start = date("Y-m-d 08:00:00", $params['start']);
        
        $collection = Mage::getModel('affiliate/affiliatebinarypreferred')->getCollection()
                ->addFieldToFilter('customer_id', $id)->addFieldToFilter('start_date', $start);
        
        Mage::register('collection', $collection->getData());
        Mage::register('id', $id);
        Mage::register('start', $start);
    } 
    
    
    public function editfaststartAction(){
        $params = $this->getRequest()->getParams();
        $id = $params['id'];
        
        
        Mage::register('increment', $id);
        $this->loadLayout();
        $this->renderLayout();
    }
    
    public function exportbinaryresidualCsvAction()
    {
        $fileName   = 'affiliate_binary.csv';
        $content    = $this->getLayout()->createBlock('affiliate/adminhtml_affiliatebinaryresidual_grid')->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportbinaryresidualXmlAction()
    {
        $fileName   = 'affiliate_binary.xml';
        $content    = $this->getLayout()->createBlock('affiliate/adminhtml_affiliatebinaryresidual_grid')->getXml();

        $this->_sendUploadResponse($fileName, $content);
    }
    
    public function exportbinarycheckCsvAction()
    {
        $fileName   = 'affiliate_check.csv';
        $content    = $this->getLayout()->createBlock('affiliate/adminhtml_affiliatebinarycheck_grid')->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }    
    
    public function exportbinarycheckXmlAction()
    {
        $fileName   = 'affiliate_check.xml';
        $content    = $this->getLayout()->createBlock('affiliate/adminhtml_affiliatebinarycheck_grid')->getXml();

        $this->_sendUploadResponse($fileName, $content);
    }
    
    public function exportbinaryconstructorCsvAction()
    {
        $fileName   = 'affiliate_check.csv';
        $content    = $this->getLayout()->createBlock('affiliate/adminhtml_affiliatebinaryconstructor_grid')->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }    
    
    public function exportbinaryconstructorXmlAction()
    {
        $fileName   = 'affiliate_constructor.xml';
        $content    = $this->getLayout()->createBlock('affiliate/adminhtml_affiliatebinaryconstructor_grid')->getXml();

        $this->_sendUploadResponse($fileName, $content);
    }
    
    public function exportbinarypreferredCsvAction()
    {
        $fileName   = 'affiliate_preferred.csv';
        $content    = $this->getLayout()->createBlock('affiliate/adminhtml_affiliatebinarypreferred_grid')->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }    
    
    public function exportbinarypreferredXmlAction()
    {
        $fileName   = 'affiliate_preferred.xml';
        $content    = $this->getLayout()->createBlock('affiliate/adminhtml_affiliatebinarypreferred_grid')->getXml();

        $this->_sendUploadResponse($fileName, $content);
    }

    protected function _sendUploadResponse($fileName, $content, $contentType='application/octet-stream')
    {
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK','');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'attachment; filename='.$fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        die;
    }
}
?>