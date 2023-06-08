<?php

/**
 * @author BAKKBONE Australia
 * @package BkfDdFilter
 * @license GNU General Public License (GPL) 3.0
**/

defined("BKF_EXEC") or die("Ah, sweet silence.");

class BkfDdFilter{
	
	function __construct(){
		add_action('pre_get_posts', array($this, 'action_parse_query'), PHP_INT_MAX, 1 );
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
				$query->set('post_status', array("wc-processing","wc-made","wc-collect","wc-out","wc-scheduled","wc-new","wc-accept","wc-invoiced","wc-phone-draft"));
			}
			if ( isset( $_GET['dd_filter'] ) && $_GET['dd_filter'] != '' ) {
				$meta_query = $query->get( 'meta_query' );
				if($meta_query == ''){
					$meta_query = array();
				}
				$meta_query[] = array(
					'meta_key' => '_delivery_timestamp',
					'value'	=> strtotime($_GET['dd_filter']),
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
		
		$allstatus = array("wc-processing","wc-made","wc-collect","wc-out","wc-scheduled","wc-new","wc-accept","wc-invoiced","wc-phone-draft");
		
		$url_to_redirect_full = add_query_arg(array('post_type' => 'shop_order', 'show_all' => 'true'), admin_url('edit.php'));
		$url_to_redirect_all = add_query_arg(array('post_type' => 'shop_order'), admin_url('edit.php'));
		$text_full_active = '<a href="%s" class="current" aria-current="page">'.esc_html__('All', 'bakkbone-florist-companion').' <span class="count">(%d)</span></a>';
		$text_all_active = '<a href="%s" class="current" aria-current="page">'.esc_html__('Active', 'bakkbone-florist-companion').' <span class="count">(%d)</span></a>';
		$text_full_inactive = '<a href="%s">'.esc_html__('All', 'bakkbone-florist-companion').' <span class="count">(%d)</span></a>';
		$text_all_inactive = '<a href="%s">'.esc_html__('Active', 'bakkbone-florist-companion').' <span class="count">(%d)</span></a>';
		
		$allquery = array();
		foreach($fullquery as $full){
			if(in_array($full->post_type, $allstatus)){
				$allquery[] = $full;
			}
		}
		$allcount = count($allquery);
		
		if($post_type == 'shop_order'){
			if($show_all == 'true'){
				$views['all'] = sprintf($text_all_inactive, $url_to_redirect_all, bkf_all_count());
				$views['full'] = sprintf($text_full_active, $url_to_redirect_full, bkf_full_count());
			} elseif ($post_status == '' || $post_status == null){
				$views['all'] = sprintf($text_all_active, $url_to_redirect_all, bkf_all_count());
				$views['full'] = sprintf($text_full_inactive, $url_to_redirect_full, bkf_full_count());
			} else {
				$views['all'] = sprintf($text_all_inactive, $url_to_redirect_all, bkf_all_count());
				$views['full'] = sprintf($text_full_inactive, $url_to_redirect_full, bkf_full_count());
			}
		}		
		
		return $views;
	}

	function bkf_display_admin_shop_order_dd_filter(){
		global $pagenow, $post_type;
		if( 'shop_order' === $post_type && 'edit.php' === $pagenow ) {
			$current = isset($_GET['dd_filter'])? $_GET['dd_filter'] : '';
			echo '<input type="text" name="dd_filter" id="dd_filter" placeholder="'.get_option('bkf_ddi_setting')['ddt'].'" value="'.$current.'" />';
			?>
	<script id="bkf_dd_filter">
				jQuery(document).ready(function( $ ) {
						var setCalsClearButton = function(year,month,elem){

						var afterShow = function(){
							var d = new $.Deferred();
							var cnt = 0;
							setTimeout(function(){
								if(elem.dpDiv[0].style.display === "block"){
									d.resolve();
								}
								if(cnt >= 500){
									d.reject("datepicker show timeout");
								}
								cnt++;
							},10);
							return d.promise();
						}();

						afterShow.done(function(){
							jQuery('.ui-datepicker').css('z-index', 2000);

							var buttonPane = jQuery( elem ).datepicker( "widget" ).find( ".ui-datepicker-buttonpane" );

							var btn = jQuery('<button class="ui-datepicker-current ui-state-default ui-priority-primary ui-corner-all" type="button"><?php esc_html_e('Clear', 'bakkbone-florist-companion'); ?></button>');
							btn.off("click").on("click", function () {
								jQuery.datepicker._clearDate( elem.input[0] );
							});
							btn.appendTo( buttonPane );
						});
					}
					jQuery("#dd_filter").attr( 'readOnly' , 'true' );
					jQuery("#dd_filter").datepicker( {
						dateFormat: "DD, d MM yy",
						hideIfNoPrevNext: true,
						firstDay: 1,
						constrainInput: true,
						showButtonPanel: true,
						beforeShow: function(inst, elem){
							setCalsClearButton(null, null, elem);
						},
						onChangeMonthYear: function(inst, elem){
							setCalsClearButton(null, null, elem);
						}
					} );
			 } );
	</script>
	<?php
		}
	}
	
}