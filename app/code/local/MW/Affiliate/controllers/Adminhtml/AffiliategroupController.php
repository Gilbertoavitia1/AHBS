<?php

class MW_Affiliate_Adminhtml_AffiliategroupController extends Mage_Adminhtml_Controller_Action
{

	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('affiliate/group')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Manager Groups'), Mage::helper('adminhtml')->__('Manager Groups'));
		
		return $this;
	}   
 
	public function indexAction() {
		$this->_initAction()
			->renderLayout();
	}

	public function editAction() {
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('affiliate/affiliategroup')->load($id);
		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}

			Mage::register('affiliate_data_group', $model);
			$this->loadLayout();
			$this->_setActiveMenu('affiliate/group');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('affiliate/adminhtml_affiliategroup_edit'))
				->_addLeft($this->getLayout()->createBlock('affiliate/adminhtml_affiliategroup_edit_tabs'));

			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('affiliate')->__('Affiliate group does not exist'));
			$this->_redirect('*/*/');
		}
	}
 
	public function newAction() {
		$this->_forward('edit');
	}
	public function programAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }
	public function programGridAction()
    {
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('affiliate/adminhtml_affiliategroup_edit_tab_program', 'admin.group.programs')->toHtml()
        );
    }
	public function memberAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }
	public function memberGridAction()
    {
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('affiliate/adminhtml_affiliategroup_edit_tab_member', 'admin.group.members')->toHtml()
        );
    }
 
	public function saveAction() {
		$data = $this->getRequest()->getPost();
		if ($data) {	
		    //$data = $this->getRequest()->getPost();
			$group_id = $this->getRequest()->getParam('id');
			$model = Mage::getModel('affiliate/affiliategroup');
			try {
				$_members = $this->getRequest()->getParam('addmember');
				$members = $_members['group'];
				$_programs = $this->getRequest()->getParam('addprogram');
				$programs = $_programs['member'];
			   // var_dump($members);exit;	
			   // set lai group name
				$collection_group = $model ->load($group_id);
				$collection_group ->setGroupName($data['group_name'])
								  ->setLimitDay($data['limit_day'])
								  ->setLimitOrder($data['limit_order'])
							      ->setLimitCommission($data['limit_commission'])->save();
				// edit group
				if($group_id!=''){
					// truong hop join member vao group	
					if(isset($members))
					{   
						// xoa cac member trong group de update lai du lieu moi
						$collection_members = Mage::getModel('affiliate/affiliategroupmember')->getCollection()
				        							->addFieldToFilter('group_id',$group_id);
				        if(sizeof($collection_members) > 0){
				        	 foreach ($collection_members as $collection_member) {
				        	 	$collection_member->delete();
				        	 }
				        }
						$this ->saveGroupMember($members, $group_id);	
					}
					// them code join program 					
					//var_dump($programs);exit;
					if(isset($programs))
					{   
						$collection_programs = Mage::getModel('affiliate/affiliategroupprogram')->getCollection()
				        					->addFieldToFilter('group_id',$group_id);
				        if(sizeof($collection_programs) > 0){
				        	 foreach ($collection_programs as $collection_program) {
				        	 	$collection_program->delete();
				        	 }
				        }
						$this ->saveGroupProgram($programs, $group_id);	
					}	 
				}
				// truong hop them moi group
				if($group_id == ''){
					$collection_groups = Mage::getModel('affiliate/affiliategroup')->getCollection()
				        					->setOrder('group_id', 'DESC');
					foreach ($collection_groups as $collection_group) {
						$group_id = $collection_group ->getGroupId();
						break;
					}
					if(isset($members))
					{   
						$this ->saveGroupMember($members, $group_id);	
					}
					// them code join program 					
					if(isset($programs))
					{   
						$this ->saveGroupProgram($programs, $group_id);	
					}			
					
				}
				
				//set total member customer program 
				Mage::helper('affiliate')->setTotalMemberProgram();
				
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('affiliate')->__('The group has successfully saved'));
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
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('affiliate')->__('Unable to find group to save'));
        $this->_redirect('*/*/');
	}
	public function saveGroupMember($members, $group_id)
	{   
		$member_idss = explode("&",$members);
		$datamember = array();
		foreach ($member_idss as $member_ids) {
			$member_id = explode("=",$member_ids);
			$datamember['customer_id'] = $member_id[0];
			$datamember['group_id'] = $group_id;
			if($datamember['customer_id'] != 0) 
			{   // set lai gia tri group moi cho member da tham gia group 
				$group_members = Mage::getModel('affiliate/affiliategroupmember')->getCollection()
	        							->addFieldToFilter('customer_id',$datamember['customer_id']);
		        if(sizeof($group_members) > 0){
		        	 foreach ($group_members as $group_member) {
		        	 	$group_member->setGroupId($group_id)->save();
		        	 }
		        	
		        } else{
		        	Mage::getModel("affiliate/affiliategroupmember")->setData($datamember)->save();
		        }
				
			}
		}	
	}
	public function saveGroupProgram($programs, $group_id)
	{
		$program_idss = explode("&",$programs);
		$dataprogram = array();
		foreach ($program_idss as $program_ids) {
			$program_id = explode("=",$program_ids);
			$dataprogram['program_id'] = $program_id[0];
			$dataprogram['group_id'] = $group_id;
			if($dataprogram['program_id'] != 0) 
			{
				Mage::getModel("affiliate/affiliategroupprogram")->setData($dataprogram)->save();
			}
		}	
		
	}
	public function deleteAction() {
		if( $this->getRequest()->getParam('id') >0 ) {
			try {
				$store_id = Mage::app()->getStore()->getId();
				$default_group = (int)Mage::helper('affiliate')->getDefaultGroupAffiliateStore($store_id);
				$group_id = $this->getRequest()->getParam('id');
				$error = 0;
				$group_members = Mage::getModel('affiliate/affiliategroupmember')->getCollection()
	        							->addFieldToFilter('group_id', $group_id);
				if(sizeof($group_members) > 0 || $group_id == 1 || $group_id == $default_group){
	        		$error = 1;
	        		if($group_id == 1 || $group_id == $default_group){
	        				Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Can not deleted the group with id = %s, which is an affiliate default group.',$group_id));
	        		}else{
	        				Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Can not deleted the group with id = %s, which contains active affiliate member.',$group_id));
	        		 }
	        	}else{
		        	$model = Mage::getModel('affiliate/affiliategroup');
					 
					$model->setId($this->getRequest()->getParam('id'))
						->delete();
					$group_programs = Mage::getModel('affiliate/affiliategroupprogram')->getCollection()
	                                                 ->addFieldToFilter('group_id',$group_id);
	                foreach ($group_programs as $group_program) {
	                    $group_program->delete();
	                }
	        	}
	        	//set total member customer program 
				Mage::helper('affiliate')->setTotalMemberProgram();
				
				if($error == 0) Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('The group has successfully deleted'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/');
	}
	
	public function massDeleteAction() {
        $group_ids = $this->getRequest()->getParam('group_id');
        if(!is_array($group_ids)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
            	$store_id = Mage::app()->getStore()->getId();
				$default_group = (int)Mage::helper('affiliate')->getDefaultGroupAffiliateStore($store_id);
            	$error = 0;
                foreach ($group_ids as $group_id) {
                	$group_members = Mage::getModel('affiliate/affiliategroupmember')->getCollection()
	        							->addFieldToFilter('group_id', $group_id);
	        		if(sizeof($group_members) > 0 || $group_id == 1 || $group_id == $default_group){
	        			$error = $error + 1;
	        			if($group_id == 1 || $group_id == $default_group){
	        				Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Can not deleted the group with id = %s, which is an affiliate default group.',$group_id));
	        			}else{
	        				Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Can not deleted the group with id = %s, which contains active affiliate member.',$group_id));
	        			}
	        		}else{
	        			$group = Mage::getModel('affiliate/affiliategroup')->load($group_id);
                    	$group->delete();
                    	
	        			$group_programs = Mage::getModel('affiliate/affiliategroupprogram')->getCollection()
                                                 ->addFieldToFilter('group_id',$group_id);
		                foreach ($group_programs as $group_program) {
		                    $group_program->delete();
		                }
		                
	        		}
                    
                }
                //set total member customer program 
				Mage::helper('affiliate')->setTotalMemberProgram();
				
                if((count($group_ids) - $error) > 0){
                	Mage::getSingleton('adminhtml/session')->addSuccess(
	                    Mage::helper('adminhtml')->__('Total of %d record(s) were successfully deleted', count($group_ids) - $error)
	                );
                }
                
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
    public function exportCsvAction()
    {
        $fileName   = 'affiliate_group.csv';
        $content    = $this->getLayout()->createBlock('affiliate/adminhtml_affiliategroup_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'affiliate_group.xml';
        $content    = $this->getLayout()->createBlock('affiliate/adminhtml_affiliategroup_grid')
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