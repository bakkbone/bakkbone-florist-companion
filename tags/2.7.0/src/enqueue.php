<?php

/**
 * @author BAKKBONE Australia
 * @package BkfEnqueueStyles
 * @license GNU General Public License (GPL) 3.0
**/

defined("BKF_EXEC") or die("Ah, sweet silence.");

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
		<style id="bkf_fonts">
			/* latin-ext */
			@font-face {
			  font-family: 'Oxygen';
			  font-style: normal;
			  font-weight: 300;
			  font-display: swap;
			  src: url(<?php echo BKF_URL . 'assets/fonts/Oxygen'; ?>/2sDcZG1Wl4LcnbuCJW8zZmW5Kb8VZBHR.woff2) format('woff2');
			  unicode-range: U+0100-02AF, U+0304, U+0308, U+0329, U+1E00-1E9F, U+1EF2-1EFF, U+2020, U+20A0-20AB, U+20AD-20CF, U+2113, U+2C60-2C7F, U+A720-A7FF;
			}
			/* latin */
			@font-face {
			  font-family: 'Oxygen';
			  font-style: normal;
			  font-weight: 300;
			  font-display: swap;
			  src: url(<?php echo BKF_URL . 'assets/fonts/Oxygen'; ?>/2sDcZG1Wl4LcnbuCJW8zaGW5Kb8VZA.woff2) format('woff2');
			  unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+0304, U+0308, U+0329, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
			}
			/* latin-ext */
			@font-face {
			  font-family: 'Oxygen';
			  font-style: normal;
			  font-weight: 400;
			  font-display: swap;
			  src: url(<?php echo BKF_URL . 'assets/fonts/Oxygen'; ?>/2sDfZG1Wl4LcnbuKgE0mRUe0A4Uc.woff2) format('woff2');
			  unicode-range: U+0100-02AF, U+0304, U+0308, U+0329, U+1E00-1E9F, U+1EF2-1EFF, U+2020, U+20A0-20AB, U+20AD-20CF, U+2113, U+2C60-2C7F, U+A720-A7FF;
			}
			/* latin */
			@font-face {
			  font-family: 'Oxygen';
			  font-style: normal;
			  font-weight: 400;
			  font-display: swap;
			  src: url(<?php echo BKF_URL . 'assets/fonts/Oxygen'; ?>/2sDfZG1Wl4LcnbuKjk0mRUe0Aw.woff2) format('woff2');
			  unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+0304, U+0308, U+0329, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
			}
			/* latin-ext */
			@font-face {
			  font-family: 'Oxygen';
			  font-style: normal;
			  font-weight: 700;
			  font-display: swap;
			  src: url(<?php echo BKF_URL . 'assets/fonts/Oxygen'; ?>/2sDcZG1Wl4LcnbuCNWgzZmW5Kb8VZBHR.woff2) format('woff2');
			  unicode-range: U+0100-02AF, U+0304, U+0308, U+0329, U+1E00-1E9F, U+1EF2-1EFF, U+2020, U+20A0-20AB, U+20AD-20CF, U+2113, U+2C60-2C7F, U+A720-A7FF;
			}
			/* latin */
			@font-face {
			  font-family: 'Oxygen';
			  font-style: normal;
			  font-weight: 700;
			  font-display: swap;
			  src: url(<?php echo BKF_URL . 'assets/fonts/Oxygen'; ?>/2sDcZG1Wl4LcnbuCNWgzaGW5Kb8VZA.woff2) format('woff2');
			  unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+0304, U+0308, U+0329, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
			}
		</style>
		<?php
}

}