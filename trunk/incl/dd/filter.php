<?php

/**
 * @author BAKKBONE Australia
 * @package BkfDdFilter
 * @license GNU General Public License (GPL) 3.0
**/

defined("BKF_EXEC") or die("Silence is golden");

class BkfDdFilter{

    function __construct(){
		add_action('pre_get_posts', 'bkf_process_admin_shop_order_dd_filter');
		add_action('wp_enqueue_scripts', 'bkf_filter_enqueue_datepicker');
		add_action('restrict_manage_posts', 'bkf_display_admin_shop_order_dd_filter');
    }
	
	function bkf_process_admin_shop_order_dd_filter( $query ) {
	    global $pagenow;
	    if ( $query->is_admin && $pagenow == 'edit.php' && isset( $_GET['dd_filter'] ) 
	        && $_GET['dd_filter'] != '' && $_GET['post_type'] == 'shop_order' ) {
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