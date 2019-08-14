<?php
$installer=$this;
$installer->startSetup();
echo 'Running This Upgrade: '.get_class($this)."\n <br /> \n";
$installer->run("
DROP TABLE IF EXISTS `{$this->getTable('paygreen/buttons')}`;
CREATE TABLE IF NOT EXISTS `{$this->getTable('paygreen/buttons')}` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `label` VARCHAR(100) NULL,
  `image` VARCHAR(45) NULL,
  `height` INT NULL,
  `position` INT NULL,
  `displayType` VARCHAR(45) NULL DEFAULT 'default',
  `nbPayment` INT NOT NULL DEFAULT 1,
  `minAmount` DECIMAL(10,2) NULL,
  `maxAmount` DECIMAL(10,2) NULL,
  PRIMARY KEY (`id`))ENGINE=InnoDB DEFAULT CHARSET=utf8;
");
$connection=$installer->getConnection();
if (!$connection->fetchOne("SELECT id FROM ".$installer->getTable('paygreen/buttons'))) {
    $connection->insert($installer->getTable('paygreen/buttons'), array(
        'label'      => 'Payer par carte bancaire',
        'position'   => '1',
        'height'        => '60',
        'displayType'     => 'bloc',
        'nbPayment'       => '1',
    ));
   
}
$installer->endSetup();