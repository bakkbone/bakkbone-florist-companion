// Date
jQuery(document).ready(function( $ ) {
	var ele = document.getElementsByName('shipping_method[0]');
	for(i = 0; i < ele.length; i++) {
		if(ele[i].checked)
			var currentShippingMethod = ele[i].value;
	}
	if (currentShippingMethod === null && typeof ele[0] !== 'undefined') {
		currentShippingMethod = ele[0].value;
	} else if (currentShippingMethod === null) {
		currentShippingMethod = '';
	}
	
	var datesList = [];
	for (const [dateString, dateObject] of Object.entries(bkf_dd_options.datesListFull)) {
		if (! dateObject.pass) {
			datesList[dateString] = dateObject.outcome;
		} else {
			if (typeof dateObject.mr !== 'undefined') {
				dateObject.mr.forEach(
					function(item, index, array){
						if (item == currentShippingMethod) {
							datesList[dateString] = [false, 'unavailable', bkf_dd_options.mrText];
						}
					}
				);
			}
			if (typeof dateObject.msl !== 'undefined') {
				dateObject.msl.forEach(
					function(item, index, array){
						timeNow = new Date();
						timeThen = new Date(item[2]);
						if (item == currentShippingMethod && timeNow >= timeThen) {
							datesList[dateString] = [false, 'unavailable', bkf_dd_options.sdcMethod];
						}
					}
				);
			}
		}
	}
	
	jQuery("#delivery_date").attr( 'readOnly' , 'true' );
	jQuery(".delivery_date").datepicker( {
		minDate: 0,
		maxDate: bkf_dd_options.maxDate,
		dateFormat: "DD, d MM yy",
		hideIfNoPrevNext: true,
		firstDay: 1,
		constrainInput: true,
		beforeShowDay: blockedDates,
		onUpdateDatepicker: newTitles,
		showButtonPanel: true,
		showOtherMonths: true,
		selectOtherMonths: true,
	} );
	function blockedDates(date) {
		var d = date.getDate();
		var m = date.getMonth() + 1;
		var y = date.getFullYear();
		var theIndex = d + '.' + m + '.' + y;
		if (typeof datesList[theIndex] !== 'undefined') {
		    currentResult = datesList[theIndex];
		} else {
		    currentResult = [true];
		}
		return currentResult;
	}
	function newTitles(inst){
		dates = jQuery('.ui-datepicker-calendar td');
		dates.each(function($){
			jQuery(this).css('pointer-events', 'auto');
		});
	}
} );

jQuery(document.body).on( 'change', 'input.shipping_method', function($) {
	var ele = document.getElementsByName('shipping_method[0]');
	for(i = 0; i < ele.length; i++) {
		if(ele[i].checked)
			var currentShippingMethod = ele[i].value;
	}
	if (currentShippingMethod === null && typeof ele[0] !== 'undefined') {
		currentShippingMethod = ele[0].value;
	} else if (currentShippingMethod === null) {
		currentShippingMethod = '';
	}
	var datesList = [];
	for (const [dateString, dateObject] of Object.entries(bkf_dd_options.datesListFull)) {
		if (! dateObject.pass) {
			datesList[dateString] = dateObject.outcome;
		} else {
			if (typeof dateObject.mr !== 'undefined') {
				dateObject.mr.forEach(
					function(item, index, array){
						if (item == currentShippingMethod) {
							datesList[dateString] = [false, 'unavailable', bkf_dd_options.mrText];
						}
					}
				);
			}
			if (typeof dateObject.msl !== 'undefined') {
				dateObject.msl.forEach(
					function(item, index, array){
						timeNow = new Date();
						timeThen = new Date(item[2]);
						if (item == currentShippingMethod && timeNow >= timeThen) {
							datesList[dateString] = [false, 'unavailable', bkf_dd_options.sdcMethod];
						}
					}
				);
			}
		}
	}
		
	jQuery(".delivery_date").datepicker( "option", {
		beforeShowDay: blockedDates2
	} );
	function blockedDates2(date) {
		var d = date.getDate();
		var m = date.getMonth() + 1;
		var y = date.getFullYear();
		var theIndex = d + '.' + m + '.' + y;
		if (typeof datesList[theIndex] !== 'undefined') {
		    currentResult = datesList[theIndex];
		} else {
		    currentResult = [true];
		}
		return currentResult;
	}
} );

// Timeslot

jQuery(document).on( 'change', 'input.shipping_method, input.delivery_date', function($) {
	const select = document.querySelector('#delivery_timeslot');
	const datefield = document.querySelector('#delivery_date').value;
	
	jQuery(select).empty($);
	if (datefield.length && bkf_dd_options_ts.length) {
		if (bkf_dd_options.debug) {
			console.debug('change input: datefield.length && bkf_dd_options_ts.length');
		}
		jQuery.ajax(bkf_dd_options.ajax_url, {
			async: false,
			data: {
				action: 'bkf_retrieve_session_ts',
			},
			method: 'POST',
			success: function(result){
				currentTs = JSON.parse(result);
			}
		});
		bkf_dd_options_ts.forEach(newOption);
	} else {
		if (bkf_dd_options.debug) {
			console.debug('change input: !datefield.length || !bkf_dd_options_ts.length');
		}
		const wrapper = document.querySelector('#delivery_timeslot_field');
		jQuery(wrapper).addClass('bkf-hidden');
		document.querySelector('#delivery_timeslot').removeAttribute('required');
	}
	    
	function newOption(value) {
		const wrapper = document.querySelector('#delivery_timeslot_field');
		const select = document.querySelector('#delivery_timeslot');
		var currentShippingMethod;
		var ele = document.getElementsByName('shipping_method[0]');
		if (bkf_dd_options.debug) {
			console.debug(ele);
			console.debug(ele[0]);
			console.debug(ele[0].value);
		}
		for(i = 0; i < ele.length; i++) {
			if(ele[i].checked) {
				currentShippingMethod = ele[i].value;
			}
		}
		if (typeof currentShippingMethod == 'undefined' && typeof ele[0] !== 'undefined') {
			currentShippingMethod = ele[0].value;
		} else if (typeof currentShippingMethod == 'undefined' && typeof ele !== 'undefined') {
			currentShippingMethod = ele.value;
		} else if (typeof currentShippingMethod == 'undefined') {
			currentShippingMethod = '';
		}
		var days = ["sunday", "monday", "tuesday", "wednesday", "thursday", "friday", "saturday"];
		var aDate = document.querySelector('#delivery_date').value;
		var theDate = Date.parse(aDate);
		var theDateObject = new Date(theDate);
		var delDay = days[theDateObject.getDay()];
		
		if( value.day == delDay && value.method == currentShippingMethod ){
			if(currentTs == value.slot){
				let newOption = new Option(value.text, value.slot, true, true);
				select.add(newOption, undefined);
			} else {
				let newOption = new Option(value.text, value.slot, false);
				select.add(newOption, undefined);
			}
		}

		if(document.querySelector('#delivery_timeslot').options.length) {
			const wrapper = document.querySelector('#delivery_timeslot_field');
			jQuery(wrapper).removeClass('bkf-hidden');
			document.querySelector('#delivery_timeslot').setAttribute('required', '');
		} else {
			jQuery(wrapper).addClass('bkf-hidden');
			document.querySelector('#delivery_timeslot').removeAttribute('required');
		}
	
	}
});

jQuery(document).ready( function($) {
	const select = document.querySelector('#delivery_timeslot');
	const datefield = document.querySelector('#delivery_date').value;
	
	jQuery(select).empty($);
    if (datefield.length && bkf_dd_options_ts.length) {
		if (bkf_dd_options.debug) {
			console.debug('document ready: datefield.length && bkf_dd_options_ts.length');
		}
		jQuery.ajax(bkf_dd_options.ajax_url, {
			async: false,
			data: {
				action: 'bkf_retrieve_session_ts',
			},
			method: 'POST',
			success: function(result){
				currentTs = JSON.parse(result);
			}
		});
		bkf_dd_options_ts.forEach(newOption);
    } else {
		if (bkf_dd_options.debug) {
			console.debug('document ready: !datefield.length || !bkf_dd_options_ts.length');
		}
        const wrapper = document.querySelector('#delivery_timeslot_field');
        jQuery(wrapper).addClass('bkf-hidden');
        document.querySelector('#delivery_timeslot').removeAttribute('required');
    }
    
    function newOption(value) {
		const wrapper = document.querySelector('#delivery_timeslot_field');
		const select = document.querySelector('#delivery_timeslot');
		var currentShippingMethod;
		var ele = document.getElementsByName('shipping_method[0]');
		if (bkf_dd_options.debug) {
			console.debug(ele);
			console.debug(ele[0]);
			console.debug(ele[0].value);
		}
		for(i = 0; i < ele.length; i++) {
			if(ele[i].checked) {
				currentShippingMethod = ele[i].value;
			}
		}
		if (typeof currentShippingMethod == 'undefined' && typeof ele[0] !== 'undefined') {
			currentShippingMethod = ele[0].value;
		} else if (typeof currentShippingMethod == 'undefined' && typeof ele !== 'undefined') {
			currentShippingMethod = ele.value;
		} else if (typeof currentShippingMethod == 'undefined') {
			currentShippingMethod = '';
		}
		var days = ["sunday", "monday", "tuesday", "wednesday", "thursday", "friday", "saturday"];
		var aDate = document.querySelector('#delivery_date').value;
		var theDate = Date.parse(aDate);
		var theDateObject = new Date(theDate);
		var delDay = days[theDateObject.getDay()];
		
		if( value.day == delDay && value.method == currentShippingMethod ){
			if(currentTs == value.slot){
				let newOption = new Option(value.text, value.slot, true, true);
				select.add(newOption, undefined);
			} else {
				let newOption = new Option(value.text, value.slot, false);
				select.add(newOption, undefined);
			}
		}

		if(document.querySelector('#delivery_timeslot').options.length) {
			const wrapper = document.querySelector('#delivery_timeslot_field');
			jQuery(wrapper).removeClass('bkf-hidden');
			document.querySelector('#delivery_timeslot').setAttribute('required', '');
		} else {
			jQuery(wrapper).addClass('bkf-hidden');
			document.querySelector('#delivery_timeslot').removeAttribute('required');
		}
		
	}
});

jQuery( document ).ready (function($){
	jQuery( 'body' ).on( 'blur change', '#delivery_date', function(){
		const wrapper = jQuery(this).closest( '.form-row' );
		if( ! /[a-zA-Z]{6,9}\,\ \d{1,2}\ [a-zA-Z]{3,9}\ \d{4}/.test( jQuery(this).val() ) ) {
			wrapper.addClass( 'woocommerce-invalid' );
			wrapper.removeClass( 'woocommerce-validated' );
		} else {
			wrapper.addClass( 'woocommerce-validated' );
		}
	});
	jQuery( 'body' ).on( 'blur change', '#delivery_date, #delivery_timeslot', function(){
		const wrapper = jQuery('#delivery_timeslot').closest( '.form-row' );
		const item = document.getElementById("delivery_timeslot");
		if(item.hasAttribute('required') && item.value == '') {
			wrapper.addClass( 'woocommerce-invalid' );
			wrapper.removeClass( 'woocommerce-validated' );
		} else {
			wrapper.addClass( 'woocommerce-validated' );
		}
	});
	jQuery('form.checkout').on('change', '#delivery_date, #delivery_timeslot', function(){
		var dd = jQuery('#delivery_date').val();
		var ts = jQuery('#delivery_timeslot').val();
		jQuery.ajax({
			type: 'POST',
			url: wc_checkout_params.ajax_url,
			data: {
				'action': 'bkf_checkout_get_ajax_data',
				'delivery_date': dd,
				'delivery_timeslot': ts,
			},
			success: function (result) {
				jQuery('body').trigger('update_checkout');
			},
			error: function(error){
				console.log(error);
			}
		});
	});
	jQuery('form.checkout').on('change', '#shipping_notes, #card_message', function(){
		var sn = jQuery('#shipping_notes').val();
		var cm = jQuery('#card_message').val();
		jQuery.ajax({
			type: 'POST',
			url: wc_checkout_params.ajax_url,
			data: {
				'action': 'bkf_checkout_get_ajax_data',
				'shipping_notes': sn,
				'card_message': cm,
			},
			success: function (result) {
				jQuery('body').trigger('update_checkout');
			},
			error: function(error){
				console.log(error);
			}
		});
	});
});

if (bkf_dd_options.pickup) {
	jQuery( document ).ready(function( $ ) {

		jQuery('form.checkout').on('change', "input[name=\'ship_type\']", function($){

			var ship_type;
			var ele = document.getElementsByName("ship_type");
			for(i = 0; i < ele.length; i++) {

				if(ele[i].checked)
				var ship_type = ele[i].value;

			}
			if (bkf_dd_options.debug) {
				console.debug('ship_type changed to: ' + ship_type);
			}
			
			jQuery.ajax({
				type: 'POST',
				url: bkf_dd_options.ajax_url,
				data: {
					'action': 'bkf_checkout_get_ajax_data',
					'ship_type': ship_type,
				},
				success: function (result) {
					jQuery('body').trigger('update_checkout');
					if (bkf_dd_options.debug) {
						console.debug(result);
					}
				},
				error: function(error){
					console.error(error);
				}
			});
			if (ship_type == "delivery") {
				jQuery("#customer_details .woocommerce-shipping-fields").fadeIn();
			} else if (ship_type == "pickup") {
				jQuery("#customer_details .woocommerce-shipping-fields").fadeOut();
			}
		}
		);
	}
	);
}