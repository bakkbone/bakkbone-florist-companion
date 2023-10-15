<?php

/**
 * @author BAKKBONE Australia
 * @package BKF\Petals\CPT
 * @license GNU General Public License (GPL) 3.0
**/

namespace BKF\Petals;

defined("BKF_EXEC") or die("Ah, sweet silence.");

class CPT{

	function __construct(){
		$bkfoptions = get_option("bkf_features_setting");
		if($bkfoptions["petals_on"]) {
			add_action("init", array($this, "bkfRegisterPetalsOrderPost"));
			add_action("load-post.php", array($this, "popost_help_tab"));
			add_action("load-post-new.php", array($this, "popost_help_tab"));
			add_action("load-edit.php", array($this, "popost_help_tab"));
			add_action('init', array($this, 'bkfmakePrintContentsSaySaved'));
			add_action('acf/init', array($this, 'bkf_acf_add_local_field_groups'));
			add_action('acf/save_post', array($this, 'bkf_save_po'), 10, 3);
			add_filter('single_template', array($this, 'bkf_po_template'));
			add_filter('post_row_actions', array($this, 'bkf_qe'), 10, 2);
			add_filter('pre_get_document_title', array($this, 'bkf_po_title'));
			add_filter('get_edit_post_link', array($this, 'bkf_po_edit_link'));
			add_filter('manage_bkf_petals_order_posts_columns', array($this, 'bkf_po_table'));
			add_action('manage_bkf_petals_order_posts_custom_column', array($this, 'bkf_po_table_content'), 10, 2);
			add_filter('manage_edit-bkf_petals_order_sortable_columns', array($this, 'bkf_po_col_sort'), 10, 1 );
			add_action('pre_get_posts', array($this, 'bkf_po_filter') );
			add_filter('bulk_actions-edit-bkf_petals_order', 'removeBulkActionsInput' );
			if(is_admin()){
				add_filter('comments_clauses', array($this, 'bkf_exclude_order_comments'), 10, 1 );
				add_filter('comment_feed_where', array($this, 'exclude_order_comments_from_feed_where') );
			}
		}
	}
	
	function removeBulkActionsInput ($actions) {
			return [];
	}
	
	public static function bkf_exclude_order_comments( $clauses ) {
		$clauses['where'] .= ( $clauses['where'] ? ' AND ' : '' ) . " comment_type != 'petals_order_note' ";
		return $clauses;
	}
	
	public static function bkf_exclude_order_comments_from_feed_where( $where ) {
		return $where . ( $where ? ' AND ' : '' ) . " comment_type != 'order_note' ";
	}
	
	function bkf_po_table( $columns ) {
		$new = [];

		foreach($columns as $key => $title) {
			if ($key=='title'){
				$new['potitle'] = _x('Petals Order Number', 'order list column title', 'bakkbone-florist-companion');
				$new['podeldate'] = __('Delivery Date', 'bakkbone-florist-companion');
				$new['porecipient'] = __('Recipient\'s Name', 'bakkbone-florist-companion');
				$new['potown'] = __('Town/City', 'bakkbone-florist-companion');
			}
			$new[$key] = $title;
		}
		
		unset($new['title'], $new['cb']);
		return $new;
	}
	
	function bkf_po_table_content( $column, $post_id ) {
		if($column == 'potitle'){
			$post = get_post($post_id);
			$link = get_permalink($post);
			$on = get_post_meta($post_id, '_petals_on', true);
			if($on == ''){
				$on = get_post_meta($post_id, 'sendid', true);
			}
			echo '<a class="row-title" href="'.$link.'" aria-label="'.$on.'">'.$on.'</a>';
		}
		if($column == 'podeldate'){
			$ddraw = get_post_meta($post_id, 'deldate', true);
			$ddtime = strtotime($ddraw);
			echo '<p>'.date('l, jS F Y', $ddtime).'</p>';
		}
		if($column == 'porecipient'){
			echo '<p>'.get_post_meta($post_id, 'recipient', true).'</p>';
		}
		if($column == 'potown'){
			echo '<p>'.get_post_meta($post_id, 'town', true).'</p>';
		}
	}
	
	function bkf_po_col_sort( $a ){
		return wp_parse_args( array( 'potitle' => 'sendid', 'podeldate' => 'deldate', 'potown' => 'town'), $a );
	}
	
	function bkf_po_filter( $query ) {
		if ( ! is_admin() ) return;
		if( empty( $_GET['orderby'] ) || empty( $_GET['order'] ) ) return;
		if( $_GET['orderby'] == 'sendid' ) {
			$query->set('meta_key', 'sendid' );
			$query->set('orderby', 'meta_value');
			$query->set('order', $_GET['order'] );
		} elseif( $_GET['orderby'] == 'deldate' ) {
			$query->set('meta_key', 'deldate' );
			$query->set('orderby', 'meta_value');
			$query->set('order', $_GET['order'] );
		} elseif( $_GET['orderby'] == 'town' ) {
			$query->set('meta_key', 'town' );
			$query->set('orderby', 'meta_value');
			$query->set('order', $_GET['order'] );
		}
		return $query;
	}

	function bkf_po_edit_link($link){
		preg_match('/\=\d+/', $link, $matches);
		preg_match('/\d+/', $matches[0], $matches2);
		$post = get_post($matches2[0]);
		if($post->post_type == 'bkf_petals_order'){
			return null;
		} else {
			return $link;
		}
	}
	
	function bkf_po_title($title){
		$thispost = get_post(0, 'OBJECT');
		$post_id = $thispost->post_title;
		if ( get_post_type() !== 'bkf_petals_order' ){
			return $title;
		} else {
			$title = sprintf(__('Petals Order #%s', 'bakkbone-florist-companion'), $post_id);
		}
		return $title;
	}
	
	function bkf_qe( $actions = [], $post = null ) {
		if ( get_post_type() !== 'bkf_petals_order' ) {
			return $actions;
		}
		if ( isset( $actions['inline hide-if-no-js'] ) ) {
			unset( $actions['inline hide-if-no-js'] );
		}
		return $actions;
	}
	
	function bkf_po_template($template){
		global $post;
		$post_type = $post->post_type;
		if( 'bkf_petals_order' === $post_type ){
			$template = dirname( __FILE__ ) . '/templates/petals-single.php';
		}		
		return $template;
	}
	
	function bkf_save_po($post_id){
		if(get_post_type($post_id) == 'bkf_petals_order'){
			$sendid = implode(get_metadata( 'post', $post_id, 'sendid' ));
			wp_update_post( array(
				'ID'			=> $post_id,
				'post_title'	=> $sendid,
				'post_name'	=> $sendid
			) );
			$tvalue = get_post_custom_values('tvalue', $post_id)[0];
			update_post_meta($post_id, 'tvalue', number_format($tvalue,2,".",""), $tvalue);
			$upsellAmt = get_post_custom_values('upsellAmt', $post_id)[0];
			update_post_meta($post_id, 'upsellAmt', number_format($upsellAmt,2,".",""), $upsellAmt);
			$crtycode = get_post_custom_values('crtycode', $post_id)[0];
			update_post_meta($post_id,'crtyname', array(
				'AF' => 'Afghanistan',
				'AX' => 'Åland Islands',
				'AL' => 'Albania',
				'DZ' => 'Algeria',
				'AS' => 'American Samoa',
				'AD' => 'Andorra',
				'AO' => 'Angola',
				'AI' => 'Anguilla',
				'AQ' => 'Antarctica',
				'AG' => 'Antigua and Barbuda',
				'AR' => 'Argentina',
				'AM' => 'Armenia',
				'AW' => 'Aruba',
				'AU' => 'Australia',
				'AT' => 'Austria',
				'AZ' => 'Azerbaijan',
				'BS' => 'Bahamas',
				'BH' => 'Bahrain',
				'BD' => 'Bangladesh',
				'BB' => 'Barbados',
				'BY' => 'Belarus',
				'BE' => 'Belgium',
				'BZ' => 'Belize',
				'BJ' => 'Benin',
				'BM' => 'Bermuda',
				'BT' => 'Bhutan',
				'BO' => 'Bolivia (Plurinational State of)',
				'BQ' => 'Bonaire, Sint Eustatius and Saba',
				'BA' => 'Bosnia and Herzegovina',
				'BW' => 'Botswana',
				'BV' => 'Bouvet Island',
				'BR' => 'Brazil',
				'IO' => 'British Indian Ocean Territory',
				'BN' => 'Brunei Darussalam',
				'BG' => 'Bulgaria',
				'BF' => 'Burkina Faso',
				'BI' => 'Burundi',
				'CV' => 'Cabo Verde',
				'KH' => 'Cambodia',
				'CM' => 'Cameroon',
				'CA' => 'Canada',
				'KY' => 'Cayman Islands',
				'CF' => 'Central African Republic',
				'TD' => 'Chad',
				'CL' => 'Chile',
				'CN' => 'China',
				'CX' => 'Christmas Island',
				'CC' => 'Cocos (Keeling) Islands',
				'CO' => 'Colombia',
				'KM' => 'Comoros',
				'CG' => 'Congo',
				'CD' => 'Congo, Democratic Republic of the',
				'CK' => 'Cook Islands',
				'CR' => 'Costa Rica',
				'CI' => 'Côte d\'Ivoire',
				'HR' => 'Croatia',
				'CU' => 'Cuba',
				'CW' => 'Curaçao',
				'CY' => 'Cyprus',
				'CZ' => 'Czechia',
				'DK' => 'Denmark',
				'DJ' => 'Djibouti',
				'DM' => 'Dominica',
				'DO' => 'Dominican Republic',
				'EC' => 'Ecuador',
				'EG' => 'Egypt',
				'SV' => 'El Salvador',
				'GQ' => 'Equatorial Guinea',
				'ER' => 'Eritrea',
				'EE' => 'Estonia',
				'SZ' => 'Eswatini',
				'ET' => 'Ethiopia',
				'FK' => 'Falkland Islands (Malvinas)',
				'FO' => 'Faroe Islands',
				'FJ' => 'Fiji',
				'FI' => 'Finland',
				'FR' => 'France',
				'GF' => 'French Guiana',
				'PF' => 'French Polynesia',
				'TF' => 'French Southern Territories',
				'GA' => 'Gabon',
				'GM' => 'Gambia',
				'GE' => 'Georgia',
				'DE' => 'Germany',
				'GH' => 'Ghana',
				'GI' => 'Gibraltar',
				'GR' => 'Greece',
				'GL' => 'Greenland',
				'GD' => 'Grenada',
				'GP' => 'Guadeloupe',
				'GU' => 'Guam',
				'GT' => 'Guatemala',
				'GG' => 'Guernsey',
				'GN' => 'Guinea',
				'GW' => 'Guinea-Bissau',
				'GY' => 'Guyana',
				'HT' => 'Haiti',
				'HM' => 'Heard Island and McDonald Islands',
				'VA' => 'Holy See',
				'HN' => 'Honduras',
				'HK' => 'Hong Kong',
				'HU' => 'Hungary',
				'IS' => 'Iceland',
				'IN' => 'India',
				'ID' => 'Indonesia',
				'IR' => 'Iran (Islamic Republic of)',
				'IQ' => 'Iraq',
				'IE' => 'Ireland',
				'IM' => 'Isle of Man',
				'IL' => 'Israel',
				'IT' => 'Italy',
				'JM' => 'Jamaica',
				'JP' => 'Japan',
				'JE' => 'Jersey',
				'JO' => 'Jordan',
				'KZ' => 'Kazakhstan',
				'KE' => 'Kenya',
				'KI' => 'Kiribati',
				'KP' => 'Korea (Democratic People\'s Republic of)',
				'KR' => 'Korea, Republic of',
				'KW' => 'Kuwait',
				'KG' => 'Kyrgyzstan',
				'LA' => 'Lao People\'s Democratic Republic',
				'LV' => 'Latvia',
				'LB' => 'Lebanon',
				'LS' => 'Lesotho',
				'LR' => 'Liberia',
				'LY' => 'Libya',
				'LI' => 'Liechtenstein',
				'LT' => 'Lithuania',
				'LU' => 'Luxembourg',
				'MO' => 'Macao',
				'MG' => 'Madagascar',
				'MW' => 'Malawi',
				'MY' => 'Malaysia',
				'MV' => 'Maldives',
				'ML' => 'Mali',
				'MT' => 'Malta',
				'MH' => 'Marshall Islands',
				'MQ' => 'Martinique',
				'MR' => 'Mauritania',
				'MU' => 'Mauritius',
				'YT' => 'Mayotte',
				'MX' => 'Mexico',
				'FM' => 'Micronesia (Federated States of)',
				'MD' => 'Moldova, Republic of',
				'MC' => 'Monaco',
				'MN' => 'Mongolia',
				'ME' => 'Montenegro',
				'MS' => 'Montserrat',
				'MA' => 'Morocco',
				'MZ' => 'Mozambique',
				'MM' => 'Myanmar',
				'NA' => 'Namibia',
				'NR' => 'Nauru',
				'NP' => 'Nepal',
				'NL' => 'Netherlands',
				'NC' => 'New Caledonia',
				'NZ' => 'New Zealand',
				'NI' => 'Nicaragua',
				'NE' => 'Niger',
				'NG' => 'Nigeria',
				'NU' => 'Niue',
				'NF' => 'Norfolk Island',
				'MK' => 'North Macedonia',
				'MP' => 'Northern Mariana Islands',
				'NO' => 'Norway',
				'OM' => 'Oman',
				'PK' => 'Pakistan',
				'PW' => 'Palau',
				'PS' => 'Palestine, State of',
				'PA' => 'Panama',
				'PG' => 'Papua New Guinea',
				'PY' => 'Paraguay',
				'PE' => 'Peru',
				'PH' => 'Philippines',
				'PN' => 'Pitcairn',
				'PL' => 'Poland',
				'PT' => 'Portugal',
				'PR' => 'Puerto Rico',
				'QA' => 'Qatar',
				'RE' => 'Réunion',
				'RO' => 'Romania',
				'RU' => 'Russian Federation',
				'RW' => 'Rwanda',
				'BL' => 'Saint Barthélemy',
				'SH' => 'Saint Helena, Ascension and Tristan da Cunha',
				'KN' => 'Saint Kitts and Nevis',
				'LC' => 'Saint Lucia',
				'MF' => 'Saint Martin (French part)',
				'PM' => 'Saint Pierre and Miquelon',
				'VC' => 'Saint Vincent and the Grenadines',
				'WS' => 'Samoa',
				'SM' => 'San Marino',
				'ST' => 'Sao Tome and Principe',
				'SA' => 'Saudi Arabia',
				'SN' => 'Senegal',
				'RS' => 'Serbia',
				'SC' => 'Seychelles',
				'SL' => 'Sierra Leone',
				'SG' => 'Singapore',
				'SX' => 'Sint Maarten (Dutch part)',
				'SK' => 'Slovakia',
				'SI' => 'Slovenia',
				'SB' => 'Solomon Islands',
				'SO' => 'Somalia',
				'ZA' => 'South Africa',
				'GS' => 'South Georgia and the South Sandwich Islands',
				'SS' => 'South Sudan',
				'ES' => 'Spain',
				'LK' => 'Sri Lanka',
				'SD' => 'Sudan',
				'SR' => 'Suriname',
				'SJ' => 'Svalbard and Jan Mayen',
				'SE' => 'Sweden',
				'CH' => 'Switzerland',
				'SY' => 'Syrian Arab Republic',
				'TW' => 'Taiwan, Province of China',
				'TJ' => 'Tajikistan',
				'TZ' => 'Tanzania, United Republic of',
				'TH' => 'Thailand',
				'TL' => 'Timor-Leste',
				'TG' => 'Togo',
				'TK' => 'Tokelau',
				'TO' => 'Tonga',
				'TT' => 'Trinidad and Tobago',
				'TN' => 'Tunisia',
				'TR' => 'Türkiye',
				'TM' => 'Turkmenistan',
				'TC' => 'Turks and Caicos Islands',
				'TV' => 'Tuvalu',
				'UG' => 'Uganda',
				'UA' => 'Ukraine',
				'AE' => 'United Arab Emirates',
				'GB' => 'United Kingdom of Great Britain and Northern Ireland',
				'UM' => 'United States Minor Outlying Islands',
				'US' => 'United States of America',
				'UY' => 'Uruguay',
				'UZ' => 'Uzbekistan',
				'VU' => 'Vanuatu',
				'VE' => 'Venezuela (Bolivarian Republic of)',
				'VN' => 'Viet Nam',
				'VG' => 'Virgin Islands (British)',
				'VI' => 'Virgin Islands (U.S.)',
				'WF' => 'Wallis and Futuna',
				'EH' => 'Western Sahara',
				'YE' => 'Yemen',
				'ZM' => 'Zambia',
				'ZW' => 'Zimbabwe'
			)[$crtycode]);
			
			$mn = get_option('bkf_petals_setting')['mn'];
			$password = get_option('bkf_petals_setting')['ppw'];
			$pw = base64_decode($password);
			$sendidraw = get_post_custom_values('sendid', $post_id);
			$recipientraw = get_post_custom_values('recipient', $post_id);
			$surnameraw = get_post_custom_values('surname', $post_id);
			$addressraw = get_post_custom_values('address', $post_id);
			$townraw = get_post_custom_values('town', $post_id);
			$stateraw = get_post_custom_values('state', $post_id);
			$postalcoderaw = get_post_custom_values('postalcode', $post_id);
			$crtynameraw = get_post_custom_values('crtyname', $post_id);
			$crtycoderaw = get_post_custom_values('crtycode', $post_id);
			$phoneraw = get_post_custom_values('phone', $post_id);
			$descriptionraw = get_post_custom_values('description', $post_id);
			$messageraw = get_post_custom_values('message', $post_id);
			$commentsraw = get_post_custom_values('comments', $post_id);
			$makeupraw = get_post_custom_values('makeup', $post_id);
			$deldateraw = get_post_custom_values('deldate', $post_id);
			$deltimeraw = get_post_custom_values('deltime', $post_id);
			$tvalueraw = get_post_custom_values('tvalue', $post_id);
			$supplierraw = get_post_custom_values('supplier', $post_id);
			$productidraw = get_post_custom_values('productid', $post_id);
			$contact_nameraw = get_post_custom_values('contact_name', $post_id);
			$contact_emailraw = get_post_custom_values('contact_email', $post_id);
			$contact_phoneraw = get_post_custom_values('contact_phone', $post_id);
			$addresstyperaw = get_post_custom_values('addresstype', $post_id);
			$occasionraw = get_post_custom_values('occasion', $post_id);
			$upsellraw = get_post_custom_values('upsell', $post_id);
			$upsellAmtraw = get_post_custom_values('upsellAmt', $post_id);
			if(!empty($sendidraw && $sendidraw[0] !== '' && $sendidraw[0] !== '0.00')){ $sendid = '<sendid>'.$sendidraw[0].'</sendid>'."\n"; } else { $sendid = ''; }
			if(!empty($recipientraw && $recipientraw[0] !== '' && $recipientraw[0] !== '0.00')){ $recipient = '<recipient>'.$recipientraw[0].'</recipient>'."\n"; } else { $recipient = ''; }
			if(!empty($surnameraw && $surnameraw[0] !== '' && $surnameraw[0] !== '0.00')){ $surname = '<surname>'.$surnameraw[0].'</surname>'."\n"; } else { $surname = ''; }
			if(!empty($addressraw && $addressraw[0] !== '' && $addressraw[0] !== '0.00')){ $address = '<address>'.$addressraw[0].'</address>'."\n"; } else { $address = ''; }
			if(!empty($townraw && $townraw[0] !== '' && $townraw[0] !== '0.00')){ $town = '<town>'.$townraw[0].'</town>'."\n"; } else { $town = ''; }
			if(!empty($stateraw && $stateraw[0] !== '' && $stateraw[0] !== '0.00')){ $state = '<state>'.$stateraw[0].'</state>'."\n"; } else { $state = ''; }
			if(!empty($postalcoderaw && $postalcoderaw[0] !== '' && $postalcoderaw[0] !== '0.00')){ $postalcode = '<postalcode>'.$postalcoderaw[0].'</postalcode>'."\n"; } else { $postalcode = ''; }
			if(!empty($crtynameraw && $crtynameraw[0] !== '' && $crtynameraw[0] !== '0.00')){ $crtyname = '<crtyname>'.$crtynameraw[0].'</crtyname>'."\n"; } else { $crtyname = ''; }
			if(!empty($crtycoderaw && $crtycoderaw[0] !== '' && $crtycoderaw[0] !== '0.00')){ $crtycode = '<crtycode>'.$crtycoderaw[0].'</crtycode>'."\n"; } else { $crtycode = ''; }
			if(!empty($phoneraw && $phoneraw[0] !== '' && $phoneraw[0] !== '0.00')){ $phone = '<phone>'.$phoneraw[0].'</phone>'."\n"; } else { $phone = ''; }
			if(!empty($descriptionraw && $descriptionraw[0] !== '' && $descriptionraw[0] !== '0.00')){ $description = '<description>'.$descriptionraw[0].'</description>'."\n"; } else { $description = ''; }
			if(!empty($messageraw && $messageraw[0] !== '' && $messageraw[0] !== '0.00')){ $message = '<message>'.$messageraw[0].'</message>'."\n"; } else { $message = ''; }
			if(!empty($commentsraw && $commentsraw[0] !== '' && $commentsraw[0] !== '0.00')){ $comments = '<comments>'.$commentsraw[0].'</comments>'."\n"; } else { $comments = ''; }
			if(!empty($makeupraw && $makeupraw[0] !== '' && $makeupraw[0] !== '0.00')){ $makeup = '<makeup>'.$makeupraw[0].'</makeup>'."\n"; } else { $makeup = ''; }
			if(!empty($deldateraw && $deldateraw[0] !== '' && $deldateraw[0] !== '0.00')){ $deldate = '<deldate>'.$deldateraw[0].'</deldate>'."\n"; } else { $deldate = ''; }
			if(!empty($deltimeraw && $deltimeraw[0] !== '' && $deltimeraw[0] !== '0.00')){ $deltime = '<deltime>'.$deltimeraw[0].'</deltime>'."\n"; } else { $deltime = ''; }
			if(!empty($tvalueraw && $tvalueraw[0] !== '' && $tvalueraw[0] !== '0.00')){ $tvalue = '<tvalue>'.$tvalueraw[0].'</tvalue>'."\n"; } else { $tvalue = ''; }
			if(!empty($supplierraw && $supplierraw[0] !== '' && $supplierraw[0] !== '0.00')){ $supplier = '<supplier>'.$supplierraw[0].'</supplier>'."\n"; } else { $supplier = ''; }
			if(!empty($productidraw && $productidraw[0] !== '' && $productidraw[0] !== '0.00')){ $productid = '<productid>'.$productidraw[0].'</productid>'."\n"; } else { $productid = ''; }
			if(!empty($contact_nameraw && $contact_nameraw[0] !== '' && $contact_nameraw[0] !== '0.00')){ $contact_name = '<contact_name>'.$contact_nameraw[0].'</contact_name>'."\n"; } else { $contact_name = ''; }
			if(!empty($contact_emailraw && $contact_emailraw[0] !== '' && $contact_emailraw[0] !== '0.00')){ $contact_email = '<contact_email>'.$contact_emailraw[0].'</contact_email>'."\n"; } else { $contact_email = ''; }
			if(!empty($contact_phoneraw && $contact_phoneraw[0] !== '' && $contact_phoneraw[0] !== '0.00')){ $contact_phone = '<contact_phone>'.$contact_phoneraw[0].'</contact_phone>'."\n"; } else { $contact_phone = ''; }
			if(!empty($addresstyperaw && $addresstyperaw[0] !== '' && $addresstyperaw[0] !== '0.00')){ $addresstype = '<addresstype>'.$addresstyperaw[0].'</addresstype>'."\n"; } else { $addresstype = ''; }
			if(!empty($occasionraw && $occasionraw[0] !== '' && $occasionraw[0] !== '0.00')){ $occasion = '<occasion>'.$occasionraw[0].'</occasion>'."\n"; } else { $occasion = ''; }
			if(!empty($upsellraw && $upsellraw[0] !== '' && $upsellraw[0] !== '0.00')){ $upsell = '<upsell>'.$upsellraw[0].'</upsell>'."\n"; } else { $upsell = ''; }
			if(!empty($upsellAmtraw && $upsellAmtraw[0] !== '' && $upsellAmtraw[0] !== '0.00')){ $upsellAmt = '<upsellAmt>'.$upsellAmtraw[0].'</upsellAmt>'."\n"; } else { $upsellAmt = ''; }
		
			$url = 'http://pin.petals.com.au/wconnect/wc.isa?pbo~&ctype=34';
			$body = '<?xml version="1.0" encoding="UTF-8"?>'."\n".'<order>'."\n".'<recordtype>01</recordtype>'."\n".'<seller>'.$mn.'</seller>'."\n".'<password>'.$pw.'</password>'.$sendid.$recipient.$surname.$address.$town.$state.$postalcode.$crtyname.$crtycode.$phone.$description.$message.$comments.$makeup.$deldate.$deltime.$tvalue.$supplier.$productid.$contact_name.$contact_email.$contact_phone.$addresstype.$occasion.$upsell.$upsellAmt.''."\n".'</order>';
			$response = wp_remote_post($url, array(
				'method'	=> 'POST',
				'headers'   => array('Content-Type' => 'application/xml'),
				'body'	  => $body
			));
			$rawxml = $response['body'];
			$xml = simplexml_load_string($rawxml);
			$symbol = '</strong>: ';
			$xmlarray = json_decode(json_encode((array)$xml), TRUE);
			unset($xmlarray['password']);
			$implosion = implode('<br><strong>', array_map(
						function($k, $v) use($symbol) { 
							return $k . $symbol . $v;
						}, 
						array_keys($xmlarray), 
						array_values($xmlarray)
						)
					);
				
			if($xml->type == '100'){
				update_post_meta($post_id, '_petals_on', $xmlarray['petalsid']);
				$note = __('<strong>Order sent.<br>Response from Petals: </strong><br>', 'bakkbone-florist-companion') . '<strong>' . $implosion;
				$comment_author_email  = 'bkf@';
				$comment_author_email .= isset( $_SERVER['HTTP_HOST'] ) ? str_replace( 'www.', '', sanitize_text_field( wp_unslash( $_SERVER['HTTP_HOST'] ) ) ) : 'noreply.com';
				$comment_author_email  = sanitize_email( $comment_author_email );
				$commentargs = array(
					'comment_agent'		=> __('Petals API', 'bakkbone-florist-companion'),
					'comment_type'		=> 'petals_order_note',
					'comment_author'	=> __('Petals Exchange', 'bakkbone-florist-companion'),
					'comment_author_email'	=> $comment_author_email,
					'comment_content'	=> $note . '<br><br>' . $fullnote,
					'comment_post_ID'	=> $post_id,
					'comment_approved'	=> 1
				);
				$comment = wp_insert_comment($commentargs, true);
				$wc_emails = WC()->mailer()->get_emails();
				$wc_emails['WC_Email_Petals_Inbound']->trigger( $post_id, $comment );
			} else {
				$note = __('<strong>Order sending failed.<br>Response from Petals: </strong><br>', 'bakkbone-florist-companion') . $xml->text;
				$fullnote = __('<strong>Full transmission from Petals: </strong><br>', 'bakkbone-florist-companion') . '<strong>' . $implosion;
				$comment_author_email  = 'bkf@';
				$comment_author_email .= isset( $_SERVER['HTTP_HOST'] ) ? str_replace( 'www.', '', sanitize_text_field( wp_unslash( $_SERVER['HTTP_HOST'] ) ) ) : 'noreply.com';
				$comment_author_email  = sanitize_email( $comment_author_email );
				$commentargs = array(
					'comment_agent'		=> __('Petals API', 'bakkbone-florist-companion'),
					'comment_type'		=> 'petals_order_note',
					'comment_author'	=> __('Petals Exchange', 'bakkbone-florist-companion'),
					'comment_author_email'	=> $comment_author_email,
					'comment_content'	=> $note . '<br><br>' . $fullnote,
					'comment_post_ID'	=> $post_id,
					'comment_approved'	=> 1
				);
				$comment = wp_insert_comment($commentargs, true);
				$wc_emails = WC()->mailer()->get_emails();
				$wc_emails['WC_Email_Petals_Inbound']->trigger( $post_id, $comment );
			}
			
			header("Location: ".get_permalink($post_id));
			die();
		}
	}
	
	function bkf_acf_add_local_field_groups() {
		acf_add_local_field_group(array(
			'key' => 'group_64191e3faabdb',
			'title' => 'bkf_petals_order_fields',
			'fields' => array(
				array(
					'key' => 'bkf_po_field_sendid',
					'label' => __('Order Number', 'bakkbone-florist-companion'),
					'name' => 'sendid',
					'aria-label' => '',
					'type' => 'text',
					'instructions' => __('An order reference of up to 6 digits. Orders with duplicate or missing numbers, or numbers longer than 6 digits, will be rejected by Petals.', 'bakkbone-florist-companion'),
					'required' => 1,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => 'bkf_po_field',
						'id' => '',
					),
					'default_value' => '',
					'maxlength' => 6,
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
				),
				array(
					'key' => 'bkf_po_order_items',
					'label' => __('Order Items', 'bakkbone-florist-companion'),
					'name' => '',
					'aria-label' => '',
					'type' => 'accordion',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => 'bkf_po_field',
						'id' => '',
					),
					'open' => 1,
					'multi_expand' => 1,
					'endpoint' => 0,
				),
				array(
					'key' => 'bkf_po_field_description',
					'label' => __('Description', 'bakkbone-florist-companion'),
					'name' => 'description',
					'aria-label' => '',
					'type' => 'text',
					'instructions' => __('Order description in words. This field should contain sufficient info to supply the order. For example; 6 red roses bouquet, paper wrapped plus bottle of sparkling wine. Additional information on make-up can be supplied in the make-up field. Special delivery and and other special instructions can be added to the comments field. Not all partners are able to pass on some or all of the make-up and comments fields due to legacy systems so please keep the material in these fields to the minimum.', 'bakkbone-florist-companion'),
					'required' => 1,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '75',
						'class' => 'bkf_po_field',
						'id' => '',
					),
					'default_value' => '',
					'maxlength' => 100,
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
				),
				array(
					'key' => 'bkf_po_field_productid',
					'label' => __('Petals Product ID', 'bakkbone-florist-companion'),
					'name' => 'productid',
					'aria-label' => '',
					'type' => 'text',
					'instructions' => __('The Petals product ID. Please refer to the pricing guide for product IDs and descriptions.', 'bakkbone-florist-companion'),
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '25',
						'class' => 'bkf_po_field',
						'id' => '',
					),
					'default_value' => '',
					'maxlength' => 10,
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
				),
				array(
					'key' => 'bkf_po_field_makeup',
					'label' => __('Makeup', 'bakkbone-florist-companion'),
					'name' => 'makeup',
					'aria-label' => '',
					'type' => 'text',
					'instructions' => __('Provide optional information on how to make up the order. Note that this is only a guide. Petals Partners do not guarantee specific make-ups unless directly agreed between the Partners.', 'bakkbone-florist-companion'),
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => 'bkf_po_field',
						'id' => '',
					),
					'default_value' => '',
					'maxlength' => 200,
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
				),
				array(
					'key' => 'bkf_po_field_upsell',
					'label' => __('Add-Ons', 'bakkbone-florist-companion'),
					'name' => 'upsell',
					'aria-label' => '',
					'type' => 'text',
					'instructions' => __('This is where you would include any add-ons such as chocolates, balloons etc', 'bakkbone-florist-companion'),
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '75',
						'class' => 'bkf_po_field',
						'id' => '',
					),
					'default_value' => '',
					'maxlength' => 100,
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
				),
				array(
					'key' => 'bkf_po_field_upsellAmt',
					'label' => __('Add-Ons Value', 'bakkbone-florist-companion'),
					'name' => 'upsellAmt',
					'aria-label' => '',
					'type' => 'number',
					'instructions' => __('This is where you would include the total value of all add ons', 'bakkbone-florist-companion'),
					'required' => 0,
					'conditional_logic' => array(
						array(
							array(
								'field' => 'bkf_po_field_upsell',
								'operator' => '!=empty',
							),
						),
					),
					'wrapper' => array(
						'width' => '25',
						'class' => 'bkf_po_field',
						'id' => '',
					),
					'default_value' => '',
					'min' => '',
					'max' => '',
					'placeholder' => '$$$.¢¢',
					'step' => '0.01',
					'prepend' => '',
					'append' => '',
				),
				array(
					'key' => 'bkf_po_field_tvalue',
					'label' => __('Total Order Value', 'bakkbone-florist-companion'),
					'name' => 'tvalue',
					'aria-label' => '',
					'type' => 'number',
					'instructions' => __('Total value of the order in the currency Petals has you registered to send and receive orders in. Format is $$$.¢¢, eg. 123.00 - must include the cents.', 'bakkbone-florist-companion'),
					'required' => 1,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => 'bkf_po_field',
						'id' => '',
					),
					'default_value' => '',
					'min' => '',
					'max' => '',
					'placeholder' => '$$$.¢¢',
					'step' => '0.01',
					'prepend' => '',
					'append' => '',
				),
				array(
					'key' => 'bkf_po_order_delivery',
					'label' => __('Delivery', 'bakkbone-florist-companion'),
					'name' => '',
					'aria-label' => '',
					'type' => 'accordion',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => 'bkf_po_field',
						'id' => '',
					),
					'open' => 1,
					'multi_expand' => 1,
					'endpoint' => 0,
				),
				array(
					'key' => 'bkf_po_field_deldate',
					'label' => __('Delivery Date', 'bakkbone-florist-companion'),
					'name' => 'deldate',
					'aria-label' => '',
					'type' => 'date_picker',
					'instructions' => '',
					'required' => 1,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => 'bkf_po_field',
						'id' => '',
					),
					'display_format' => 'l, jS F Y',
					'return_format' => 'Ymd',
					'first_day' => 1,
				),
				array(
					'key' => 'bkf_po_field_deltime',
					'label' => __('Delivery Time', 'bakkbone-florist-companion'),
					'name' => 'deltime',
					'aria-label' => '',
					'type' => 'text',
					'instructions' => __('Optional: text notes - e.g. am / pm, funeral time', 'bakkbone-florist-companion'),
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => 'bkf_po_field',
						'id' => '',
					),
					'default_value' => '',
					'maxlength' => 20,
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
				),
				array(
					'key' => 'bkf_po_order_recipient',
					'label' => __('Recipient', 'bakkbone-florist-companion'),
					'name' => '',
					'aria-label' => '',
					'type' => 'accordion',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => 'bkf_po_field',
						'id' => '',
					),
					'open' => 1,
					'multi_expand' => 1,
					'endpoint' => 0,
				),
				array(
					'key' => 'bkf_po_field_recipient',
					'label' => __('Recipient\'s Name', 'bakkbone-florist-companion'),
					'name' => 'recipient',
					'aria-label' => '',
					'type' => 'text',
					'instructions' => __('Name of person receiving flowers', 'bakkbone-florist-companion'),
					'required' => 1,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => 'bkf_po_field',
						'id' => '',
					),
					'default_value' => '',
					'maxlength' => 60,
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
				),
				array(
					'key' => 'bkf_po_field_surname',
					'label' => __('Recipient\'s Surname', 'bakkbone-florist-companion'),
					'name' => 'surname',
					'aria-label' => '',
					'type' => 'text',
					'instructions' => __('Recipient\'s surname - first 8 characters', 'bakkbone-florist-companion'),
					'required' => 1,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => 'bkf_po_field',
						'id' => '',
					),
					'default_value' => '',
					'maxlength' => 8,
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
				),
				array(
					'key' => 'bkf_po_field_addresstype',
					'label' => __('Address Type', 'bakkbone-florist-companion'),
					'name' => 'addresstype',
					'aria-label' => '',
					'type' => 'text',
					'instructions' => __('Should be the "location" part of the address, ie. Home/Business/Hospital/Church etc', 'bakkbone-florist-companion'),
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => 'bkf_po_field',
						'id' => '',
					),
					'default_value' => '',
					'maxlength' => 15,
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
				),
				array(
					'key' => 'bkf_po_field_address',
					'label' => __('Street Address', 'bakkbone-florist-companion'),
					'name' => 'address',
					'aria-label' => '',
					'type' => 'text',
					'instructions' => '',
					'required' => 1,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => 'bkf_po_field',
						'id' => '',
					),
					'default_value' => '',
					'maxlength' => 100,
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
				),
				array(
					'key' => 'bkf_po_field_type',
					'label' => __('Town/City', 'bakkbone-florist-companion'),
					'name' => 'town',
					'aria-label' => '',
					'type' => 'text',
					'instructions' => '',
					'required' => 1,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => 'bkf_po_field',
						'id' => '',
					),
					'default_value' => '',
					'maxlength' => 30,
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
				),
				array(
					'key' => 'bkf_po_field_state',
					'label' => __('State/Province', 'bakkbone-florist-companion'),
					'name' => 'state',
					'aria-label' => '',
					'type' => 'text',
					'instructions' => '',
					'required' => 1,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '75',
						'class' => 'bkf_po_field',
						'id' => '',
					),
					'default_value' => '',
					'maxlength' => 10,
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
				),
				array(
					'key' => 'bkf_po_field_postalcode',
					'label' => __('Postal Code', 'bakkbone-florist-companion'),
					'name' => 'postalcode',
					'aria-label' => '',
					'type' => 'text',
					'instructions' => '',
					'required' => 1,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '25',
						'class' => 'bkf_po_field',
						'id' => '',
					),
					'default_value' => '',
					'maxlength' => 15,
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
				),
				array(
					'key' => 'bkf_po_field_crtycode',
					'label' => __('Country', 'bakkbone-florist-companion'),
					'name' => 'crtycode',
					'aria-label' => '',
					'type' => 'select',
					'instructions' => '',
					'required' => 1,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => 'bkf_po_field',
						'id' => '',
					),
					'choices' => array(
	'AF' => __('Afghanistan', 'bakkbone-florist-companion'),
	'AX' => __('Åland Islands', 'bakkbone-florist-companion'),
	'AL' => __('Albania', 'bakkbone-florist-companion'),
	'DZ' => __('Algeria', 'bakkbone-florist-companion'),
	'AS' => __('American Samoa', 'bakkbone-florist-companion'),
	'AD' => __('Andorra', 'bakkbone-florist-companion'),
	'AO' => __('Angola', 'bakkbone-florist-companion'),
	'AI' => __('Anguilla', 'bakkbone-florist-companion'),
	'AQ' => __('Antarctica', 'bakkbone-florist-companion'),
	'AG' => __('Antigua and Barbuda', 'bakkbone-florist-companion'),
	'AR' => __('Argentina', 'bakkbone-florist-companion'),
	'AM' => __('Armenia', 'bakkbone-florist-companion'),
	'AW' => __('Aruba', 'bakkbone-florist-companion'),
	'AU' => __('Australia', 'bakkbone-florist-companion'),
	'AT' => __('Austria', 'bakkbone-florist-companion'),
	'AZ' => __('Azerbaijan', 'bakkbone-florist-companion'),
	'BS' => __('Bahamas', 'bakkbone-florist-companion'),
	'BH' => __('Bahrain', 'bakkbone-florist-companion'),
	'BD' => __('Bangladesh', 'bakkbone-florist-companion'),
	'BB' => __('Barbados', 'bakkbone-florist-companion'),
	'BY' => __('Belarus', 'bakkbone-florist-companion'),
	'BE' => __('Belgium', 'bakkbone-florist-companion'),
	'BZ' => __('Belize', 'bakkbone-florist-companion'),
	'BJ' => __('Benin', 'bakkbone-florist-companion'),
	'BM' => __('Bermuda', 'bakkbone-florist-companion'),
	'BT' => __('Bhutan', 'bakkbone-florist-companion'),
	'BO' => __('Bolivia (Plurinational State of)', 'bakkbone-florist-companion'),
	'BQ' => __('Bonaire, Sint Eustatius and Saba', 'bakkbone-florist-companion'),
	'BA' => __('Bosnia and Herzegovina', 'bakkbone-florist-companion'),
	'BW' => __('Botswana', 'bakkbone-florist-companion'),
	'BV' => __('Bouvet Island', 'bakkbone-florist-companion'),
	'BR' => __('Brazil', 'bakkbone-florist-companion'),
	'IO' => __('British Indian Ocean Territory', 'bakkbone-florist-companion'),
	'BN' => __('Brunei Darussalam', 'bakkbone-florist-companion'),
	'BG' => __('Bulgaria', 'bakkbone-florist-companion'),
	'BF' => __('Burkina Faso', 'bakkbone-florist-companion'),
	'BI' => __('Burundi', 'bakkbone-florist-companion'),
	'CV' => __('Cabo Verde', 'bakkbone-florist-companion'),
	'KH' => __('Cambodia', 'bakkbone-florist-companion'),
	'CM' => __('Cameroon', 'bakkbone-florist-companion'),
	'CA' => __('Canada', 'bakkbone-florist-companion'),
	'KY' => __('Cayman Islands', 'bakkbone-florist-companion'),
	'CF' => __('Central African Republic', 'bakkbone-florist-companion'),
	'TD' => __('Chad', 'bakkbone-florist-companion'),
	'CL' => __('Chile', 'bakkbone-florist-companion'),
	'CN' => __('China', 'bakkbone-florist-companion'),
	'CX' => __('Christmas Island', 'bakkbone-florist-companion'),
	'CC' => __('Cocos (Keeling) Islands', 'bakkbone-florist-companion'),
	'CO' => __('Colombia', 'bakkbone-florist-companion'),
	'KM' => __('Comoros', 'bakkbone-florist-companion'),
	'CG' => __('Congo', 'bakkbone-florist-companion'),
	'CD' => __('Congo, Democratic Republic of the', 'bakkbone-florist-companion'),
	'CK' => __('Cook Islands', 'bakkbone-florist-companion'),
	'CR' => __('Costa Rica', 'bakkbone-florist-companion'),
	'CI' => __('Côte d\'Ivoire', 'bakkbone-florist-companion'),
	'HR' => __('Croatia', 'bakkbone-florist-companion'),
	'CU' => __('Cuba', 'bakkbone-florist-companion'),
	'CW' => __('Curaçao', 'bakkbone-florist-companion'),
	'CY' => __('Cyprus', 'bakkbone-florist-companion'),
	'CZ' => __('Czechia', 'bakkbone-florist-companion'),
	'DK' => __('Denmark', 'bakkbone-florist-companion'),
	'DJ' => __('Djibouti', 'bakkbone-florist-companion'),
	'DM' => __('Dominica', 'bakkbone-florist-companion'),
	'DO' => __('Dominican Republic', 'bakkbone-florist-companion'),
	'EC' => __('Ecuador', 'bakkbone-florist-companion'),
	'EG' => __('Egypt', 'bakkbone-florist-companion'),
	'SV' => __('El Salvador', 'bakkbone-florist-companion'),
	'GQ' => __('Equatorial Guinea', 'bakkbone-florist-companion'),
	'ER' => __('Eritrea', 'bakkbone-florist-companion'),
	'EE' => __('Estonia', 'bakkbone-florist-companion'),
	'SZ' => __('Eswatini', 'bakkbone-florist-companion'),
	'ET' => __('Ethiopia', 'bakkbone-florist-companion'),
	'FK' => __('Falkland Islands (Malvinas)', 'bakkbone-florist-companion'),
	'FO' => __('Faroe Islands', 'bakkbone-florist-companion'),
	'FJ' => __('Fiji', 'bakkbone-florist-companion'),
	'FI' => __('Finland', 'bakkbone-florist-companion'),
	'FR' => __('France', 'bakkbone-florist-companion'),
	'GF' => __('French Guiana', 'bakkbone-florist-companion'),
	'PF' => __('French Polynesia', 'bakkbone-florist-companion'),
	'TF' => __('French Southern Territories', 'bakkbone-florist-companion'),
	'GA' => __('Gabon', 'bakkbone-florist-companion'),
	'GM' => __('Gambia', 'bakkbone-florist-companion'),
	'GE' => __('Georgia', 'bakkbone-florist-companion'),
	'DE' => __('Germany', 'bakkbone-florist-companion'),
	'GH' => __('Ghana', 'bakkbone-florist-companion'),
	'GI' => __('Gibraltar', 'bakkbone-florist-companion'),
	'GR' => __('Greece', 'bakkbone-florist-companion'),
	'GL' => __('Greenland', 'bakkbone-florist-companion'),
	'GD' => __('Grenada', 'bakkbone-florist-companion'),
	'GP' => __('Guadeloupe', 'bakkbone-florist-companion'),
	'GU' => __('Guam', 'bakkbone-florist-companion'),
	'GT' => __('Guatemala', 'bakkbone-florist-companion'),
	'GG' => __('Guernsey', 'bakkbone-florist-companion'),
	'GN' => __('Guinea', 'bakkbone-florist-companion'),
	'GW' => __('Guinea-Bissau', 'bakkbone-florist-companion'),
	'GY' => __('Guyana', 'bakkbone-florist-companion'),
	'HT' => __('Haiti', 'bakkbone-florist-companion'),
	'HM' => __('Heard Island and McDonald Islands', 'bakkbone-florist-companion'),
	'VA' => __('Holy See', 'bakkbone-florist-companion'),
	'HN' => __('Honduras', 'bakkbone-florist-companion'),
	'HK' => __('Hong Kong', 'bakkbone-florist-companion'),
	'HU' => __('Hungary', 'bakkbone-florist-companion'),
	'IS' => __('Iceland', 'bakkbone-florist-companion'),
	'IN' => __('India', 'bakkbone-florist-companion'),
	'ID' => __('Indonesia', 'bakkbone-florist-companion'),
	'IR' => __('Iran (Islamic Republic of)', 'bakkbone-florist-companion'),
	'IQ' => __('Iraq', 'bakkbone-florist-companion'),
	'IE' => __('Ireland', 'bakkbone-florist-companion'),
	'IM' => __('Isle of Man', 'bakkbone-florist-companion'),
	'IL' => __('Israel', 'bakkbone-florist-companion'),
	'IT' => __('Italy', 'bakkbone-florist-companion'),
	'JM' => __('Jamaica', 'bakkbone-florist-companion'),
	'JP' => __('Japan', 'bakkbone-florist-companion'),
	'JE' => __('Jersey', 'bakkbone-florist-companion'),
	'JO' => __('Jordan', 'bakkbone-florist-companion'),
	'KZ' => __('Kazakhstan', 'bakkbone-florist-companion'),
	'KE' => __('Kenya', 'bakkbone-florist-companion'),
	'KI' => __('Kiribati', 'bakkbone-florist-companion'),
	'KP' => __('Korea (Democratic People\'s Republic of)', 'bakkbone-florist-companion'),
	'KR' => __('Korea, Republic of', 'bakkbone-florist-companion'),
	'KW' => __('Kuwait', 'bakkbone-florist-companion'),
	'KG' => __('Kyrgyzstan', 'bakkbone-florist-companion'),
	'LA' => __('Lao People\'s Democratic Republic', 'bakkbone-florist-companion'),
	'LV' => __('Latvia', 'bakkbone-florist-companion'),
	'LB' => __('Lebanon', 'bakkbone-florist-companion'),
	'LS' => __('Lesotho', 'bakkbone-florist-companion'),
	'LR' => __('Liberia', 'bakkbone-florist-companion'),
	'LY' => __('Libya', 'bakkbone-florist-companion'),
	'LI' => __('Liechtenstein', 'bakkbone-florist-companion'),
	'LT' => __('Lithuania', 'bakkbone-florist-companion'),
	'LU' => __('Luxembourg', 'bakkbone-florist-companion'),
	'MO' => __('Macao', 'bakkbone-florist-companion'),
	'MG' => __('Madagascar', 'bakkbone-florist-companion'),
	'MW' => __('Malawi', 'bakkbone-florist-companion'),
	'MY' => __('Malaysia', 'bakkbone-florist-companion'),
	'MV' => __('Maldives', 'bakkbone-florist-companion'),
	'ML' => __('Mali', 'bakkbone-florist-companion'),
	'MT' => __('Malta', 'bakkbone-florist-companion'),
	'MH' => __('Marshall Islands', 'bakkbone-florist-companion'),
	'MQ' => __('Martinique', 'bakkbone-florist-companion'),
	'MR' => __('Mauritania', 'bakkbone-florist-companion'),
	'MU' => __('Mauritius', 'bakkbone-florist-companion'),
	'YT' => __('Mayotte', 'bakkbone-florist-companion'),
	'MX' => __('Mexico', 'bakkbone-florist-companion'),
	'FM' => __('Micronesia (Federated States of)', 'bakkbone-florist-companion'),
	'MD' => __('Moldova, Republic of', 'bakkbone-florist-companion'),
	'MC' => __('Monaco', 'bakkbone-florist-companion'),
	'MN' => __('Mongolia', 'bakkbone-florist-companion'),
	'ME' => __('Montenegro', 'bakkbone-florist-companion'),
	'MS' => __('Montserrat', 'bakkbone-florist-companion'),
	'MA' => __('Morocco', 'bakkbone-florist-companion'),
	'MZ' => __('Mozambique', 'bakkbone-florist-companion'),
	'MM' => __('Myanmar', 'bakkbone-florist-companion'),
	'NA' => __('Namibia', 'bakkbone-florist-companion'),
	'NR' => __('Nauru', 'bakkbone-florist-companion'),
	'NP' => __('Nepal', 'bakkbone-florist-companion'),
	'NL' => __('Netherlands', 'bakkbone-florist-companion'),
	'NC' => __('New Caledonia', 'bakkbone-florist-companion'),
	'NZ' => __('New Zealand', 'bakkbone-florist-companion'),
	'NI' => __('Nicaragua', 'bakkbone-florist-companion'),
	'NE' => __('Niger', 'bakkbone-florist-companion'),
	'NG' => __('Nigeria', 'bakkbone-florist-companion'),
	'NU' => __('Niue', 'bakkbone-florist-companion'),
	'NF' => __('Norfolk Island', 'bakkbone-florist-companion'),
	'MK' => __('North Macedonia', 'bakkbone-florist-companion'),
	'MP' => __('Northern Mariana Islands', 'bakkbone-florist-companion'),
	'NO' => __('Norway', 'bakkbone-florist-companion'),
	'OM' => __('Oman', 'bakkbone-florist-companion'),
	'PK' => __('Pakistan', 'bakkbone-florist-companion'),
	'PW' => __('Palau', 'bakkbone-florist-companion'),
	'PS' => __('Palestine, State of', 'bakkbone-florist-companion'),
	'PA' => __('Panama', 'bakkbone-florist-companion'),
	'PG' => __('Papua New Guinea', 'bakkbone-florist-companion'),
	'PY' => __('Paraguay', 'bakkbone-florist-companion'),
	'PE' => __('Peru', 'bakkbone-florist-companion'),
	'PH' => __('Philippines', 'bakkbone-florist-companion'),
	'PN' => __('Pitcairn', 'bakkbone-florist-companion'),
	'PL' => __('Poland', 'bakkbone-florist-companion'),
	'PT' => __('Portugal', 'bakkbone-florist-companion'),
	'PR' => __('Puerto Rico', 'bakkbone-florist-companion'),
	'QA' => __('Qatar', 'bakkbone-florist-companion'),
	'RE' => __('Réunion', 'bakkbone-florist-companion'),
	'RO' => __('Romania', 'bakkbone-florist-companion'),
	'RU' => __('Russian Federation', 'bakkbone-florist-companion'),
	'RW' => __('Rwanda', 'bakkbone-florist-companion'),
	'BL' => __('Saint Barthélemy', 'bakkbone-florist-companion'),
	'SH' => __('Saint Helena, Ascension and Tristan da Cunha', 'bakkbone-florist-companion'),
	'KN' => __('Saint Kitts and Nevis', 'bakkbone-florist-companion'),
	'LC' => __('Saint Lucia', 'bakkbone-florist-companion'),
	'MF' => __('Saint Martin (French part)', 'bakkbone-florist-companion'),
	'PM' => __('Saint Pierre and Miquelon', 'bakkbone-florist-companion'),
	'VC' => __('Saint Vincent and the Grenadines', 'bakkbone-florist-companion'),
	'WS' => __('Samoa', 'bakkbone-florist-companion'),
	'SM' => __('San Marino', 'bakkbone-florist-companion'),
	'ST' => __('Sao Tome and Principe', 'bakkbone-florist-companion'),
	'SA' => __('Saudi Arabia', 'bakkbone-florist-companion'),
	'SN' => __('Senegal', 'bakkbone-florist-companion'),
	'RS' => __('Serbia', 'bakkbone-florist-companion'),
	'SC' => __('Seychelles', 'bakkbone-florist-companion'),
	'SL' => __('Sierra Leone', 'bakkbone-florist-companion'),
	'SG' => __('Singapore', 'bakkbone-florist-companion'),
	'SX' => __('Sint Maarten (Dutch part)', 'bakkbone-florist-companion'),
	'SK' => __('Slovakia', 'bakkbone-florist-companion'),
	'SI' => __('Slovenia', 'bakkbone-florist-companion'),
	'SB' => __('Solomon Islands', 'bakkbone-florist-companion'),
	'SO' => __('Somalia', 'bakkbone-florist-companion'),
	'ZA' => __('South Africa', 'bakkbone-florist-companion'),
	'GS' => __('South Georgia and the South Sandwich Islands', 'bakkbone-florist-companion'),
	'SS' => __('South Sudan', 'bakkbone-florist-companion'),
	'ES' => __('Spain', 'bakkbone-florist-companion'),
	'LK' => __('Sri Lanka', 'bakkbone-florist-companion'),
	'SD' => __('Sudan', 'bakkbone-florist-companion'),
	'SR' => __('Suriname', 'bakkbone-florist-companion'),
	'SJ' => __('Svalbard and Jan Mayen', 'bakkbone-florist-companion'),
	'SE' => __('Sweden', 'bakkbone-florist-companion'),
	'CH' => __('Switzerland', 'bakkbone-florist-companion'),
	'SY' => __('Syrian Arab Republic', 'bakkbone-florist-companion'),
	'TW' => __('Taiwan, Province of China', 'bakkbone-florist-companion'),
	'TJ' => __('Tajikistan', 'bakkbone-florist-companion'),
	'TZ' => __('Tanzania, United Republic of', 'bakkbone-florist-companion'),
	'TH' => __('Thailand', 'bakkbone-florist-companion'),
	'TL' => __('Timor-Leste', 'bakkbone-florist-companion'),
	'TG' => __('Togo', 'bakkbone-florist-companion'),
	'TK' => __('Tokelau', 'bakkbone-florist-companion'),
	'TO' => __('Tonga', 'bakkbone-florist-companion'),
	'TT' => __('Trinidad and Tobago', 'bakkbone-florist-companion'),
	'TN' => __('Tunisia', 'bakkbone-florist-companion'),
	'TR' => __('Türkiye', 'bakkbone-florist-companion'),
	'TM' => __('Turkmenistan', 'bakkbone-florist-companion'),
	'TC' => __('Turks and Caicos Islands', 'bakkbone-florist-companion'),
	'TV' => __('Tuvalu', 'bakkbone-florist-companion'),
	'UG' => __('Uganda', 'bakkbone-florist-companion'),
	'UA' => __('Ukraine', 'bakkbone-florist-companion'),
	'AE' => __('United Arab Emirates', 'bakkbone-florist-companion'),
	'GB' => __('United Kingdom of Great Britain and Northern Ireland', 'bakkbone-florist-companion'),
	'UM' => __('United States Minor Outlying Islands', 'bakkbone-florist-companion'),
	'US' => __('United States of America', 'bakkbone-florist-companion'),
	'UY' => __('Uruguay', 'bakkbone-florist-companion'),
	'UZ' => __('Uzbekistan', 'bakkbone-florist-companion'),
	'VU' => __('Vanuatu', 'bakkbone-florist-companion'),
	'VE' => __('Venezuela (Bolivarian Republic of)', 'bakkbone-florist-companion'),
	'VN' => __('Viet Nam', 'bakkbone-florist-companion'),
	'VG' => __('Virgin Islands (British)', 'bakkbone-florist-companion'),
	'VI' => __('Virgin Islands (U.S.)', 'bakkbone-florist-companion'),
	'WF' => __('Wallis and Futuna', 'bakkbone-florist-companion'),
	'EH' => __('Western Sahara', 'bakkbone-florist-companion'),
	'YE' => __('Yemen', 'bakkbone-florist-companion'),
	'ZM' => __('Zambia', 'bakkbone-florist-companion'),
	'ZW' => __('Zimbabwe', 'bakkbone-florist-companion')
),
					'default_value' => 'AU',
					'return_format' => 'array',
					'multiple' => 0,
					'allow_null' => 0,
					'ui' => 1,
					'ajax' => 0,
					'placeholder' => '',
				),
				array(
					'key' => 'bkf_po_field_phone',
					'label' => __('Recipient\'s Phone', 'bakkbone-florist-companion'),
					'name' => 'phone',
					'aria-label' => '',
					'type' => 'text',
					'instructions' => __('It is strongly recommended that you seek and provide contact numbers for recipients to reduce delivery problems. Be advised that some partners in some countries will not deliver without a telephone number.', 'bakkbone-florist-companion'),
					'required' => 1,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => 'bkf_po_field',
						'id' => '',
					),
					'default_value' => '',
					'maxlength' => 25,
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
				),
				array(
					'key' => 'bkf_po_order_information',
					'label' => __('Information', 'bakkbone-florist-companion'),
					'name' => '',
					'aria-label' => '',
					'type' => 'accordion',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => 'bkf_po_field',
						'id' => '',
					),
					'open' => 1,
					'multi_expand' => 1,
					'endpoint' => 0,
				),
				array(
					'key' => 'bkf_po_field_message',
					'label' => __('Card Message', 'bakkbone-florist-companion'),
					'name' => 'message',
					'aria-label' => '',
					'type' => 'text',
					'instructions' => '',
					'required' => 1,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => 'bkf_po_field',
						'id' => '',
					),
					'default_value' => '',
					'maxlength' => 200,
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
				),
				array(
					'key' => 'bkf_po_field_occasion',
					'label' => __('Occasion', 'bakkbone-florist-companion'),
					'name' => 'occasion',
					'aria-label' => '',
					'type' => 'text',
					'instructions' => __('Should be the occasion they select with the card message', 'bakkbone-florist-companion'),
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => 'bkf_po_field',
						'id' => '',
					),
					'default_value' => '',
					'maxlength' => 15,
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
				),
				array(
					'key' => 'bkf_po_field_comments',
					'label' => __('Comments', 'bakkbone-florist-companion'),
					'name' => 'comments',
					'aria-label' => '',
					'type' => 'text',
					'instructions' => __('Any comments, delivery notes, or special instructions', 'bakkbone-florist-companion'),
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => 'bkf_po_field',
						'id' => '',
					),
					'default_value' => '',
					'maxlength' => 250,
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
				),
				array(
					'key' => 'bkf_po_field_supplier',
					'label' => __('Designated Executing Florist', 'bakkbone-florist-companion'),
					'name' => 'supplier',
					'aria-label' => '',
					'type' => 'text',
					'instructions' => __('Member number of the supplying florist. Normally this will be provided by Petals when it chooses a florist to supply the order. However, if you have a preference, you can insert a valid Petals member number in this field.', 'bakkbone-florist-companion'),
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => 'bkf_po_field',
						'id' => '',
					),
					'default_value' => '',
					'maxlength' => 5,
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
				),
				array(
					'key' => 'bkf_po_order_customer',
					'label' => __('Your Customer', 'bakkbone-florist-companion'),
					'name' => '',
					'aria-label' => '',
					'type' => 'accordion',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => 'bkf_po_field',
						'id' => '',
					),
					'open' => 0,
					'multi_expand' => 1,
					'endpoint' => 0,
				),
				array(
					'key' => 'bkf_po_field_contact_name',
					'label' => __('Customer Name', 'bakkbone-florist-companion'),
					'name' => 'contact_name',
					'aria-label' => '',
					'type' => 'text',
					'instructions' => __('Purchaser or other contact person\'s name for the sell side of this order. (Optional, but needed if Petals might need to contact the purchaser)', 'bakkbone-florist-companion'),
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => 'bkf_po_field',
						'id' => '',
					),
					'default_value' => '',
					'maxlength' => 30,
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
				),
				array(
					'key' => 'bkf_po_field_contact_email',
					'label' => __('Customer Email', 'bakkbone-florist-companion'),
					'name' => 'contact_email',
					'aria-label' => '',
					'type' => 'email',
					'instructions' => __('Purchaser or other contact person\'s email for the sell side of this order. (Optional, but needed if Petals might need to contact the purchaser)', 'bakkbone-florist-companion'),
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => 'bkf_po_field',
						'id' => '',
					),
					'default_value' => '',
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
				),
				array(
					'key' => 'bkf_po_field_contact_phone',
					'label' => __('Customer Phone', 'bakkbone-florist-companion'),
					'name' => 'contact_phone',
					'aria-label' => '',
					'type' => 'text',
					'instructions' => __('Purchaser or other contact person\'s business hours phone for the sell side of this order. (Optional, but needed if Petals might need to contact the purchaser)', 'bakkbone-florist-companion'),
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => 'bkf_po_field',
						'id' => '',
					),
					'default_value' => '',
					'maxlength' => 25,
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
				),
			),
			'location' => array(
				array(
					array(
						'param' => 'post_type',
						'operator' => '==',
						'value' => 'bkf_petals_order',
					),
				),
			),
			'menu_order' => 0,
			'position' => 'acf_after_title',
			'style' => 'seamless',
			'label_placement' => 'top',
			'instruction_placement' => 'field',
			'hide_on_screen' => '',
			'active' => true,
			'description' => '',
			'show_in_rest' => 1,
		));
	}
	
	function bkfmakePrintContentsSaySaved(){
		global $pagenow;
		if ( isset($pagenow) && $pagenow == 'post-new.php'
			&& isset($_GET['post_type']) && $_GET['post_type'] === 'bkf_petals_order'){
			add_filter(
				'gettext',
				function($translated,$text_domain,$original){
					if($translated === 'Publish'){
						return __('Send Order', 'bakkbone-florist-companion');
					}
					return $translated;
				},
				10,
				3
			);
		}
		if ( isset($pagenow) && $pagenow == 'edit.php'
			&& isset($_GET['post_type']) && $_GET['post_type'] === 'bkf_petals_order'){
			add_filter(
				'gettext',
				function($translated,$text_domain,$original){
					if($translated === 'Published'){
						return __('Order sent to Petals', 'bakkbone-florist-companion');
					}
					if($translated === 'Publish'){
						return __('Send Order', 'bakkbone-florist-companion');
					}
					return $translated;
				},
				10,
				3
			);
		}
	}
	
	function popost_help_tab(){
		$screen = get_current_screen();
		if($screen->id == 'edit-bkf_petals_order' || $screen->id == 'bkf_petals_order'){
			$id = 'petalsorder_help';
			$callback = array($this, 'petalsorder_help');
			$screen->add_help_tab(array( 
			   'id' => $id,
			   'title' => __('Documentation','bakkbone-florist-companion'),
			   'callback' => $callback
			));
		}
	}
	
	function petalsorder_help(){
		?>
		<h2><?php esc_html_e('View documentation for this page at: ','bakkbone-florist-companion'); ?></h2>
			<a href="https://plugins.bkbn.au/docs/bkf/petals-network/petals-network-integration/" target="_blank">https://plugins.bkbn.au/docs/bkf/petals-network/petals-network-integration/</a>
		<?php
	}
	
	function bkfRegisterPetalsOrderPost(){
		$labels = array(
			"name"						=> _x("Petals Orders", "post type general name", "bakkbone-florist-companion"),
			"singular_name"				=> _x("Petals Order", "post type singular name", "bakkbone-florist-companion"),
			"menu_name"					=> _x("Petals Orders", "admin menu", "bakkbone-florist-companion"),
			"name_admin_bar"			=> _x("Petals Order", "add new on admin bar", "bakkbone-florist-companion"),
			"add_new"					=> _x("Send New", "Add New", "bakkbone-florist-companion"),
			"add_new_item"				=> __("Send New Petals Order", "bakkbone-florist-companion"),
			"new_item"					=> __("New Order", "bakkbone-florist-companion"),
			"edit_item"					=> __("Edit Order", "bakkbone-florist-companion"),
			"view_item"					=> __("View Order", "bakkbone-florist-companion"),
			"all_items"					=> __("All Orders", "bakkbone-florist-companion"),
			"search_items"				=> __("Search Orders", "bakkbone-florist-companion"),
			"parent_item_colon"			=> __("Parent Orders:", "bakkbone-florist-companion"),
			"not_found"					=> __("Not found", "bakkbone-florist-companion"),
			"not_found_in_trash"		=> __("Not found in trash", "bakkbone-florist-companion"),
			"attributes"				=> __("Order Attributes", "bakkbone-florist-companion"),
			"insert_into_item"			=> __("Insert into order", "bakkbone-florist-companion"),
			"uploaded_to_this_item"		=> __("Uploaded to this order", "bakkbone-florist-companion"),
			"filter_items_list"			=> __("Filter orders list", "bakkbone-florist-companion"),
			"items_list_navigation"		=> __("Orders list navigation", "bakkbone-florist-companion"),
			"items_list"				=> __("Orders list", "bakkbone-florist-companion"),
			"item_published"			=> __("Order sent.", "bakkbone-florist-companion"),
			"item_published_privately"	=> __("Order sent privately.", "bakkbone-florist-companion"),
			"item_reverted_to_draft"	=> __("Order reverted to draft.", "bakkbone-florist-companion"),
			"item_scheduled"			=> __("Order scheduled.", "bakkbone-florist-companion"),
			"item_updated"				=> __("Order updated.", "bakkbone-florist-companion"),
			"item_link"					=> __("Order link", "bakkbone-florist-companion"),
			"item_link_description"		=> __("A link to a Petals Order.", "bakkbone-florist-companion")
		);
		
		$capability_type = 'petals_order';
		$capabilities = array(
			"edit_post" => "edit_petals_order",
			"read_post" => "read_petals_order",
			"delete_post" => "delete_petals_order",
			"edit_posts" => "edit_petals_orders",
			"edit_others_posts" => "edit_others_petals_orders",
			"delete_posts" => "delete_petals_orders",
			"publish_posts" => "publish_petals_orders",
			"read_private_posts" => "read_private_petals_orders",
			"create_posts" => "create_petals_orders"
		);
		$supports = array('none');
		$args = array(
			"label"					=> _x("Petals Order", "label", "bakkbone-florist-companion"),
			"labels"				=> $labels,
			"menu_icon"				=> 'dashicons-cart',
			"public"				=> true,
			"publicly_queryable"	=> true,
			"show_ui"				=> true,
			"show_in_menu"			=> true,
			"query_var"				=> 'petals_order',
			"show_in_nav_menus"		=> false,
			"show_in_admin_bar"		=> true,
			"show_in_rest"			=> true,
			"rewrite"				=> array("slug" => "petals_order"),
			"capability_type"		=> $capability_type,
			"capabilities"			=> $capabilities,
			"hierarchical"			=> false,
			"exclude_from_search"	=> true,
			"has_archive"			=> false,
			"menu_position"			=> 3,
			"taxonomies"			=> [],
			"supports"				=> $supports,
			"delete_with_user"		=> false
		);
		register_post_type("bkf_petals_order", $args);
		flush_rewrite_rules();
		
		$roles = array(
			'administrator' => get_role('administrator'),
			'editor' => get_role('editor'),
			'author' => get_role('author'),
			'contributor' => get_role('contributor'),
			'shop_manager' => get_role('shop_manager')
		);
		foreach($roles as $role){
			$role->add_cap('edit_petals_order', true);
			$role->add_cap('read_petals_order', true);
			$role->add_cap('delete_petals_order', false);
			$role->add_cap('edit_petals_orders', true);
			$role->add_cap('edit_others_petals_orders', true);
			$role->add_cap('delete_petals_orders', false);
			$role->add_cap('publish_petals_orders', true);
			$role->add_cap('read_private_petals_orders', true);
			$role->add_cap('create_petals_orders', true);
		}
	}
}