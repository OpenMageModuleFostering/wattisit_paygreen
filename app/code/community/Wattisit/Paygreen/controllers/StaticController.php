<?php
class Wattisit_Paygreen_StaticController extends Mage_Core_Controller_Front_Action
{

    public function getAction()
    {
        $type = $this->getRequest()->getParam('t');
 
        switch ($type) {
            case 'icons':
                return $this->_icons();
                break;
            default:
                $this->getResponse()->setHeader('HTTP/1.1','404 Not Found');
                $this->getResponse()->setHeader('Status','404 File not found');
                break;
        }
    }
 
 
    private function _icons()
    {
        $file = $this->getRequest()->getParam('f', 'paygreen_paiement.png');
        $type = $this->getRequest()->getParam('t');
 
        $helper = Mage::helper('paygreen');
 
        if (in_array($file, $helper->getAllowedFiles($type))) {
            $icon = new Varien_Image($helper->getExtPubDir($helper::DIR_ICONS).DS.$file);
            $icon->keepTransparency(true);
            header("Content-type: ".$icon->getMimeType());
            $this->getResponse()->setHeader('Content-Type', $icon->getMimeType());
            $this->getResponse()->setBody(readfile($helper->getExtPubDir($helper::DIR_ICONS).DS.$file));
        } else {
        	die('404 '.$file.' '.$type);
            $this->getResponse()->setHeader('HTTP/1.1','404 Not Found');
            $this->getResponse()->setHeader('Status','404 File not found');
        }
    }

}