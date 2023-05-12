<?php

/**
 * @author BAKKBONE Australia
 * @package BkfDdFilter
 * @license GNU General Public License (GPL) 3.0
**/

defined("BKF_EXEC") or die("Silence is golden");

class BkfDdFilter{
	
    function __construct(){
		add_action('pre_get_posts', array($this, 'action_parse_query'), PHP_INT_MAX, 1 );
		add_action('wp_enqueue_scripts', array($this, 'bkf_filter_enqueue_datepicker'));
		add_action('restrict_manage_posts', array($this, 'bkf_display_admin_shop_order_dd_filter'));
		add_filter('views_edit-shop_order', array($this, 'bkf_custom_filters'));
    }
	
	function action_parse_query($query){ 
	    global $pagenow;
		$post_type = '';
		if(isset($_GET['post_type'])){
			$post_type = $_GET['post_type'];
		}
		$show_all = 'false';
		$post_status = '';
		if(isset($_GET['show_all'])){
			$show_all = $_GET['show_all'];
		}
		if(isset($_GET['post_status'])){
			$post_status = $_GET['post_status'];
		}
		
		if($post_type == 'shop_order'){
			if($show_all == 'true'){
				$statuslist = wc_get_order_statuses();
				$status = array();
				foreach($statuslist as $key => $value){
					$status[] = $key;
				}
				$query->set('post_status', $status);
			} elseif ($post_status == '' || $post_status == null){
				$query->set('post_status', array("wc-processing","wc-made","wc-collect","wc-out","wc-relay","wc-scheduled","wc-new","wc-accept","wc-payment-await","wc-on-hold"));
			}
		    if ( isset( $_GET['dd_filter'] ) && $_GET['dd_filter'] != '' ) {
		        $meta_query = $query->get( 'meta_query' );
				if($meta_query == ''){
					$meta_query = array();
				}
		        $meta_query[] = array(
		            'meta_key' => '_delivery_timestamp',
		            'value'    => strtotime($_GET['dd_filter']),
		        );
		        $query->set( 'meta_query', $meta_query );
		    }
		}	
	}
	
	function bkf_custom_filters($views){
	    global $pagenow;
		$post_type = $_GET['post_type'];
		$show_all = 'false';
		$post_status = '';
		if(isset($_GET['show_all'])){
			$show_all = $_GET['show_all'];
		}
		if(isset($_GET['post_status'])){
			$post_status = $_GET['post_status'];
		}
		
		$statuslistfull = wc_get_order_statuses();
		$fullstatus = array();
		foreach($statuslistfull as $key => $value){
			$fullstatus[] = $key;
		}
		$fullargs = array('post_type' => 'shop_order', 'post_status' => $fullstatus, 'numberposts' => '-1');
		$fullquery = get_posts($fullargs);
		$fullcount = count($fullquery);
		
		$allstatus = array("wc-processing","wc-made","wc-collect","wc-out","wc-scheduled","wc-new","wc-accept");
		
		$url_to_redirect_full = add_query_arg(array('post_type' => 'shop_order', 'show_all' => 'true'), admin_url('edit.php'));
		$url_to_redirect_all = add_query_arg(array('post_type' => 'shop_order'), admin_url('edit.php'));
		$text_full_active = '<a href="%s" class="current" aria-current="page">'.__('All', 'bakkbone-florist-companion').' <span class="count">(%d)</span></a>';
		$text_all_active = '<a href="%s" class="current" aria-current="page">'.__('Active', 'bakkbone-florist-companion').' <span class="count">(%d)</span></a>';
		$text_full_inactive = '<a href="%s">'.__('All', 'bakkbone-florist-companion').' <span class="count">(%d)</span></a>';
		$text_all_inactive = '<a href="%s">'.__('Active', 'bakkbone-florist-companion').' <span class="count">(%d)</span></a>';
		
		$allquery = array();
		foreach($fullquery as $full){
			if(in_array($full->post_type, $allstatus)){
				$allquery[] = $full;
			}
		}
		$allcount = count($allquery);
		
		$func = new BakkboneFloristCompanion;
		if($post_type == 'shop_order'){
			if($show_all == 'true'){
				$views['all'] = sprintf($text_all_inactive, $url_to_redirect_all, $func->all_count());
				$views['full'] = sprintf($text_full_active, $url_to_redirect_full, $func->full_count());
			} elseif ($post_status == '' || $post_status == null){
				$views['all'] = sprintf($text_all_active, $url_to_redirect_all, $func->all_count());
				$views['full'] = sprintf($text_full_inactive, $url_to_redirect_full, $func->full_count());
			} else {
				$views['all'] = sprintf($text_all_inactive, $url_to_redirect_all, $func->all_count());
				$views['full'] = sprintf($text_full_inactive, $url_to_redirect_full, $func->full_count());
			}
		}		
		
		return $views;
	}

	function bkf_filter_enqueue_datepicker() {
	    global $pagenow, $post_type;
		if ( 'shop_order' === $post_type && 'edit.php' === $pagenow ) {
		 wp_enqueue_script( 'jquery-ui-datepicker' );
		 wp_register_style( 'jquery-ui', '//code.jquery.com/ui/1.13.2/themes/overcast/jquery-ui.css' );
		 wp_enqueue_style( 'jquery-ui' );
		}  
	}
	function bkf_display_admin_shop_order_dd_filter(){
	    global $pagenow, $post_type;
	    if( 'shop_order' === $post_type && 'edit.php' === $pagenow ) {
	        $current = isset($_GET['dd_filter'])? $_GET['dd_filter'] : '';
	        echo '<input type="text" name="dd_filter" id="dd_filter" placeholder="'.__('Delivery Date','bakkbone-florist-companion').'" value="'.$current.'" />';
			?>
	<script id="bkf_dd_filter">
	    	    jQuery(document).ready(function( $ ) {
					$("#dd_filter").attr( 'readOnly' , 'true' );
	    	        $("#dd_filter").datepicker( {
	    	        	dateFormat: "DD, d MM yy",
	    	        	hideIfNoPrevNext: true,
	    	        	firstDay: 1,
	    	        	constrainInput: true,
						showButtonPanel: true
	    	        } );
		     } );
			 </script>
	<?php
	    }
	}
	
}