<?php
/**
 * @author BAKKBONE Australia
 * @package PetalsSingle
 * @license GNU General Public License (GPL) 3.0
 * Template Name: Petals Order
 * Template Post Type: bkf_petals_order
**/

$thispost = get_post();
$post_id = $thispost->ID;
$countries = array(
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
);
$sendid = get_post_meta($post_id, 'sendid', true);
$description = get_post_meta($post_id, 'description', true);
$productid = get_post_meta($post_id, 'productid', true);
$makeup = get_post_meta($post_id, 'makeup', true);
$upsell = get_post_meta($post_id, 'upsell', true);
$upsellamt = get_post_meta($post_id, 'upsellamt', true);
$tvalue = get_post_meta($post_id, 'tvalue', true);
$deldate = get_post_meta($post_id, 'deldate', true);
$deltime = get_post_meta($post_id, 'deltime', true);
$recipient = get_post_meta($post_id, 'recipient', true);
$surname = get_post_meta($post_id, 'surname', true);
$addresstype = get_post_meta($post_id, 'addresstype', true);
$address = get_post_meta($post_id, 'address', true);
$town = get_post_meta($post_id, 'town', true);
$state = get_post_meta($post_id, 'state', true);
$postalcode = get_post_meta($post_id, 'postalcode', true);
$crtycode = get_post_meta($post_id, 'crtycode', true);
$crtyname = get_post_meta($post_id, 'crtyname', true);
$phone = get_post_meta($post_id, 'phone', true);
$message = get_post_meta($post_id, 'message', true);
$occasion = get_post_meta($post_id, 'occasion', true);
$comments = get_post_meta($post_id, 'comments', true);
$supplier = get_post_meta($post_id, 'supplier', true);
$contact_name = get_post_meta($post_id, 'contact_name', true);
$contact_email = get_post_meta($post_id, 'contact_email', true);
$contact_phone = get_post_meta($post_id, 'contact_phone', true);

if( wp_is_block_theme() ) {
	?><!doctype html>
	<html <?php language_attributes(); ?>>
	<head>
		<meta charset="<?php bloginfo( 'charset' ); ?>">
		<?php wp_head(); ?>
	</head>

	<body <?php body_class(); ?>>
	<?php wp_body_open(); ?>
	<div class="wp-site-blocks"><header><?php
	block_template_part('header');
	echo '</header>';
} else {
	get_header();
}

while ( have_posts() ) :
	if(current_user_can( 'read_petals_order')){
		echo '<main><h2>'.sprintf(__('Petals Order #%s', 'bakkbone-florist-companion'), $sendid).'</h2><table class="bkf_po_table">';
		if($description !== '' && $description !== null){ echo '<tr><th>'.esc_html__('Description', 'bakkbone-florist-companion').'</th><td>'.$description.'</td></tr>'; }
		if($productid !== '' && $productid !== null){ echo '<tr><th>'.esc_html__('Petals Product ID', 'bakkbone-florist-companion').'</th><td>'.$productid.'</td></tr>'; }
		if($makeup !== '' && $makeup !== null){ echo '<tr><th>'.esc_html__('Makeup', 'bakkbone-florist-companion').'</th><td>'.$makeup.'</td></tr>'; }
		if($upsell !== '' && $upsell !== null){ echo '<tr><th>'.esc_html__('Add-Ons', 'bakkbone-florist-companion').'</th><td>'.$upsell.'</td></tr>'; }
		if($upsellamt !== '' && $upsellamt !== null){ echo '<tr><th>'.esc_html__('Add-Ons Value', 'bakkbone-florist-companion').'</th><td>'.bkf_currency_symbol().$upsellamt.'</td></tr>'; }
		if($tvalue !== '' && $tvalue !== null){ echo '<tr><th>'.esc_html__('Total Order Value', 'bakkbone-florist-companion').'</th><td>'.bkf_currency_symbol().$tvalue.'</td></tr>'; }
		if($deldate !== '' && $deldate !== null){ echo '<tr><th>'.esc_html__('Delivery Date', 'bakkbone-florist-companion').'</th><td>'.$deldate.'</td></tr>'; }
		if($deltime !== '' && $deltime !== null){ echo '<tr><th>'.esc_html__('Delivery Time', 'bakkbone-florist-companion').'</th><td>'.$deltime.'</td></tr>'; }
		if($recipient !== '' && $recipient !== null){ echo '<tr><th>'.esc_html__('Recipient\'s Name', 'bakkbone-florist-companion').'</th><td>'.$recipient.'</td></tr>'; }
		if($surname !== '' && $surname !== null){ echo '<tr><th>'.esc_html__('Recipient\'s Surname', 'bakkbone-florist-companion').'</th><td>'.$surname.'</td></tr>'; }
		if($addresstype !== '' && $addresstype !== null){ echo '<tr><th>'.esc_html__('Address Type', 'bakkbone-florist-companion').'</th><td>'.$addresstype.'</td></tr>'; }
		if($address !== '' && $address !== null){ echo '<tr><th>'.esc_html__('Street Address', 'bakkbone-florist-companion').'</th><td>'.$address.'</td></tr>'; }
		if($town !== '' && $town !== null){ echo '<tr><th>'.esc_html__('Town/City', 'bakkbone-florist-companion').'</th><td>'.$town.'</td></tr>'; }
		if($state !== '' && $state !== null){ echo '<tr><th>'.esc_html__('State/Province', 'bakkbone-florist-companion').'</th><td>'.$state.'</td></tr>'; }
		if($postalcode !== '' && $postalcode !== null){ echo '<tr><th>'.esc_html__('Postal Code', 'bakkbone-florist-companion').'</th><td>'.$postalcode.'</td></tr>'; }
		if($crtyname !== '' && $crtyname !== null){ echo '<tr><th>'.esc_html__('Country', 'bakkbone-florist-companion').'</th><td>'.$crtyname.'</td></tr>'; }
		if($phone !== '' && $phone !== null){ echo '<tr><th>'.esc_html__('Recipient\'s Phone', 'bakkbone-florist-companion').'</th><td>'.$phone.'</td></tr>'; }
		if($message !== '' && $message !== null){ echo '<tr><th>'.esc_html__('Card Message', 'bakkbone-florist-companion').'</th><td>'.$message.'</td></tr>'; }
		if($occasion !== '' && $occasion !== null){ echo '<tr><th>'.esc_html__('Occasion', 'bakkbone-florist-companion').'</th><td>'.$occasion.'</td></tr>'; }
		if($comments !== '' && $comments !== null){ echo '<tr><th>'.esc_html__('Comments', 'bakkbone-florist-companion').'</th><td>'.$comments.'</td></tr>'; }
		if($supplier !== '' && $supplier !== null){ echo '<tr><th>'.esc_html__('Designated Executing Florist', 'bakkbone-florist-companion').'</th><td>'.$supplier.'</td></tr>'; }
		if($contact_name !== '' && $contact_name !== null){ echo '<tr><th>'.esc_html__('Customer Name', 'bakkbone-florist-companion').'</th><td>'.$contact_name.'</td></tr>'; }
		if($contact_email !== '' && $contact_email !== null){ echo '<tr><th>'.esc_html__('Customer Email', 'bakkbone-florist-companion').'</th><td>'.$contact_email.'</td></tr>'; }
		if($contact_phone !== '' && $contact_phone !== null){ echo '<tr><th>'.esc_html__('Customer Phone', 'bakkbone-florist-companion').'</th><td>'.$contact_phone.'</td></tr>'; }
		echo '</table>';
		$comments = get_comments(array('post_id' => $post_id));
		$commentscount = get_comments(array('post_id' => $post_id, 'count' => true));
		echo '<h3>'.esc_html__('Messages to/from Petals Network:', 'bakkbone-florist-companion').'</h3>';
		?>
		<div id="bkf_pm" name="bkf_pm">
			<p><select required form="bkf_pm" class="bkf-form-control" id="petals_msg_type" name="msgtype">
				<option value="" disabled selected><?php esc_html_e('Select a message type...', 'bakkbone-florist-companion'); ?></option>
				<option value="M"><?php esc_html_e("Message - non-complaint", "bakkbone-florist-companion"); ?></option>
				<option value="C"><?php esc_html_e("Complaint", "bakkbone-florist-companion"); ?></option>
				<option value="F"><?php esc_html_e("Final message (no response required)", "bakkbone-florist-companion"); ?></option>
				<option value="D"><?php esc_html_e("Mark order as delivered", "bakkbone-florist-companion"); ?></option>
			</select></p>
			<p><input type="text" required class="bkf-form-control big" form="bkf_pm" name="msg" id="petals_msg_body" placeholder="<?php esc_html_e("Message to Petals","bakkbone-florist-companion"); ?>" /></p>
			<p><button form="bkf_pm" class="button wp-element-button" onclick="ajaxPetalsMessage()"><?php esc_html_e("Send Message","bakkbone-florist-companion"); ?></button></p>
		</div>
		<script>
			jQuery(document.body).ready(function($){
				jQuery('#petals_msg_type').select2({
					dropdownCssClass: ['bkf-font', 'bkf-select2']
				});
			});
			function ajaxPetalsMessage( $ ) {
					var postNonce = "<?php echo wp_create_nonce("bkf"); ?>";
					var orderId = "<?php echo $post->ID; ?>";
					var msgType = document.getElementById("petals_msg_type").value;
					var msgBody = document.getElementById("petals_msg_body").value;
					var postUrl = "<?php echo admin_url('admin-ajax.php'); ?>" + '?action=petals_msg_frontend&nonce=' + postNonce + '&orderid=' + orderId + '&msgtype=' + msgType + '&msgbody=' + msgBody;
					if(msgType == '' || msgBody == ''){
						alert('<?php esc_html_e('Please fill in both fields before attempting to send a message.', 'bakkbone-florist-companion'); ?>');
					} else {
						if(confirm('<?php esc_html_e('Send Message?', 'bakkbone-florist-companion'); ?>')){
							alert('<?php esc_html_e('Please wait...', 'bakkbone-florist-companion'); ?>')
							jQuery.post(postUrl);
							setTimeout(ajaxPetalsMessageProceed,3000);
						};
					};
				}
				function ajaxPetalsMessageProceed( $ ) {
					document.getElementById("petals_msg_type").value = '';
					document.getElementById("petals_msg_body").value = '';
					alert('<?php esc_html_e("Message queued for sending - this page will now attempt to refresh to display the outcome in the messages. An email will also be sent to the site admin.", "bakkbone-florist-companion"); ?>');
					location.reload();					
				}
		</script><?php
		if($commentscount !== 0){
			echo '<div id="petals_order_notes">';
			wp_list_comments(array('type'=>'petals_order_note','style'=>'div'),$comments);
			echo '</div>';
		}
	} else {
		echo '<main><h2>'.esc_html__('Not Authorized', 'bakkbone-florist-companion').'</h2><p>'.esc_html__('Sorry, you must be logged in as an authorized user to view this content.','bakkbone-florist-companion').'</p></main>';
	}
	the_post();
	get_template_part( 'template-parts/content/content-single' );
	echo '</main>';
endwhile;

if( wp_is_block_theme() ) {
	?></div><footer>
	<?php wp_footer();
	block_template_part('footer');
	echo '</footer>';
} else {
	get_footer();
}