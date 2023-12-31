<?php
/**
 * WebEngine CMS
 * https://webenginecms.org/
 * 
 * @version 1.2.1
 * @author Lautaro Angelico <http://lautaroangelico.com/>
 * @copyright (c) 2013-2020 Lautaro Angelico, All Rights Reserved
 * 
 * Licensed under the MIT license
 * http://opensource.org/licenses/MIT
 */

$webengineModules = array(
	'_global' => array(
		array('News','news'),
		array('Login','login'),
		array('Register','register'),
		array('Downloads','downloads'),
		
		array('Donation','donation'),
		array('PayPal','paypal'),
		array('Binance','binance'),
		
		array('Rankings','rankings'),
		array('Castle Siege','castlesiege'),
		array('Email System','email'),
		array('Profiles','profiles'),
		array('Contact Us','contact'),
		array('Forgot Password','forgotpassword'),
	),
	'_usercp' => array(
		array('Add Stats','addstats'),
		array('Clear PK','clearpk'),
		array('Clear Skill-Tree','clearskilltree'),
		array('My Account','myaccount'),
		array('Change Password','mypassword'),
		array('Change Email','myemail'),
		array('Character Reset','reset'),
		array('Reset Stats','resetstats'),
		array('Unstick Character','unstick'),
		array('Vote and Reward','vote'),
		array('Buy Zen','buyzen'),
	),
);

echo '<h1 class="page-header">Module Manager</h1>';

echo '<div class="row">';
	
	echo '<div class="col-md-6">';
		echo '<h4>Global:</h4>';
		echo '<div class="modulesManager">';
		foreach($webengineModules['_global'] as $moduleList) {
			echo '<a href="'.admincp_base("modules_manager&config=".$moduleList[1]).'" class="btn btn-primary m-1" style="width:20%;">'.$moduleList[0].'</a>';
		}
		echo '</div>';
	echo '</div>';
	echo '<div class="col-md-6">';
		echo '<h4>User CP:</h4>';
		echo '<div class="modulesManager">';
			foreach($webengineModules['_usercp'] as $moduleList) {
				echo '<a href="'.admincp_base("modules_manager&config=".$moduleList[1]).'" class="btn btn-info m-1" style="width:20%;">'.$moduleList[0].'</a>';
			}
		echo '</div>';
	echo '</div>';

echo '</div>';

echo '<hr>';

if(check_value($_GET['config'])) {
	$filePath = __PATH_ADMINCP_MODULES__.'mconfig/'.$_GET['config'].'.php';
	if(file_exists($filePath)) {
		include($filePath);
	} else {
		message('error','Invalid module.');
	}
}