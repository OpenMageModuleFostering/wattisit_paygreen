<?php

class Wattisit_Paygreen_Block_Form extends Mage_Payment_Block_Form {
    protected function _construct() {
        $this->setTemplate('paygreen/paiement.phtml');
        parent::_construct();
    }

    public function getButtons() {
		return Mage::getModel('paygreen/buttons')->getCollection()
				->setOrder('position','asc');
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
}