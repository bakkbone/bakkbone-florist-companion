<?php

/**
 * @author BAKKBONE Australia
 * @package BkfEnqueueStyles
 * @license GNU General Public License (GPL) 3.0
**/


defined("BKF_EXEC") or die("Silent is golden");

/**
 * BkfEnqueueStyles
**/
class BkfEnqueueStyles{


	/**
	 * BkfEnqueueStyles:__construct()
	**/
	function __construct()
	{
	
		// front-end
		add_action("wp_enqueue_scripts", array($this, "bkfLoadStyles"));
	
	}
	
	
	/**
	 * BkfEnqueueStyles:bkfLoadStyles
	 * ref: https://developer.wordpress.org/reference/functions/wp_enqueue_style/
	**/
	function bkfLoadStyles()
	{
		wp_enqueue_style("bkf_bkf", BKF_URL . "assets/css/bkf.css", array("bkf_default"),"1.1","all" );
		wp_enqueue_style("bkf_default", BKF_URL . "assets/css/default.css", array(),"1.0.1","all" );
	}
	
	
}
