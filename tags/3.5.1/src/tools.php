<?php

/**
 * @author BAKKBONE Australia
 * @package BKF_Tools
 * @license GNU General Public License (GPL) 3.0
**/

defined("BKF_EXEC") or die("Ah, sweet silence.");

class BKF_Tools{
	
	function __construct(){
		add_action("admin_menu", [$this, "menu"], 2);
	}
	
	function menu(){
		$admin_page = add_menu_page(
			__("Florist Tools","bakkbone-florist-companion"),
			__("Florist Tools","bakkbone-florist-companion"),
			"manage_woocommerce",
			"bkf_tools",
			[$this, 'page'],
			BKF_SVG_FLOWERS,
			2.2
		);
		add_action( 'load-'.$admin_page, [$this, 'help_tab'] );
	}
	
	function help_tab(){
		$screen = get_current_screen();
		$id = 'bkf_tools_help';
		$callback = [$this, 'help'];
		$screen->add_help_tab( array( 
		   'id' => $id,
		   'title' => __('Documentation','bakkbone-florist-companion'),
		   'callback' => $callback
		) );	
	}
	
	function help(){
		?>
		<h2><?php esc_html_e('View documentation for this page at: ','bakkbone-florist-companion'); ?></h2>
			<a href="https://docs.floristpress.org/florist-tools/" target="_blank">https://docs.floristpress.org/florist-tools/</a>
		<?php
	}
	
	function page(){
		?>
		<div class="wrap">
			<div class="bkf-loading-bg loading" style="display:none;"><img class="bkf-loading-spin" src="<?php echo BKF_URL . '/assets/img/spinner.svg'; ?>" /></div>
			<div class="bkf-box">
			<h1><?php esc_html_e("Florist Tools","bakkbone-florist-companion") ?></h1>
				<div class="inside">
					<form method="post" action="<?php echo admin_url('admin-ajax.php'); ?>">
						<fieldset class="bkf-fieldset">
							<legend><?php esc_html_e('Order Document Actions', 'bakkbone-florist-companion'); ?></legend>
							<?php wp_nonce_field('bkf', 'nonce'); ?>
							<input class="bkf-form-control avg-text" style="margin-left: 0;" type="text" id="order_id" name="order_id" placeholder="<?php esc_html_e('Order Number', 'bakkbone-florist-companion'); ?>" required />
							<select class="bkf-form-control avg-text" name="action" id="action" required>
								<option disabled selected value=""><?php esc_html_e('Select an action...', 'bakkbone-florist-companion'); ?></option>
								<option value="bkf_resend_invoice"><?php echo esc_html(sprintf(__('Resend %s to Customer', 'bakkbone-florist-companion'), get_option('bkf_pdf_setting')['inv_title'])); ?></option>
								<option value="bkfdi"><?php echo esc_html(sprintf(__('Download %s', 'bakkbone-florist-companion'), get_option('bkf_pdf_setting')['inv_title'])); ?></option>
								<option value="bkfdw"><?php echo esc_html(sprintf(__('Download %s', 'bakkbone-florist-companion'), get_option('bkf_pdf_setting')['ws_title'])); ?></option>
							</select>
							<?php submit_button(__('Go', 'bakkbone-florist-companion'), 'primary avg-text', 'submit', false, 'disabled'); ?>
						</fieldset>
					</form>
				</div>
			</div>
		</div>
		<script>
			jQuery(document).ready(function($){
				jQuery('#action').select2({
					dropdownCssClass: ['bkf-font', 'bkf-select2']
				});
			});
			jQuery(document.body).on('keyup', '#order_id', function($) {
				var loading = jQuery(".loading");
				loading.fadeIn();
				var orderId = jQuery('#order_id').val();
				var action = jQuery('#action').val();
				var checkUrl = "<?php echo admin_url('admin-ajax.php?action=bkf_valid_order&id='); ?>" + orderId;
				jQuery.ajax({url: checkUrl, type: 'GET', success: function(result){
					if (JSON.parse(result) && action !== null) {
						jQuery('#order_id').addClass('bkf-validated');
						jQuery('#order_id').removeClass('bkf-invalid');
						jQuery('#submit').prop('disabled', false);
						loading.fadeOut();
					} else if(JSON.parse(result)) {
						jQuery('#order_id').addClass('bkf-validated');
						jQuery('#order_id').removeClass('bkf-invalid');
						jQuery('#submit').prop('disabled', true);
						loading.fadeOut();
					} else {
						jQuery('#order_id').addClass('bkf-invalid');
						jQuery('#order_id').removeClass('bkf-validated');
						jQuery('#submit').prop('disabled', true);
						loading.fadeOut();
					}
				}});
			});
			jQuery('#action').on('change', function($) {
				var loading = jQuery(".loading");
				loading.fadeIn();
				var orderId = jQuery('#order_id').val();
				var action = jQuery('#action').val();
				var checkUrl = "<?php echo admin_url('admin-ajax.php?action=bkf_valid_order&id='); ?>" + orderId;
				jQuery.ajax({url: checkUrl, type: 'GET', success: function(result){
					if (JSON.parse(result) && action !== null) {
						jQuery('#order_id').addClass('bkf-validated');
						jQuery('#order_id').removeClass('bkf-invalid');
						jQuery('#submit').prop('disabled', false);
						loading.fadeOut();
					} else if(JSON.parse(result)) {
						jQuery('#order_id').addClass('bkf-validated');
						jQuery('#order_id').removeClass('bkf-invalid');
						jQuery('#submit').prop('disabled', true);
						loading.fadeOut();
					} else {
						jQuery('#order_id').addClass('bkf-invalid');
						jQuery('#order_id').removeClass('bkf-validated');
						jQuery('#submit').prop('disabled', true);
						loading.fadeOut();
					}
				}});
			});
		</script>
		<?php
	}
}