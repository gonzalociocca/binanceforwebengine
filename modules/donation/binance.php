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
$debug = 1; // debug 1/0 on/off

$urlapi = "https://bpay.binanceapi.com/binancepay/openapi/v2/order";

for($x=0;$x<$cantidadbotones;$x++){
    // Generate nonce string
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $nonce = '';
    for($i=1; $i <= 32; $i++)
    {
        $pos = null;
		try {
			$pos = mt_rand(0, strlen($chars) - 1);
		} catch (Exception $e) {
		print "Caught exception[mt_rand]: " . $e->getMessage();
		} finally {
			if($debug == 1) {print ' 5:' . $pos;}
		}
        $char = $chars[$pos];
        $nonce .= $char;
    }
    $ch = null;
	try {
		$ch = curl_init();
	} catch (Exception $e) {
    print "Caught exception[curl]: " . $e->getMessage();
	} finally {
		if($debug == 1) {print ' 5:' . $ch;}
	}
	
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
	
    $json_request = null;
	try {
		$json_request = json_encode($request);
	} catch (Exception $e) {
    print "Caught exception[JSON]: " . $e->getMessage();
	} finally {
		if($debug == 1) {print ' 5:' . $json_request;}
	}
	
    $payload = $timestamp."\n".$nonce."\n".$json_request."\n";
    $binance_pay_key = $api_key;
    $binance_pay_secret = $secret_key;
	
    $signature = null;
	try {
		$signature = strtoupper(hash_hmac('SHA512',$payload,$binance_pay_secret));
	} catch (Exception $e) {
    print "Caught exception[hash_hmac]: " . $e->getMessage();
	} finally {
		if($debug == 1) {print ' 5:' . $signature;}
	}
	
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

    $result = null;
	
	try {
		$result = curl_exec($ch);
	} catch (Exception $e) {
    print "Caught exception[curl_exec]: " . $e->getMessage();
	} finally {
		if($debug == 1) {print ' 5:' . $result;}
	}
	
    if (curl_errno($ch)) { echo 'Error:' . curl_error($ch); }
    curl_close ($ch);

    //echo $result;
	$checkout= null;
	
	try {
		$checkout= json_decode($result,true)['data']['universalUrl'];
	} catch (Exception $e) {
    print "Caught exception[json_decode]: " . $e->getMessage();
	} finally {
		if($debug == 1) {print ' 5:' . $checkout;}
	}
	
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
