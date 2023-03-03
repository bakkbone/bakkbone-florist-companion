<?php

/**
 * @author BAKKBONE Australia
 * @package BkfPetalsDecision
 * @license GNU General Public License (GPL) 3.0
**/

defined("BKF_EXEC") or die("Silence is golden");

class BkfPetalsDecision{
  
    function __construct() {
      $bkfoptions = get_option("bkf_features_setting");
      if($bkfoptions["petals_on"] == 1) {
          add_action('wp_ajax_petals_decision', array($this, 'bkf_petals_decision_ajax') ); 
      }else{};
    }
    
    function bkf_petals_decision_ajax(){
        if ( !wp_verify_nonce( $_REQUEST['nonce'], "bkf_petals_decision_nonce")) {
          exit("No funny business please");
        }

        $reasons = array(
            '293' => 'Cannot deliver flowers',
            '294' => 'Don\'t have the required flowers',
            '270' => 'We cannot deliver to this location ever',
            '280' => 'Cannot deliver to this location today',
            '281' => 'Do not have these flowers but could do a florist choice',
            '282' => 'Do not have any flowers to meet delivery date',
            '272' => 'Need more information to deliver this order',
            '283' => 'Do not have this container but could do with a substitution of container',
            '273' => 'Do not do this product ever',
            '274' => 'There is a problem with this address',
            '284' => 'This area is restricted, can go on next run but not this delivery date',
            '285' => 'This area is restricted and can\'t be delivered until next week'
            );
        $mn         = get_option('bkf_petals_setting')['mn'];
        $password   = get_option('bkf_petals_setting')['ppw'];
        $pw         = base64_decode($password);        
        $petalsid   = $_REQUEST['petalsid'];
        $orderid    = $_REQUEST['orderid'];
        $outcome    = $_REQUEST['outcome'];
        if($outcome == 'reject'){
            $code       = $_REQUEST['code'];
        }
        $url        = 'https://pin.petals.com.au/wconnect/wc.isa?pbo~&ctype=45'
        if($outcome == 'accept'){
            $body       = '<?xml version="1.0" encoding="UTF-8"?>
<message>
<member>'.$mn.'</member>
<password>'.$pw.'</password>
<petalsid>'.$petalsid.'</petalsid>
<recordtype>10</recordtype>
<type>'.strtoupper($outcome).'</type>
<notes></notes>
</message>';
        }
        if($outcome == 'reject'){
            $body       = '<?xml version="1.0" encoding="UTF-8"?>
<message>
<member>'.$mn.'</member>
<password>'.$pw.'</password>
<petalsid>'.$petalsid.'</petalsid>
<recordtype>10</recordtype>
<type>'.strtoupper($outcome).'</type>
<rejectreason>'.$reasons[$code].'</rejectreason>
<rejectcode>'.$code.'</rejectcode>
<notes></notes>
</message>';            
        }
        $response = wp_remote_post($url, array(
            'method'    => 'POST',
            'headers'   => array('Content-Type' => 'application/xml'),
            'body'      => $body
            ));
        $order = new WC_Order( $orderid );
        $rawxml = $response['body'];
        $xml = simplexml_load_string($rawxml);
        $symbol = '</strong>: ';
        $xmlarray = json_decode(json_encode((array)$xml), TRUE);
        $implosion = implode('<br><strong>', array_map(
                    function($k, $v) use($symbol) { 
                        return $k . $symbol . $v;
                    }, 
                    array_keys($xmlarray), 
                    array_values($xmlarray)
                    )
                );
                
        if($xml->type == '300'){
            if($outcome == 'accept'){
                $note = __('<strong>Order accepted.<br>Response from Petals: </strong><br>', 'bakkbone-florist-companion') . '<strong>' . $implosion;
                $ordernote = $order->add_order_note($note);
                $orderstatus = $order->update_status($outcome);
            }else{
                $note = __('<strong>Order rejected.<br>Response from Petals: </strong><br>', 'bakkbone-florist-companion') . '<strong>' . $implosion;
                $order->add_order_note($note);
                $order->update_status($outcome);
            }
        }else{
            if($outcome == 'accept'){
                $note = __('<strong>Order acceptance failed.<br>Response from Petals: </strong><br>', 'bakkbone-florist-companion') . $xml->text;
                $order->add_order_note( $note );
            }else{
                $note = __('<strong>Order rejection failed.<br>Response from Petals: </strong><br>', 'bakkbone-florist-companion') . $xml->text;
                $order->add_order_note( $note );
            }
            $wc_emails = WC()->mailer()->get_emails();
            $wc_emails['WC_Email_Petals_Note']->trigger( $order->get_id() );
        }
        
        if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
          $result = json_encode($result);
          echo $result;
        }
        else {
          header("Location: ".$_SERVER["HTTP_REFERER"]);
        }
        die();
    }
	
}