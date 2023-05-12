<?php

/**
 * @author BAKKBONE Australia
 * @package BkfPOPosts
 * @license GNU General Public License (GPL) 3.0
**/

defined("BKF_EXEC") or die("Silence is golden");

class BkfPOPosts{

	function __construct()
	{
        $bkfoptions = get_option("bkf_features_setting");
        if($bkfoptions["petals_on"] == 1) {
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
	        return array();
	}
	
	public static function bkf_exclude_order_comments( $clauses ) {
		$clauses['where'] .= ( $clauses['where'] ? ' AND ' : '' ) . " comment_type != 'petals_order_note' ";
		return $clauses;
	}
	
	public static function bkf_exclude_order_comments_from_feed_where( $where ) {
		return $where . ( $where ? ' AND ' : '' ) . " comment_type != 'order_note' ";
	}
	
	function bkf_po_table( $columns ) {
	    $new = array();

	    foreach($columns as $key => $title) {
	        if ($key=='title'){
		        $new['potitle'] = _x('Petals Order Number', 'order list column title', 'bakkbone-florist-companion');
				$new['podeldate'] = BKF_PO_FIELD_DELDATE;
				$new['porecipient'] = BKF_PO_FIELD_RECIPIENT;
				$new['potown'] = BKF_PO_FIELD_TOWN;
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
			$title = sprintf(BKF_ORDER_VIEW_TITLE, $post_id);
		}
		return $title;
	}
	
	function bkf_qe( $actions = array(), $post = null ) {
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
			update_post_meta($post_id,'crtyname', BKF_ISO_COUNTRIES_FIXED[$crtycode]);
			
	        $mn         = get_option('bkf_petals_setting')['mn'];
	        $password   = get_option('bkf_petals_setting')['ppw'];
	        $pw         = base64_decode($password);
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
	            'method'    => 'POST',
	            'headers'   => array('Content-Type' => 'application/xml'),
	            'body'      => $body
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
				$fullnote = BKF_PETALS_FULL_TRANSMISSION . '<strong>' . $implosion;
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
			
	        if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
	          $result = json_encode($result);
	          echo $result;
	        }
	        else {
	          header("Location: ".get_permalink($post_id));
	        }
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
					'label' => BKF_PO_FIELD_SENDID,
					'name' => 'sendid',
					'aria-label' => '',
					'type' => 'text',
					'instructions' => BKF_PO_FIELD_SENDID_INFO,
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
					'label' => BKF_PO_ORDER_ITEMS,
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
					'label' => BKF_PO_FIELD_DESCRIPTION,
					'name' => 'description',
					'aria-label' => '',
					'type' => 'text',
					'instructions' => BKF_PO_FIELD_DESCRIPTION_INFO,
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
					'label' => BKF_PO_FIELD_PRODUCTID,
					'name' => 'productid',
					'aria-label' => '',
					'type' => 'text',
					'instructions' => BKF_PO_FIELD_PRODUCTID_INFO,
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
					'label' => BKF_PO_FIELD_MAKEUP,
					'name' => 'makeup',
					'aria-label' => '',
					'type' => 'text',
					'instructions' => BKF_PO_FIELD_MAKEUP_INFO,
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
					'label' => BKF_PO_FIELD_UPSELL,
					'name' => 'upsell',
					'aria-label' => '',
					'type' => 'text',
					'instructions' => BKF_PO_FIELD_UPSELL_INFO,
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
					'label' => BKF_PO_FIELD_UPSELLAMT,
					'name' => 'upsellAmt',
					'aria-label' => '',
					'type' => 'number',
					'instructions' => BKF_PO_FIELD_UPSELLAMT_INFO,
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
					'label' => BKF_PO_FIELD_TVALUE,
					'name' => 'tvalue',
					'aria-label' => '',
					'type' => 'number',
					'instructions' => BKF_PO_FIELD_TVALUE_INFO,
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
					'label' => BKF_PO_ORDER_DELIVERY,
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
					'label' => BKF_PO_FIELD_DELDATE,
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
					'label' => BKF_PO_FIELD_DELTIME,
					'name' => 'deltime',
					'aria-label' => '',
					'type' => 'text',
					'instructions' => BKF_PO_FIELD_DELTIME_INFO,
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
					'label' => BKF_PO_ORDER_RECIPIENT,
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
					'label' => BKF_PO_FIELD_RECIPIENT,
					'name' => 'recipient',
					'aria-label' => '',
					'type' => 'text',
					'instructions' => BKF_PO_FIELD_RECIPIENT_INFO,
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
					'label' => BKF_PO_FIELD_SURNAME,
					'name' => 'surname',
					'aria-label' => '',
					'type' => 'text',
					'instructions' => BKF_PO_FIELD_SURNAME_INFO,
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
					'label' => BKF_PO_FIELD_ADDRESSTYPE,
					'name' => 'addresstype',
					'aria-label' => '',
					'type' => 'text',
					'instructions' => BKF_PO_FIELD_ADDRESSTYPE_INFO,
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
					'label' => BKF_PO_FIELD_ADDRESS,
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
					'label' => BKF_PO_FIELD_TOWN,
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
					'label' => BKF_PO_FIELD_STATE,
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
					'label' => BKF_PO_FIELD_POSTALCODE,
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
					'label' => BKF_PO_FIELD_CRTYNAME,
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
					'choices' => BKF_ISO_COUNTRIES,
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
					'label' => BKF_PO_FIELD_PHONE,
					'name' => 'phone',
					'aria-label' => '',
					'type' => 'text',
					'instructions' => BKF_PO_FIELD_PHONE_INFO,
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
					'label' => BKF_PO_ORDER_INFORMATION,
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
					'label' => BKF_PO_FIELD_MESSAGE,
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
					'label' => BKF_PO_FIELD_OCCASION,
					'name' => 'occasion',
					'aria-label' => '',
					'type' => 'text',
					'instructions' => BKF_PO_FIELD_OCCASION_INFO,
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
					'label' => BKF_PO_FIELD_COMMENTS,
					'name' => 'comments',
					'aria-label' => '',
					'type' => 'text',
					'instructions' => BKF_PO_FIELD_COMMENTS_INFO,
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
					'label' => BKF_PO_FIELD_SUPPLIER,
					'name' => 'supplier',
					'aria-label' => '',
					'type' => 'text',
					'instructions' => BKF_PO_FIELD_SUPPLIER_INFO,
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
					'label' => BKF_PO_ORDER_CUSTOMER,
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
					'label' => BKF_PO_FIELD_CONTACT_NAME,
					'name' => 'contact_name',
					'aria-label' => '',
					'type' => 'text',
					'instructions' => BKF_PO_FIELD_CONTACT_NAME_INFO,
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
					'label' => BKF_PO_FIELD_CONTACT_EMAIL,
					'name' => 'contact_email',
					'aria-label' => '',
					'type' => 'email',
					'instructions' => BKF_PO_FIELD_CONTACT_EMAIL_INFO,
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
					'label' => BKF_PO_FIELD_CONTACT_PHONE,
					'name' => 'contact_phone',
					'aria-label' => '',
					'type' => 'text',
					'instructions' => BKF_PO_FIELD_CONTACT_PHONE_INFO,
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
			   'title' => BKF_HELP_TITLE,
			   'callback' => $callback
			));
		}
	}
	
	function petalsorder_help(){
		?>
		<h2><?php echo BKF_HELP_SUBTITLE; ?></h2>
			<a href="https://plugins.bkbn.au/docs/bkf/petals-network/petals-network-integration/" target="_blank">https://plugins.bkbn.au/docs/bkf/petals-network/petals-network-integration/</a>
		<?php
	}
	
	function bkfRegisterPetalsOrderPost()
	{
		$labels = array(
			"name"			=> _x("Petals Orders", "post type general name", "bakkbone-florist-companion"),
			"singular_name"		=> _x("Petals Order", "post type singular name", "bakkbone-florist-companion"),
			"menu_name"		=> _x("Petals Orders", "admin menu", "bakkbone-florist-companion"),
			"name_admin_bar"	=> _x("Petals Order", "add new on admin bar", "bakkbone-florist-companion"),
			"add_new"		=> _x("Send New", "Add New", "bakkbone-florist-companion"),
			"add_new_item"		=> __("Send New Petals Order", "bakkbone-florist-companion"),
			"new_item"		=> __("New Order", "bakkbone-florist-companion"),
			"edit_item"		=> __("Edit Order", "bakkbone-florist-companion"),
			"view_item"		=> __("View Order", "bakkbone-florist-companion"),
			"all_items"		=> __("All Orders", "bakkbone-florist-companion"),
			"search_items"		=> __("Search Orders", "bakkbone-florist-companion"),
			"parent_item_colon"	=> __("Parent Orders:", "bakkbone-florist-companion"),
			"not_found"		=> __("Not found", "bakkbone-florist-companion"),
			"not_found_in_trash"	=> __("Not found in trash", "bakkbone-florist-companion"),
			"attributes"	=> __("Order Attributes", "bakkbone-florist-companion"),
			"insert_into_item"	=> __("Insert into order", "bakkbone-florist-companion"),
			"uploaded_to_this_item"	=> __("Uploaded to this order", "bakkbone-florist-companion"),
			"filter_items_list"	=> __("Filter orders list", "bakkbone-florist-companion"),
			"items_list_navigation"	=> __("Orders list navigation", "bakkbone-florist-companion"),
			"items_list"	=> __("Orders list", "bakkbone-florist-companion"),
			"item_published"	=> __("Order sent.", "bakkbone-florist-companion"),
			"item_published_privately"	=> __("Order sent privately.", "bakkbone-florist-companion"),
			"item_reverted_to_draft"	=> __("Order reverted to draft.", "bakkbone-florist-companion"),
			"item_scheduled"	=> __("Order scheduled.", "bakkbone-florist-companion"),
			"item_updated"	=> __("Order updated.", "bakkbone-florist-companion"),
			"item_link"	=> __("Order link", "bakkbone-florist-companion"),
			"item_link_description"	=> __("A link to a Petals Order.", "bakkbone-florist-companion")
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
			"menu_position"			=> 2,
			"taxonomies"			=> array(),
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