<?php
class MW_Affiliate_Adminhtml_AffiliatewebsiteController extends Mage_Adminhtml_Controller_Action
{   
	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('affiliate/website')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));
		
		return $this;
	}   
 
	public function indexAction() {
		$this->_initAction()
			->renderLayout();
	}
	public function memberAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }
	public function deleteAction() {
		if( $this->getRequest()->getParam('website_id') > 0 ) {
			try {
				$model = Mage::getModel('affiliate/affiliatewebsitemember');
				 
				$model->setId($this->getRequest()->getParam('website_id'))
					->delete();
					 
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully deleted'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('website_id' => $this->getRequest()->getParam('website_id')));
			}
		}
		$this->_redirect('*/*/');
	}
	public function massDeleteAction() {
		//var_dump($this->getRequest()->getParams());exit;
        $affiliatewebsiteIds = $this->getRequest()->getParam('affiliatewebsiteGrid');
        if(!is_array($affiliatewebsiteIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($affiliatewebsiteIds as $affiliatewebsiteId) {
                    $invitation = Mage::getModel('affiliate/affiliatewebsitemember')->load($affiliatewebsiteId);
                    $invitation->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($affiliatewebsiteIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
	
    public function massStatusAction()
    {
        $affiliatewebsiteIds = $this->getRequest()->getParam('affiliatewebsiteGrid');
        if(!is_array($affiliatewebsiteIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($affiliatewebsiteIds as $affiliatewebsiteId) {
                    $invitation = Mage::getSingleton('affiliate/affiliatewebsitemember')
                        ->load($affiliatewebsiteId)
                        ->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($affiliatewebsiteIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
	public function exportCsvAction()
    {
        $fileName   = 'affiliate_website.csv';
        $content    = $this->getLayout()->createBlock('affiliate/adminhtml_affiliatewebsite_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'affiliate_website.xml';
        $content    = $this->getLayout()->createBlock('affiliate/adminhtml_affiliatewebsite_grid')
            ->getXml();

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