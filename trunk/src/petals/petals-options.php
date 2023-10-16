<?php
/**
 * @author BAKKBONE Australia
 * @package BKF_Petals_Options
 * @license GNU General Public License (GPL) 3.0
**/

defined("BKF_EXEC") or die("Ah, sweet silence.");

class BKF_Petals_Options{

	private $bkf_petals_setting = [];
	private $bkf_petals_product_setting = [];

	function __construct(){
		if(get_option('bkf_features_setting')['petals_on'] == true){
			if(is_admin()){
				$this->bkf_petals_setting = get_option("bkf_petals_setting");
				$this->bkf_petals_product_setting = get_option("bkf_petals_product_setting");
				add_action("admin_menu",array($this,"bkfAddPetalsPageOption"),50);
				add_action("admin_init",array($this,"bkfAddPetalsPageInit"));
			}
		}
	}

	function bkfAddPetalsPageOption(){
		$admin_page = add_submenu_page(
			"bkf_options",
			__("Petals Network Integration","bakkbone-florist-companion"),
			__("Petals Network","bakkbone-florist-companion"),
			"manage_woocommerce",
			"bkf_petals",
			array($this,"bkfPetalsOptionsPageContent"),
			50
		);
		add_action( 'load-'.$admin_page, array($this, 'bkf_pn_help_tab') );
	}
	
	function bkf_pn_help_tab(){
		$screen = get_current_screen();
		$id = 'bkf_pn_help';
		$callback = array($this, 'bkf_pn_help');
		$screen->add_help_tab( array( 
		   'id' => $id,
		   'title' => __('Documentation','bakkbone-florist-companion'),
		   'callback' => $callback
		) );	
	}
	
	function bkf_pn_help(){
		?>
		<h2><?php esc_html_e('View documentation for this page at: ','bakkbone-florist-companion'); ?></h2>
			<a href="https://plugins.bkbn.au/docs/bkf/florist-options/petals-network/" target="_blank">https://plugins.bkbn.au/docs/bkf/florist-options/petals-network/</a>
		<?php
	}

	function bkfPetalsOptionsPageContent(){
		$this->bkf_petals_setting = get_option("bkf_petals_setting");
		?>
		<div class="wrap">
			<div class="bkf-box">
			<h1><?php esc_html_e("Petals Network","bakkbone-florist-companion") ?></h1>
				<div class="inside">
					<form method="post" action="options.php">
						<?php settings_fields("bkf_petals_options_group"); ?>
						<?php do_settings_sections("bkf-petals"); ?>
						<?php submit_button(); ?>
					</form>
				</div>
			</div>
		</div>
		<?php
	}

	function bkfAddPetalsPageInit(){
		register_setting(
			"bkf_petals_options_group",
			"bkf_petals_setting",
			array($this,"bkfAddPetalsOptionsSanitize")
		);
		register_setting(
			"bkf_petals_options_group",
			"bkf_petals_product_setting",
			array($this,"bkfAddPetalsProductOptionsSanitize")
		);
		
		add_settings_section(
			"bkf_petals_section",
			__("Petals Network Integration","bakkbone-florist-companion"),
			array($this,"bkfPetalsOptionsInfo"),
			"bkf-petals"
		);
		add_settings_section(
			"bkf_petals_product_section",
			__("Outbound Order Product","bakkbone-florist-companion"),
			array($this,"bkfPetalsProductOptionsInfo"),
			"bkf-petals"
		);		
		
		add_settings_field(
			"mn",
			__("Member Number","bakkbone-florist-companion"),
			array($this,"bkfPetalsMemberNumberCallback"),
			"bkf-petals",
			"bkf_petals_section"
		);

		add_settings_field(
			"ppw",
			__("Password","bakkbone-florist-companion"),
			array($this,"bkfPetalsPasswordCallback"),
			"bkf-petals",
			"bkf_petals_section"
		);

		add_settings_field(
			"bkf_petals_link",
			__("URL","bakkbone-florist-companion"),
			array($this,"bkfPetalsURLCallback"),
			"bkf-petals",
			"bkf_petals_section"
		);
		
		add_settings_field(
			"cat",
			__("Product Category","bakkbone-florist-companion"),
			array($this,"bkfPetalsCatCallback"),
			"bkf-petals",
			"bkf_petals_product_section"
		);

		add_settings_field(
			"product",
			__("Product","bakkbone-florist-companion"),
			array($this,"bkfPetalsProductCallback"),
			"bkf-petals",
			"bkf_petals_product_section"
		);
		
	}

	function bkfAddPetalsOptionsSanitize($input){
		$new_input = [];
		
		if(isset($input["mn"]))
			$new_input["mn"] = sanitize_text_field($input["mn"]);
			
		if(isset($input["ppw"]))
			$new_input["ppw"] = base64_encode($input["ppw"]);
			
		return $new_input;
	}

	function bkfAddPetalsProductOptionsSanitize($input){
		$new_input = [];
		
		if(isset($input["cat"]))
			$new_input["cat"] = sanitize_text_field($input["cat"]);

		if(isset($input["product"]))
			$new_input["product"] = sanitize_text_field($input["product"]);

		return $new_input;
	}

	function bkfPetalsOptionsInfo(){
		echo '<p class="bkf-pageinfo">';
		esc_html_e("Your site will use this information to communicate with Petals.", "bakkbone-florist-companion");
		echo '</p>';
	}
	
	function bkfPetalsProductOptionsInfo(){
		echo '<p class="bkf-pageinfo">';
		esc_html_e("These settings relate to the WooCommerce product that will be used when an order from Petals is received.", "bakkbone-florist-companion");
		echo '</p>';
	}

	function bkfPetalsMemberNumberCallback(){
	
		if(isset($this->bkf_petals_setting["mn"])){
			$value = esc_attr($this->bkf_petals_setting["mn"]);
		} else {
			$value = "";
		}
		?>
		<input autocomplete="off" class="bkf-form-control small-text" id="bkf-petals-member-number" type="number" name="bkf_petals_setting[mn]" placeholder="1234" value="<?php echo $value; ?>" />
		<p class="description"><?php esc_html_e("Your Petals Exchange member number.","bakkbone-florist-companion") ?></p>
		<?php
	}

	function bkfPetalsPasswordCallback(){
	
		if(isset($this->bkf_petals_setting["ppw"])){
			$value = esc_attr(base64_decode($this->bkf_petals_setting["ppw"]));
		} else {
			$value = "";
		}
		?>
		<input autocomplete="off" class="bkf-form-control medium-text" id="bkf-petals-password" type="password" name="bkf_petals_setting[ppw]" placeholder="********" value="<?php echo $value; ?>" />
		<p class="description"><?php esc_html_e("Your Petals Exchange password. This is encrypted before being stored in your site's database, and is used only to communicate directly with Petals Network.","bakkbone-florist-companion") ?></p>
		<?php
	}

	function bkfPetalsURLCallback(){
		$value = admin_url('admin-ajax.php?action=petals_outbound');
		?>
		<div class="bkfptooltip" style="width:100%;"><div onclick="bkfCopy()" onmouseout="bkfpCopyOut()" style="width:100%;"><input disabled class="bkf-form-control large-text" style="width:50%;min-width:300px;" id="bkfCopy" type="text" value="<?php echo $value; ?>" />  <span class="bkfptooltiptext" id="bkfpTooltip"><?php esc_html_e('Click to copy to clipboard', 'bakkbone-florist-companion'); ?></span>
</div></div>
		<p class="description"><?php echo wp_kses_post(__('Provide the link above to the Petals <a href="mailto:eflorist@petalsnetwork.com?subject=XML Opt-In&body=Shop Name: %0D%0AMy Full Name: %0D%0APhone: %0D%0AEmail: %0D%0AMember Number: %0D%0AXML Link: %0D%0A%0D%0APlease opt my store into XML orders alongside the Exchange, using the details above.">eFlorist Team</a>, requesting to <em>opt in to XML orders alongside the Exchange to the link provided</em>. The link can be copied to your clipboard by simply clicking the link above.', 'bakkbone-florist-companion')); ?></p>
		<p class="description"><?php echo wp_kses_post(__('<strong>Please note:</strong> The integration will not work unless all other fields on this page are completed <em>and</em> the request is sent to Petals as per the above.', 'bakkbone-florist-companion')); ?></p>
		<?php
	}
		
	function bkfPetalsCatCallback(){
		if(isset($this->bkf_petals_product_setting["cat"])){
			$value = esc_attr($this->bkf_petals_product_setting["cat"]);
		} else {
			$value = "";
		}
		$args = array(
			"show_option_none" => __("Select a Category...", 'bakkbone-florist-companion'),
			"option_none_value" => "",
			"value" => "term_id",
			"hierarchical" => true,
			"taxonomy" => "product_cat",
			"show_count" => false,
			"hide_empty" => false,
			"echo" => true,
			"id" => "bkf-petals-cat",
			"name" => "bkf_petals_product_setting[cat]",
			"selected" => $value,
			"class" => "postform bkf-form-control"
		);
		?>
		<?php wp_dropdown_categories($args); ?>
		<p class="description"><?php esc_html_e("Please select the category which will contain the product.", "bakkbone-florist-companion"); ?></p>
		<script>
			jQuery(document).ready(function($){
				jQuery('#bkf-petals-cat option').first().prop('disabled', true);
				jQuery('#bkf-petals-cat').select2({
					dropdownCssClass: ['bkf-font', 'bkf-select2']
				});
			});
		</script>
		<?php
	}

	function bkfPetalsProductCallback(){

		if(isset($this->bkf_petals_product_setting["product"])){
			$value = esc_attr($this->bkf_petals_product_setting["product"]);
		} else {
			$value = "";
		}
		
		if(empty($this->bkf_petals_product_setting["cat"])){
			$productdisabled = " disabled";
		} else {
			$productdisabled = "";
		}
		?>
			<select id="bkf-petals-product" class="bkf-form-control" name="bkf_petals_product_setting[product]"<?php echo $productdisabled ?>>
			<?php
		$orderby = 'term_order';
		$order = 'asc';
		$category = get_terms('product_cat', array("include" => $this->bkf_petals_product_setting["cat"]));
		$cat = $category[0]->slug;
		$args = array(
			'category'	=> array($cat),
			'orderby'	=> $orderby,
			'order'		=> $order,
			'limit'		=> '-1'
			);
		$products = wc_get_products( $args );
		if(empty($value)) {
			echo "<option value=\"\" selected disabled>".esc_html__('Select a Product...', 'bakkbone-florist-companion')."</option>";	
		} else {
			echo "<option value=\"\" disabled>".esc_html__('Select a Product...', 'bakkbone-florist-companion')."</option>";
		}
		foreach($products as $key => $product){
			if($value == $product->get_id()){
				echo "<option selected value=\"" . $product->get_id() . "\">" . $product->get_name() . "</option>";
			} else {
				echo "<option value=\"" . $product->get_id() . "\">" . $product->get_name() . "</option>";
			}
		}
		?>
		</select>
		<script>
			jQuery(document).ready(function($){
				jQuery('#bkf-petals-product').select2({
					dropdownCssClass: ['bkf-font', 'bkf-select2']
				});
			});
		</script>
		<?php
		if(empty($this->bkf_petals_product_setting["cat"])){
			?>
			<p class="description"><?php esc_html_e("Select a category above first, and Save Changes, then this option will become available.","bakkbone-florist-companion"); ?></p>
			<?php
		} else {
			?>
			<p class="description"><?php esc_html_e("Please select the product.","bakkbone-florist-companion"); ?></p>
			<?php
		}
		if(empty($value) && !empty($this->bkf_petals_product_setting["cat"])) {
			$nonce = wp_create_nonce("bkf");
			$ajaxurl = admin_url('admin-ajax.php?action=bkf_cpp&nonce='.$nonce);
		?><a data-nonce="<?php echo $nonce ?>" href="<?php echo $ajaxurl ?>"><?php esc_html_e("Or click here to generate a compatible product in one click.", "bakkbone-florist-companion"); ?></a><?php
		}
	}
	
}