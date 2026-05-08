<?php
/**
 * @author BAKKBONE Australia
 * @package BKF_Localisation
 * @license GNU General Public License (GPL) 3.0
**/

defined("__BKF_EXEC__") or die("Ah, sweet silence.");

class BKF_Localisation{
	
	private $bkf_localisation_setting = [];
		
	function __construct() {
		global $bkf_localisation_version;
		$bkf_localisation_version = 2.3;
		add_action("plugins_loaded", [$this,"bkf_update_localisation_check"]);
		$this->bkf_localisation_setting = get_option("bkf_localisation_setting");
		if (bkf_is_gravityforms_active()){
			add_filter( "gform_phone_formats" , [$this, 'bkf_au_phone_format'] );
			add_filter( "gform_address_types" , [$this, 'bkf_au_address_format'], 10, 2 );
		}
	}
	
	function bkf_update_localisation_check() {
		global $bkf_localisation_version;
		$current_local = get_option( 'bkf_localisation_version' );
		if ( $current_local != $bkf_localisation_version ) {
			if($current_local == '' || $current_local == null || $current_local < 2.2){
				$bkfoptions = get_option("bkf_options_setting");
				$csheading = array_key_exists('cs_heading', $bkfoptions) ? $bkfoptions['cs_heading'] : '';
				$noship = array_key_exists('noship', $bkfoptions) ? $bkfoptions['noship'] : '';
				$oldlocal = get_option("bkf_localisation_setting");
				$newlocal = [];
				if($oldlocal['billing_label_business'] !== null && $oldlocal['billing_label_business'] !== ''){
					$newlocal['billing_label_business'] = $oldlocal['billing_label_business'];
				} else {
					$newlocal['billing_label_business'] = __('Business Name', 'bakkbone-florist-companion');
				}
				if($oldlocal['global_label_state'] !== null && $oldlocal['global_label_state'] !== ''){
					$newlocal['global_label_state'] = $oldlocal['global_label_state'];
				} else {
					$newlocal['global_label_state'] = __('State/Territory', 'bakkbone-florist-companion');
				}
				if($oldlocal['global_label_suburb'] !== null && $oldlocal['global_label_suburb'] !== ''){
					$newlocal['global_label_suburb'] = $oldlocal['global_label_suburb'];
				} else {
					$newlocal['global_label_suburb'] = __('Suburb', 'bakkbone-florist-companion');
				}
				if($oldlocal['global_label_postcode'] !== null && $oldlocal['global_label_postcode'] !== ''){
					$newlocal['global_label_postcode'] = $oldlocal['global_label_postcode'];
				} else {
					$newlocal['global_label_postcode'] = __('Postcode', 'bakkbone-florist-companion');
				}
				if($oldlocal['global_label_country'] !== null && $oldlocal['global_label_country'] !== ''){
					$newlocal['global_label_country'] = $oldlocal['global_label_country'];
				} else {
					$newlocal['global_label_country'] = __('Country', 'bakkbone-florist-companion');
				}
				if($oldlocal['global_label_telephone'] !== null && $oldlocal['global_label_telephone'] !== ''){
					$newlocal['global_label_telephone'] = $oldlocal['global_label_telephone'];
				} else {
					$newlocal['global_label_telephone'] = __('Phone', 'bakkbone-florist-companion');
				}
				if($oldlocal['delivery_label_business'] !== null && $oldlocal['delivery_label_business'] !== ''){
					$newlocal['delivery_label_business'] = $oldlocal['delivery_label_business'];
				} else {
					$newlocal['delivery_label_business'] = __('Business/Hospital/Hotel Name', 'bakkbone-florist-companion');
				}
				if($oldlocal['delivery_description_business'] !== null && $oldlocal['delivery_description_business'] !== ''){
					$newlocal['delivery_description_business'] = $oldlocal['delivery_description_business'];
				} else {
					$newlocal['delivery_description_business'] = __('For hospitals/hotels/etc., please include ward/room information if known', 'bakkbone-florist-companion');
				}
				if($oldlocal['delivery_label_notes'] !== null && $oldlocal['delivery_label_notes'] !== ''){
					$newlocal['delivery_label_notes'] = $oldlocal['delivery_label_notes'];
				} else {
					$newlocal['delivery_label_notes'] = __('Anything we need to know about the address?', 'bakkbone-florist-companion');
				}
				if($oldlocal['delivery_description_notes'] !== null && $oldlocal['delivery_description_notes'] !== ''){
					$newlocal['delivery_description_notes'] = $oldlocal['delivery_description_notes'];
				} else {
					$newlocal['delivery_description_notes'] = __('eg. gate code, fence, dog, etc.', 'bakkbone-florist-companion');
				}
				if($oldlocal['additional_description_cardmessage'] !== null && $oldlocal['additional_description_cardmessage'] !== ''){
					$newlocal['additional_description_cardmessage'] = $oldlocal['additional_description_cardmessage'];
				} else {
					$newlocal['additional_description_cardmessage'] = __("We'll include this with your gift. Maximum %s characters.", 'bakkbone-florist-companion');
				}
				if($oldlocal['csheading'] !== null && $oldlocal['csheading'] !== ''){
					$newlocal['csheading'] = $oldlocal['csheading'];
				} elseif($csheading !== null && $csheading !== '') {
					$newlocal['csheading'] = $csheading;
				} else {
					$newlocal['csheading'] = __('How about adding...','bakkbone-florist-companion');
				}
				if($oldlocal['noship'] !== null && $oldlocal['noship'] !== ''){
					$newlocal['noship'] = $oldlocal['noship'];
				} elseif($noship !== null && $noship !== '') {
					$newlocal['noship'] = $noship;
				} else {
					$newlocal['noship'] = __('You have selected a suburb or region we do not deliver to.','bakkbone-florist-companion');
				}
				update_option('bkf_localisation_setting', $newlocal);
				unset($bkfoptions['cs_heading'],$bkfoptions['noship']);
				update_option('bkf_options_setting',$bkfoptions);
				update_option('bkf_localisation_version', $bkf_localisation_version);
			}
		}
	}
		
	function bkf_au_phone_format( $phone_formats ) {
		$phone_formats['au'] = array(
			'label'	   => __('Australia Mobile', 'bakkbone-florist-companion'),
			'mask'		=> '9999 999 999',
			'regex'	   => '/^\d{4} \d{3} \d{3}$/',
			'instruction' => '#### ### ###',
		);
		$phone_formats['aul'] = array(
			'label'	   => __('Australia Landline', 'bakkbone-florist-companion'),
			'mask'		=> '99 9999 9999',
			'regex'	   => '/^\d{2} \d{4} \d{4}$/',
			'instruction' => '## #### ####',
		);
		return $phone_formats;
	}
	
	function bkf_au_address_format( $formats, $form_id ) {
		$formats['au'] = array(
			'label'	   => esc_html__( 'Australia', 'bakkbone-florist-companion' ),
			'zip_label'   => gf_apply_filters( array( 'gform_address_zip', $form_id ), esc_html( get_option('bkf_localisation_setting')['global_label_postcode'] ), $form_id ),
			'state_label' => gf_apply_filters( array( 'gform_address_state', $form_id ), esc_html( get_option('bkf_localisation_setting')['global_label_state'] ), $form_id ),
			'country'	 => 'Australia',
			'states'	  => array(
				'',
				'ACT'	=> __('Australian Capital Territory', 'bakkbone-florist-companion'),
				'NSW'	=> __('New South Wales', 'bakkbone-florist-companion'),
				'NT'	=> __('Northern Territory', 'bakkbone-florist-companion'),
				'QLD'	=> __('Queensland', 'bakkbone-florist-companion'),
				'SA'	=> __('South Australia', 'bakkbone-florist-companion'),
				'TAS'	=> __('Tasmania', 'bakkbone-florist-companion'),
				'VIC'	=> __('Victoria', 'bakkbone-florist-companion'),
				'WA'	=> __('Western Australia', 'bakkbone-florist-companion'),
			)
		);
		
		return $formats;
	}
}