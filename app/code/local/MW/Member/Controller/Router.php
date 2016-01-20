<?php
class MW_Member_Controller_Router extends Mage_Core_Controller_Varien_Router_Abstract{
    public function initControllerRouters($observer){
        $front = $observer->getEvent()->getFront();
        $front->addRouter('member', $this);
        $front->addRouter('mwcon', $this);
        return $this;
    }
    
    public function match(Zend_Controller_Request_Http $request){
        if (!Mage::isInstalled()) {
            Mage::app()->getFrontController()->getResponse()
                ->setRedirect(Mage::getUrl('install'))
                ->sendResponse();
            exit;
        }
        $pathInfo = trim($request->getPathInfo(), '/');
        $params = explode('/', $pathInfo);
        if ((isset($params[0]) && $params[0] == 'miembro') || (isset($params[0]) && $params[0] == 'member')) {
             //redirect to mymodule/brands/index/brand_name/addidas
             $request->setModuleName('member')  
                     ->setControllerName('index')
                     ->setActionName('index');
            if (isset($params[1])){
                 $request->setParam('member_url', $params[1]);
            }
            $request->setAlias(
                Mage_Core_Model_Url_Rewrite::REWRITE_REQUEST_PATH_ALIAS,
                $pathInfo
            );
            return true;
        }
        
        if (isset($params[0]) && $params[0] == 'mwcon') {
            $request->setModuleName('member')  
                     ->setControllerName('index')
                     ->setActionName('signin');
            if (isset($params[1])){
                 $request->setParam('member_id', $params[1]);
            }
            $request->setAlias(
                Mage_Core_Model_Url_Rewrite::REWRITE_REQUEST_PATH_ALIAS,
                $pathInfo
            );
            return true;
        }
        
        return false;
    }
}
