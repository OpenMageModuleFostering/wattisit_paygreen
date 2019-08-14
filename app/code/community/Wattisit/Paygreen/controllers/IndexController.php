<?php

class Wattisit_Paygreen_IndexController extends Mage_Core_Controller_Front_Action
{
	 public function indexAction()
	 {
			$this->loadLayout();
					$this->renderLayout();
	 }

	public function paiementAction() {
			$this->loadLayout();

			$btn_id = $this->getRequest()->getParam('b');

			$_session = Mage::getSingleton('checkout/session');
			$order = Mage::getModel('sales/order')->loadByIncrementId($_session->getLastRealOrderId());

			$btn =  Mage::getModel('paygreen/buttons')->load($btn_id);

			$paygreen = Mage::helper('paygreen')->initPaygren();

			if($order->getBaseGrandTotal() == 0){ // If order amount is null, exit payment process
					//Mage::helper('payline')->setOrderStatus($this->order, Mage::getStoreConfig('payment/payline_common/authorized_order_status'));
					 $order->setState(Mage_Sales_Model_Order::STATE_PAYMENT_REVIEW, true, 'Payment Success.');

					return true;
			}
			$customer = Mage::getModel('customer/customer')->load($order->getCustomerId());
			$buyerLastName = substr($customer->getLastname(), 0, 50);
			if ($buyerLastName == null || $buyerLastName == '') {
				$buyerLastName = substr($billingAddress->getLastname(), 0, 50);
			}
			$buyerFirstName = substr($customer->getFirstname(), 0, 50);
			if ($buyerFirstName == null || $buyerFirstName == '') {
				$buyerFirstName = substr($billingAddress->getFirstname(), 0, 50);
			}
			$email = $customer->getEmail();
	        if ($email == null || $email == '') {
	        	$email = $order->getCustomerEmail();
	        }

	        $country = 'FR';
	        $billingAddress = $order->getBillingAddress();
        	if($billingAddress != null){
        		$country = $billingAddress->getCountry();
        	}

			$paygreen->customer(
				$order->getCustomerId(), 
				$buyerLastName, 
				$buyerFirstName, 
				$email, 
				$country
			);

			$paygreen->generateForNbPaiement(
				substr($order->getRealOrderId(), 0, 50), 
				$btn->nbPayment, 
				$order->getBaseGrandTotal(),
				$order->getBaseCurrencyCode()
			);
			$paygreen->return_cancel_url = Mage::getUrl('paygreen/index/return');
			$paygreen->return_url = Mage::getUrl('paygreen/index/return');
			$paygreen->return_callback_url = Mage::getUrl('paygreen/index/validation');

			$block = $this->getLayout()->createBlock('paygreen/payment')
			->setData('btn', $btn)
			->setData('paygreen', $paygreen)
			->setTemplate('paygreen/caller.phtml'); 
			$this->getLayout()->getBlock('content')->append($block);
			$this->renderLayout();
	}

	public function returnAction() {
		$this->loadLayout();
		if($this->getRequest()->getPost()) {
			$data = $this->getRequest()->getPost('data');

		} else {
			$data = $this->getRequest()->getParams()['data'];
		}

		if($data == null)
			mageCoreErrorHandler(E_ERROR, "Donnée vide, veuillez nous contacter", __FILE__, __LINE__);
		
		$paygreen = Mage::helper('paygreen')->initPaygren();
		$paygreen->parseData($data);

		$order = Mage::getModel('sales/order')->loadByIncrementId($paygreen->transaction_id);

		$block = $this->getLayout()->createBlock('paygreen/payment')
			->setData('paygreen', $paygreen)
			->setData('order', $order)
			->setTemplate('paygreen/returned.phtml'); 
		$this->getLayout()->getBlock('content')->append($block);
		$this->renderLayout();
	}



	

	public function validationAction() {
		if($this->getRequest()->getPost()) {
			$data = $this->getRequest()->getPost('data');

		} else {
			$data = $this->getRequest()->getParams()['data'];
		}

		if($data == null)
			mageCoreErrorHandler(E_ERROR, "Donnée vide, veuillez nous contacter", __FILE__, __LINE__);
		
		$paygreen = Mage::helper('paygreen')->initPaygren();
		$paygreen->parseData($data);

		$order = Mage::getModel('sales/order')->loadByIncrementId($paygreen->transaction_id);

		Mage::helper('paygreen')->setOrderStatus($paygreen, $order);
		Mage::helper('paygreen')->addTransaction($paygreen, $order);

		$order->save();

		echo '<pre>';
        print_r($paygreen);
	}
}