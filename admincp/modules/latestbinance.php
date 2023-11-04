//==================================================================================================================================================================================================================================================================================================
//Binance Module for WebEngine
//CopyRight belongs to Gonzalo Ciocca & ImmortalMu.net
//Free Resource on https://github.com/gonzalociocca/binanceforwebengine
//WhatsApp : +54 0343-570-4950 - Gon
//==================================================================================================================================================================================================================================================================================================
<h1 class="page-header">Binance Donations</h1>
<?php
try {
	$database = (config('SQL_USE_2_DB',true) ? $dB2 : $dB);
	
	$binancelDonations = $database->query_fetch("SELECT * FROM ".WEBENGINE_BINANCE_TRANSACTIONS." ORDER BY id DESC");
	if(!is_array($binancelDonations)) throw new Exception("There are no Binance transactions in the database.");
	
	echo '<table id="paypal_donations" class="table table-condensed table-hover">';
	echo '<thead>';
		echo '<tr>';
			echo '<th>Transaction ID</th>';
			echo '<th>Account</th>';
			echo '<th>Fee</th>';
			echo '<th>Credit</th>';
			echo '<th>Status</th>';
		echo '</tr>';
	echo '</thead>';
	echo '<tbody>';
	foreach($binancelDonations as $data) {
		$userData = $common->accountInformation($data['userID']);

		echo '<tr>'; 
			echo '<td>'.$data['transactionId'].'</td>';
			echo '<td><a href="'.admincp_base("accountinfo&id=".$data['userID']).'">'.$userData[_CLMN_USERNM_].'</a></td>';
			echo '<td>'.$data['fee'].' '.$data['feeCurrency'].'</td>';
			echo '<td>'.$data['creditAmount'].' '.$data['creditType'].'</td>';
			echo '<td>'.$data['statusmsg'].'</td>';
		echo '</tr>';
	}
	echo '
	</tbody>
	</table>';
} catch(Exception $ex) {
	message('error', $ex->getMessage());
}
?>