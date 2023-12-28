<?php
/**
 * @author BAKKBONE Australia
 * @package BKF_Admin_Notices
 * @license GNU General Public License (GPL) 3.0
**/

defined("__BKF_EXEC__") or die("Ah, sweet silence.");

class BKF_Admin_Notices{

	function __construct(){
		add_action('plugins_loaded', [$this, 'check_plugin_compatibility']);
		$features = get_option('bkf_features_setting');
		if($features){
			if($features['excerpt_pa'] && bkf_is_breakdance_active()){
				add_action("admin_notices", [$this, 'breakdance_clash']);
			}
		}
		add_action('admin_notices', [$this, 'notices']);
		add_action('wp_dashboard_setup', [$this, 'dashwidgets']);
		if (bkf_is_woocommerce_active()) {
			add_action('admin_bar_menu', [$this, 'admin_bar_item'], 500);
		} else {
			add_action('admin_notices', [$this, 'installwoo']);
		}
	}

	function admin_bar_item($admin_bar){
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			return;
		}

		$features = get_option('bkf_features_setting');
		$petals = $features['petals_on'];
		$woosettings = $features['settingsbar'];

		$admin_bar->add_menu( array(
			'id'	=> 'floristpress',
			'parent'=> null,
			'title'	=> '<span style="display:inline-block;padding-right:10px;position:relative;top:5px;max-width:12px;">'.__BKF_SVG_FLOWERS_RAW__.'</span>'.__( 'FloristPress', 'bakkbone-florist-companion' ),
			'meta'	=> [
				'title' => sprintf(__( 'FloristPress v%s', 'bakkbone-florist-companion' ), __BKF_VERSION__),
			]
		) );

		$admin_bar->add_menu( array(
			'id'    => 'floristpress-options',
			'parent'=> 'floristpress',
			'title' => __('Florist Options', 'bakkbone-florist-companion'),
		) );

		$admin_bar->add_menu( array(
			'id'    => 'floristpress-options-general',
			'parent'=> 'floristpress-options',
			'title' => __('General Options', 'bakkbone-florist-companion'),
			'href'  => bkf_wc_admin_link('bkf'),
		) );
		$admin_bar->add_menu( array(
			'id'    => 'floristpress-options-pdf',
			'parent'=> 'floristpress-options',
			'title' => __('PDF Options', 'bakkbone-florist-companion'),
			'href'  => bkf_wc_admin_link('bkf', 'pdf'),
		) );
		$admin_bar->add_menu( array(
			'id'    => 'floristpress-options-localisation',
			'parent'=> 'floristpress-options',
			'title' => __('Localization', 'bakkbone-florist-companion'),
			'href'  => bkf_wc_admin_link('bkf', 'localisation'),
		) );
		if($petals){
			$admin_bar->add_menu( array(
			    'id'	=> 'floristpress-options-petals',
				'parent'=> 'floristpress-options',
				'title'	=> __('Petals Network', 'bakkbone-florist-companion'),
				'href'	=> bkf_wc_admin_link('bkf', 'petals'),
			) );
		}

		$admin_bar->add_menu( array(
			'id'    => 'floristpress-tools',
			'parent'=> 'floristpress',
			'title' => __('Florist Tools', 'bakkbone-florist-companion'),
			'href'  => admin_url('tools.php?page=bkf_tools'),
		) );

		$admin_bar->add_menu( array(
			'id'    => 'floristpress-dd',
			'parent'=> 'floristpress',
			'title' => __('Delivery Dates', 'bakkbone-florist-companion'),
		) );

		$admin_bar->add_menu( array(
			'id'    => 'floristpress-dd-general',
			'parent'=> 'floristpress-dd',
			'title' => __('General Options', 'bakkbone-florist-companion'),
			'href'  => bkf_wc_admin_link('bkf_dd'),
		) );
		$admin_bar->add_menu( array(
			'id'    => 'floristpress-dd-weekdays',
			'parent'=> 'floristpress-dd',
			'title' => __('Weekdays', 'bakkbone-florist-companion'),
		) );
		$admin_bar->add_menu( array(
			'id'    => 'floristpress-dd-weekdays-1',
			'parent'=> 'floristpress-dd-weekdays',
			'title' => __('Weekdays and Lead Times', 'bakkbone-florist-companion'),
			'href'  => bkf_wc_admin_link('bkf_dd', 'wd'),
		) );
		$admin_bar->add_menu( array(
			'id'    => 'floristpress-dd-weekdays-methodspecific',
			'parent'=> 'floristpress-dd-weekdays',
			'title' => __('Method Lead Times', 'bakkbone-florist-companion'),
			'href'  => bkf_wc_admin_link('bkf_dd', 'mlt'),
		) );
		$admin_bar->add_menu( array(
			'id'    => 'floristpress-dd-weekdays-methods',
			'parent'=> 'floristpress-dd-weekdays',
			'title' => __('Method Restrictions', 'bakkbone-florist-companion'),
			'href'  => bkf_wc_admin_link('bkf_dd', 'mr'),
		) );
		$admin_bar->add_menu( array(
			'id'    => 'floristpress-dd-blocks',
			'parent'=> 'floristpress-dd',
			'title' => __('Blocked Dates', 'bakkbone-florist-companion'),
		) );
		$admin_bar->add_menu( array(
			'id'    => 'floristpress-dd-blocks-1',
			'parent'=> 'floristpress-dd-blocks',
			'title' => __('Blocked Delivery Dates', 'bakkbone-florist-companion'),
			'href'  => bkf_wc_admin_link('bkf_dd', 'ddb'),
		) );
		$admin_bar->add_menu( array(
			'id'    => 'floristpress-dd-blocks-category',
			'parent'=> 'floristpress-dd-blocks',
			'title' => __('Category Blocks', 'bakkbone-florist-companion'),
			'href'  => bkf_wc_admin_link('bkf_dd', 'cb'),
		) );
		$admin_bar->add_menu( array(
			'id'    => 'floristpress-dd-timeslots',
			'parent'=> 'floristpress-dd',
			'title' => __('Timeslots', 'bakkbone-florist-companion'),
			'href'  => bkf_wc_admin_link('bkf_dd', 'ts'),
		) );
		$admin_bar->add_menu( array(
			'id'    => 'floristpress-dd-fees',
			'parent'=> 'floristpress-dd',
			'title' => __('Fees', 'bakkbone-florist-companion'),
		) );
		$admin_bar->add_menu( array(
			'id'    => 'floristpress-dd-fees-1',
			'parent'=> 'floristpress-dd-fees',
			'title' => __('Fees Options', 'bakkbone-florist-companion'),
			'href'  => bkf_wc_admin_link('bkf_dd', 'fee'),
		) );
		$admin_bar->add_menu( array(
			'id'    => 'floristpress-dd-fees-datespecific',
			'parent'=> 'floristpress-dd-fees',
			'title' => __('Blocked Dates', 'bakkbone-florist-companion'),
			'href'  => bkf_wc_admin_link('bkf_dd', 'ds'),
		) );

		$admin_bar->add_menu( array(
			'id'    => 'floristpress-calendar',
			'parent'=> 'floristpress',
			'title' => __('Delivery Calendar', 'bakkbone-florist-companion'),
			'href'  => admin_url('admin.php?page=bkf_dc'),
		) );

		$admin_bar->add_menu( array(
			'id'    => 'floristpress-phone',
			'parent'=> 'floristpress',
			'title' => __('New Phone Order', 'bakkbone-florist-companion'),
			'href'  => admin_url('admin.php?page=new-phone_order'),
		) );

		$admin_bar->add_menu( array(
			'id'    => 'floristpress-dspost',
			'parent'=> 'floristpress',
			'title' => __('Delivery Suburb Pages', 'bakkbone-florist-companion'),
		) );

		$admin_bar->add_menu( array(
			'id'    => 'floristpress-dspost-all',
			'parent'=> 'floristpress-dspost',
			'title' => __('View All', 'bakkbone-florist-companion'),
			'href'  => admin_url('edit.php?post_type=bkf_delivery_suburb'),
		) );
		$admin_bar->add_menu( array(
			'id'    => 'floristpress-dspost-new',
			'parent'=> 'floristpress-dspost',
			'title' => __('New Suburb Page', 'bakkbone-florist-companion'),
			'href'  => admin_url('post-new.php?post_type=bkf_delivery_suburb'),
		) );
		$admin_bar->add_menu( array(
			'id'    => 'floristpress-dspost-category',
			'parent'=> 'floristpress-dspost',
			'title' => __('Categories', 'bakkbone-florist-companion'),
			'href'  => admin_url('edit-tags.php?taxonomy=category&post_type=bkf_delivery_suburb'),
		) );

		$admin_bar->add_menu( array(
			'id'    => 'floristpress-delivery-calendar',
			'parent'=> null,
			'title' => '<span class="ab-icon dashicons-before dashicons-calendar-alt"></span><span class="ab-label">'.__('Delivery Calendar', 'bakkbone-florist-companion').'</span>',
			'href'  => admin_url('admin.php?page=bkf_dc'),
		) );

		$admin_bar->add_menu( array(
			'id'    => 'floristpress-block-dates',
			'parent'=> null,
			'title' => '<span class="ab-label">'.__('Block Dates', 'bakkbone-florist-companion').'</span>',
			'href'  => bkf_wc_admin_link('bkf_dd', 'ddb'),
		) );

		if($petals){
			$admin_bar->add_menu( array(
			    'id'	=> 'floristpress-petals',
				'parent'=> 'floristpress',
				'title'	=> __('Petals Network', 'bakkbone-florist-companion'),
			) );
			$admin_bar->add_menu( array(
			    'id'	=> 'floristpress-petals-options',
				'parent'=> 'floristpress-petals',
				'title'	=> __('Options', 'bakkbone-florist-companion'),
				'href'	=> bkf_wc_admin_link('bkf', 'petals'),
			) );
			$admin_bar->add_menu( array(
			    'id'	=> 'floristpress-petals-sent',
				'parent'=> 'floristpress-petals',
				'title'	=> __('Sent Orders', 'bakkbone-florist-companion'),
				'href'	=> admin_url('edit.php?post_type=bkf_petals_order'),
			) );
			$admin_bar->add_menu( array(
			    'id'	=> 'floristpress-petals-send',
				'parent'=> 'floristpress-petals',
				'title'	=> __('Send Order', 'bakkbone-florist-companion'),
				'href'	=> admin_url('post-new.php?post_type=bkf_petals_order'),
			) );
		}

		if ($woosettings) {
			$admin_bar->add_menu( array(
				'id'	=> 'bkfwoo',
				'parent'=> null,
				'title'	=> __( 'Woo Settings', 'bakkbone-florist-companion' ),
			) );
			$admin_bar->add_menu( array(
				'id'	=> 'bkfwoo-general',
				'parent'=> 'bkfwoo',
				'title'	=> __( 'General', 'woocommerce' ),
				'href'	=> admin_url('admin.php?page=wc-settings&tab=general'),
			) );
			$admin_bar->add_menu( array(
				'id'	=> 'bkfwoo-products',
				'parent'=> 'bkfwoo',
				'title'	=> __( 'Products', 'woocommerce' ),
				'href'	=> admin_url('admin.php?page=wc-settings&tab=products'),
			) );
			$admin_bar->add_menu( array(
				'id'	=> 'bkfwoo-products-general',
				'parent'=> 'bkfwoo-products',
				'title'	=> __( 'General', 'woocommerce' ),
				'href'	=> admin_url('admin.php?page=wc-settings&tab=products&section='),
			) );
			$admin_bar->add_menu( array(
				'id'	=> 'bkfwoo-products-inventory',
				'parent'=> 'bkfwoo-products',
				'title'	=> __( 'Inventory', 'woocommerce' ),
				'href'	=> admin_url('admin.php?page=wc-settings&tab=products&section=inventory'),
			) );
			$admin_bar->add_menu( array(
				'id'	=> 'bkfwoo-products-downloadable',
				'parent'=> 'bkfwoo-products',
				'title'	=> __( 'Downloadable products', 'woocommerce' ),
				'href'	=> admin_url('admin.php?page=wc-settings&tab=products&section=downloadable'),
			) );
			$admin_bar->add_menu( array(
				'id'	=> 'bkfwoo-products-download_urls',
				'parent'=> 'bkfwoo-products',
				'title'	=> __( 'Approved download directories', 'woocommerce' ),
				'href'	=> admin_url('admin.php?page=wc-settings&tab=products&section=download_urls'),
			) );
			$admin_bar->add_menu( array(
				'id'	=> 'bkfwoo-products-advanced',
				'parent'=> 'bkfwoo-products',
				'title'	=> __( 'Advanced', 'woocommerce' ),
				'href'	=> admin_url('admin.php?page=wc-settings&tab=products&section=advanced'),
			) );
			if (wc_tax_enabled()) {
				$admin_bar->add_menu( array(
					'id'	=> 'bkfwoo-tax',
					'parent'=> 'bkfwoo',
					'title'	=> __( 'Tax', 'woocommerce' ),
					'href'	=> admin_url('admin.php?page=wc-settings&tab=tax'),
				) );
				$sections = array(
					''         => __( 'Tax options', 'woocommerce' ),
					'standard' => __( 'Standard rates', 'woocommerce' ),
				);

				// Get tax classes and display as links.
				$tax_classes = WC_Tax::get_tax_classes();

				foreach ( $tax_classes as $class ) {
					/* translators: $s tax rate section name */
					$sections[ sanitize_title( $class ) ] = sprintf( __( '%s rates', 'woocommerce' ), $class );
				}

				foreach ($sections as $key => $value) {
					$admin_bar->add_menu( array(
						'id'	=> 'bkfwoo-tax-'.$key,
						'parent'=> 'bkfwoo-tax',
						'title'	=> $value,
						'href'	=> admin_url('admin.php?page=wc-settings&tab=tax&section='.$key),
					) );
				}
			}
			$admin_bar->add_menu( array(
				'id'	=> 'bkfwoo-shipping',
				'parent'=> 'bkfwoo',
				'title'	=> __( 'Delivery', 'bakkbone-florist-companion' ),
				'href'	=> admin_url('admin.php?page=wc-settings&tab=shipping'),
			) );
			$admin_bar->add_menu( array(
				'id'	=> 'bkfwoo-shipping-zones',
				'parent'=> 'bkfwoo-shipping',
				'title'	=> __( 'Delivery zones', 'bakkbone-florist-companion' ),
				'href'	=> admin_url('admin.php?page=wc-settings&tab=shipping&section='),
			) );

			$zones = bkf_get_shipping_zones();
			foreach ($zones as $zone) {
				$admin_bar->add_menu( array(
					'id'	=> 'bkfwoo-shipping-zones-'.$zone['id'],
					'parent'=> 'bkfwoo-shipping-zones',
					'title'	=> $zone['name'],
					'href'	=> admin_url('admin.php?page=wc-settings&tab=shipping&zone_id='.$zone['id']),
				) );

				foreach ($zone['methods'] as $method) {
					$admin_bar->add_menu( array(
						'id'	=> 'bkfwoo-shipping-zones-'.$zone['id'].'-'.$method['instanceid'],
						'parent'=> 'bkfwoo-shipping-zones-'.$zone['id'],
						'title'	=> $method['usertitle'],
						'href'	=> admin_url('admin.php?page=wc-settings&tab=shipping&instance_id='.$method['instanceid']),
					) );
				}
			}

			$admin_bar->add_menu( array(
				'id'	=> 'bkfwoo-shipping-options',
				'parent'=> 'bkfwoo-shipping',
				'title'	=> __( 'Delivery options', 'bakkbone-florist-companion' ),
				'href'	=> admin_url('admin.php?page=wc-settings&tab=shipping&section=options'),
			) );
			$admin_bar->add_menu( array(
				'id'	=> 'bkfwoo-shipping-classes',
				'parent'=> 'bkfwoo-shipping',
				'title'	=> __( 'Delivery classes', 'bakkbone-florist-companion' ),
				'href'	=> admin_url('admin.php?page=wc-settings&tab=shipping&section=classes'),
			) );
			$admin_bar->add_menu( array(
				'id'	=> 'bkfwoo-checkout',
				'parent'=> 'bkfwoo',
				'title'	=> __( 'Payments', 'woocommerce' ),
				'href'	=> admin_url('admin.php?page=wc-settings&tab=checkout'),
			) );

			$gateways = WC()->payment_gateways->payment_gateways();
			foreach ($gateways as $key => $value) {
				$status = $value->is_available() ? '✔' : '✖';
				$admin_bar->add_menu( array(
					'id'	=> 'bkfwoo-checkout-'.$key,
					'parent'=> 'bkfwoo-checkout',
					'title'	=> $status.' '.$value->get_method_title(),
					'href'	=> admin_url('admin.php?page=wc-settings&tab=checkout&section='.$key),
				) );
			}

			$admin_bar->add_menu( array(
				'id'	=> 'bkfwoo-account',
				'parent'=> 'bkfwoo',
				'title'	=> __( 'Accounts & Privacy', 'woocommerce' ),
				'href'	=> admin_url('admin.php?page=wc-settings&tab=account'),
			) );
			$admin_bar->add_menu( array(
				'id'	=> 'bkfwoo-email',
				'parent'=> 'bkfwoo',
				'title'	=> __( 'Emails', 'woocommerce' ),
				'href'	=> admin_url('admin.php?page=wc-settings&tab=email'),
			) );
			$admin_bar->add_menu( array(
				'id'	=> 'bkfwoo-integration',
				'parent'=> 'bkfwoo',
				'title'	=> __( 'Integration', 'woocommerce' ),
				'href'	=> admin_url('admin.php?page=wc-settings&tab=integration'),
			) );
			$admin_bar->add_menu( array(
				'id'	=> 'bkfwoo-advanced',
				'parent'=> 'bkfwoo',
				'title'	=> __( 'Advanced', 'woocommerce' ),
				'href'	=> admin_url('admin.php?page=wc-settings&tab=advanced'),
			) );
			$admin_bar->add_menu( array(
				'id'	=> 'bkfwoo-advanced-setup',
				'parent'=> 'bkfwoo-advanced',
				'title'	=> __( 'Page setup', 'woocommerce' ),
				'href'	=> admin_url('admin.php?page=wc-settings&tab=advanced&section='),
			) );
			$admin_bar->add_menu( array(
				'id'	=> 'bkfwoo-advanced-keys',
				'parent'=> 'bkfwoo-advanced',
				'title'	=> __( 'REST API', 'woocommerce' ),
				'href'	=> admin_url('admin.php?page=wc-settings&tab=advanced&section=keys'),
			) );
			$admin_bar->add_menu( array(
				'id'	=> 'bkfwoo-advanced-webhooks',
				'parent'=> 'bkfwoo-advanced',
				'title'	=> __( 'Webhooks', 'woocommerce' ),
				'href'	=> admin_url('admin.php?page=wc-settings&tab=advanced&section=webhooks'),
			) );
			$admin_bar->add_menu( array(
				'id'	=> 'bkfwoo-advanced-legacy_api',
				'parent'=> 'bkfwoo-advanced',
				'title'	=> __( 'Legacy API', 'woocommerce' ),
				'href'	=> admin_url('admin.php?page=wc-settings&tab=advanced&section=legacy_api'),
			) );
			$admin_bar->add_menu( array(
				'id'	=> 'bkfwoo-advanced-woocommerce_com',
				'parent'=> 'bkfwoo-advanced',
				'title'	=> __( 'WooCommerce.com', 'woocommerce' ),
				'href'	=> admin_url('admin.php?page=wc-settings&tab=advanced&section=woocommerce_com'),
			) );
			$admin_bar->add_menu( array(
				'id'	=> 'bkfwoo-advanced-features',
				'parent'=> 'bkfwoo-advanced',
				'title'	=> __( 'Features', 'woocommerce' ),
				'href'	=> admin_url('admin.php?page=wc-settings&tab=advanced&section=features'),
			) );
			$admin_bar->add_menu( array(
				'id'	=> 'bkfwoo-bkf',
				'parent'=> 'bkfwoo',
				'title'	=> __( 'FloristPress', 'woocommerce' ),
				'href'	=> bkf_wc_admin_link(),
			) );
			$bkf_sections = [
				''				=> __('General', 'bakkbone-florist-companion'),
				'pdf'			=> __('PDFs', 'bakkbone-florist-companion'),
				'localisation'	=> __('Localization', 'bakkbone-florist-companion')
			];
			foreach ($bkf_sections as $key => $title) {
				$admin_bar->add_menu( array(
					'id'	=> 'bkfwoo-bkf-sub'.$key,
					'parent'=> 'bkfwoo-bkf',
					'title'	=> $title,
					'href'	=> bkf_wc_admin_link('bkf', $key),
				) );
			}
			$admin_bar->add_menu( array(
				'id'	=> 'bkfwoo-bkf_dd',
				'parent'=> 'bkfwoo',
				'title'	=> __( 'Delivery Dates', 'woocommerce' ),
				'href'	=> bkf_wc_admin_link('bkf_dd'),
			) );
			$bkf_dd_sections = [
				''		=> __('General', 'bakkbone-florist-companion'),
				'wd'	=> __('Weekdays', 'bakkbone-florist-companion'),
				'mlt'	=> __('Method Lead Times', 'bakkbone-florist-companion'),
				'mr'	=> __('Method Restrictions', 'bakkbone-florist-companion'),
				'ddb'	=> __('Blocked Dates', 'bakkbone-florist-companion'),
				'cb'	=> __('Category Blocks', 'bakkbone-florist-companion'),
				'ts'	=> __('Timeslots', 'bakkbone-florist-companion'),
				'fee'	=> __('Fees', 'bakkbone-florist-companion'),
				'ds'	=> __('Date Fees', 'bakkbone-florist-companion'),
			];
			foreach ($bkf_dd_sections as $key => $title) {
				$admin_bar->add_menu( array(
					'id'	=> 'bkfwoo-bkf_dd-sub'.$key,
					'parent'=> 'bkfwoo-bkf_dd',
					'title'	=> $title,
					'href'	=> bkf_wc_admin_link('bkf_dd', $key),
				) );
			}
		}

	}

	function check_plugin_compatibility(){
		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$all_plugins = get_plugins();
		$plugin_data = get_plugin_data(__BKF_FILE__);
		$bkftitle = $plugin_data['Name'];

		$untested = [];
		$incompatible = [];

		foreach($all_plugins as $plugin){
			$tested = $plugin['BKF tested up to'] !== '' && $plugin['BKF tested up to'] !== null ? $plugin['BKF tested up to'] : __BKF_VERSION__;
			$requires = $plugin['BKF requires at least'] !== '' && $plugin['BKF requires at least'] !== null ? $plugin['BKF requires at least'] : "0.0.0";
			if(bkf_compare_semantic_version(__BKF_VERSION__, $tested)){
				$untested[] = $plugin;
			}
			if(bkf_compare_semantic_version($requires, __BKF_VERSION__)){
				$incompatible[] = $plugin;
			}
		}

		foreach($untested as $offender){
			/* translators: %1s: conflicting plugin name. %2s: this plugin name. */
			echo '<div class="notice-error notice"><p>'. sprintf(__('<strong>%1s</strong> has not been tested with your current version of %2s – please ask the developer to update their plugin.','bakkbone-florist-companion'), $offender['Title'], $bkftitle).'</p></div>';
		}
		foreach($incompatible as $offender){
			/* translators: %1s: conflicting plugin name. %2s: Required version of this plugin. %3s: this plugin name. */
			echo '<div class="notice-error notice"><p>'. sprintf(__('<strong>%1s</strong> has requires at least version <strong>%2s</strong> of %3s – please update %3s.','bakkbone-florist-companion'), $offender['Title'], $offender['BKF requires at least'], $bkftitle, $bkftitle).'</p></div>';
		}
	}

	function breakdance_clash(){
		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$all_plugins = get_plugins();
		$bdtitle = $all_plugins['breakdance/plugin.php']['Title'];
		$plugin_data = get_plugin_data(__BKF_FILE__);
		/* translators: %1s: conflicting plugin name. %2s: this plugin name. %3s: opening link tag. %4s: closing link tag. */
		echo '<div class="notice-error notice"><p>'. sprintf(__('<strong>%1s</strong> already includes the capability to show Short Descriptions in product archives – please disable the %2s Short Description feature %3shere%4s.','bakkbone-florist-companion'), $bdtitle, $plugin_data["Name"], '<a href="'.esc_url( add_query_arg( array( 'page' => 'bkf_options' ), get_admin_url() . 'admin.php' ) ).'">', '</a>').'</p>
		</div>';
	}

	function notices(){
		$uid = get_current_user_id();
		$submeta = get_user_meta($uid, 'bkf_sub_notice_dismissed', true);

		if(!$submeta){
			/* translators: %1s: subscription link. %2s: dashboard link. %3s: Canny roadmap link. */
			echo '<div class="notice notice-bkf is-dismissible"><p>'.sprintf(__('Want to get notified when updates/improvements to FloristPress are available? <a href="%1s" target="_blank">Click here</a> to subscribe. You can also keep up to date by reading the news feed on your <a href="%2s">dashboard</a>, and by checking <a href="%3s" target="_blank">Canny</a>.','bakkbone-florist-companion'), 'https://plugins.bkbn.au/bkf-subscribe/', admin_url('index.php'), 'https://floristpress.canny.io/').'</p><a class="notice-dismiss" style="text-decoration:none;" href="'.admin_url('admin-ajax.php?action=bkf_sub_notice_dismissed&uid='.$uid).'"></a></div>';
		}
	}

	function installwoo(){
		$plugin_data = get_plugin_data(__BKF_FILE__);
		echo '<div class="notice-error notice">
			<p>'. sprintf(__('<strong>%s</strong> requires WooCommerce to be installed and activated on your site.','bakkbone-florist-companion'), $plugin_data["Name"]).'</p>
		</div>';
	}

	function dashwidgets() {
		if (bkf_is_woocommerce_active()) {
			wp_add_dashboard_widget('bkf_today', __("Today's Deliveries", "bakkbone-florist-companion"), [$this, 'dashtoday']);
			wp_add_dashboard_widget('bkf_recent', __("Recent Orders", "bakkbone-florist-companion"), [$this, 'dashrecent']);
			wp_add_dashboard_widget('bkf_shipping', __("Delivery Methods", 'bakkbone-florist-companion'), [$this, 'dashshipping']);
			wp_add_dashboard_widget('bkf_blocks', __("Blocked Delivery Dates", 'bakkbone-florist-companion'), [$this, 'dashblocks']);
		}
		wp_add_dashboard_widget('bkf_news', __("FloristPress", 'bakkbone-florist-companion'), [$this, 'dashnews']);
	}

	function dashtoday() {
		$todayserver = strtotime('today midnight');
		$todaylocal = strtotime('today midnight '.wp_timezone_string());
		$array = wc_get_orders(array(
			'type' => 'shop_order',
			'status' => array('wc-new','wc-accept','wc-processing','wc-completed','wc-scheduled','wc-prepared','wc-collect','wc-out','wc-relayed','wc-processed','wc-collected','wc-phone-draft','wc-invoiced'),
			'limit' => 999,
			)
		);
		$orders = [];
		foreach ($array as $value) {
			if ($value->get_meta('_delivery_timestamp', true) == $todayserver || $value->get_meta('_delivery_timestamp', true) == $todaylocal) {
				$orders[] = $value;
			}
		}
		if(!count($orders)){
			echo '<p>'.esc_html__('No deliveries today...', 'bakkbone-florist-companion').'</p>';
		} else {
			echo '<ul>';
			foreach($orders as $wcorder){
				$on = $wcorder->get_id();
				$recipient = $wcorder->get_formatted_shipping_full_name();
				$customer = $wcorder->get_formatted_billing_full_name();
				$suburb = $wcorder->get_shipping_city();
				$url = $wcorder->get_edit_order_url();
				$phone = $wcorder->get_shipping_phone();
				$shipping = 0;
				$shipping = $wcorder->needs_shipping_address();
				$pickup = bkf_order_has_physical($on) && !$shipping ? 1 : 0;
				$wsnonce = wp_create_nonce("bkf");
				$wsurl = admin_url( 'admin-ajax.php?action=bkfdw&order_id=' . $on . '&nonce=' . $wsnonce );

				if($shipping){
					echo '<li><h3><a href="'.$url.'"><strong>#'.$on." - ". $suburb.'</strong></a></h3><p>'.$recipient.' - <a href="tel:'.$phone.'">'.$phone.'</a></strong></p><p><strong><a href="'.$wsurl.'">'.get_option('bkf_pdf_setting')['ws_title'].'</a></strong></p></li>';
				} elseif($pickup) {
					echo '<li><h3><a href="'.$url.'"><strong>#'.$on.' - '.esc_html__('Pickup', 'bakkbone-florist-companion').', '.$customer.'</strong></h3><p><strong><a href="'.$wsurl.'">'.get_option('bkf_pdf_setting')['ws_title'].'</a></strong></p></li>';
				} else {
					echo '<li><h3><a href="'.$url.'"><strong>#'.$on.', '.$customer.'</strong></a></h3></li>';
				}
			}
			echo '</ul>';
		}
	}

	function dashrecent(){
		$recentorders = wc_get_orders(array(
			'type' => 'shop_order',
			'limit' => 5,
			'status' => array('wc-new','wc-accept','wc-processing','wc-completed','wc-scheduled','wc-prepared','wc-collect','wc-out','wc-relayed','wc-processed','wc-collected','wc-phone-draft','wc-invoiced'),
			'orderby' => 'date',
			'order' => 'DESC',
		));
		if(count($recentorders) == 0){
			echo '<p>'.esc_html__('No orders yet...', 'bakkbone-florist-companion').'</p>';
		} else {
			echo '<ul>';
			foreach($recentorders as $wcorder){
				$on = $wcorder->get_id();
				$value = $wcorder->get_formatted_order_total();
				$url = $wcorder->get_edit_order_url();
				$suburb = $wcorder->get_shipping_city();
				$phone = $wcorder->get_shipping_phone();
				$ts = $wcorder->get_meta('_delivery_timestamp', true);
				$dd = wp_date("l, j F Y", $ts);
				if($wcorder->get_meta('_delivery_timeslot', false)){
					$ts = $wcorder->get_meta($on, '_delivery_timeslot', true);
					$dd .= '<br>'.$ts;
				}
				$shipping = 0;
				$shipping = $wcorder->needs_shipping_address();
				$pickup = bkf_order_has_physical($on) && !$shipping ? 1 : 0;
				$wsnonce = wp_create_nonce("bkf");
				$wsurl = admin_url( 'admin-ajax.php?action=bkfdw&order_id=' . $on . '&nonce=' . $wsnonce );

				if($shipping){
					$text = '<li><h3><a href="'.$url.'" target="_blank"><strong>#'.$on." - ".$suburb.', '.$value.'</strong></a></h3><p>'.$dd.'</p><p><strong><a href="'.$wsurl.'">'.get_option('bkf_pdf_setting')['ws_title'].'</a></strong></p></li>';
				} elseif ($pickup) {
					$text = '<li><h3><a href="'.$url.'" target="_blank"><strong>#'.$on.' - '.esc_html__('Pickup', 'bakkbone-florist-companion').', '.$value.'</strong></a></h3><p>'.$dd.'</p><p><strong><a href="'.$wsurl.'">'.get_option('bkf_pdf_setting')['ws_title'].'</a></strong></p></li>';
				} else {
					$text = '<li><h3><a href="'.$url.'" target="_blank"><strong>#'.$on." - ". $value.'</strong></a></h3></li>';
				}

				echo $text;
			}
			echo '</ul>';
		}
	}

	function dashnews(){
		$url = 'https://news.bkbn.au/bkf.xml';
		$feed = bkf_get_rss_feed($url);
		$guid = array_column($feed, 'guid');
		array_multisort($guid, SORT_DESC, SORT_STRING, $feed);
		$feed = array_slice($feed, 0, 3, true);
		echo '<div style="text-align:center"><img src="'.__BKF_URL__.'assets/img/B22_landscape_250.png" style="max-width:150px;"/><h3>'.esc_html__('News and updates from the FloristPress team', 'bakkbone-florist-companion').'</h3></div><ul>';
		foreach($feed as $item){
			$ts = strtotime($item->pubDate);
			$date = wp_date("D j<\s\up>S</\s\up> M 'y, H:i", $ts);
			echo '<li><h3>'.$item->title.'</h3><p>'.$item->description.'</p><p class="date"><em>'.$date.'</em></p></li>';
		}
		echo '</ul><p>'.sprintf(__('You can also keep up to date and request features/integrations on <a href="%s" target="_blank">Canny</a>.'), 'https://floristpress.canny.io/').'</p>';
	}

	function dashshipping(){
		?>
		<script>
			function showResult(str) {
				if (str.length==0) {
					document.getElementById("livesearch").innerHTML="";
					document.getElementById("livesearch").style.border="0px";
					document.getElementById("livesearch").style.backgroundColor="initial";
					document.getElementById("livesearch").style.boxShadow="none";
					document.getElementById("livesearch").style.borderRadius="0";
					return;
				}
				var xmlhttp=new XMLHttpRequest();
				xmlhttp.onreadystatechange=function() {
					if (this.readyState==4 && this.status==200) {
						document.getElementById("livesearch").innerHTML=this.responseText;
						document.getElementById("livesearch").style.border="1px solid #A5ACB2";
						document.getElementById("livesearch").style.backgroundColor="#FFFFFF";
						document.getElementById("livesearch").style.boxShadow="5px 5px 10px #222222";
						document.getElementById("livesearch").style.borderRadius="5px";
					}
				}
				xmlhttp.open("GET","<?php echo admin_url('admin-ajax.php?action=bkf_search_suburbs&query=') ?>"+str,true);
				xmlhttp.send();
			}
		</script>
		<div style="position:relative;">
			<form>
		 		<input type="text" class="bkf-form-control" style="width:100%;" placeholder="<?php esc_html_e('Start typing to check a suburb\'s cost...', 'bakkbone-florist-companion'); ?>" onkeyup="showResult(this.value)">
				<div id="livesearch" style="position:absolute;bottom:auto;left:0;"></div>
				<p class="description"><?php esc_html_e('This search box checks only FloristPress delivery methods, and does not check states/zones etc.', 'bakkbone-florist-companion'); ?></p>
			</form>
		</div>
		<?php

		$zones = bkf_get_shipping_zones();
		echo '<ul>';
		foreach($zones as $zone){
		    echo '<li><h3><a href="'.esc_url( add_query_arg( array( 'page' => 'wc-settings', 'tab' => 'shipping', 'zone_id' => $zone['id'] ), get_admin_url() . 'admin.php' ) ).'" target="_blank"><strong>'.$zone['name'].'</a></strong></h3><p>'.wp_kses_post(sprintf(__('<strong>Location:</strong> %s', 'bakkbone-florist-companion'), $zone['location'])).'</p>';
		    if(empty($zone['methods'])){
	            echo '<p>'.esc_html__('No delivery methods currently configured for this Zone.', 'bakkbone-florist-companion').'</p>';
		    } else {
    		    foreach($zone['methods'] as $method){
    		        $link = esc_url( add_query_arg( array( 'page' => 'wc-settings', 'tab' => 'shipping', 'instance_id' => $method['instanceid'] ), get_admin_url() . 'admin.php' ) );
    		        echo '<div style="display:flex;flex-direction:column;width:calc(100% - 20px);margin:5px;padding:5px;border:1px solid grey;border-radius:5px;"><h4 style="margin-bottom:0;"><em><small>'.$method['title'].'</small></em><br><a href="'.$link.'" target="_blank"><strong>'.$method['usertitle'].'</strong></a> ('.bkf_currency_symbol().number_format(bkf_calc_cost($method['cost']), 2, ".", "").')</h4>';
    		        if($method['type'] == 'floristpress'){
    		            if(empty($method['method_suburbs'])){
    		                echo '<p>'.esc_html__('No suburbs have been configured for this method, and it will not be available to any customers.', 'bakkbone-florist-companion'),'</p>';
    		            } else {
    		                $suburbs = $method['method_suburbs'];
    		                sort($suburbs);
    		                echo '<div style="columns:50px 3;">';
    		                foreach($suburbs as $suburb){
    		                    echo '<p>'.$suburb.'</p>';
    		                }
    		                echo '</div>';
    		            }
    		        }
    		        echo '</div>';
    		    }
		    }

		    echo '</li>';
		}
		echo '</ul>';
	}

	function dashblocks(){
		$bkf_dd_closed = get_option("bkf_dd_closed");
		$closedsort = $bkf_dd_closed;
		ksort($closedsort);
		$bkf_dd_full = get_option("bkf_dd_full");
		$fullsort = $bkf_dd_full;
		ksort($fullsort);
		$nonce = wp_create_nonce("bkf");
		$ajaxurl = admin_url('admin-ajax.php');
		$phtext = __("Date", "bakkbone-florist-companion");
		$ubtext = __("Unblock Date", "bakkbone-florist-companion");
		$ct = __('Closed','bakkbone-florist-companion');
		$gt = __('Closed (Global)','bakkbone-florist-companion');
		$ft = __('Fully Booked','bakkbone-florist-companion');
		/* translators: %1s: opening link tag. %2s: closing link tag. */
		echo '<h3 style="text-align:center;"><strong>'.wp_kses_post(sprintf(__('View and manage your blocked dates in more detail %1shere%2s'), '<a href="'.admin_url('admin.php?page=bkf_ddb').'">','</a>')).'</strong></h3>';
		?>
			<div class="bkf-form" style="width:auto;text-align:center;">
				<form id="add-closed" action="<?php echo $ajaxurl; ?>" />
					<h4 style="margin:0;"><?php esc_html_e('Add Closure Date', 'bakkbone-florist-companion'); ?></h4>
					<?php wp_nonce_field('bkf', 'nonce'); ?>
					<input type="hidden" name="action" value="bkf_dd_add_closed" />
					<p style="margin:5px 0;"><input style="margin-left:0;" type="text" name="date" class="closure-date input-text bkf-form-control" required placeholder="<?php echo $phtext; ?>" autocomplete="off" /></p>
					<p style="margin:5px 0;"><input type="submit" class="button button-primary button-large" value="<?php esc_html_e('Add Date as Closed', 'bakkbone-florist-companion'); ?>"></p>
					<?php if (count($closedsort) > 0){
						echo '<hr><ul style="list-style:inside disc;text-align:left;">';
						foreach ($closedsort as $key => $value) {
							echo '<li>'.esc_html($value).'</li>';
						}
						echo '</ul>';
					} ?>
				</form>
			</div>
			<div class="bkf-form" style="width:auto;text-align:center;">
				<form id="add-full" action="<?php echo $ajaxurl; ?>" />
					<h4 style="margin:0;"><?php esc_html_e('Add Fully Booked Date', 'bakkbone-florist-companion'); ?></h4>
					<?php wp_nonce_field('bkf', 'nonce'); ?>
					<input type="hidden" name="action" value="bkf_dd_add_full" />
					<p style="margin:5px 0;"><input style="margin-left:0;" type="text" name="date" class="closure-date input-text bkf-form-control" required placeholder="<?php echo $phtext; ?>" autocomplete="off" /></p>
					<p style="margin:5px 0;"><input type="submit" class="button button-primary button-large" value="<?php esc_html_e('Add Date as Fully Booked', 'bakkbone-florist-companion'); ?>"></p>
					<?php if (count($fullsort) > 0){
						echo '<hr><ul style="list-style:inside disc;text-align:left;">';
						foreach ($fullsort as $key => $value) {
							echo '<li>'.esc_html($value).'</li>';
						}
						echo '</ul>';
					} ?>
				</form>
			</div>
			<script id="datepicker">
   			jQuery(document).ready(function( $ ) {
   				jQuery(".closure-date").attr( 'readOnly' , 'true' );
   				jQuery(".closure-date").datepicker( {
   					firstDay: 1,
   					minDate: 0,
   					dateFormat: "DD, d MM yy",
   					hideIfNoPrevNext: true,
   					constrainInput: true,
   					beforeShowDay: blockedDates
   				} );
     			 var closedDatesList = [<?php
   		 		$closeddates = get_option('bkf_dd_closed');
   				if( !empty($closeddates)){
   				 $i = 0;
   				 $len = count($closeddates);
   				 foreach($closeddates as $date){
   					 $ts = strtotime($date);
   					 $jsdate = wp_date('n,j,Y',$ts);
   					 if ($i == $len - 1) {
   					 echo '['.$jsdate.']';
   			 } else {
   					 echo '['.$jsdate.'],';
   					 }
   					 $i++;
   			 };}; ?>];
      			 var fullDatesList = [<?php
   		 		$fulldates = get_option('bkf_dd_full');
   				if( !empty($fulldates)){
   				 $i = 0;
   				 $len = count($fulldates);
   				 foreach($fulldates as $date){
   					 $ts = strtotime($date);
   					 $jsdate = wp_date('n,j,Y',$ts);
   					 if ($i == $len - 1) {
   					 echo '['.$jsdate.']';
   				 } else {
   					 echo '['.$jsdate.'],';
   					 }
   					 $i++;
   				 };}; ?>];

   		 function blockedDates(date) {
   			 var w = date.getDay();
   			 var m = date.getMonth();
   			 var d = date.getDate();
   			 var y = date.getFullYear();

   			 <?php if(get_option('bkf_dd_setting')['monday'] == false){ ?>
   				if (date >= new Date("<?php echo bkf_get_monday(); ?>") && w == 1) {
   				return [false, "closed", '<?php echo $gt; ?>'];
   				}<?php }; ?>
   				<?php if(get_option('bkf_dd_setting')['tuesday'] == false){ ?>
   				if (date >= new Date("<?php echo bkf_get_monday(); ?>") && w == 2) {
   				return [false, "closed", '<?php echo $gt; ?>'];
   				}<?php }; ?>
   				<?php if(get_option('bkf_dd_setting')['wednesday'] == false){ ?>
   				if (date >= new Date("<?php echo bkf_get_monday(); ?>") && w == 3) {
   				return [false, "closed", '<?php echo $gt; ?>'];
   				}<?php }; ?>
   				<?php if(get_option('bkf_dd_setting')['thursday'] == false){ ?>
   				if (date >= new Date("<?php echo bkf_get_monday(); ?>") && w == 4) {
   				return [false, "closed", '<?php echo $gt; ?>'];
   				}<?php }; ?>
   				<?php if(get_option('bkf_dd_setting')['friday'] == false){ ?>
   				if (date >= new Date("<?php echo bkf_get_monday(); ?>") && w == 5) {
   				return [false, "closed", '<?php echo $gt; ?>'];
   				}<?php }; ?>
   				<?php if(get_option('bkf_dd_setting')['saturday'] == false){ ?>
   				if (date >= new Date("<?php echo bkf_get_monday(); ?>") && w == 6) {
   				return [false, "closed", '<?php echo $gt; ?>'];
   				}<?php }; ?>
   				<?php if(get_option('bkf_dd_setting')['sunday'] == false){ ?>
   				if (date >= new Date("<?php echo bkf_get_monday(); ?>") && w == 0) {
   				return [false, "closed", '<?php echo $gt; ?>'];
   				}<?php }; ?>

   		 for (i = 0; i < closedDatesList.length; i++) {
   		   if ((m == closedDatesList[i][0] - 1) && (d == closedDatesList[i][1]) && (y == closedDatesList[i][2]))
   		   {
   		   	 return [false,"closed","<?php echo $ct; ?>"];
   		   }
   		 }
   		 for (i = 0; i < fullDatesList.length; i++) {
   		   if ((m == fullDatesList[i][0] - 1) && (d == fullDatesList[i][1]) && (y == fullDatesList[i][2]))
   		   {
   			 return [false,"booked","<?php echo $ft; ?>"];
   		   }
   		 }
   		 return [true];
   	 }
   		 } );
   		</script>
		<?php
	}
}