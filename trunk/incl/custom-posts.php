<?php

/**
 * @author BAKKBONE Australia
 * @package BkfCustomPosts
 * @license GNU General Public License (GPL) 3.0
**/



defined("BKF_EXEC") or die("Silent is golden");

/**
 * BkfCustomPosts
**/
class BkfCustomPosts{


	/**
	 * BkfCustomPosts:__construct()
	**/
	function __construct()
	{
		// delivery-suburb
		add_action("init", array($this, "bkfRegisterDeliverySuburbPost"));
		
	}
	
	
	/**
	 * BkfCustomPosts:bkfRegisterDeliverySuburbPost()
	 * ref: https://developer.wordpress.org/reference/functions/register_post_type/
	**/
	function bkfRegisterDeliverySuburbPost()
	{
		
		// labels for posts
		$labels = array(
			"name"					=> _x("Delivery Suburbs", "post type general name", "bakkbone-florist-companion"),
			"singular_name"			=> _x("Delivery Suburb", "post type singular name", "bakkbone-florist-companion"),
			"menu_name"				=> _x("Delivery Suburbs", "admin menu", "bakkbone-florist-companion"),
			"name_admin_bar"		=> _x("Delivery Suburbs", "add new on admin bar", "bakkbone-florist-companion"),
			"add_new"				=> _x("Add New", "Add New", "bakkbone-florist-companion"),
			"add_new_item"			=> __("Add New Suburb", "bakkbone-florist-companion"),
			"new_item"				=> __("New Suburb", "bakkbone-florist-companion"),
			"edit_item"				=> __("Edit Suburb", "bakkbone-florist-companion"),
			"view_item"				=> __("View Suburb", "bakkbone-florist-companion"),
			"all_items"				=> __("All Suburbs", "bakkbone-florist-companion"),
			"search_items"			=> __("Search Suburbs", "bakkbone-florist-companion"),
			"parent_item_colon"		=> __("Parent Suburbs:", "bakkbone-florist-companion"),
			"not_found"				=> __("Not found", "bakkbone-florist-companion"),
			"not_found_in_trash"	=> __("Not found in trash", "bakkbone-florist-companion")
		);
		
		// roles
		$capability_type = "post";
		$capabilities = array();
		
		// support or features
		$supports = array("title");
		
		// arg
		$args = array(
			"label"					=>__("Delivery Suburbs", "bakkbone-florist-companion"),
			"labels"				=> $labels,
			"description"			=> __("", "bakkbone-florist-companion"),
			"menu_icon"				=> "dashicons-admin-multisite",
			"public"				=> true,
			"publicly_queryable"	=> true,
			"show_ui"				=> true,
			"show_in_menu"			=> true,
			"query_var"				=> true,
			"show_in_nav_menus"		=> true,
			"show_in_admin_bar"		=> false,
			"show_in_rest"			=> true,
			"rewrite"				=> array("slug" => get_option('bkf_suburb_slug', $default = "suburb")),
			"capability_type"		=> $capability_type,
			"capabilities"			=> $capabilities,
			"hierarchical"			=> false,
			"has_archive"			=> true,
			"menu_position"			=> null,
			"taxonomies"			=> array(),
			"supports"				=> $supports
		);
		register_post_type("bkf_delivery_suburb", $args);
		flush_rewrite_rules();
	}
	
	
		
		
		
}

