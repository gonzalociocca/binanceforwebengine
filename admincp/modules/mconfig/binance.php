//==================================================================================================================================================================================================================================================================================================
//Binance Module for WebEngine
//CopyRight belongs to Gonzalo Ciocca & ImmortalMu.net
//Free Resource on https://github.com/gonzalociocca/binanceforwebengine
//WhatsApp : +54 0343-570-4950 - Gon
//==================================================================================================================================================================================================================================================================================================

<h1 class="page-header">Binance Settings</h1>
<?php
function saveChanges() {
	global $_POST;
	foreach($_POST as $setting) {
		if(!check_value($setting)) {
			message('error','Datos Perdidos (Faltan completar algunas filas).');
			return;
		}
	}
	
	// Binance
	$xmlPath = __PATH_MODULE_CONFIGS__.'donation.binance.xml';
	$xml = simplexml_load_file($xmlPath);
	$xml->active = $_POST['setting_1'];
	$xml->api_key = $_POST['setting_2'];
	$xml->secret_key = $_POST['setting_3'];
	$xml->binance_title = $_POST['setting_4'];
	$xml->binance_description = $_POST['setting_5'];
	$xml->binance_currency = $_POST['setting_6'];
	$xml->binance_return_url = $_POST['setting_8'];
	$xml->binance_api_return_url = $_POST['setting_9'];
	$xml->credit_config = $_POST['setting_10'];
	$xml->credit_selected = $_POST['setting_11'];

	$xml->pack_1_price = $_POST['setting_12'];
	$xml->pack_1_credits = $_POST['setting_13'];
	$xml->pack_2_price = $_POST['setting_14'];
	$xml->pack_2_credits = $_POST['setting_15'];
	$xml->pack_3_price = $_POST['setting_16'];
	$xml->pack_3_credits = $_POST['setting_17'];
	$xml->pack_4_price = $_POST['setting_18'];
	$xml->pack_4_credits = $_POST['setting_19'];
	$xml->pack_5_price = $_POST['setting_20'];
	$xml->pack_5_credits = $_POST['setting_21'];
	$xml->pack_6_price = $_POST['setting_22'];
	$xml->pack_6_credits = $_POST['setting_23'];
	$xml->pack_7_price = $_POST['setting_24'];
	$xml->pack_7_credits = $_POST['setting_25'];
	$xml->pack_8_price = $_POST['setting_26'];
	$xml->pack_8_credits = $_POST['setting_27'];
	$xml->pack_9_price = $_POST['setting_28'];
	$xml->pack_9_credits = $_POST['setting_29'];
	$xml->pack_10_price = $_POST['setting_30'];
	$xml->pack_10_credits = $_POST['setting_31'];
	$save2 = $xml->asXML($xmlPath);
	

	if($save2) {
		message('success','[Binance] Settings successfully saved.');
	} else {
		message('error','[Binance] There has been an error while saving changes.');
	}

}

if(check_value($_POST['submit_changes'])) {
	saveChanges();
}

loadModuleConfigs('donation.binance');

$creditSystem = new CreditSystem();
?>
<form action="" method="post">
	<table class="table table-striped table-bordered table-hover module_config_tables">
		<tr>
			<th>Status<br/><span>Habilitar o Deshabilitar el modulo de Binance.</span></th>
			<td>
				<? enabledisableCheckboxes('setting_1',mconfig('active'),'Enabled','Disabled'); ?>
			</td>
		</tr>
		<tr>
			<th>Binance API KEY<br/><span>Ingresa el API KEY de tu cuenta de Binance.<a href="https://www.mercadopago.com.ar/developers/panel/credentials" target="_blank">click here</a>.</span></span></th>
			<td>
				<input style="width: 100%;" class="input-xxlarge" type="text" name="setting_2" value="<?=mconfig('api_key')?>"/>
			</td>
		</tr>
		<tr>
			<th>Binance Secret KEY<br/><span>Ingresa el Secret KEY de tu cuenta de Binance.<a href="https://www.mercadopago.com.ar/developers/panel/credentials" target="_blank">click here</a>.</span></span></th>
			<td>
				<input style="width: 100%;" class="input-xxlarge" type="text" name="setting_3" value="<?=mconfig('secret_key')?>"/>
			</td>
		</tr>
		<tr>
			<th>Binance Donations Title<br/><span>Titulo de la compra. Ejemplo: "Servicio de WCoinC".</span></th>
			<td>
				<input style="width: 100%;" class="input-xxlarge" type="text" name="setting_4" value="<?=mconfig('binance_title')?>"/>
			</td>
		</tr>
		<tr>
			<th>Binance Donations Description<br/><span>Descripción de la compra. Ejemplo: "Servicio asociado al Mu Online".</span></th>
			<td>
				<input style="width: 100%;" class="input-xxlarge" type="text" name="setting_5" value="<?=mconfig('binance_description')?>"/>
			</td>
		</tr>
		<tr>
			<th>Currency Code<br/><span>Elije la moneda de tu País: </span></th>
			<td>
				<input style="width: 100%;" class="input-xxlarge" type="text" name="setting_6" value="<?=mconfig('binance_currency')?>"/>
			</td>
		</tr>
		<tr>
		<tr>
			<th>Return/Cancel URL<br/><span>URL en donde el cliente va a volver si cancela o compra el producto.</span></th>
			<td>
				<input style="width: 100%;" class="input-xxlarge" type="text" name="setting_8" value="<?=mconfig('binance_return_url')?>"/>
			</td>
		</tr>
		<tr>
			<th>IPN Notify URL<br/><span>URL en donde se encuentra alojada la API <br>(si se realiza una compra o se cancela la API noficara en la DB dicho proceso).<br/></span></th>
			<td>
				<input style="width: 100%;" class="input-xxlarge" type="text" name="setting_9" value="<?=mconfig('binance_api_return_url')?>"/>
			</td>
		</tr>
		<tr>
			<th>Credit Configuration<br/><span>Elije el tipo de credito que el comprador va a recibir<br></span></th>
			<td>
				<?php echo $creditSystem->buildSelectInput("setting_10", mconfig('credit_config'), "form-control"); ?>
	   			 <input type="hidden" id="creditsValue" name="setting_11" value="<?=mconfig('credit_selected')?>" />
			</td>
		</tr>
		<script type="text/javascript" src="http://code.jquery.com/jquery-1.7.1.min.js"></script>
		<script>
			$(".form-control").on('click', function(event){
				credits = $(".form-control option:selected").text();
 				document.getElementById("creditsValue").value = credits;
			});
		</script>
		
		<tr>
			<th>Configurar Pack 1<br/><span>Elije el precio y los Creditos que el cliente recibira al pagar  (0 = Deshabilita el pack)<br></span></th>
			<td>
				<input style="width: 20%;" class="input-xxlarge" type="text" name="setting_12" value="<?=mconfig('pack_1_price')?>"/><br/>
		
				<input style="width: 20%;" class="input-xxlarge" type="text" name="setting_13" value="<?=mconfig('pack_1_credits')?>"/>
			</td>
		</tr>
		<tr>
			<th>Configurar Pack 2<br/><span>Elije el precio y los Creditos que el cliente recibira al pagar  (0 = Deshabilita el pack)<br></span></th>
			<td>
				<input style="width: 20%;" class="input-xxlarge" type="text" name="setting_14" value="<?=mconfig('pack_2_price')?>"/><br/>
		
				<input style="width: 20%;" class="input-xxlarge" type="text" name="setting_15" value="<?=mconfig('pack_2_credits')?>"/>
			</td>
		</tr>
		<tr>
			<th>Configurar Pack 3<br/><span>Elije el precio y los Creditos que el cliente recibira al pagar  (0 = Deshabilita el pack)<br></span></th>
			<td>
				<input style="width: 20%;" class="input-xxlarge" type="text" name="setting_16" value="<?=mconfig('pack_3_price')?>"/><br/>
		
				<input style="width: 20%;" class="input-xxlarge" type="text" name="setting_17" value="<?=mconfig('pack_3_credits')?>"/>
			</td>
		</tr>
		<tr>
			<th>Configurar Pack 4<br/><span>Elije el precio y los Creditos que el cliente recibira al pagar  (0 = Deshabilita el pack)<br></span></th>
			<td>
				<input style="width: 20%;" class="input-xxlarge" type="text" name="setting_18" value="<?=mconfig('pack_4_price')?>"/><br/>
		
				<input style="width: 20%;" class="input-xxlarge" type="text" name="setting_19" value="<?=mconfig('pack_4_credits')?>"/>
			</td>
		</tr>
		<tr>
			<th>Configurar Pack 5<br/><span>Elije el precio y los Creditos que el cliente recibira al pagar  (0 = Deshabilita el pack)<br></span></th>
			<td>
				<input style="width: 20%;" class="input-xxlarge" type="text" name="setting_20" value="<?=mconfig('pack_5_price')?>"/><br/>
		
				<input style="width: 20%;" class="input-xxlarge" type="text" name="setting_21" value="<?=mconfig('pack_5_credits')?>"/>
			</td>
		</tr>
		<tr>
			<th>Configurar Pack 6<br/><span>Elije el precio y los Creditos que el cliente recibira al pagar  (0 = Deshabilita el pack)<br></span></th>
			<td>
				<input style="width: 20%;" class="input-xxlarge" type="text" name="setting_22" value="<?=mconfig('pack_6_price')?>"/><br/>
		
				<input style="width: 20%;" class="input-xxlarge" type="text" name="setting_23" value="<?=mconfig('pack_6_credits')?>"/>
			</td>
		</tr>
		<tr>
			<th>Configurar Pack 7<br/><span>Elije el precio y los Creditos que el cliente recibira al pagar  (0 = Deshabilita el pack)<br></span></th>
			<td>
				<input style="width: 20%;" class="input-xxlarge" type="text" name="setting_24" value="<?=mconfig('pack_7_price')?>"/><br/>
		
				<input style="width: 20%;" class="input-xxlarge" type="text" name="setting_25" value="<?=mconfig('pack_7_credits')?>"/>
			</td>
		</tr>
		<tr>
			<th>Configurar Pack 8<br/><span>Elije el precio y los Creditos que el cliente recibira al pagar  (0 = Deshabilita el pack)<br></span></th>
			<td>
				<input style="width: 20%;" class="input-xxlarge" type="text" name="setting_26" value="<?=mconfig('pack_8_price')?>"/><br/>
		
				<input style="width: 20%;" class="input-xxlarge" type="text" name="setting_27" value="<?=mconfig('pack_8_credits')?>"/>
			</td>
		</tr>
		<tr>
			<th>Configurar Pack 9<br/><span>Elije el precio y los Creditos que el cliente recibira al pagar  (0 = Deshabilita el pack)<br></span></th>
			<td>
				<input style="width: 20%;" class="input-xxlarge" type="text" name="setting_28" value="<?=mconfig('pack_9_price')?>"/><br/>
		
				<input style="width: 20%;" class="input-xxlarge" type="text" name="setting_29" value="<?=mconfig('pack_9_credits')?>"/>
			</td>
		</tr>
		<tr>
			<th>Configurar Pack 10<br/><span>Elije el precio y los Creditos que el cliente recibira al pagar  (0 = Deshabilita el pack)<br></span></th>
			<td>
				<input style="width: 20%;" class="input-xxlarge" type="text" name="setting_30" value="<?=mconfig('pack_10_price')?>"/><br/>
		
				<input style="width: 20%;" class="input-xxlarge" type="text" name="setting_31" value="<?=mconfig('pack_10_credits')?>"/>
			</td>
		</tr>
		<tr>
			<td colspan="2"><input type="submit" name="submit_changes" value="Save Changes" class="btn btn-success"/></td>
		</tr>
	</table>
</form>