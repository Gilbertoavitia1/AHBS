<?php
class MW_Affiliate_Adminhtml_AffiliatebannerController extends Mage_Adminhtml_Controller_Action
{   
	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('affiliate/banner')
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
	public function memberGridAction()
    {
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('affiliate/adminhtml_affiliatebanner_edit_tab_member', 'admin.banner.members')->toHtml()
        );
    }
	public function editAction() {
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('affiliate/affiliatebanner')->load($id);

		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}

			Mage::register('affiliate_data_banner', $model);

			$this->loadLayout();
			$this->_setActiveMenu('affiliate/banner');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('affiliate/adminhtml_affiliatebanner_edit'))
				->_addLeft($this->getLayout()->createBlock('affiliate/adminhtml_affiliatebanner_edit_tabs'));

			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('affiliate')->__('Banner does not exist'));
			$this->_redirect('*/*/');
		}
	}
 
	public function newAction() {
		$this->_forward('edit');
	}
	public function saveAction() {
		if ($data = $this->getRequest()->getPost()) 
		{
			$group_ids = "";
			if(isset($data["group_id"])){
				$group_ids = implode(",",$data["group_id"]);
			}
			$data["group_id"] = $group_ids;
			
			if (isset($data['store_view'])){
					$stores = $data['store_view'];
					$storesCount = count($stores);
					$storesIndex = 1;
					$storesData = '';
					$check = 0;
					foreach ($stores as $store){
						if($store == '0') $check = 1;
						$storesData .= $store;
						if ($storesIndex < $storesCount){
							$storesData .= ',';
						}
						$storesIndex++;
					}
					if($check == 1) $data['store_view'] = '0';
					else $data['store_view'] = $storesData;
				}
			if(isset($_FILES['image_name']['name']) && $_FILES['image_name']['name'] != '') {
				try {	
					/* Starting upload */	
					$uploader = new Varien_File_Uploader('image_name');
					
					// Any extention would work
	           		$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png','swf'));
					$uploader->setAllowRenameFiles(true);
					
					// Set the file upload mode 
					// false -> get the file directly in the specified folder
					// true -> get the file in the product like folders 
					//	(file.jpg will go in something like /media/f/i/file.jpg)
					$uploader->setFilesDispersion(false);
					$file_name = $uploader->getCorrectFileName($_FILES['image_name']['name']);		
					// We set media as the upload dir
					$path = Mage::getBaseDir('media') . DS."mw_affiliate";
					$uploader->save($path, $file_name);
					
				} catch (Exception $e) {
		      
		        }
	        
		        //this way the name is saved in DB
	  			//$data['image_name'] = 'mw_affiliate/'.$_FILES['image_name']['name'];
	  			$data['image_name'] = 'mw_affiliate/'.$file_name;
			}
			else
			{
				if(isset($data['image_name']['delete']) && $data['image_name']['delete'] == 1) {
						 $data['image_name'] = '';
					} else {
						unset($data['image_name']);
					}
			}
	  			
			$model = Mage::getModel('affiliate/affiliatebanner');	
			if(Mage::app()->isSingleStoreMode()) $data['store_view'] = '0';	
			$model->setData($data)
				->setId($this->getRequest()->getParam('id'));
			
			try {

				$model->save();
				
				// save member to banner
				$_members = $this->getRequest()->getParam('addmember');
				$members = $_members['banner'];	
				if(isset($members))
				{   
					// xoa cac member trong group de update lai du lieu moi
					$collection_members = Mage::getModel('affiliate/affiliatebannermember')->getCollection()
			        							->addFieldToFilter('banner_id',$model->getId());
			        if(sizeof($collection_members) > 0){
			        	 foreach ($collection_members as $collection_member) {
			        	 	$collection_member->delete();
			        	 }
			        }
					$this ->saveBannerMember($members, $model->getId());	
				}
			
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('affiliate')->__('The banner has successfully saved'));
				Mage::getSingleton('adminhtml/session')->setFormData(false);

				if ($this->getRequest()->getParam('back')) {
					$this->_redirect('*/*/edit', array('id' => $model->getId()));
					return;
				}
				$this->_redirect('*/*/');
				return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('affiliate')->__('Unable to find banner to save'));
        $this->_redirect('*/*/');
	}
	public function saveBannerMember($members, $banner_id)
	{   
		$member_idss = explode("&",$members);
		$datamember = array();
		foreach ($member_idss as $member_ids) {
			$member_id = explode("=",$member_ids);
			$datamember['customer_id'] = $member_id[0];
			$datamember['banner_id'] = $banner_id;
			if($datamember['customer_id'] != 0) 
			{   
		        Mage::getModel("affiliate/affiliatebannermember")->setData($datamember)->save();	
			}
		}	
	}
 
	public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				$model = Mage::getModel('affiliate/affiliatebanner');
				 
				$model->setId($this->getRequest()->getParam('id'))
					->delete();
					 
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('The banner has successfully deleted'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/');
	}
	public function massDeleteAction() {
		//var_dump($this->getRequest()->getParams());exit;
        $invitationIds = $this->getRequest()->getParam('affiliatebannerGrid');
        if(!is_array($invitationIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($invitationIds as $invitationId) {
                    $invitation = Mage::getModel('affiliate/affiliatebanner')->load($invitationId);
                    $invitation->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($invitationIds)
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
        $invitationIds = $this->getRequest()->getParam('affiliatebannerGrid');
        if(!is_array($invitationIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($invitationIds as $invitationId) {
                    $invitation = Mage::getSingleton('affiliate/affiliatebanner')
                        ->load($invitationId)
                        ->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($invitationIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
	public function exportCsvAction()
    {
        $fileName   = 'affiliate_banner.csv';
        $content    = $this->getLayout()->createBlock('affiliate/adminhtml_affiliatebanner_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'affiliate_banner.xml';
        $content    = $this->getLayout()->createBlock('affiliate/adminhtml_affiliatebanner_grid')
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