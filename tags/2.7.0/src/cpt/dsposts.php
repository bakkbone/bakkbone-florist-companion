<?php

/**
 * @author BAKKBONE Australia
 * @package BkfDsPosts
 * @license GNU General Public License (GPL) 3.0
**/

defined("BKF_EXEC") or die("Ah, sweet silence.");

class BkfDsPosts{

	function __construct()
	{
		add_action("init", array($this, "bkfRegisterDeliverySuburbPost"));
		add_action("load-edit.php", array($this, "dspost_help_tab"));
		add_action("load-post.php", array($this, "dspost_help_tab"));
		add_action("load-post-new.php", array($this, "dspost_help_tab"));
	}
	
	function dspost_help_tab(){
		$screen = get_current_screen();
		if($screen->id == 'edit-bkf_delivery_suburb' || $screen->id == 'bkf_delivery_suburb'){
			$id = 'dspost_help';
			$callback = array($this, 'dspost_help');
			$screen->add_help_tab(array( 
			   'id' => $id,
			   'title' => BKF_HELP_TITLE,
			   'callback' => $callback
			));
		}
	}
	
	function dspost_help(){
		?>
		<h2><?php echo BKF_HELP_SUBTITLE; ?></h2>
			<a href="https://plugins.bkbn.au/docs/bkf/suburbs-post/using-delivery-suburbs-posts/" target="_blank">https://plugins.bkbn.au/docs/bkf/suburbs-post/using-delivery-suburbs-posts/</a>
		<?php
	}
	
	function bkfRegisterDeliverySuburbPost()
	{
		
		$labels = array(
			"name"			=> _x("Delivery Suburbs", "post type general name", "bakkbone-florist-companion"),
			"singular_name"		=> _x("Delivery Suburb", "post type singular name", "bakkbone-florist-companion"),
			"menu_name"		=> _x("Delivery Suburbs", "admin menu", "bakkbone-florist-companion"),
			"name_admin_bar"	=> _x("Delivery Suburb", "add new on admin bar", "bakkbone-florist-companion"),
			"add_new"		=> _x("Add New", "Add New", "bakkbone-florist-companion"),
			"add_new_item"		=> __("Add New Suburb", "bakkbone-florist-companion"),
			"new_item"		=> __("New Suburb", "bakkbone-florist-companion"),
			"edit_item"		=> __("Edit Suburb", "bakkbone-florist-companion"),
			"view_item"		=> __("View Suburb", "bakkbone-florist-companion"),
			"all_items"		=> __("All Suburbs", "bakkbone-florist-companion"),
			"search_items"		=> __("Search Suburbs", "bakkbone-florist-companion"),
			"parent_item_colon"	=> __("Parent Suburbs:", "bakkbone-florist-companion"),
			"not_found"		=> __("Not found", "bakkbone-florist-companion"),
			"not_found_in_trash"	=> __("Not found in trash", "bakkbone-florist-companion"),
			"attributes"	=> __("Suburb Attributes", "bakkbone-florist-companion"),
			"insert_into_item"	=> __("Insert into suburb", "bakkbone-florist-companion"),
			"uploaded_to_this_item"	=> __("Uploaded to this suburb", "bakkbone-florist-companion"),
			"filter_items_list"	=> __("Filter suburbs list", "bakkbone-florist-companion"),
			"items_list_navigation"	=> __("Suburbs list navigation", "bakkbone-florist-companion"),
			"items_list"	=> __("Suburbs list", "bakkbone-florist-companion"),
			"item_published"	=> __("Suburb published.", "bakkbone-florist-companion"),
			"item_published_privately"	=> __("Suburb published privately.", "bakkbone-florist-companion"),
			"item_reverted_to_draft"	=> __("Suburb reverted to draft.", "bakkbone-florist-companion"),
			"item_scheduled"	=> __("Suburb scheduled.", "bakkbone-florist-companion"),
			"item_updated"	=> __("Suburb updated.", "bakkbone-florist-companion"),
			"item_link"	=> __("Suburb link", "bakkbone-florist-companion"),
			"item_link_description"	=> __("A link to a Delivery Suburb.", "bakkbone-florist-companion")
		);
		
		$capability_type = "post";
		$supports = array("title", "editor");
		$args = array(
			"label"				=> __("Delivery Suburbs", "bakkbone-florist-companion"),
			"labels"			=> $labels,
			"menu_icon"			=> BKF_SVG_SUBURB,
			"public"			=> true,
			"publicly_queryable"		=> true,
			"show_ui"			=> true,
			"show_in_menu"			=> true,
			"query_var"			=> __("suburb", "bakkbone-florist-companion"),
			"show_in_nav_menus"		=> true,
			"show_in_admin_bar"		=> true,
			"show_in_rest"			=> true,
			"rest_base"			=> __("suburb", "bakkbone-florist-companion"),
			"rewrite"			=> array("slug" => "suburb"),
			"capability_type"		=> $capability_type,
			"hierarchical"			=> false,
			"has_archive"			=> true,
			"menu_position"			=> null,
			"taxonomies"			=> array(),
			"supports"			=> $supports,
			"delete_with_user"		=> false
		);
		register_post_type("bkf_delivery_suburb", $args);
		flush_rewrite_rules();
	}
}