<?php

class Sparx_Storelocator_Adminhtml_StorelocatorController extends Mage_Adminhtml_Controller_action
{
	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('storelocator/items')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Store Manager'), Mage::helper('adminhtml')->__('Store Manager'));
		
		return $this;
	}   
 
	public function indexAction() {
		$this->_initAction()
			->renderLayout();
	}

	public function editAction() {
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('storelocator/storelocator')->load($id);

		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}

			Mage::register('storelocator_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('storelocator/items');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Store Manager'), Mage::helper('adminhtml')->__('Store Manager'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Store News'), Mage::helper('adminhtml')->__('Store News'));

                        $this->getLayout()->getBlock('head')
                        ->setCanLoadExtJs(true)
                        ->setCanLoadTinyMce(true);

			$this->_addContent($this->getLayout()->createBlock('storelocator/adminhtml_storelocator_edit'))
				->_addLeft($this->getLayout()->createBlock('storelocator/adminhtml_storelocator_edit_tabs'));
                       
			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('storelocator')->__('Store does not exist'));
			$this->_redirect('*/*/');
		}
	}
 
	public function newAction() {
		$this->_forward('edit');
	}
 
	public function saveAction() {
		if ($data = $this->getRequest()->getPost()) {
			$id = $this->getRequest()->getParam('id');
                        $storemodel = Mage::getModel('storelocator/storelocator');
                        if(isset($data['state_id'])){
                        $state = Mage::getModel('directory/region')->load($data['state_id']);
                        $data['state'] = $state->getName();
                        }
                    
                        if(!$data['zoom_level']){
                         $data['zoom_level'] = Sparx_Storelocator_Helper_Data::storezoomLevel;
                        }  
                        
                        if(isset($data['storeimage'])){
                         if(isset($data['storeimage']['delete'])){
                             $deleteimg = 1;
                             $data['storeimage'] = '';
                         }else{
//                             $imageData = explode('/',$data['storeimage']['value']);
                             $data['storeimage'] = $data['storeimage']['value'];                             
                         }                         
                         }
			if(isset($_FILES['storeimage']['name']) && $_FILES['storeimage']['name'] != '') {
				try {	
					/* Starting upload */	
					$uploader = new Varien_File_Uploader('storeimage');
					
					// Any extention would work
	           		        $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
					$uploader->setAllowRenameFiles(false);
					
					// Set the file upload mode 
					// false -> get the file directly in the specified folder
					// true -> get the file in the product like folders 
					//	(file.jpg will go in something like /media/f/i/file.jpg)
					$uploader->setFilesDispersion(false);
							
					// We set media as the upload dir
					$path = Mage::getBaseDir('media') . DS . 'storelocator' . DS . 'images' . DS;
                                        $imgFilename = strtotime(date('Y-m-d H:i:s')) . '_' . strtolower($_FILES['storeimage']['name']);
					$uploader->save($path, $imgFilename);
					
				} catch (Exception $e) {
		      
		        }
	        
		        //this way the name is saved in DB
                                $imgFilename = strtotime(date('Y-m-d H:i:s')) . '_' . strtolower($_FILES['storeimage']['name']);
	  			$data['storeimage'] = $imgFilename;
			}
	  			
	  			
			$model = Mage::getModel('storelocator/storelocator');		
			$model->setData($data)
				->setId($this->getRequest()->getParam('id'));
			
			try {
				if ($model->getCreatedTime == NULL || $model->getUpdateTime() == NULL) {
					$model->setCreatedTime(now())
						->setUpdateTime(now());
				} else {
					$model->setUpdateTime(now());
				}	
				
				$model->save();
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('storelocator')->__('Store was successfully saved'));
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
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('storelocator')->__('Unable to find Store to save'));
        $this->_redirect('*/*/');
	}
 
	public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				$model = Mage::getModel('storelocator/storelocator');
				 
				$model->setId($this->getRequest()->getParam('id'))
					->delete();
					 
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Store was successfully deleted'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/');
	}

    public function massDeleteAction() {
        $storelocatorIds = $this->getRequest()->getParam('storelocator');
        if(!is_array($storelocatorIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($storelocatorIds as $storelocatorId) {
                    $storelocator = Mage::getModel('storelocator/storelocator')->load($storelocatorId);
                    $storelocator->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($storelocatorIds)
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
        $storelocatorIds = $this->getRequest()->getParam('storelocator');
        if(!is_array($storelocatorIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($storelocatorIds as $storelocatorId) {
                    $storelocator = Mage::getSingleton('storelocator/storelocator')
                        ->load($storelocatorId)
                        ->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($storelocatorIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
  
    public function exportCsvAction()
    {
        $fileName   = 'storelocator.csv';
        $content    = $this->getLayout()->createBlock('storelocator/adminhtml_storelocator_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'storelocator.xml';
        $content    = $this->getLayout()->createBlock('storelocator/adminhtml_storelocator_grid')
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