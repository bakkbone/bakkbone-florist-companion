<?php
/**
 * @author BAKKBONE Australia
 * @package BKF_Classes
 * @license GNU General Public License (GPL) 3.0
**/

defined("__BKF_EXEC__") or die("Ah, sweet silence.");

class BKF_Classes {
	
	function __construct() {
		add_action('woocommerce_after_settings_shipping', [$this, 'settings']);
	}
	
	function settings() {
		if (isset($_GET['section']) && $_GET['section'] == 'classes') {
			$terms = bkf_get_shipping_classes();
			echo '<form method="GET"  onsubmit="return: false;"><table class="wc-shipping-classes widefat"><thead><tr><th>'.esc_html__('Class', 'bakkbone-florist-companion').'</th><th>'.esc_html__('Non-Floral', 'bakkbone-florist-companion').'</th></thead>';
			foreach ($terms as $term) {
				$checked = $term['nonfloral'] ? ' checked' : '';
				echo '<tr><th><strong>'.$term['name'].'</strong></th><td><label class="bkf-switch"><input data-term_id="'.$term['term_id'].'" type="checkbox" class="bkf_nonfloral" id="bkf_shipping_class_'.$term['term_id'].'_nonfloral" name="bkf_shipping_class_'.$term['term_id'].'_nonfloral"'.$checked.' /><span class="bkf-slider round"></span></label>'.wc_help_tip(esc_html__('If enabled, this delivery class will be marked as non-floral. If <strong>only</strong> non-floral items are in the cart, FloristPress features will not apply to the checkout.', 'bakkbone-florist-companion')).'</td></tr>';
			}
			$exclude = get_option('bkf_nonfloral_exclude_shipping', false) ? ' checked' : '';
			echo '</table></form><p><label><input type="checkbox" id="nonfloral_exclude_shipping" name="nonfloral_exclude_shipping" '.$exclude.'/>'.esc_html__('Do not offer FloristPress delivery methods for non-floral orders', 'bakkbone-florist-companion').'</label></p>';
		?>
		<script type="text/javascript">
			jQuery('.bkf_nonfloral').on('change', function($){
				var term_id = jQuery(this).data('term_id');
				var checked = this.checked;
				<?php if(bkf_debug(true)){ ?>console.log(term_id + ': ' + checked); <?php } ?>
				jQuery.ajax(
					ajaxurl,
					{
						data: {
							action: 'bkf_nonfloral',
							term_id: term_id,
							nonfloral: checked
						},
						success: function(result){window.alert('<?php esc_html_e('Update successful', 'bakkbone-florist-companion'); ?>')},
						error: function(result){window.alert('<?php esc_html_e('Update unsuccessful', 'bakkbone-florist-companion'); ?>')}
					}
				);
			});
			jQuery('#nonfloral_exclude_shipping').on('change', function($){
				var checked = this.checked;
				<?php if(bkf_debug(true)){ ?>console.log('#nonfloral_exclude_shipping: ' + checked); <?php } ?>
				jQuery.ajax(
					ajaxurl,
					{
						data: {
							action: 'bkf_nonfloral_exclude_shipping',
							exclude: checked
						},
						success: function(result){window.alert('<?php esc_html_e('Update successful', 'bakkbone-florist-companion'); ?>')},
						error: function(result){window.alert('<?php esc_html_e('Update unsuccessful', 'bakkbone-florist-companion'); ?>')}
					}
				);
			});
		</script>
		<?php
		}
	}

}