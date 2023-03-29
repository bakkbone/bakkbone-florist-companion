<?php

/**
 * @author BAKKBONE Australia
 * @package BakkboneFloristCompanion
 * @license GNU General Public License (GPL) 3.0
**/

defined("BKF_EXEC") or die("Silence is golden");

class BakkboneFloristCompanion{
	
	function __construct(){
		
	}
	
	function getRssFeed($feed_url) {
		$content = file_get_contents($feed_url);
		$x = new SimpleXmlElement($content);
		
		$items = array();
		foreach($x->channel->item as $entry) {
			$items[] = $entry;
		}
		return $items;
	}
	
	function full_count(){
		$statuslist = wc_get_order_statuses();
		$total = 0;
		foreach($statuslist as $key => $value){
			$count = wc_orders_count($key);
			$total += $count;
		}
		return $total;
	}
	
	function all_count(){
		$allstatus = array("wc-processing","wc-made","wc-collect","wc-out","wc-scheduled","wc-new","wc-accept");
		$total = 0;
		foreach($allstatus as $key){
			$count = wc_orders_count($key);
			$total += $count;
		}
		return $total;
	}
	
}