<?php
class Wattisit_Paygreen_Adminhtml_ButtonsController extends Mage_Adminhtml_Controller_Action
{
	public function indexAction()
	{
		$merchant_id = trim(Mage::getStoreConfig('payment/paygreen/merchant_id'));
		$access_key =  trim(Mage::getStoreConfig('payment/paygreen/access_key'));
		if(empty($merchant_id) || empty($access_key))
			Mage::getSingleton('adminhtml/session')->addError('Vous devez configurer le module Paygreen : System > Configuration > Payment Methods > Paygreen');
		$this->loadLayout();
		$this->renderLayout();
	}

	public function saveAction() {
		if($this->getRequest()->getPost()) {
			if($this->getRequest()->getPost('submitPaygreenModuleButton', '0') == '1')
				$this->_saveItem($this->getRequest());
			else if($this->getRequest()->getPost('submitPaygreenModuleButtonDelete', '0') == '1')
				$this->_deleteItem($this->getRequest());
		}
		return $this->_redirect('adminpaygreen/adminhtml_buttons/index');
	}

	private function _saveItem($req) {
		if($req->getPost('id') > 0)
			$button = Mage::getModel('paygreen/buttons')->load($req->getPost('id'));
		else 
			$button = Mage::getModel('paygreen/buttons');

		if(array_key_exists('image', $_FILES) && is_array($_FILES['image'])) {
			if($_FILES['image']['error'] == 0) {
				$uploader = new Varien_File_Uploader('image');
    			$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
    			$uploader->setAllowRenameFiles(false);
    			$uploader->setFilesDispersion(false);

    			$path = Mage::getModuleDir('', 'Wattisit_Paygreen').DS.'public'.DS.'icons'.DS;

    			$uploader->save($path, $_FILES['image']['name']);

    			$button->setData('image', $_FILES['image']['name']);

			} else {
				Mage::getSingleton('adminhtml/session')->addError('Impossible de changer l\'image.');
			}
		}

		$button->setData('label', $req->getPost('label'));
		$button->setData('height', $req->getPost('height'));
		if($req->getPost('position') > 0)
			$button->setData('position', $req->getPost('position'));
		else 
			$button->setData('position', Mage::getModel('paygreen/buttons')->getCollection()->getSize()+1);
		try {
			$button->save();
			Mage::getSingleton('adminhtml/session')->addSuccess('Le bouton "'.$button->getData('label').'" à bien été sauvé.');
		
		} catch (Exception $e){
			Mage::getSingleton('adminhtml/session')->addError('Impossible de sauver ce boutton : '.$ex->getMessage());

		}
	}

	private function _deleteItem($req) {
		$model = Mage::getModel('paygreen/buttons');
		try {
			$model->setId($req->getPost('id'))->delete();
			Mage::getSingleton('adminhtml/session')->addSuccess('Le bouton "'.$req->getPost('label').'" à correctement été supprimé.');
		
		} catch (Exception $e){
			Mage::getSingleton('adminhtml/session')->addError('Impossible de supprimer ce boutton : '.$ex->getMessage());

		}
	}
}