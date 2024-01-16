<?php
            define('access', 'api');
            require $_SERVER['DOCUMENT_ROOT'].'/includes/webengine.php';

            
  class access_db{
    


            public function checkMcDbStatus(){
                 $conn = Connection::Database('MuOnline');
                 $check = $conn->query_fetch("SELECT * FROM WEBENGINE_BINANCE_TRANSACTIONS'");
                 if(!$check){
                                $conn->query("create table WEBENGINE_BINANCE_TRANSACTIONS (
                                    id int IDENTITY(1,1) PRIMARY KEY,
                                    userID varchar(50) NULL,
									creditType varchar(50) NULL,
									creditAmount varchar(50) NULL,
									fee varchar(50) NULL,
									feeCurrency varchar(50) NULL,
									transactionId varchar(50) NULL,
									descmsg varchar(50) NULL,
									statusmsg varchar(50) NULL
                            );");
                    return true;
                  }
            }



            public function checkDbId($transactionId){
                $conn = Connection::Database('MuOnline');
                $check = $conn->query_fetch("SELECT transactionId FROM WEBENGINE_BINANCE_TRANSACTIONS WHERE transactionId = '$transactionId'");
                if(!$check){
                    return false;
                }else{
                    return true;
                }
            }


            public function userID($userMu){
                $conn = Connection::Database('MuOnline');
                $check = $conn->query_fetch("SELECT memb_guid FROM MEMB_INFO WHERE memb___id = '$userMu'");
                if(!$check){
                    return $check;
                }else{
                    return $check;
                }
            }
            
            public function checkDbStatus($transactionId){
                $conn = Connection::Database('MuOnline');
                $check = $conn->query_fetch("SELECT status FROM WEBENGINE_BINANCE_TRANSACTIONS WHERE transactionId = '$transactionId'");
                foreach($check as $result){
                    $status = $result['status'];
                }
                if($status == "PAY_SUCCESS" || $status == "PAY_CLOSED"){ // test mode
                    return true;
                }else{
                    return false;
                }
            }       

            public function success($userID, $Credits){
				$fp = fopen('locked.txt','a+');
				if(flock($fp,LOCK_EX)){
                try {
                    # user id
                    if(!Validator::UnsignedNumber($userID)) throw new Exception("invalid userid");
                    // common class
                    $common = new common();
                    $accountInfo = $common->accountInformation($userID);
                    if(!is_array($accountInfo)) throw new Exception("invalid account");
                    $creditSystem = new CreditSystem();
                    $creditSystem->setConfigId(mconfig('credit_config'));
                    $configSettings = $creditSystem->showConfigs(true);
                    switch($configSettings['config_user_col_id']) {
                        case 'userid':
                            $creditSystem->setIdentifier($accountInfo[_CLMN_MEMBID_]);
                            break;
                        case 'username':
                            $creditSystem->setIdentifier($accountInfo[_CLMN_USERNM_]);
                            break;
                        case 'email':
                            $creditSystem->setIdentifier($accountInfo[_CLMN_EMAIL_]);
                            break;
                        default:
                            throw new Exception("invalid identifier");
                    }
                    $creditSystem->addCredits($Credits);
                } catch (Exception $ex) {// someone has to worry about this.
                }
				flock($fp,LOCK_UN);
				}
				fclose($fp);
            }    

            public function registerPayDb($data){
                                       
                //REGISTRAMOS EN LA BASE DE DATOS MCPAGO LOS DATOS
                $conn = Connection::Database('MuOnline');
              
                $query = "INSERT into WEBENGINE_BINANCE_TRANSACTIONS (userID,creditType,creditAmount,fee,feeCurrency,transactionId,descmsg,statusmsg) "
                ."VALUES "  
			    . "(:userID,:creditType,:creditAmount,:fee,:feeCurrency,:transactionId,:descmsg,:statusmsg)";

                $saveConfig = $conn->query($query, $data);
            }
 
    }

?>
