<?php
class PaygrenModuleUtils extends PaygreenClient {

	public function generateForNbPaiement($transactionId, $nbPaiement, $amount, $currency) {
		if($nbPaiement>1) {
			$occurenceAmount = floor($amount / $nbPaiement);
			$firstAmount  = $amount - ($occurenceAmount * ($nbPaiement-1));

			$paiement->subscribtionPaiement(
				$paiement::RECURRING_MONTHLY,
				$nbPaiement,
				date('d')
			);
			if($occurenceAmount != $firstAmount)
				$paiement->subscriptionFirstAmount(
					round($firstAmount * 100)
				);
		} else {
			$occurenceAmount = $amount;
		}
		$this->transaction(
			$transactionId, 
			round($occurenceAmount * 100), 
			$currency
		);
	}

}