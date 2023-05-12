<?php

/**
 * @author BAKKBONE Australia
 * @package BkfEnqueueStyles
 * @license GNU General Public License (GPL) 3.0
**/


defined("BKF_EXEC") or die("Silence is golden");

/**
 * BkfEnqueueStyles
**/
class BkfEnqueueStyles{


	function __construct()
	{	
		add_action("wp_enqueue_scripts", array($this, "bkfLoadStyles"));
		if(is_admin()){
			add_action("admin_enqueue_scripts", array($this, "bkfLoadStyles"));
			add_action("admin_enqueue_scripts", array($this, "bkfLoadAdminScripts"));
			add_action("admin_head", array($this, "bkfFonts"), 1);
		}
	
	}
	
	function bkfLoadStyles()
	{
		wp_enqueue_style("bkf_bkf", BKF_URL . "assets/css/bkf.css", array(),"1","all" );
		wp_enqueue_style("bkf_default", BKF_URL . "assets/css/default.css", array(),"1","all" );
		wp_enqueue_style("bkf_calendar", BKF_URL . "assets/css/calendar.css", array(),"1","all" );
	}

	function bkfLoadAdminScripts($hooks)
	{
		wp_enqueue_script("bkf_select", BKF_URL . "assets/js/select.js",'','','all' );
		wp_enqueue_script("bkf_copy", BKF_URL . "assets/js/copy.js",'','','all' );
		wp_enqueue_script("fullcalendar", BKF_URL . "incl/lib/fullcalendar/dist/index.global.js",'','','all' );
	}
	
	function bkfFonts() {
		?>
		<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin><link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,300;1,400;1,500;1,600;1,700;1,800&display=swap" rel="stylesheet">
	<?php
}

}