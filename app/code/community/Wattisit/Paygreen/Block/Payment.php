<?php
class Wattisit_Paygreen_Block_Payment extends Mage_Core_Block_Template
{
	public function methodblock()
	{
		return '>>'.$this->getMerchantId() ;
	}

	protected function getMerchantId() {
		return trim(Mage::getStoreConfig('payment/paygreen/merchant_id'));
	}
	protected function getAccessKey() {
		return trim(Mage::getStoreConfig('payment/paygreen/access_key'));
	}

	protected function title() {
		return trim(Mage::getStoreConfig('payment/paygreen/title'));
	}
	protected function acceptedMessage() {
		return trim(Mage::getStoreConfig('payment/paygreen/paiement_accepted'));
	}
	protected function refusedMessage() {
		return trim(Mage::getStoreConfig('payment/paygreen/paiement_refused'));
	}
	protected function cancelledMessage() {
		return trim(Mage::getStoreConfig('payment/paygreen/paiement_cancelled'));
	}

	public function isConfigure() {
		$merchant_id = $this->getMerchantId();
		$access_key =  $this->getAccessKey();
		return isset($merchant_id) && isset($access_key);
	}

	public function getAll() {
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