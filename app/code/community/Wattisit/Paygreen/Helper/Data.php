<?php

class Wattisit_Paygreen_Helper_Data extends Mage_Core_Helper_Data
{
    const DIR_ICONS = 'icons';
    
    /*public $merchant_id                  = '';
    public $access_key                   = '';
    public $paiement_accepted                   = '';
    public $paiement_refused                   = '';*/

    public function __get($name) {
        return Mage::getStoreConfig('payment/paygreen/'.$name);
    }

    public function getExtPubDir($type)
    {
        return __DIR__.DS.'..'.DS.'public'.DS.$type;
    }

    public function getAllowedFiles($dir)
    {
        $results = array();
        $handler = opendir($this->getExtPubDir($dir));
 
        /* Might be improved later via cache, not to list entire folder on each request. */
        while ($file = readdir($handler)) {
            if ($file != "." && $file != "..") {
                $results[] = $file;
            }
        }
 
        closedir($handler);
 
        return $results;
    }

    public function initPaygren() {
        $paygreenSdkPath  = __DIR__.DS.'..'.DS.'lib'.DS;
        require_once($paygreenSdkPath.'PaygreenClient.php');
        require_once($paygreenSdkPath.'PaygrenModuleUtils.php');
        $paygreen = new PaygrenModuleUtils($this->merchant_id);
        $paygreen->setToken($this->access_key);
        return $paygreen;
    }

    public function setOrderStatus($paygreen, $order)
    {
        switch($paygreen->result['status']) {
            case PaygreenClient::STATUS_REFUSED:
                $status = $this->failed_order_status;
            break;
            case PaygreenClient::STATUS_SUCCESSED:
                $status = $this->authorized_order_status;
            break;
            case PaygreenClient::STATUS_CANCELLING:
                $status = $this->canceled_order_status;
            break;
            default:
                return false;
        }

        $state = Mage::getModel('sales/order_status')->getCollection()
            ->joinStates()
            ->addFieldToFilter('state_table.status', $status)
            ->getFirstItem()
            ->getState();

        if (empty($state)) {
            $state = Mage_Sales_Model_Order::STATE_PROCESSING;
        }
        $order->setState($state, $status);
    }

    public function addTransaction($paygreen, $order)
    {
        $transactionId = $paygreen->pid;
        if (version_compare(Mage::getVersion(), '1.4', 'ge')) {
            /* @var $payment Mage_Payment_Model_Method_Abstract */
            $payment = $order->getPayment();
            if (!$payment->getTransaction($transactionId)) { // if transaction isn't saved yet
                $transaction = Mage::getModel('sales/order_payment_transaction');
                $transaction->setTxnId($transactionId);
                $transaction->setOrderPaymentObject($order->getPayment());
                $transaction->setTxnType(Mage_Sales_Model_Order_Payment_Transaction::TYPE_PAYMENT);
                /*if ($paymentAction == '100') {
                    
                } else if ($paymentAction == '101') {
                    $transaction->setTxnType(Mage_Sales_Model_Order_Payment_Transaction::TYPE_PAYMENT);
                }*/
                $transaction->save();
                $order->sendNewOrderEmail();
            }
        } else {
            $order->getPayment()->setLastTransId($transactionId);
            $order->sendNewOrderEmail();
        }

    }
}

// end class
