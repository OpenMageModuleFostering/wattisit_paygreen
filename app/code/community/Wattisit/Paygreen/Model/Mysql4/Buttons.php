<?php
class Wattisit_Paygreen_Model_Mysql4_Buttons extends Mage_Core_Model_Mysql4_Abstract
{
     public function _construct()
     {
         $this->_init('paygreen/buttons', 'id');
     }
}