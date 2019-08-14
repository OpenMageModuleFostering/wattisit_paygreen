<?php

class Wattisit_Paygreen_Block_Info extends Mage_Payment_Block_Info {
    protected function _construct() {
        parent::_construct();
        $this->setTemplate('paygreen/informations.phtml');
    }

    public function getButtonType() {
    	$info = $this->getAdditional();
    	return $info['button'];
    }


    public function getButton() {
    	return Mage::getModel('paygreen/buttons')->load($this->getButtonType());
    }
    

    protected function getAdditional() {
    	$info = $this->getData('info');
        if (!($info instanceof Mage_Payment_Model_Info)) {
            Mage::throwException($this->__('Cannot retrieve the payment info model object.'));
        }
    	return $info->getAdditionalInformation();
    }
    public function getIcons($img) {
		$url = $this->getUrl('paygreen/static/get', 
								array(
									't' => 'icons', 
									'f' => $img
								)
							);
		return substr($url, 0, -1);
	}

    /*public function getInfo()
    {
        $info = $this->getData('info');
        if (!($info instanceof Mage_Payment_Model_Info)) {
            Mage::throwException($this->__('Cannot retrieve the payment info model object.'));
        }
        return $info;
    }*/
}