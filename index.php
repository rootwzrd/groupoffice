<?php
/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @copyright Copyright Intermesh
 * @version $Id: json.php 8246 2011-10-05 13:55:38Z mschering $
 * @author Merijn Schering <mschering@intermesh.nl>
 */

$root = dirname(__FILE__).'/';

//initialize autoloading of library
require_once('GO.php');
//GO::init();

if(empty($_REQUEST['r']) && PHP_SAPI!='cli'){	
	if(GO::config()->force_ssl && !GO_Base_Util_Http::isHttps()){
		 header("HTTP/1.1 301 Moved Permanently");
		 header("Location: https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
		 exit();
	}
}

// Hook:Maestrano
// Load Maestrano
require $root . '/maestrano/app/init/base.php';
$maestrano = MaestranoService::getInstance();
// Require authentication straight away if intranet
// mode enabled
if ($maestrano->isSsoIntranetEnabled()) {
  if (!$maestrano->getSsoSession()->isValid()) {
    header("Location: " . $maestrano->getSsoInitUrl());
  }
}

// Hook:Maestrano
// Log user via cookie or check Maestrano session is still valid
if(!GO::user()) {
  GO::session()->loginWithCookies();	
} else {
  
  if ($maestrano->isSsoEnabled()) {
    if (!$maestrano->getSsoSession()->isValid()) {
      header("Location: " . $maestrano->getSsoInitUrl());
    }
  }
}
	
// Hook:Maestrano
// Redirect to SSO login
if(!GO::user()) {
  if ($maestrano->isSsoEnabled()) {
    header("Location: " . $maestrano->getSsoInitUrl());
  } else {
    //try with HTTP auth
    if(!empty($_SERVER['PHP_AUTH_USER']) && !empty($_SERVER['PHP_AUTH_PW'])){
    	GO::session()->login($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']);
    }
  }
}


//check if GO is installed
if(empty($_REQUEST['r']) && PHP_SAPI!='cli'){	
	
	if(GO::user() && isset($_SESSION['GO_SESSION']['after_login_url'])){
		$url = GO::session()->values['after_login_url'];
		unset(GO::session()->values['after_login_url']);
		header('Location: '.$url);
		exit();
	}
	
	$installed=true;
	if(!GO::config()->get_config_file() || empty(GO::config()->db_user)){			
		$installed=false;
	}else
	{
		$stmt = GO::getDbConnection()->query("SHOW TABLES");
		if(!$stmt->rowCount())
			$installed=false;
	}
	if(!$installed){
		header('Location: '.GO::config()->host.'install/');				
		exit();
	}

	//check for database upgrades
	$mtime = GO::config()->get_setting('upgrade_mtime');

	if($mtime!=GO::config()->mtime)
	{
		GO::infolog("Running system update");
		header('Location: '.GO::url('maintenance/upgrade'));
		exit();
	}
}

GO::router()->runController();