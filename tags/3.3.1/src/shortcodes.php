<?php
/**
 * @author BAKKBONE Australia
 * @package BKF_Shortcodes
 * @license GNU General Public License (GPL) 3.0
**/

defined("BKF_EXEC") or die("Ah, sweet silence.");

class BKF_Shortcodes{

	function __construct(){
		add_action("init",array($this,"bkfPageTitle"));
		add_action("init",array($this,"bkfPageTitleTinymce"));
		add_action("init",array($this,"bkfSiteTitle"));
		add_action("init",array($this,"bkfSiteTitleTinymce"));
	}

	function bkfPageTitle(){
		add_shortcode("bkf_page_title", [$this, 'bkfPageTitleShortcode']);
	}

	function bkfPageTitleShortcode($atts, $content, $tag){
		$atts = shortcode_atts(
			array(
			), $atts, $tag);
			
			$content = get_the_title();		
		
		return $content;
	}
	
	function bkfPageTitleTinymce(){
		add_filter("mce_external_plugins", [$this, 'bkfPageTitleTinymcePlugin']);
		add_action("admin_enqueue_scripts",array($this,"bkfPageTitleTinymceStyle"));
		add_filter("mce_buttons", [$this, 'bkfPageTitleAddTinymceButton']);
	}
	
	function bkfPageTitleTinymcePlugin($plugin_array){
		$plugin_array["bkf_page_title"] = BKF_URL . "/assets/tinymce-plugins/bkf_page_title/bkf_page_title.js";
		return $plugin_array;
	}

	function bkfPageTitleTinymceStyle($buttons){
		wp_register_style("bkf_page_title_style", BKF_URL . "/assets/tinymce-plugins/bkf_page_title/bkf_page_title.css",[],"1.0.1" );
		wp_enqueue_style("bkf_page_title_style");
	}

	function bkfPageTitleAddTinymceButton($buttons){
		array_push($buttons, "bkf_page_title"); 
		return $buttons;
	}

	function bkfSiteTitle(){
		add_shortcode("bkf_site_title", [$this, 'bkfSiteTitleShortcode']);
	}

	function bkfSiteTitleShortcode($atts, $content, $tag){
		$atts = shortcode_atts(
			array(
			), $atts, $tag);
		
			$content = get_bloginfo($show = 'name');		
		
		return $content;
	}

	function bkfSiteTitleTinymce(){
		add_filter("mce_external_plugins", [$this, 'bkfSiteTitleTinymcePlugin']);
		add_action("admin_enqueue_scripts",array($this,"bkfSiteTitleTinymceStyle"));
		add_filter("mce_buttons", [$this, 'bkfSiteTitleAddTinymceButton']);
	}

	function bkfSiteTitleTinymcePlugin($plugin_array){
		$plugin_array["bkf_site_title"] = BKF_URL . "/assets/tinymce-plugins/bkf_site_title/bkf_site_title.js";
		return $plugin_array;
	}

	function bkfSiteTitleTinymceStyle($buttons){
		wp_register_style("bkf_site_title_style", BKF_URL . "/assets/tinymce-plugins/bkf_site_title/bkf_site_title.css",[],"1.0.1" );
		wp_enqueue_style("bkf_site_title_style");
	}

	function bkfSiteTitleAddTinymceButton($buttons){
		array_push($buttons, "bkf_site_title"); 
		return $buttons;
	}
	
}