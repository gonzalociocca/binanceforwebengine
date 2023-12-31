//==================================================================================================================================================================================================================================================================================================
//Binance Module for WebEngine
//CopyRight belongs to Gonzalo Ciocca & ImmortalMu.net
//Free Resource on https://github.com/gonzalociocca/binanceforwebengine
//WhatsApp : +54 0343-570-4950 - Gon
//==================================================================================================================================================================================================================================================================================================

<?php
(!isLoggedIn()) ? redirect(1,'login') : null;

// Load Binance Settings Settings
loadModuleConfigs('donation.binance');

$common = new common();
$accountInfo = $common->accountInformation($_SESSION['userid']);

for ($i = 0;$i <= 10; $i++){

	$precio = mconfig('pack_'.$i.'_price');
	$monto = (int)mconfig('pack_'.$i.'_credits');

	if($precio ==! 0 && $monto ==! 0){

		$cantidadbotones = $i;
        $precios[] = $precio;
		$montos[] = $monto;
	}
}
		$paylinks[] = array($cantidadbotones);



$api_key = mconfig('api_key');
$secret_key = mconfig('secret_key');
$titulo = mconfig('binance_title');
$description= mconfig('binance_description');
$tipoDeMoneda = mconfig('binance_currency'); 
$user = $accountInfo[_CLMN_USERNM_];
$success =  mconfig('binance_return_url');  
$creditSelected = mconfig('credit_selected'); 

$urlapi = "https://bpay.binanceapi.com/binancepay/openapi/v2/order";

for($x=0;$x<$cantidadbotones;$x++){
    // Generate nonce string
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $nonce = '';
    for($i=1; $i <= 32; $i++)
    {
        $pos = mt_rand(0, strlen($chars) - 1);
        $char = $chars[$pos];
        $nonce .= $char;
    }
    $ch = curl_init();
    $timestamp = round(microtime(true) * 1000);
    // Request body
	
     $request = array(
       "returnUrl" => $success, 
       "env" => array(
             "terminalType" => "APP" 
          ), 
       "merchantTradeNo" => mt_rand(982538,9825382937292), 
       "orderAmount" => $precios[$x], 
       "currency" => $tipoDeMoneda, 
	   //"orderExpireTime" => $timestamp+30000, //i was testing so this was useful before.
       "goods" => array(
                "goodsType" => "02", 
                "goodsCategory" => "6000", 
                "referenceGoodsId" => "7876763A3B", 
                "goodsName" => $montos[$x]." ".$creditSelected." for ".$user."", //we read this and give credits to the buyer.
                "goodsDetail" => $description 
             ) 
    );
    $json_request = json_encode($request);
    $payload = $timestamp."\n".$nonce."\n".$json_request."\n";
    $binance_pay_key = $api_key;
    $binance_pay_secret = $secret_key;
    $signature = strtoupper(hash_hmac('SHA512',$payload,$binance_pay_secret));
    $headers = array();
    $headers[] = "Content-Type: application/json";
    $headers[] = "BinancePay-Timestamp: $timestamp";
    $headers[] = "BinancePay-Nonce: $nonce";
    $headers[] = "BinancePay-Certificate-SN: $binance_pay_key";
    $headers[] = "BinancePay-Signature: $signature";

    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_URL, $urlapi);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json_request);

    $result = curl_exec($ch);
    if (curl_errno($ch)) { echo 'Error:' . curl_error($ch); }
    curl_close ($ch);

    //echo $result;
	$checkout= json_decode($result,true)['data']['universalUrl'];
	$paylinks[$x]=$checkout;
}



echo '<div class="page-content">';  
      echo '<div class="page-title"><span> Dona por WCoinP</span></div>';  

	            for($i = 0; $i < $cantidadbotones; $i++){

                    echo '<div class="paypal-gateway-container">';  
                        echo '<div class="paypal-gateway-content">';  
                            echo '<div class="binance-gateway-logo"></div>';
                	        echo '<div class="paypal-gateway-form"><div>$'.$precios[$i].' = '.$montos[$i].' '.$creditSelected.'</div></div>';
                	        echo '<div class="paypal-gateway-continue">
                             <a class="btn btn-success" href="'.$paylinks[$i].'">Pay with Binance</a>
                		          </div>';
                        echo '</div>';
                    echo '</div><br>';
	            }

echo '</div>';

?>
