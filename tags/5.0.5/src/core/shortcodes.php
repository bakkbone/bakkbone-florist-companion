<?php
/**
 * @author BAKKBONE Australia
 * @package BKF_Shortcodes
 * @license GNU General Public License (GPL) 3.0
**/

defined("__BKF_EXEC__") or die("Ah, sweet silence.");

class BKF_Shortcodes{

	function __construct(){
		add_action("init", [$this, "init"]);
		add_filter("mce_external_plugins", [$this, 'mce_external_plugins']);
		add_action("admin_enqueue_scripts", [$this, "admin_enqueue_scripts"]);
		add_action("wp_enqueue_scripts", [$this, "wp_enqueue_scripts"]);
		add_filter("mce_buttons", [$this, 'mce_buttons']);
	}

	function init(){
		add_shortcode("bkf_page_title", [$this, 'bkf_page_title']);
		add_shortcode("bkf_site_title", [$this, 'bkf_site_title']);
		add_shortcode("bkf_suburb_search", [$this, 'bkf_suburb_search']);
	}

	function bkf_page_title($atts, $content, $tag){
		$atts = shortcode_atts([], $atts, $tag);
		$content = get_the_title();		
		
		return $content;
	}
	
	function bkf_site_title($atts, $content, $tag){
		$atts = shortcode_atts([], $atts, $tag);
		$content = get_bloginfo('name');		
		
		return $content;
	}
	
	function bkf_suburb_search($atts, $content, $tag){
		$atts = shortcode_atts([
			'placeholder'	=> __('Start typing a suburb to check the delivery cost...', 'bakkbone-florist-companion'),
			'noresults'		=> __('No suburbs matched your search.', 'bakkbone-florist-companion'),
			'header'		=> __('We deliver to these suburbs matching your search:', 'bakkbone-florist-companion')
		], $atts, $tag);
		$placeholder = esc_html($atts['placeholder']);
		$noresults = esc_html($atts['noresults']);
		$header = esc_html($atts['header']);
		
		$content= '<div class="bkf-suburb-search">
			<form>
		 		<input type="text" class="bkf-suburb-search-input" placeholder="'.stripslashes($placeholder).'" onkeyup="bkfSuburbSearchShowResult(this.value)">
				<div id="bkflivesearch" style="display: none;" data-noresults="'.$noresults.'" data-header="'.$header.'"></div>
			</form>
		</div>';
		
		return $content;
	}

	function mce_external_plugins($plugin_array){
		$plugin_array["bkf_page_title"] = __BKF_URL__ . "/assets/tinymce-plugins/bkf_page_title.js";
		$plugin_array["bkf_site_title"] = __BKF_URL__ . "/assets/tinymce-plugins/bkf_site_title.js";
		return $plugin_array;
	}

	function admin_enqueue_scripts(){
		wp_register_style("bkf_tinymce", __BKF_URL__ . "/assets/tinymce-plugins/bkf_tinymce.css",[],"1.0.1" );
		wp_enqueue_style("bkf_tinymce");
	}
	
	function wp_enqueue_scripts(){
	    wp_enqueue_script( 'bkf_suburb_search', __BKF_URL__ . '/assets/js/suburb_search.js', [], '1.0.1' );

	    // Add inline script to pass PHP variable to JS file
	    $url = admin_url('admin-ajax.php?action=bkf_search_suburbs_frontend&query=');
	    $inline_script = 'var thisAjaxUrl = "' . $url . '";';
	    wp_add_inline_script( 'bkf_suburb_search', $inline_script );
	}

	function mce_buttons($buttons){
		$buttons[] = 'bkf_page_title';
		$buttons[] = 'bkf_site_title';
		return $buttons;
	}

}