<?php
/**
 * @author BAKKBONE Australia
 * @package BKF_Enqueue
 * @license GNU General Public License (GPL) 3.0
**/

defined("__BKF_EXEC__") or die("Ah, sweet silence.");

class BKF_Enqueue{

	function __construct(){
		add_action("wp_enqueue_scripts", [$this, 'global']);
		add_action("wp_enqueue_scripts", [$this, 'frontend']);
		add_action("admin_enqueue_scripts", [$this, 'global']);
		add_action("admin_enqueue_scripts", [$this, 'backend']);
		add_action("admin_head", [$this, 'fonts'], 1);
	}
	
	function has_block_checkout() {
		$checkout = wc_get_page_id('checkout');
		return has_block('woocommerce/checkout', $checkout);
	}

	function global(){
		$min = (defined('SCRIPT_DEBUG') && SCRIPT_DEBUG) || bkf_debug() ? '' : '.min';
		wp_register_style('jquery-ui-overcast', '//code.jquery.com/ui/1.13.2/themes/overcast/jquery-ui.css');
		wp_register_style('jquery-ui-dark-hive', '//code.jquery.com/ui/1.13.2/themes/dark-hive/jquery-ui.css');
		wp_register_style('select2css', __BKF_URL__ . "lib/select2/css/select2{$min}.css");
		wp_enqueue_style("bkf_bkf", __BKF_URL__ . "assets/css/bkf{$min}.css", [],"1","all" );
		wp_enqueue_style("bkf_calendar", __BKF_URL__ . "assets/css/calendar{$min}.css", [],"1","all" );
		global $post_type;
		if ( 'bkf_petals_order' === $post_type ) {
			wp_enqueue_style("bkf_petals", __BKF_URL__ . "assets/css/petals{$min}.css", [],"1","all" );
		}
	}

	function frontend(){
		$min = (defined('SCRIPT_DEBUG') && SCRIPT_DEBUG) || bkf_debug() ? '' : '.min';
		if (is_checkout() && !$this->has_block_checkout()) {
		    
		    $cart = WC()->cart->get_cart();
	        $dateslist = bkf_get_checkout_datepicker_dates($cart);
		    
			wp_enqueue_script("bkf_dd", __BKF_URL__ . "assets/js/dd{$min}.js", ['jquery']);
			wp_enqueue_script("bkf_pickup", __BKF_URL__ . "assets/js/pickup{$min}.js", ['jquery']);

			$ts = bkf_get_timeslots();
			$all_options = '[';
			foreach($ts as $slot) {
				$all_options .= '{text: "'.date("g:i a", strtotime($slot['start'])).' - '.date("g:i a", strtotime($slot['end'])).'", slot: "'.$slot['id'].'", method: "'.$slot['method'].'", day: "'.$slot['day'].'"},';
			}
			$all_options .= ']';
		    $url = admin_url('admin-ajax.php');
		    $vars = [
		    	'datesListFull'	=> $dateslist,
		    	'ajax_url'		=> $url,
		    	'maxDate'		=> '+'.get_option('bkf_ddi_setting')['ddi'].'w',
		    	'mrText'		=> __('Your selected delivery method is not available on this day', 'bakkbone-florist-companion'),
		    	'sdcMethod'		=> __('Order Cutoff has passed for your selected delivery method on this day', 'bakkbone-florist-companion'),
		    	'debug'			=> (defined('SCRIPT_DEBUG') && SCRIPT_DEBUG) || bkf_debug() ? true : false,
		    	'pickup'		=> bkf_shop_has_pickup() ? true : false,
		    ];
		    $dd_inline_script = 'const bkf_dd_options = '.json_encode($vars).'; const bkf_dd_options_ts = '.$all_options.';';
		    wp_add_inline_script( 'bkf_dd', $dd_inline_script, 'before' );
		}
		wp_enqueue_script( 'jquery-ui-datepicker' );
		wp_enqueue_style( 'jquery-ui-overcast' );
		global $post_type;
		if ( 'bkf_petals_order' === $post_type ) {
			wp_enqueue_script('select2', __BKF_URL__ . "lib/select2/js/select2.full{$min}.js", 'jquery');
			wp_enqueue_style('select2css');
		}
		wp_enqueue_script( 'bkf_suburb_search', __BKF_URL__ . "assets/js/suburb_search{$min}.js" );

	    $url_ss = admin_url('admin-ajax.php?action=bkf_search_suburbs_frontend&query=');
	    $inline_script_ss = 'var thisAjaxUrl = "' . $url_ss . '";';
	    wp_add_inline_script( 'bkf_suburb_search', $inline_script_ss, 'before' );

	}
	
	function backend(){
		$min = (defined('SCRIPT_DEBUG') && SCRIPT_DEBUG) || bkf_debug() ? '' : '.min';
		wp_enqueue_script('select2', __BKF_URL__ . "lib/select2/js/select2.full{$min}.js", 'jquery');
		wp_enqueue_style('select2css');
		wp_enqueue_style('dashicons');
		wp_enqueue_script("bkf_copy", __BKF_URL__ . "assets/js/copy{$min}.js",'jquery','','all' );
		$inline_script_copy = 'var toCopy = "' . esc_html__('Click to copy to clipboard', 'bakkbone-florist-companion') . '"; var copied = "' . esc_html__('Copied to clipboard!', 'bakkbone-florist-companion') . '";';
		wp_add_inline_script( 'bkf_copy', $inline_script_copy );
		wp_enqueue_script("fullcalendar", __BKF_URL__ . "lib/fullcalendar/dist/index.global{$min}.js",'','','all' );
		if(!strpos(get_current_screen()->id,'page_gf_')){
			wp_enqueue_script( 'jquery-ui-datepicker' );
			wp_enqueue_style( 'jquery-ui-dark-hive' );
		}
		if(get_post_type() == 'bkf_petals_order'){
			wp_dequeue_script( 'autosave' );
			wp_enqueue_script("bkf_petals", __BKF_URL__ . "assets/js/petals{$min}.js",'','','all' );
		}
		if(get_current_screen()->id == 'toplevel_page_bkf_dc'){
			wp_enqueue_script('jquery-ui-core', 'jquery');
			wp_enqueue_script('jquery-ui-button', 'jquery');
			wp_enqueue_script('jquery-ui-dialog', 'jquery');
			wp_enqueue_style( 'jquery-ui-dark-hive' );
		}
		wp_register_style("bkf_tinymce", __BKF_URL__ . "/assets/tinymce-plugins/bkf_tinymce.css");
		wp_enqueue_style("bkf_tinymce");
	}

	function fonts() {
		?>
		<style id="bkf_fonts">
			/* latin-ext */
			@font-face {
			  font-family: 'Oxygen';
			  font-style: normal;
			  font-weight: 300;
			  font-display: swap;
			  src: url(<?php echo __BKF_URL__ . 'assets/fonts/Oxygen'; ?>/2sDcZG1Wl4LcnbuCJW8zZmW5Kb8VZBHR.woff2) format('woff2');
			  unicode-range: U+0100-02AF, U+0304, U+0308, U+0329, U+1E00-1E9F, U+1EF2-1EFF, U+2020, U+20A0-20AB, U+20AD-20CF, U+2113, U+2C60-2C7F, U+A720-A7FF;
			}
			/* latin */
			@font-face {
			  font-family: 'Oxygen';
			  font-style: normal;
			  font-weight: 300;
			  font-display: swap;
			  src: url(<?php echo __BKF_URL__ . 'assets/fonts/Oxygen'; ?>/2sDcZG1Wl4LcnbuCJW8zaGW5Kb8VZA.woff2) format('woff2');
			  unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+0304, U+0308, U+0329, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
			}
			/* latin-ext */
			@font-face {
			  font-family: 'Oxygen';
			  font-style: normal;
			  font-weight: 400;
			  font-display: swap;
			  src: url(<?php echo __BKF_URL__ . 'assets/fonts/Oxygen'; ?>/2sDfZG1Wl4LcnbuKgE0mRUe0A4Uc.woff2) format('woff2');
			  unicode-range: U+0100-02AF, U+0304, U+0308, U+0329, U+1E00-1E9F, U+1EF2-1EFF, U+2020, U+20A0-20AB, U+20AD-20CF, U+2113, U+2C60-2C7F, U+A720-A7FF;
			}
			/* latin */
			@font-face {
			  font-family: 'Oxygen';
			  font-style: normal;
			  font-weight: 400;
			  font-display: swap;
			  src: url(<?php echo __BKF_URL__ . 'assets/fonts/Oxygen'; ?>/2sDfZG1Wl4LcnbuKjk0mRUe0Aw.woff2) format('woff2');
			  unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+0304, U+0308, U+0329, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
			}
			/* latin-ext */
			@font-face {
			  font-family: 'Oxygen';
			  font-style: normal;
			  font-weight: 700;
			  font-display: swap;
			  src: url(<?php echo __BKF_URL__ . 'assets/fonts/Oxygen'; ?>/2sDcZG1Wl4LcnbuCNWgzZmW5Kb8VZBHR.woff2) format('woff2');
			  unicode-range: U+0100-02AF, U+0304, U+0308, U+0329, U+1E00-1E9F, U+1EF2-1EFF, U+2020, U+20A0-20AB, U+20AD-20CF, U+2113, U+2C60-2C7F, U+A720-A7FF;
			}
			/* latin */
			@font-face {
			  font-family: 'Oxygen';
			  font-style: normal;
			  font-weight: 700;
			  font-display: swap;
			  src: url(<?php echo __BKF_URL__ . 'assets/fonts/Oxygen'; ?>/2sDcZG1Wl4LcnbuCNWgzaGW5Kb8VZA.woff2) format('woff2');
			  unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+0304, U+0308, U+0329, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
			}
		</style>
		<?php
	}
	
}