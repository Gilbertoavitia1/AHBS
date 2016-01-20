<?php

class Mage_Firstdatae4_Adminhtml_RecurringController extends Mage_Adminhtml_Controller_Action
{
    protected function _initAction() {
        $this->loadLayout()->_setActiveMenu('firstdatae4/firstdatae4');
        return $this;
    }   
 
    public function indexAction() {    
        $this->_initAction()->renderLayout();
    }
    
    public function deleteAction(){
        $id     = $this->getRequest()->getParam('id');
        
        $model = Mage::getModel('firstdatae4/recurring');
        $model->deleteRecurring($id);
        
        $this->_redirect('*/*/');       
    }
     
    public function editAction() {
        $id     = $this->getRequest()->getParam('id');
        $model  = Mage::getModel('firstdatae4/recurring')->load($id);
        
        if ($model->getId() || $id == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {
                    $model->setData($data);
            }

            Mage::register('recurring_data', $model);

            $this->loadLayout();
            $this->_setActiveMenu('firstdatae4/firstdatae4');

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

            $this->_addContent($this->getLayout()->createBlock('firstdatae4/adminhtml_recurring_edit'));
//            ->_addLeft($this->getLayout()->createBlock('firstdatae4/adminhtml_recurring_edit_tabs'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('firstdatae4')->__('Item does not exist'));
            $this->_redirect('*/*/');
        }
    }
}
?>
