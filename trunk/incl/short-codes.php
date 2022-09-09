<?php

/**
 * @author BAKKBONE Australia
 * @package BkfShortCodes
 * @license GNU General Public License (GPL) 3.0
**/


defined("BKF_EXEC") or die("Silent is golden");

/**
 * BkfShortCodes
**/
class BkfShortCodes{


	/**
	 * BkfShortCodes:__construct()
	**/
	function __construct()
	{
		// bkf_page_title
		add_action("init",array($this,"bkfPageTitle"));
		add_action("init",array($this,"bkfPageTitleTinymce"));
		
		// bkf_site_title
		add_action("init",array($this,"bkfSiteTitle"));
		add_action("init",array($this,"bkfSiteTitleTinymce"));
		
	}
	
	
	// TODO: LAYOUT FOR `PAGE-TITLE`
	/**
	 * BkfShortCodes:bkfPageTitle()
	**/
	function bkfPageTitle(){
		add_shortcode("bkf_page_title", array($this, "bkfPageTitleShortcode"));
	}
	
	
	/**
	 * BkfShortCodes:bkfPageTitleShortcode($atts, $content, $tag)
	**/
	function bkfPageTitleShortcode($atts, $content, $tag){
			// shortcode option/attributes
		$atts = shortcode_atts(
			array(
			), $atts, $tag);
		
		
		// CUSTOM-CODE
return get_the_title();		
		
		
		return $content;
	}
	
	
	/**
	 * BkfShortCodes:bkfPageTitleTinymce()
	**/
	function bkfPageTitleTinymce(){
		add_filter("mce_external_plugins", array($this, "bkfPageTitleTinymcePlugin"));
		add_action("admin_enqueue_scripts",array($this,"bkfPageTitleTinymceStyle"));
		add_filter("mce_buttons", array($this, "bkfPageTitleAddTinymceButton"));
	}
	
	
	/**
	 * BkfShortCodes:bkfPageTitleTinymcePlugin($plugin_array)
	**/
	function bkfPageTitleTinymcePlugin($plugin_array){
		$plugin_array["bkf_page_title"] = BKF_URL . "/assets/tinymce-plugins/bkf_page_title/bkf_page_title.js";
		return $plugin_array;
	}
	
	
	/**
	 * BkfShortCodes:bkfPageTitleTinymceStyle($hooks)
	**/
	function bkfPageTitleTinymceStyle($buttons){
		wp_register_style("bkf_page_title_style", BKF_URL . "/assets/tinymce-plugins/bkf_page_title/bkf_page_title.css",array(),"1.0.1" );
		wp_enqueue_style("bkf_page_title_style");
	}
	
	
	/**
	 * BkfShortCodes:bkfPageTitleAddTinymceButton($buttons)
	**/
	function bkfPageTitleAddTinymceButton($buttons){
		array_push($buttons, "bkf_page_title"); 
		return $buttons;
	}
	
	
	// TODO: LAYOUT FOR `SITE-TITLE`
	/**
	 * BkfShortCodes:bkfSiteTitle()
	**/
	function bkfSiteTitle(){
		add_shortcode("bkf_site_title", array($this, "bkfSiteTitleShortcode"));
	}
	
	
	/**
	 * BkfShortCodes:bkfSiteTitleShortcode($atts, $content, $tag)
	**/
	function bkfSiteTitleShortcode($atts, $content, $tag){
			// shortcode option/attributes
		$atts = shortcode_atts(
			array(
			), $atts, $tag);
		
		
		// CUSTOM-CODE
return get_bloginfo($show = 'name');		
		
		
		return $content;
	}
	
	
	/**
	 * BkfShortCodes:bkfSiteTitleTinymce()
	**/
	function bkfSiteTitleTinymce(){
		add_filter("mce_external_plugins", array($this, "bkfSiteTitleTinymcePlugin"));
		add_action("admin_enqueue_scripts",array($this,"bkfSiteTitleTinymceStyle"));
		add_filter("mce_buttons", array($this, "bkfSiteTitleAddTinymceButton"));
	}
	
	
	/**
	 * BkfShortCodes:bkfSiteTitleTinymcePlugin($plugin_array)
	**/
	function bkfSiteTitleTinymcePlugin($plugin_array){
		$plugin_array["bkf_site_title"] = BKF_URL . "/assets/tinymce-plugins/bkf_site_title/bkf_site_title.js";
		return $plugin_array;
	}
	
	
	/**
	 * BkfShortCodes:bkfSiteTitleTinymceStyle($hooks)
	**/
	function bkfSiteTitleTinymceStyle($buttons){
		wp_register_style("bkf_site_title_style", BKF_URL . "/assets/tinymce-plugins/bkf_site_title/bkf_site_title.css",array(),"1.0.1" );
		wp_enqueue_style("bkf_site_title_style");
	}
	
	
	/**
	 * BkfShortCodes:bkfSiteTitleAddTinymceButton($buttons)
	**/
	function bkfSiteTitleAddTinymceButton($buttons){
		array_push($buttons, "bkf_site_title"); 
		return $buttons;
	}
	
	
}
