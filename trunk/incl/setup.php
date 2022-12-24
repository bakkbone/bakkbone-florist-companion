<?php

/**
 * @author BAKKBONE Australia
 * @package BakkboneFloristCompanion
 * @license GNU General Public License (GPL) 3.0
**/


defined("BKF_EXEC") or die("Silent is golden");

/**
 * BakkboneFloristCompanion
**/
class BakkboneFloristCompanion{
	
	/**
	 * BakkboneFloristCompanion:__construct()
	**/
	function __construct()
	{
		$customPosts = new BkfCustomPosts();  
		$enqueueStyles = new BkfEnqueueStyles();  
		$plugin_options = new BkfPluginOptions();  
		$plugin_options = new BkfShortCodes();  
		$admin_notices = new BkfAdminNotices();  
		$rest_api  = new BkfRestApi();  
		$bkfcore = new BkfCore();
		$bkforderstatus = new BkfOrderStatus();
		$bkfpdf = new BkfPdf();
		$bkfpetals = new BkfPetals();
		$bkfwcemail = new Bkf_WC_Email();

	}
	
	
}
