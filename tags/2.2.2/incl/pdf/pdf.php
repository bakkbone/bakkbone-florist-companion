<?php

/**
 * @author BAKKBONE Australia
 * @package BkfPdf
 * @license GNU General Public License (GPL) 3.0
**/

defined("BKF_EXEC") or die("Silence is golden");
use Dompdf\Dompdf;
class BkfPdf{
	
    function __construct(){
		$this->store = array();
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
		$cusph = implode(get_post_meta($orderid,'_billing_phone'));
		$cuseml = implode(get_post_meta($orderid,'_billing_email'));
		if($cusph !== ''){
			$cusadd .= '<br><strong>'.__('Phone: ', 'bakkbone-florist-companion').'</strong>'.$cusph;
		}
		if($cuseml !== ''){
			$cusadd .= '<br><strong>'.__('Email: ', 'bakkbone-florist-companion').'</strong>'.$cuseml;
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
			$storephone = '<strong>'.__('Phone: ', 'bakkbone-florist-companion').'</strong>'.$this->store['phone'].'<br>';
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
			$itemtable .= '<tr><td class="center-align width-20"><p>'.$productqty.'</p></td><td class="left-align"><p>'.$productname.'</p></td><td class="right-align"><p>'.$productlinetotal.'</p></td></tr>';
		}
		
		$fees = $order->get_fees();
		$feestable = '';
		foreach($fees as $fee){
			$feename = $fee->get_name();
			$feeamt = $order->get_formatted_line_subtotal($fee);
			$feestable .= '<tr><td colspan="2" class="right-align"><p>'.$feename.'</p></td><td class="right-align"><p>'.$feename.'</p></td></tr>';
		}
		
		$taxtotal = $order->get_total_tax();
		$taxtable = '';
		if($taxtotal !== "0"){
			$taxamt = $currencycode.$order->get_total_tax();
			$taxtable .= '<tr><td colspan="2" class="right-align"><p><em>'.__('Tax', 'bakkbone-florist-companion').'</em></p></td><td class="right-align"><p><em>'.$taxamt.'</em></p></td></tr>';
		}
		
		$shippingtotal = $order->get_shipping_total();
		$shippingtable = '';
		if($shippingtotal !== "0"){
			$shippingamt = $order->get_shipping_to_display();
			$shippingtable .= '<tr><td colspan="2" class="right-align"><p>'.__('Delivery', 'bakkbone-florist-companion').'</p></td><td class="right-align"><p>'.$shippingamt.'</p></td></tr>';
		}
		
		$discounttotal = $order->get_discount_total();
		$discounttable = '';
		if($discounttotal !== "0"){
			$discountamt = $order->get_discount_to_display();
			$discounttable .= '<tr><td colspan="2" class="right-align"><p>'.__('Discount', 'bakkbone-florist-companion').'</p></td><td class="right-align"><p>'.$discountamt.'</p></td></tr>';
		}
		
		$subtotal = $order->get_subtotal_to_display();
		$total = $order->get_formatted_order_total();
		
		$template = '<html><head><title>'.$this->store['invtitle'].' #'.$orderid.'</title><style>.margin-0 {margin: 0;}table.bordered, table.bordered th, table.bordered td {border: 1px solid black;border-collapse: collapse;}.width-20 {width: 20%;}.width-100 {width: 100%;}.height-100 {height: 100%;}.top-align {vertical-align: top;}.bottom-align {vertical-align: bottom;}.middle-align {vertical-align: middle;}.right-align {text-align: right;}.left-align {text-align: left;}.center-align {text-align: center;}.bottom-sticky {position: absolute;bottom: 0;}.unpadded th p, .unpadded td p {margin: 0;}.padded th p, .padded td p {margin: 5px;}</style></head><body><div class="height-100"><h1 class="margin-0">'.$this->store['invtitle'].' #'.$orderid.'</h1><hr><table class="width-100 unpadded"><thead><tr><th class="left-align"><p>'.__('Invoice From', 'bakkbone-florist-companion').'</p></th><th class="right-align"><p>'.__('Invoice To', 'bakkbone-florist-companion').'</p></th></tr></thead><tbody><tr><td class="top-align left-align"><p>'.$this->store['name'].'<br>'.$this->store['a1'].$storea2.'<br>'.$this->store['sub'].'&nbsp;'.$this->store['state'].'&nbsp;'.$this->store['pc'].'</p><p>'.$storephone.'<strong>'.__('Email: ', 'bakkbone-florist-companion').'</strong>'.$this->store['email'].'<br><strong>'.__('Website: ', 'bakkbone-florist-companion').'</strong>'.$this->store['website'].'</p><p><strong>'.$this->store['taxlabel'].': </strong>'.$this->store['taxvalue'].'</p></td><td class="top-align right-align"><p>'.$cusadd.'</p></td></tr></tbody></table><br><table class="width-100 bordered padded"><thead><tr><th class="center-align width-20"><p>'.__('Qty', 'bakkbone-florist-companion').'</p></th><th class="center-align"><p>'.__('Item', 'bakkbone-florist-companion').'</p></th><th class="center-align"><p>'.__('Value', 'bakkbone-florist-companion').'</p></th></tr></thead><tbody>'.$itemtable.'<tr><td colspan="2" class="right-align"><p>'.__('Subtotal', 'bakkbone-florist-companion').'</p></td><td class="right-align"><p>'.$subtotal.'</p></td></tr>'.$feestable.$shippingtable.$discounttable.$taxtable.'<tr><td colspan="2" class="right-align"><p>'.__('Total', 'bakkbone-florist-companion').'</p></td><td class="right-align"><p>'.$total.'</p></td></tr></tbody></table><div class="bottom-sticky"><p>'.$this->store['invtext'].'</p></div></div></body>';
		
		$dompdf = new Dompdf();
		$dompdf->loadHtml($template);
		$dompdf->setPaper('A4', 'portrait');
		$dompdf->render();
		
		return $dompdf;
	}
	
	function worksheet($orderid){
		$order = new WC_Order($orderid);
		$cusadd = $order->get_formatted_billing_address();
		$recadd = $order->get_formatted_shipping_address();
		$dt = $order->get_meta('_delivery_timestamp');
		$dd = date('l, j M', $dt);
		$recph = implode(get_post_meta($orderid,'_shipping_phone'));
		$cusph = implode(get_post_meta($orderid,'_billing_phone'));
		$cuseml = implode(get_post_meta($orderid,'_billing_email'));
		$recnotes = implode(get_post_meta($orderid,'_shipping_notes'));
		$comm = $order->get_customer_note();
		
		global $wpdb;
		$ts = array();
		$timeslots = $wpdb->get_results(
			"
				SELECT id, method, day, start, end
				FROM {$wpdb->prefix}bkf_dd_timeslots
			"
		);
		foreach($timeslots as $timeslot){
			$ts[] = array(
				'id'		=>	$timeslot->id,
				'method'	=>	$timeslot->method,
				'day'		=>	$timeslot->day,
				'start'		=>	$timeslot->start,
				'end'		=>	$timeslot->end
			);
		}
		$tscol = array_column($ts, 'id');
		$tsraw = '';
		$tsraw = implode(get_post_meta($orderid,'_delivery_timeslot'));
		if($tsraw !== ''){
			$thistsid = array_search($delivery_timeslot, $tscol);
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
			$itemtable .= '<tr><td class="center-align width-15"><p>'.$productqty.'</p></td><td class="left-align"><p><strong>'.$productname.'</strong>'.$itemmeta.'</p></td><td class="right-align width-20"><p>'.$productlinetotal.'</p></td></tr>';
		}
		
		$fees = $order->get_fees();
		$feestable = '';
		foreach($fees as $fee){
			$feename = $fee->get_name();
			$feeamt = $order->get_formatted_line_subtotal($fee);
			$feestable .= '<tr><td colspan="2" class="right-align"><p>'.$feename.'</p></td><td class="right-align"><p>'.$feename.'</p></td></tr>';
		}
		
		$sm = $order->get_shipping_methods();
		foreach($sm as $smethod){
			$method[] = $smethod->get_method_id();
		}
		if(in_array("local_pickup", $method)){
			$ordertype = __('Pickup', 'bakkbone-florist-companion');
		} else {
			$ordertype = __('Delivery', 'bakkbone-florist-companion');
		}
		
		$shippingtotal = $order->get_shipping_total();
		$shippingtable = '';
		if($shippingtotal !== "0"){
			$shippingamt = $order->get_shipping_to_display();
			$shippingtable .= '<tr><td colspan="2" class="right-align"><p>'.__('Delivery', 'bakkbone-florist-companion').'</p></td><td class="right-align"><p>'.$shippingamt.'</p></td></tr>';
		}
		
		$discounttotal = $order->get_discount_total();
		$discounttable = '';
		if($discounttotal !== "0"){
			$discountamt = $order->get_discount_to_display();
			$discounttable .= '<tr><td colspan="2" class="right-align"><p>'.__('Discount', 'bakkbone-florist-companion').'</p></td><td class="right-align"><p>'.$discountamt.'</p></td></tr>';
		}
		
		$subtotal = $order->get_subtotal_to_display();
		$total = $order->get_formatted_order_total();
		
		if($comm !== ''){
			$ordernotes = '<p class="margin-0"><strong>'.__('Comments from Sender: ', 'bakkbone-florist-companion').'</strong>'.$comm.'</p>';
		} else {
			$ordernotes = '';
		}
		$cardmessage = $order->get_meta('_card_message');
		
		$template = '<html><head><title>'.$this->store['wstitle'].' #'.$orderid.'</title><style>.margin-0 {margin: 0;}table.bordered, table.bordered th, table.bordered td {border: 1px solid black;border-collapse: collapse;}.width-20 {width: 25%;}.width-15 {width: 15%;}.width-100 {width: 100%;}.height-100 {height: 100%;}.top-align {vertical-align: top;}.bottom-align {vertical-align: bottom;}.middle-align {vertical-align: middle;}.right-align {text-align: right;}.left-align {text-align: left;}.center-align {text-align: center;}.bottom-sticky {position: absolute;bottom: 0;}.unpadded th p, .unpadded td p {margin: 0;}.padded th p, .padded td p {margin: 5px;}.monospace {font-family: "Courier";}</style></head><body><div class="height-100"><h2 class="margin-0 center-align">'.$ordertype.' #'.$orderid.'</h2><h1 class="margin-0 center-align">'.$dd.$timeslot.'</h1><hr><table class="width-100 unpadded"><thead><tr><th class="left-align"><p>'.__('Recipient', 'bakkbone-florist-companion').'</p></th><th class="right-align"><p>'.__('Sender', 'bakkbone-florist-companion').'</p></th></tr></thead><tbody><tr><td class="top-align left-align"><p>'.$recadd.'<br>'.$recph.'<br>'.$recnotes.'</p></td><td class="top-align right-align"><p>'.$cusadd.'<br>'.$cusph.'<br>'.$cuseml.'</p></td></tr></tbody></table><br><table class="width-100 bordered padded"><thead><tr><th class="center-align width-15"><p>'.__('Qty', 'bakkbone-florist-companion').'</p></th><th class="center-align"><p>'.__('Item', 'bakkbone-florist-companion').'</p></th><th class="center-align width-20"><p>'.__('Value', 'bakkbone-florist-companion').'</p></th></tr></thead><tbody>'.$itemtable.'<tr><td colspan="2" class="right-align"><p>'.__('Subtotal', 'bakkbone-florist-companion').'</p></td><td class="right-align"><p>'.$subtotal.'</p></td></tr>'.$feestable.$shippingtable.$discounttable.'<tr><td colspan="2" class="right-align"><p>'.__('Total', 'bakkbone-florist-companion').'</p></td><td class="right-align"><p>'.$total.'</p></td></tr></tbody></table>'.$ordernotes.'<div class="bottom-sticky"><h3 class="margin-0">'.__('Card Message', 'bakkbone-florist-companion').'</h3><p class="margin-0 monospace" style="font-size:24px;">'.$cardmessage.'</p></div></div></body>';
		
		$dompdf = new Dompdf();
		$dompdf->loadHtml($template);
		$dompdf->setPaper('A4', 'portrait');
		$dompdf->render();
		
		return $dompdf;
	}
	
	function calendar($start,$end){
		$starttime = strtotime($start);
		$endtime = strtotime($end);
		
		$orders = get_posts(array(
			'post_type' => 'shop_order',
			'post_status' => array('new','accept','processing','completed','scheduled','prepared','collect','out','relayed'),
			'numberposts' => '-1',
			'orderby' => 'meta_value_num',
			'meta_key' => '_delivery_timestamp',
			'meta_query' => array(
				array('key' => '_delivery_timestamp', 'value' => $starttime, 'compare' => '>='),
				array('key' => '_delivery_timestamp', 'value' => $endtime, 'compare' => '<')
			)
		));
		$ddtitle = get_option('bkf_ddi_setting')['ddt'];
		$orderlist = array();
		foreach($orders as $order){
			$o = new WC_Order($order->ID);
			$items = $o->get_items();
			$list = array();
			foreach($items as $item){
				$list[] = $item->get_quantity() . 'x ' . $item->get_name();
			}
			$sa = array();
			if($o->get_shipping_company() !==null && $o->get_shipping_company() !== ''){
				$sa[] = $o->get_shipping_company();
			}
			if($o->get_shipping_address_1() !==null && $o->get_shipping_address_1() !== ''){
				$sa[] = $o->get_shipping_address_1();
			}
			if($o->get_shipping_address_2() !==null && $o->get_shipping_address_2() !== ''){
				$sa[] = $o->get_shipping_address_2();
			}
			$cs = get_woocommerce_currency_symbol(get_woocommerce_currency());
			$orderlist[] = array(
				'id' => $o->get_id(),
				'dd' => get_post_meta( $o->get_id(), '_delivery_date', true ),
				'dn' => get_post_meta( $o->get_id(), '_shipping_notes', true ),
				'items' => implode("<br>",$list),
				'total' => $cs.$o->get_total(),
				'rec' => $o->get_formatted_shipping_full_name(),
				'add' => implode("<br>",$sa),
				'sub' => $o->get_shipping_city(),
				'ph' => $o->get_shipping_phone()
			);
		}
		
		$ordertable = '';
		foreach($orderlist as $t){
			$ordertable .= '<tr><td class="center-align"><p>'.$t['id'].'</p></td><td><p>'.$t['dd'].'</p></td><td><p>'.$t['items'].'</p></td><td><p>'.$t['total'].'</p></td><td><p>'.$t['rec'].'</p></td><td><p>'.$t['add'].'</p></td><td><p>'.$t['sub'].'</p></td><td><p>'.$t['ph'].'</p></td><td><p>'.$t['dn'].'</p></td></tr>';
		}
		
		$template = '<html><head><title>'.__('Delivery Calendar', 'bakkbone-florist-companion').': '.date('j F Y',$starttime).' '.__('to', 'bakkbone-florist-companion').' '.date('j F Y',$endtime).'</title><style>.margin-0(margin: 0;}table, table th, table td {border: 1px solid black;border-collapse: collapse;}.width-100 {width: 100%;}.height-100 {height: 100%;}.right-align {text-align: right;}.left-align {text-align: left;}.center-align {text-align: center;}th p,td p {margin: 5px;}</style></head><body><div class="margin-0 height-100"><h2 class="center-align margin-0">'.date('j F Y',$starttime).' '.__('to', 'bakkbone-florist-companion').' '.date('j F Y',$endtime).'</h2><table class="margin-0"><thead><tr><th>'.__('Order ID', 'bakkbone-florist-companion').'</th><th>'.$ddtitle.'</th><th>'.__('Items','bakkbone-florist-companion').'</th><th>'.__('Total','bakkbone-florist-companion').'</th><th>'.__('Recipient','bakkbone-florist-companion').'</th><th>'.__('Address','bakkbone-florist-companion').'</th><th>'.__('Suburb','bakkbone-florist-companion').'</th><th>'.__('Phone','bakkbone-florist-companion').'</th><th>'.__('Notes', 'bakkbone-florist-companion').'</th></tr></thead><tbody>'.$ordertable.'</tbody></table></div></body></html>';
		
		$dompdf = new Dompdf();
		$dompdf->loadHtml($template);
		$dompdf->setPaper('A4', 'landscape');
		$dompdf->render();
		
		return $dompdf;
	}
	
}