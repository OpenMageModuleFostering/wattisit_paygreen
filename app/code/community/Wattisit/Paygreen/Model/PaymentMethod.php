<?php
class Wattisit_Paygreen_Model_PaymentMethod extends Mage_Payment_Model_Method_Abstract {
    protected $_code  = 'paygreen';

    protected $_canUseForMultishipping  = false;

    protected $_formBlockType = 'paygreen/form';
    protected $_infoBlockType = 'paygreen/info';


    /*public function isAvailable($quote = null) {
    	return true;
    }*/

    public function assignData($data)
    {

        if (!($data instanceof Varien_Object)) {
            $data = new Varien_Object($data);
        }
        $info = $this->getInfoInstance();
        $info->setAdditionalInformation(array('button' => $data->paygreen_btn))
            ;

        return $this;
    }

    public function validate()
    {
        parent::validate();

        // Validate the credit card number
        $info = $this->getInfoInstance();
        //die($info->getButton());
        //$errorMsg = Mage::helper('payment')->__('Credit card type is not allowed for this payment method.');
		//Mage::throwException($errorMsg);
        return $this;
    }

    public function getOrderPlaceRedirectUrl()
    {
    	$info = $this->getInfoInstance()->getAdditionalInformation();
        return Mage::getUrl('paygreen/index/paiement', array(
        	'b'=>$info['button']
        ));
    }

}