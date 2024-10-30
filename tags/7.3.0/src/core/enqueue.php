<?php
/**
 * @author BAKKBONE Australia
 * @package BKF_Enqueue
 * @license GNU General Public License (GPL) 3.0
**/

defined("__BKF_EXEC__") or die("Ah, sweet silence.");

class BKF_Scripts {

	function __construct(){
		$frontend = ['global', 'frontend'];
		$backend = ['global', 'backend'];
		
		foreach ($frontend as $function) {
			add_action('wp_enqueue_scripts', [$this, $function]);
		}
		foreach ($backend as $function) {
			add_action('admin_enqueue_scripts', [$this, $function]);
		}
		add_action("admin_head", [$this, 'fonts'], 1);
	}
	
	public static function has_block_checkout() {
		$checkout = wc_get_page_id('checkout');
		return has_block('woocommerce/checkout', $checkout);
	}

	function global(){
		wp_register_script('jqueryblockui', __BKF_URL__ . 'lib/blockui/jquery.blockUI.js', ['jquery'], __BKF_VERSION__);
		$min = bkf_debug(true) ? '' : '.min';
		wp_register_style('jquery-ui-overcast', '//code.jquery.com/ui/1.13.2/themes/overcast/jquery-ui.css');
		wp_register_style('jquery-ui-dark-hive', '//code.jquery.com/ui/1.13.2/themes/dark-hive/jquery-ui.css');
		wp_register_style('select2css', __BKF_URL__ . "lib/select2/css/select2{$min}.css");
		wp_enqueue_style("bkf_bkf", __BKF_URL__ . "assets/css/bkf{$min}.css", [], __BKF_VERSION__, "all" );
		wp_enqueue_style("bkf_calendar", __BKF_URL__ . "assets/css/calendar{$min}.css", [], __BKF_VERSION__, "all");
		global $post_type;
		if ( 'bkf_petals_order' === $post_type ) {
			wp_enqueue_style("bkf_petals", __BKF_URL__ . "assets/css/petals{$min}.css", [], __BKF_VERSION__, "all");
		}
	}

	function frontend(){
		$min = bkf_debug(true) ? '' : '.min';
		if (is_checkout() && !$this->has_block_checkout() && bkf_not_nonfloral()) {
		    
		    $cart = WC()->cart->get_cart();
	        $dateslist = bkf_get_checkout_datepicker_dates($cart);
		    
			wp_enqueue_script("bkf_dd", __BKF_URL__ . "assets/js/dd{$min}.js", ['jquery'], __BKF_VERSION__);

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
		    	'debug'			=> bkf_debug(true),
		    	'pickup'		=> bkf_shop_has_pickup() ? true : false,
		    ];
		    $dd_inline_script = 'const bkf_dd_options = '.json_encode($vars).'; const bkf_dd_options_ts = '.$all_options.';';
		    wp_add_inline_script( 'bkf_dd', $dd_inline_script, 'before' );
		}
		wp_enqueue_script( 'jquery-ui-datepicker' );
		wp_enqueue_style( 'jquery-ui-overcast' );
		global $post_type;
		if ( 'bkf_petals_order' === $post_type ) {
			wp_enqueue_script('select2', __BKF_URL__ . "lib/select2/js/select2.full{$min}.js", ['jquery'], __BKF_VERSION__);
			wp_enqueue_style('select2css', '', __BKF_VERSION__);
		}
		wp_enqueue_script( 'bkf_suburb_search', __BKF_URL__ . "assets/js/suburb_search{$min}.js", [], __BKF_VERSION__ );

	    $url_ss = admin_url('admin-ajax.php?action=bkf_search_suburbs_frontend&query=');
	    $inline_script_ss = 'var thisAjaxUrl = "' . $url_ss . '";';
	    wp_add_inline_script( 'bkf_suburb_search', $inline_script_ss, 'before' );

	}
	
	function backend(){
		$min = bkf_debug(true) ? '' : '.min';
		wp_enqueue_script('select2', __BKF_URL__ . "lib/select2/js/select2.full{$min}.js", ['jquery'], __BKF_VERSION__);
		wp_enqueue_style('select2css', '', [], __BKF_VERSION__);
		wp_enqueue_style('dashicons');
		wp_enqueue_script("bkf_copy", __BKF_URL__ . "assets/js/copy{$min}.js", ['jquery', 'jqueryblockui'], __BKF_VERSION__, 'all');
		
		$debug = bkf_debug(true) ? 'true' : 'false';
		$copyvars = [
			'orderTitle'	=> esc_html__('Order #%s', 'bakkbone-florist-companion'),
			'noResults'		=> wp_kses_post(__('No results for <strong>%s</strong>', 'bakkbone-florist-companion')),
			'pickupText'	=> wp_kses_post(__('<strong>Pickup:</strong> %s', 'bakkbone-florist-companion')),
			'delText'		=> wp_kses_post(__('<strong>Delivery:</strong> %s', 'bakkbone-florist-companion')),
			'recText'		=> wp_kses_post(__('<strong>Recipient:</strong> %s', 'bakkbone-florist-companion')),
			'subText'		=> '<strong>' . get_option('bkf_localisation_setting', ['global_label_suburb' => __('Suburb', 'bakkbone-florist-companion')])['global_label_suburb'] . ':</strong> %s',
			'cusText'		=> wp_kses_post(__('<strong>Customer:</strong> %s', 'bakkbone-florist-companion')),
			'wsText'		=> get_option('bkf_pdf_setting', ['ws_title' => __('Worksheet', 'bakkbone-florist-companion')])['ws_title'],
			'editText'		=> esc_html__('View/Edit Order', 'bakkbone-florist-companion'),
			'processingText'=> esc_html__('Processing...', 'bakkbone-florist-companion')
		];
		$inline_script_copy = 'var copyVars = ' . wp_json_encode($copyvars) . '; var bkfDebug = ' . $debug . '; var toCopy = "' . esc_html__('Click to copy to clipboard', 'bakkbone-florist-companion') . '"; var copied = "' . esc_html__('Copied to clipboard!', 'bakkbone-florist-companion') . '";';
		wp_add_inline_script( 'bkf_copy', $inline_script_copy );
		
		wp_enqueue_script("fullcalendar", __BKF_URL__ . "lib/fullcalendar/dist/index.global{$min}.js",'','','all' );
		if(!strpos(get_current_screen()->id,'page_gf_')){
			wp_enqueue_script( 'jquery-ui-datepicker', '', ['jquery'] );
			wp_enqueue_style( 'jquery-ui-dark-hive' );
		}
		if(get_post_type() == 'bkf_petals_order'){
			wp_dequeue_script('autosave');
			wp_enqueue_script("bkf_petals", __BKF_URL__ . "assets/js/petals{$min}.js", [], __BKF_VERSION__, 'all' );
		}
		if(get_current_screen()->id == 'toplevel_page_bkf_dc'){
			wp_enqueue_script('jquery-ui-core', '', ['jquery']);
			wp_enqueue_script('jquery-ui-button', '', ['jquery']);
			wp_enqueue_script('jquery-ui-dialog', '', ['jquery']);
			wp_enqueue_style( 'jquery-ui-dark-hive' );
		}
		wp_register_style("bkf_tinymce", __BKF_URL__ . "assets/tinymce-plugins/bkf_tinymce.css", [], __BKF_VERSION__);
		wp_enqueue_style("bkf_tinymce", '', [], __BKF_VERSION__);
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