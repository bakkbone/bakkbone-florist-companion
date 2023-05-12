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
		add_action("wp_enqueue_scripts", array($this, "bkf_enqueue_frontend"));
		if(is_admin()){
			add_action("admin_enqueue_scripts", array($this, "bkf_enqueue_frontend"));
			add_action("admin_enqueue_scripts", array($this, "bkf_enqueue_backend"));
			add_action("admin_head", array($this, "bkf_fonts"), 1);
		}
	}
	
	function bkf_enqueue_frontend()
	{
		wp_enqueue_script("bkf_select", BKF_URL . "assets/js/select.js",'','','all' );
		wp_enqueue_style("bkf_bkf", BKF_URL . "assets/css/bkf.css", array(),"1","all" );
		wp_enqueue_style("bkf_default", BKF_URL . "assets/css/default.css", array(),"1","all" );
		wp_enqueue_style("bkf_calendar", BKF_URL . "assets/css/calendar.css", array(),"1","all" );
		if(!is_admin()){
			wp_enqueue_script( 'jquery-ui-datepicker' );
	        wp_register_style( 'jquery-ui', '//code.jquery.com/ui/1.13.2/themes/overcast/jquery-ui.css' );
	        wp_enqueue_style( 'jquery-ui' );
		}
	    global $post_type;
		if ( 'bkf_petals_order' === $post_type ) {
			wp_enqueue_style("bkf_petals", BKF_URL . "assets/css/petals.css", array(),"1","all" );
		}
	}

	function bkf_enqueue_backend($hooks)
	{
		wp_enqueue_script("bkf_select", BKF_URL . "assets/js/select.js",'','','all' );
		wp_enqueue_style('dashicons');
		wp_enqueue_script("bkf_copy", BKF_URL . "assets/js/copy.js",'','','all' );
		wp_enqueue_script("fullcalendar", BKF_URL . "lib/fullcalendar/dist/index.global.min.js",'','','all' );
		if(!strpos(get_current_screen()->id,'page_gf_')){
			wp_enqueue_script( 'jquery-ui-datepicker' );
	        wp_register_style( 'jquery-ui', '//code.jquery.com/ui/1.13.2/themes/overcast/jquery-ui.css' );
	        wp_enqueue_style( 'jquery-ui' );
		}
		if(get_post_type() == 'bkf_petals_order'){
			wp_dequeue_script( 'autosave' );
			wp_enqueue_script("bkf_petals", BKF_URL . "assets/js/petals.js",'','','all' );
		}
	}
	
	function bkf_fonts() {
		?>
		<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
		<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
		<link rel="preconnect" href="https://fonts.googleapis.com">
		<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
		<link href="https://fonts.googleapis.com/css2?family=Oxygen:wght@300;400;700&display=swap" rel="stylesheet">
		<?php
}

}