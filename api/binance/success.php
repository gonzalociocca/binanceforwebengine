<?php
   header("Access-Control-Allow-Origin: *");
   header("Access-Control-Allow-Headers: *");
   header("Access-Control-Allow-Methods: *");
   header("Access-Control-Expose-Headers: Content-Length, Content-Range");
   http_response_code(200);
    require 'class/class.php'; 
    loadModuleConfigs('donation.binance');
    
    $raw_post_data = file_get_contents('php://input');
    $json_string = json_decode($raw_post_data, true);
    $headers=apache_request_headers();
	$nonce = "";
	$timestampx;
	$signaturex;
	foreach ($headers as $header => $value){
		if($header=="binancepay-signature"){
			$signaturex=$value;
		}
		if($header=="binancepay-timestamp"){
			$timestampx=$value;
		}
		if($header=="binancepay-nonce"){
			$nonce=$value;
		}
	}
	$status = $json_string['bizStatus'];
	$decodedSignature = base64_decode ( $signaturex );
	$headerx=$headers;
	$payloadx = $timestampx . "\n" . $nonce . "\n" . $raw_post_data . "\n";
	
	if($status == "PAY_CLOSED" || $status == "PAY_FAIL" || $status == "PAY_SUCCESS"){
	//Request Public Key
	$api_key=mconfig('api_key');
	$secret_key=mconfig('secret_key');
	$urlapi = "https://bpay.binanceapi.com/binancepay/openapi/certificates";   
	
    $ch = curl_init();
	$request = array(
    );
    $json_request = json_encode($request);
	$timestamp = round(microtime(true) * 1000);
    $payload = $timestamp."\n".$nonce."\n".$json_request."\n";
    $signature = strtoupper(hash_hmac('SHA512',$payload,$secret_key));
    $debug = 0; // debug = 1 if you want to test payments and give credits even if they dont pay, debug = 0 if you want to give credits to real payments
    $headers = array();
    $headers[] = "Content-Type: application/json";
    $headers[] = "binancepay-timestamp: $timestamp";
    $headers[] = "binancepay-nonce: $nonce";
    $headers[] = "binancepay-certificate-sn: $api_key";
    $headers[] = "binancepay-signature: $signature";

    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_URL, $urlapi);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json_request);

    $result = curl_exec($ch);
    if (curl_errno($ch)) { echo 'Error:' . curl_error($ch); }
    curl_close ($ch);
	
	//Verify Signature
	$publicKey = json_decode($result,true)['data']['0']['certPublic'];
	
	
	$verified = "".openssl_verify($payloadx, $decodedSignature, $publicKey, OPENSSL_ALGO_SHA256 )."";

	if($verified=="1"){
		//Right signature
		if($status == "PAY_SUCCESS" || $debug == 1){
		$info_data=json_decode($json_string['data'],true);
		$info_desc=$info_data['productName'];
		$arr = explode(" ",$info_desc);
		$info_creditamount = $arr[0];
		$info_credittype = $arr[1];
		$info_user = $arr[3];
		$info_currency = $info_data['currency'];
		$info_fee = $info_data['totalFee'];
		$info_transactionid = $json_string['bizIdStr'];
		$userID = $info_user;
            $checkUserID = access_db::userID($userID);
            foreach($checkUserID as $dat){
                $userID = $dat['memb_guid'];
            }

            //creamos un array con toda la info
            $data = array(
                'userID' => $userID,
				'creditType' => $info_credittype,
				'creditAmount' => $info_creditamount,
				'fee' => $info_fee,
				'feeCurrency' => $info_currency,
				'transactionId' => $info_transactionid,
				'descmsg' => $info_desc,
                'statusmsg' => $status
            );

            //Si no existe una base de datos de Binance La creamos!
            access_db::checkMcDbStatus();
                //BUSCAMOS EN LA DB QUE ESE info_transactionid ESTE REGISTRADO
                $checkDbId = access_db::checkDbId($info_transactionid);
                //BUSCAMOS EN LA DB SI ESTA REGISTRADO COMO PAGADO
                //$checkDbStatus = access_db::checkDbStatus($info_transactionid);
                
                //SI NO SE ENCUENTRÒ EL ID EN LA BASE DE DATOS, REGISTRAMOS EL NUEVO PAGO EN CASHOPDATA
                if(!$checkDbId){
                    access_db::success($userID, (int)$info_creditamount);
                    access_db::registerPayDb($data);
					echo '{"returnCode":"SUCCESS","returnMessage":"Accredited"}';
					
                }
	}else if($status == "PAY_CLOSED") {
		echo '{"returnCode":"SUCCESS","returnMessage":"Closed"}';
	}
	//End
	} else {
		echo '{"returnCode":"FAIL","returnMessage":"Wrong Signature"}';
		}
        } else {
			echo '{"returnCode":"FAIL","returnMessage":"Wrong Status"}';
			}
     
  
?>
