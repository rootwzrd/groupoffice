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
 * @version $Id: invitation.php 7752 2011-07-26 13:48:43Z mschering $
 * @author Merijn Schering <mschering@intermesh.nl>
 */


if (empty($_REQUEST['email'])) {
	die(GO::t("noEmailGiven"));
} else {
	$user = GO_Base_Model_User::model()->findSingleByAttribute('email', $_REQUEST['email']);
	if ($user) {
		if (empty($_REQUEST['usertoken']) || $_REQUEST['usertoken'] != $user->getSecurityToken()) 
			die(GO::t("usertokenInvalid"));
	} else {
		die(GO::t("noUserFoundWithEmail"));
	}
}
	
require(GO::view()->getTheme()->getPath().'header.php');
require(GO::config()->root_path.'views/Extjs3/default_scripts.inc.php');
?>
<script>GO.usertoken="<?php echo $_REQUEST['usertoken']; ?>";</script>
<script>GO.email="<?php echo $_REQUEST['email']; ?>";</script>
<script type="text/javascript" src="<?php echo GO::config()->host . 'views/Extjs3/javascript/ResetPassword.js'; ?>"></script>
<?php
require(GO::view()->getTheme()->getPath().'footer.php');