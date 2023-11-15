<?php
/**
 * @author BAKKBONE Australia
 * @package BKF_PDF_Core
 * @license GNU General Public License (GPL) 3.0
**/

defined("BKF_EXEC") or die("Ah, sweet silence.");
use Dompdf\Dompdf;
class BKF_PDF_Core{
	
	function __construct(){
		$this->store = [];
		$this->store['invtitle'] = get_option('bkf_pdf_setting')['inv_title'];
		$this->store['invtext'] = get_option('bkf_pdf_setting')['inv_text'];
		$this->store['wstitle'] = get_option('bkf_pdf_setting')['ws_title'];
		$this->store['name'] = get_option('bkf_pdf_setting')['inv_sn'];
		$this->store['a1'] = get_option('bkf_pdf_setting')['inv_a1'];
		$this->store['a2'] = get_option('bkf_pdf_setting')['inv_a2'];
		$this->store['sub'] = get_option('bkf_pdf_setting')['inv_sub'];
		$this->store['state'] = get_option('bkf_pdf_setting')['inv_state'];
		$this->store['pc'] = get_option('bkf_pdf_setting')['inv_pc'];
		$this->store['phone'] = get_option('bkf_pdf_setting')['inv_phone'];
		$this->store['email'] = get_option('bkf_pdf_setting')['inv_eml'];
		$this->store['website'] = get_option('bkf_pdf_setting')['inv_web'];
		$this->store['taxlabel'] = get_option('bkf_pdf_setting')['inv_tax_label'];
		$this->store['taxvalue'] = get_option('bkf_pdf_setting')['inv_tax_value'];
	}
	
	function invoice($orderid){
		$order = new WC_Order($orderid);
		$cusadd = $order->get_formatted_billing_address();
		$cusph = $order->get_billing_phone();
		$cuseml = $order->get_billing_email();
		if($cusph !== ''){
			$cusadd .= '<br><strong>'.esc_html__('Phone: ', 'bakkbone-florist-companion').'</strong>'.$cusph;
		}
		if($cuseml !== ''){
			$cusadd .= '<br><strong>'.esc_html__('Email: ', 'bakkbone-florist-companion').'</strong>'.$cuseml;
		}
		$currency = $order->get_currency();
		$currencycode = get_woocommerce_currency_symbol($currency);
		
		$storea2raw = $this->store['a2'];
		if($storea2raw !== ''){
			$storea2 = '<br>'.$storea2raw;
		} else {
			$storea2 = '';
		}
		
		$storephoneraw = $this->store['phone'];
		if($storephoneraw !== ''){
			$storephone = '<strong>'.esc_html__('Phone: ', 'bakkbone-florist-companion').'</strong>'.$this->store['phone'].'<br>';
		} else {
			$storephone = '';
		}
		
		$orderitems = $order->get_items();
		foreach($orderitems as $item){
			$type = $item->get_type();
			if($type = 'product'){
				$items[] = $item;
			}
		};
		$itemtable = '';
		foreach($items as $item){
			$id = $item->get_id();
			$productname = $item->get_name();
			$productqty = $item->get_quantity();
			$productlinetotal = $order->get_formatted_line_subtotal($item);
			if(function_exists('wcj_get_product_addons')){
				$addons = wcj_get_product_addons($item, $order->get_currency());
				$exploded = $addons !== null && $addons !== '' ? explode(', ', $addons) : false;
				if($exploded){
					foreach($exploded as $addon){
						$thisaddon = explode(': ',$addon);
						$productname .= '<br><strong>'.__('Add-on', 'bakkbone-florist-companion').':</strong> '.$thisaddon[0].' ('.$thisaddon[1].')';
					}
					$productlinetotal .= '<br>'.__('inc. add-ons', 'bakkbone-florist-companion');
				}
			}
			$itemtable .= '<tr><td class="center-align width-20"><p>'.$productqty.'</p></td><td class="left-align"><p>'.$productname.'</p></td><td class="right-align"><p>'.$productlinetotal.'</p></td></tr>';
		}
		
		$fees = $order->get_fees();
		$feestable = '';
		foreach($fees as $fee){
			$feename = $fee->get_name();
			$feevalue = round($fee->get_total() + $fee->get_total_tax(),2);
			$feeamt = bkf_currency_symbol().number_format($feevalue,2,'.','');
			$feestable .= '<tr><td colspan="2" class="right-align"><p>'.$feename.'</p></td><td class="right-align"><p>'.$feeamt.'</p></td></tr>';
		}
		
		$taxtotal = $order->get_total_tax();
		$taxtable = '';
		if($taxtotal !== "0"){
			$taxamt = $currencycode.$order->get_total_tax();
			$taxtable .= '<tr><td colspan="2" class="right-align"><p><em>'.esc_html__('Tax', 'bakkbone-florist-companion').'</em></p></td><td class="right-align"><p><em>'.$taxamt.'</em></p></td></tr>';
		}
		
		$shippingname = $order->get_shipping_method();
		$shippingamt = number_format(round($order->get_shipping_total() + $order->get_shipping_tax(), 2),2,'.','');
		$shippingtable = '<tr><td colspan="2" class="right-align"><p>'.$shippingname.'</p></td><td class="right-align"><p>'.bkf_currency_symbol().$shippingamt.'</p></td></tr>';
		
		$discounttotal = $order->get_discount_total();
		$discounttable = '';
		if($discounttotal !== "0"){
			$discountamt = $order->get_discount_to_display();
			$discounttable .= '<tr><td colspan="2" class="right-align"><p>'.esc_html__('Discount', 'bakkbone-florist-companion').'</p></td><td class="right-align"><p>'.$discountamt.'</p></td></tr>';
		}
		
		$subtotal = $order->get_subtotal_to_display();
		$total = $order->get_formatted_order_total();
		
		$template = '<html><head><title>'.$this->store['invtitle'].' #'.$orderid.'</title><style>.nomargin {margin: 0;}table.bordered, table.bordered th, table.bordered td {border: 1px solid black;border-collapse: collapse;}.width-20 {width: 20%;}.width-100 {width: 100%;}.height-100 {height: 100%;}.top-align {vertical-align: top;}.bottom-align {vertical-align: bottom;}.middle-align {vertical-align: middle;}.right-align {text-align: right;}.left-align {text-align: left;}.center-align {text-align: center;}.bottom-sticky {position: absolute;bottom: 0;}.unpadded th p, .unpadded td p {margin: 0;}.padded th p, .padded td p {margin: 5px;}</style></head><body><div class="height-100"><h1 class="nomargin">'.$this->store['invtitle'].' #'.$orderid.'</h1><hr><table class="width-100 unpadded"><thead><tr><th class="left-align"><p>'.esc_html__('Invoice From', 'bakkbone-florist-companion').'</p></th><th class="right-align"><p>'.esc_html__('Invoice To', 'bakkbone-florist-companion').'</p></th></tr></thead><tbody><tr><td class="top-align left-align"><p>'.$this->store['name'].'<br>'.$this->store['a1'].$storea2.'<br>'.$this->store['sub'].'&nbsp;'.$this->store['state'].'&nbsp;'.$this->store['pc'].'</p><p>'.$storephone.'<strong>'.esc_html__('Email: ', 'bakkbone-florist-companion').'</strong>'.$this->store['email'].'<br><strong>'.esc_html__('Website: ', 'bakkbone-florist-companion').'</strong>'.$this->store['website'].'</p><p><strong>'.$this->store['taxlabel'].': </strong>'.$this->store['taxvalue'].'</p></td><td class="top-align right-align"><p>'.$cusadd.'</p></td></tr></tbody></table><br><table class="width-100 bordered padded"><thead><tr><th class="center-align width-20"><p>'.esc_html__('Qty', 'bakkbone-florist-companion').'</p></th><th class="center-align"><p>'.esc_html__('Item', 'bakkbone-florist-companion').'</p></th><th class="center-align"><p>'.esc_html__('Value', 'bakkbone-florist-companion').'</p></th></tr></thead><tbody>'.$itemtable.'<tr><td colspan="2" class="right-align"><p>'.esc_html__('Subtotal', 'bakkbone-florist-companion').'</p></td><td class="right-align"><p>'.$subtotal.'</p></td></tr>'.$feestable.$shippingtable.$discounttable.$taxtable.'<tr><td colspan="2" class="right-align"><p>'.esc_html__('Total', 'bakkbone-florist-companion').'</p></td><td class="right-align"><p>'.$total.'</p></td></tr></tbody></table><div class="bottom-sticky"><p>'.$this->store['invtext'].'</p></div></div></body>';
		
		$dompdf = new Dompdf();
		$dompdf->loadHtml($template);
		$dompdf->setPaper(get_option('bkf_pdf_setting')['page_size'], 'portrait');
		$dompdf->render();
		
		return $dompdf;
	}
	
	function worksheet($orderid){
		$order = new WC_Order($orderid);
		$cusadd = $order->get_formatted_billing_address();
		$recadd = $order->get_formatted_shipping_address();
		$dt = $order->get_meta('_delivery_timestamp', true);
		$dd = wp_date('l, j M', $dt);
		$recph = $order->get_shipping_phone();
		$cusph = $order->get_billing_phone();
		$cuseml = $order->get_billing_email();
		$recnotesraw = $order->get_meta('_shipping_notes', true);
		$recnotes = $recnotesraw !== '' ? '<br>'.$recnotesraw : '';
		$comm = $order->get_customer_note();
		
		$ts = bkf_get_timeslots_associative();
		$tsraw = '';
		$tsraw = $order->get_meta('_delivery_timeslot', true);
		if($tsraw){
			$thistsid = 'ts'.$order->get_meta('_delivery_timeslot_id', true);
			$thists = $ts[$thistsid];
			$timeslot = '<br><small><small><small>'.date("g:i a", strtotime($thists['start'])).' - '.date("g:i a", strtotime($thists['end'])).'</small></small></small>';			
		} else {
			$timeslot = '';
		}
		
		$orderitems = $order->get_items();
		foreach($orderitems as $item){
			$type = $item->get_type();
			if($type = 'product'){
				$items[] = $item;
			}
		};
		$itemtable = '';
		foreach($items as $item){
			$id = $item->get_id();
			$productname = $item->get_name();
			$productqty = $item->get_quantity();
			$productlinetotal = $order->get_formatted_line_subtotal($item);
			$rawmeta = $item->get_all_formatted_meta_data();
			$allmeta = json_decode(json_encode($rawmeta), true);
			$itemmeta = '';
			foreach($allmeta as $meta){
				$key = $meta['display_key'];
				$value = str_replace("\n", "", strip_tags($meta['display_value']));
				$itemmeta .= '<br><strong>'.$key.':</strong> '.$value;
			}
			if(function_exists('wcj_get_product_addons')){
				$addons = wcj_get_product_addons($item, $order->get_currency());
				$exploded = $addons !== null && $addons !== '' ? explode(', ', $addons) : false;
				if($exploded){
					foreach($exploded as $addon){
						$thisaddon = explode(': ',$addon);
						$itemmeta .= '<br><strong>'.__('Add-on', 'bakkbone-florist-companion').':</strong> '.$thisaddon[0].' ('.$thisaddon[1].')';
					}
					$productlinetotal .= '<br>'.__('inc. add-ons', 'bakkbone-florist-companion');
				}
			}
			$itemtable .= '<tr><td class="center-align width-15"><p>'.$productqty.'</p></td><td class="left-align"><p><strong>'.$productname.'</strong>'.$itemmeta.'</p></td><td class="right-align width-20"><p>'.$productlinetotal.'</p></td></tr>';
		}
		
		$fees = $order->get_fees();
		$feestable = '';
		foreach($fees as $fee){
			$feename = $fee->get_name();
			$feevalue = round($fee->get_total() + $fee->get_total_tax(),2);
			$feeamt = bkf_currency_symbol().number_format($feevalue,2,'.','');
			$feestable .= '<tr class="smalltext"><td colspan="2" class="right-align"><p>'.$feename.'</p></td><td class="right-align"><p>'.$feeamt.'</p></td></tr>';
		}
		
		$sm = $order->get_shipping_methods();
		foreach($sm as $smethod){
			$method[] = $smethod->get_method_id();
		}
		
		$pickup = in_array("local_pickup", $method) ? true : false;
		if($pickup){
			$ordertype = '<span style="color:#009900;">'.esc_html__('Pickup', 'bakkbone-florist-companion').'</span>';
		} else {
			$ordertype = '<span style="color:#000099;">'.esc_html__('Delivery', 'bakkbone-florist-companion').'</span>';
		}
		
		$shippingname = $order->get_shipping_method();
		$shippingamt = number_format(round($order->get_shipping_total() + $order->get_shipping_tax(), 2),2,'.','');
		$shippingtable = '<tr class="smalltext"><td colspan="2" class="right-align"><p>'.$shippingname.'</p></td><td class="right-align"><p>'.bkf_currency_symbol().$shippingamt.'</p></td></tr>';
		
		$discounttotal = $order->get_discount_total();
		$discounttable = '';
		if($discounttotal !== "0"){
			$discountamt = $order->get_discount_to_display();
			$discounttable .= '<tr class="smalltext"><td colspan="2" class="right-align"><p>'.esc_html__('Discount', 'bakkbone-florist-companion').'</p></td><td class="right-align"><p>'.$discountamt.'</p></td></tr>';
		}
		
		$subtotal = $order->get_subtotal_to_display();
		$total = $order->get_formatted_order_total();
		
		if($comm !== ''){
			$ordernotes = $pickup ? '<tr><th>'.esc_html__('Comments from Customer', 'bakkbone-florist-companion').'</th><td colspan="2">'.$comm.'</td></tr>' : '<tr><td colspan="3"><p><strong>'.esc_html__('Comments from Sender', 'bakkbone-florist-companion').':</strong> '.$comm.'</p></td></tr>';
		} else {
			$ordernotes = '';
		}
		$cardmessage = $order->get_meta('_card_message');
		
		$sendertitle = $pickup ? __('Customer', 'bakkbone-florist-companion') : __('Sender', 'bakkbone-florist-companion');
		$rectable = $pickup ? '<td></td>' : '<td class="top-align left-align"><strong>'.esc_html__('Recipient', 'bakkbone-florist-companion').'</strong><br>'.$recadd.'<br>'.$recph.$recnotes.'</td>';
		
		$pickupclass = $pickup ? '' : ' unemph-text';
		
		$template = '<html><head><title>'.$this->store['wstitle'].' #'.$orderid.'</title><style type="text/css">.nomargin {margin: 0;}table.bordered, table.bordered th, table.bordered td {border: 1px solid black;border-collapse: collapse;}.width-20 {width: 25%;}.width-15 {width: 15%;}.width-100 {width: 100%;}.height-100 {height: 100%;}.top-align {vertical-align: top;}.bottom-align {vertical-align: bottom;}.middle-align {vertical-align: middle;}.right-align {text-align: right;}.left-align {text-align: left;}.center-align {text-align: center;}.bottom-sticky {position: absolute;bottom: 0;}.unpadded th p, .unpadded td p {margin: 0;}.padded th p, .padded td p {margin: 5px;}.monospace {font-family: "Courier";}.smalltext{font-size:12px;}.unemph-text{font-size:14px;font-style:oblique;}</style></head><body><div class="height-100"><h2 class="nomargin center-align">'.$ordertype.' #'.$orderid.'</h2><h1 class="nomargin center-align">'.$dd.$timeslot.'</h1><hr><table class="width-100 unpadded"><tbody><tr>'.$rectable.'<td class="top-align right-align'.$pickupclass.'"><p><strong>'.$sendertitle.'</strong><br>'.$cusadd.'<br>'.$cusph.'<br>'.$cuseml.'</p></td></tr></tbody></table><br><table class="width-100 bordered padded"><thead><tr><th class="center-align width-15"><p>'.esc_html__('Qty', 'bakkbone-florist-companion').'</p></th><th class="center-align"><p>'.esc_html__('Item', 'bakkbone-florist-companion').'</p></th><th class="center-align width-20"><p>'.esc_html__('Value', 'bakkbone-florist-companion').'</p></th></tr></thead><tbody>'.$itemtable.$ordernotes.'<tr><td colspan="2" class="right-align smalltext"><p>'.esc_html__('Subtotal', 'bakkbone-florist-companion').'</p></td><td class="right-align smalltext"><p>'.$subtotal.'</p></td></tr>'.$feestable.$shippingtable.$discounttable.'<tr><td colspan="2" class="right-align"><p>'.esc_html__('Total', 'bakkbone-florist-companion').'</p></td><td class="right-align"><p>'.$total.'</p></td></tr></tbody></table><div class="bottom-sticky"><h3 class="nomargin">'.esc_html__('Card Message', 'bakkbone-florist-companion').'</h3><p class="nomargin monospace" style="font-size:24px;">'.$cardmessage.'</p></div></div></body>';
				
		$dompdf = new Dompdf();
		$dompdf->loadHtml($template);
		$dompdf->setPaper(get_option('bkf_pdf_setting')['page_size'], 'portrait');
		$dompdf->render();
		
		return $dompdf;
	}
	
	function calendar($start,$end){
		$starttime = strtotime($start);
		$endtime = strtotime($end);
		$endtimeadjusted = $endtime - 86400;
		
		$orders = wc_get_orders(array(
			'post_status' => array('wc-new','wc-accept','wc-processing','wc-completed','wc-scheduled','wc-prepared','wc-collect','wc-out','wc-relayed','wc-invoiced','wc-processed','wc-collected'),
			'limit' => '-1',
			'meta_query' => array(
				array('key' => '_delivery_timestamp', 'value' => $starttime, 'compare' => '>='),
				array('key' => '_delivery_timestamp', 'value' => $endtime, 'compare' => '<')
			)
		));
		
		foreach($orders as $k => $o){
			if($o->get_meta('_delivery_timestamp', true) < $starttime || $o->get_meta('_delivery_timestamp', true) >= $endtime){
				unset($orders[$k]);
			}
		}
		
		$ddtitle = get_option('bkf_ddi_setting')['ddt'];
		$orderlist = [];
		foreach($orders as $o){
			$items = $o->get_items();
			$shipping = $o->needs_shipping_address();
			$list = [];
			foreach($items as $item){
				$itemtext = $item->get_quantity() . 'x ' . $item->get_name();
				if(function_exists('wcj_get_product_addons')){
					$addons = wcj_get_product_addons($item, $o->get_currency());
					$exploded = $addons !== null && $addons !== '' ? explode(', ', $addons) : false;
					if($exploded){
						foreach($exploded as $addon){
							$thisaddon = explode(': ',$addon);
							$itemtext .= '<br><strong>'.__('Add-on', 'bakkbone-florist-companion').':</strong> '.$thisaddon[0].' ('.$thisaddon[1].')';
						}
					}
				}
				$list[] = $itemtext;
			}
			$sa = [];
			if($o->get_shipping_company() !==null && $o->get_shipping_company() !== ''){
				$sa[] = $o->get_shipping_company();
			}
			if($o->get_shipping_address_1() !==null && $o->get_shipping_address_1() !== ''){
				$sa[] = $o->get_shipping_address_1();
			}
			if($o->get_shipping_address_2() !==null && $o->get_shipping_address_2() !== ''){
				$sa[] = $o->get_shipping_address_2();
			}
			$cs = bkf_currency_symbol();
			$orderlist[] = array(
				'id' => $o->get_id(),
				'dd' => $order->get_meta( '_delivery_date', true ),
				'ts' => $order->get_meta( '_delivery_timeslot', true ) == '' ? '' : '<br>'.$order->get_meta( '_delivery_timeslot', true ),
				'dn' => $order->get_meta( '_shipping_notes', true ),
				'items' => implode("<br>",$list),
				'total' => $cs.$o->get_total(),
				'rec' => $o->get_formatted_shipping_full_name(),
				'cus' => $o->get_formatted_billing_full_name(),
				'add' => implode("<br>",$sa),
				'sub' => $o->get_shipping_city(),
				'ph' => $o->get_shipping_phone(),
				'cusph' => $o->get_billing_phone(),
				'ship' => $shipping
			);
		}
		
		$ordertable = '';
		foreach($orderlist as $t){
			if($t['ship']){
				$ordertable .= '<tr><td class="center-align"><p>'.$t['id'].'</p></td><td><p>'.$t['dd'].$t['ts'].'</p></td><td><p>'.$t['items'].'</p></td><td><p>'.$t['total'].'</p></td><td><p>'.$t['rec'].'</p></td><td><p>'.$t['add'].'</p></td><td><p>'.$t['sub'].'</p></td><td><p>'.$t['ph'].'</p></td><td><p>'.$t['dn'].'</p></td></tr>';
			} else {
				$ordertable .= '<tr><td class="center-align"><p>'.$t['id'].'</p></td><td><p>'.$t['dd'].$t['ts'].'</p></td><td><p>'.$t['items'].'</p></td><td><p>'.$t['total'].'</p></td><td colspan="3"><p>'.$t['cus'].' - '.esc_html__('Pickup', 'bakkbone-florist-companion').'</p></td><td><p>'.$t['cusph'].'</p></td><td><p>'.$t['dn'].'</p></td></tr>';
			}
		}
		
		$template = '<html><head><title>'.esc_html__('Delivery List', 'bakkbone-florist-companion').': '.wp_date('j F Y',$starttime).' '.esc_html__('to', 'bakkbone-florist-companion').' '.wp_date('j F Y',$endtimeadjusted).'</title><style>.nomargin(margin: 0;}table, table th, table td {border: 1px solid black;border-collapse: collapse;}.width-100 {width: 100%;}.height-100 {height: 100%;}.right-align {text-align: right;}.left-align {text-align: left;}.center-align {text-align: center;}th p,td p {margin: 5px;}</style></head><body><div class="nomargin"><h2 style="margin: 0;" class="center-align nomargin">'.wp_date('j F Y',$starttime).' '.esc_html__('to', 'bakkbone-florist-companion').' '.wp_date('j F Y',$endtimeadjusted).'</h2><table class="nomargin"><thead><tr><th>'.esc_html__('ID', 'bakkbone-florist-companion').'</th><th>'.$ddtitle.'</th><th>'.esc_html__('Items','bakkbone-florist-companion').'</th><th>'.esc_html__('Total','bakkbone-florist-companion').'</th><th>'.esc_html__('Recipient','bakkbone-florist-companion').'</th><th>'.esc_html__('Address','bakkbone-florist-companion').'</th><th>'.esc_html__('Suburb','bakkbone-florist-companion').'</th><th>'.esc_html__('Phone','bakkbone-florist-companion').'</th><th>'.esc_html__('Notes', 'bakkbone-florist-companion').'</th></tr></thead><tbody>'.$ordertable.'</tbody></table></div></body></html>';
		
		$dompdf = new Dompdf();
		$dompdf->loadHtml($template);
		$dompdf->setPaper(get_option('bkf_pdf_setting')['page_size'], 'landscape');
		$dompdf->render();
		
		return $dompdf;
	}
	
}