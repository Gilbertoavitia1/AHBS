<?php
class MW_Affiliate_Adminhtml_AffiliatewithdrawnpendingController extends Mage_Adminhtml_Controller_Action
{   
	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('affiliate/withdrawn')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));
		
		return $this;
	}   
 
	public function indexAction() {
		$this->_initAction()
			->renderLayout();
	}
	public function withdrawneditAction()
	{
		$withdrawn_ids 	= (array)$this->getRequest()->getParam('affiliate_withdrawn_pending');
        $status     	= (int)$this->getRequest()->getParam('status');
        if(!is_array($withdrawn_ids)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select withdrawn(s)'));
        } else {
            try {
            	Mage::helper('affiliate')->processWithdrawn($status, $withdrawn_ids);
            } catch(Mage_Core_Model_Exception $e) {
            	$this->_getSession()->addError($e->getMessage());
        	}
            
            catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
	}
	
	public function exportCsvAction()
    {
        $fileName   = 'affiliate_withdrawn_pending.csv';
        $content    = $this->getLayout()->createBlock('affiliate/adminhtml_affiliatewithdrawnpending_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'affiliate_withdrawn_pending.xml';
        $content    = $this->getLayout()->createBlock('affiliate/adminhtml_affiliatewithdrawnpending_grid')->getXml();

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