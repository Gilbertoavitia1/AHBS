<?php
class MW_Affiliate_Adminhtml_AffiliatehistoryController extends Mage_Adminhtml_Controller_Action
{   
	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('affiliate/transaction')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));
		
		return $this;
	}   
 
	public function indexAction() {
		$this->_initAction()
			->renderLayout();
	}
	public function importAction()
    {
    	$this->loadLayout()->_setActiveMenu('affiliate/transaction');
    	$this->_addContent($this->getLayout()->createBlock('affiliate/adminhtml_affiliatehistory_edit'))
				->_addLeft($this->getLayout()->createBlock('affiliate/adminhtml_affiliatehistory_edit_tabs'));
		$this->renderLayout();
    }
	public function saveAction()
    {
	    if($_FILES['filename']['name'] != '') {
			try {
				/* Starting upload */	
				$uploader = new Varien_File_Uploader('filename');
				
				// Any extention would work
		        $uploader->setAllowedExtensions(array('csv'));
				$uploader->setAllowRenameFiles(false);
				
				// Set the file upload mode 
				// false -> get the file directly in the specified folder
				// true -> get the file in the product like folders 
				//	(file.jpg will go in something like /media/f/i/file.jpg)
				$uploader->setFilesDispersion(false);
						
				// We set media as the upload dir
				$path = Mage::getBaseDir('media').DS;
				$uploader->save($path, $_FILES['filename']['name'] );
				$filename = $path.$uploader->getUploadedFileName();
				
				$fp = @fopen($filename,'r');
				$line = 1;
				if($fp){
					$status = (int)$this->getRequest()->getParam('status_update');
					$count_success = 0;
            		$count_error = 0;
            		$tring_error = "";
					while (!feof($fp)) {
						
						$tmp = fgets($fp); //Reading a file line by line
						if($line >1){
							$content = str_replace('"','',$tmp);
							//var_dump($content);die();
							
							$orderInfo = explode(',',$content);
							//var_dump($orderInfo);die();
							if(sizeof($orderInfo) == 1){
								$order_id =  trim($orderInfo[0]);
								//echo $order_id;die();
								$transaction_collections = Mage::getModel('affiliate/affiliatetransaction')->getCollection()
																					->addFieldtoFilter('customer_invited',0)
                    																->addFieldToFilter('order_id',$order_id);
                    			if(sizeof($transaction_collections)> 0)	{
                    				$status_order = 0;
	                    			foreach ($transaction_collections as $transaction_collection) 
					              	{
					              		$status_order = (int)$transaction_collection ->getStatus();
					              		break;
					              	}
						            if($status == MW_Affiliate_Model_Status::CANCELED){
	            			
				            			if($status_order == MW_Affiliate_Model_Status::PENDING){
				            				$count_success = $count_success + 1;
				            				MW_Affiliate_Model_Observer::saveOrderCanceled($order_id);
				            			}else{
				            				$count_error = $count_error + 1;
				            				$tring_error = $tring_error.", ".$order_id;
				            			};
				            			
				            		}else if($status == MW_Affiliate_Model_Status::COMPLETE){
				            			
				            			if($status_order == MW_Affiliate_Model_Status::PENDING){
				            				$count_success = $count_success + 1;
				            				MW_Affiliate_Model_Observer::saveOrderComplete($order_id);
				            			}else{
				            				$count_error = $count_error + 1;
				            				$tring_error = $tring_error.", ".$order_id;
				            			};
				            			
				            		}else if($status == MW_Affiliate_Model_Status::CLOSED){
				            			
				            			if($status_order == MW_Affiliate_Model_Status::COMPLETE){
				            				$count_success = $count_success + 1;
				            				MW_Affiliate_Model_Observer::saveOrderClosed($order_id);
				            			}else{
				            				$count_error = $count_error + 1;
				            				$tring_error = $tring_error.", ".$order_id;
				            			};
				            		};                    			
                    			}else{
                    				if($order_id){
                    					$count_error = $count_error + 1;
                    					$tring_error = $tring_error.", ".$order_id;
                    				}
                    			};								
				             	
							}
							
						}
						$line  ++;
					}
					
					fclose($fp);
					@unlink($filename);
					$status_label = MW_Affiliate_Model_Status::getLabel($status);
					$tring_error =  trim(trim($tring_error),",");
					
					if($count_error > 0) Mage::getSingleton('adminhtml/session')->addError(
						$this->__('%s order(s) cannot be %s: (%s)',$count_error,$status_label,$tring_error)
					);
					
					if($count_success > 0) Mage::getSingleton('adminhtml/session')->addSuccess($this->__('%s order(s) have been %s',$count_success,$status_label));
					$this->_redirect("*/*/");
				}
			} catch (Exception $e) {
		      	Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
		      	$this->_redirect("*/*/import");
		    }
    	}else
    	{
    		Mage::getSingleton('adminhtml/session')->addError(Mage::helper('affiliate')->__("Please select a file to import!"));
    		$this->_redirect("*/*/import");
    	}
    }
	public function updateStatusAction()
	{
		$history_ids = (array)$this->getRequest()->getParam('mw_history_id');
        $status     = (int)$this->getRequest()->getParam('status');
        if(!is_array($history_ids)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('affiliate')->__('Please select affiliate history(s)'));
        } else {
            try {
            	$count_success = 0;
            	$count_error = 0;
            	 foreach ($history_ids as $history_id) {
            	 	
            		$history = Mage::getModel('affiliate/affiliatetransaction')->load($history_id);
            		$order_id = $history ->getOrderId();
            		$status_order = (int)$history ->getStatus();
            		
            		if($status == MW_Affiliate_Model_Status::CANCELED){
            			
            			if($status_order == MW_Affiliate_Model_Status::PENDING){
            				$count_success = $count_success + 1;
            				MW_Affiliate_Model_Observer::saveOrderCanceled($order_id);
            			}else{
            				$count_error = $count_error + 1;
            			};
            		}else if($status == MW_Affiliate_Model_Status::COMPLETE){
            			
            			if($status_order == MW_Affiliate_Model_Status::PENDING){
            				$count_success = $count_success + 1;
            				MW_Affiliate_Model_Observer::saveOrderComplete($order_id);
            			}else{
            				$count_error = $count_error + 1;
            			};
            			
            		}else if($status == MW_Affiliate_Model_Status::CLOSED){
            			
            			if($status_order == MW_Affiliate_Model_Status::COMPLETE){
            				$count_success = $count_success + 1;
            				MW_Affiliate_Model_Observer::saveOrderClosed($order_id);
            			}else{
            				$count_error = $count_error + 1;
            			};
            		};
            	 }
            	 $status_label = MW_Affiliate_Model_Status::getLabel($status);
            	 
            	 if($count_success > 0) $this->_getSession()->addSuccess($this->__('%s order(s) have been %s',$count_success,$status_label));
            	 
                 if($count_error > 0) $this->_getSession()->addError($this->__('%s order(s) cannot be %s',$count_error,$status_label));
                 
            } catch (Mage_Core_Model_Exception $e) {
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
        $fileName   = 'affiliate_history.csv';
        $content    = $this->getLayout()->createBlock('affiliate/adminhtml_affiliatehistory_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'affiliate_history.xml';
        $content    = $this->getLayout()->createBlock('affiliate/adminhtml_affiliatehistory_grid')
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