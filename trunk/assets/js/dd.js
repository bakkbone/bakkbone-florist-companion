// Date

jQuery(document).ready(function( $ ) {
	var ele = document.getElementsByName('shipping_method[0]');
	for(i = 0; i < ele.length; i++) {
		if(ele[i].checked)
			var currentShippingMethod = ele[i].value;
	}
	if (currentShippingMethod == null) {
		var currentShippingMethod = ele[0].value;
	}
	jQuery.ajax(thisAjaxUrl, {
		async: false,
		data: {
			action: 'bkf_dd',
			method: currentShippingMethod
		},
		method: 'POST',
		success: function(result){
			datesList = JSON.parse(result);
		}
	});
	
	jQuery("#delivery_date").attr( 'readOnly' , 'true' );
	jQuery(".delivery_date").datepicker( {
		minDate: 0,
		maxDate: maxDate,
		dateFormat: "DD, d MM yy",
		hideIfNoPrevNext: true,
		firstDay: 1,
		constrainInput: true,
		beforeShowDay: blockedDates,
		onUpdateDatepicker: newTitles,
		showButtonPanel: true,
		showOtherMonths: true,
		selectOtherMonths: true,
		changeMonth: true,
		changeYear: true,
	} );
	function blockedDates(date) {
		var d = date.getDate();
		var m = date.getMonth() + 1;
		var y = date.getFullYear();
		var theIndex = d + '.' + m + '.' + y;
		result = datesList[theIndex];
		if (result == null) {
			result = [true];
		}
		return result;
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
	if (currentShippingMethod == null) {
		var currentShippingMethod = ele[0].value;
	}
	jQuery.ajax(thisAjaxUrl, {
		async: false,
		data: {
			action: 'bkf_dd',
			method: currentShippingMethod
		},
		method: 'POST',
		success: function(result){
			datesList = JSON.parse(result);
		}
	});
	
	jQuery(".delivery_date").datepicker( "option", {
		beforeShowDay: blockedDates2
	} );
	function blockedDates2(date) {
		var d = date.getDate();
		var m = date.getMonth() + 1;
		var y = date.getFullYear();
		var theIndex = d + '.' + m + '.' + y;
		result = datesList[theIndex];
		if (result == null) {
			result = [true];
		}
		return result;
	}
} );

// Timeslot

jQuery(document).on( 'change', 'input.shipping_method, input.delivery_date', function($) {
		const select = document.querySelector('#delivery_timeslot');
		jQuery(select).empty($);
		if(all_options.length == 0) {
			const wrapper = document.querySelector('#delivery_timeslot_field');
			jQuery(wrapper).addClass('bkf-hidden');
			document.querySelector('#delivery_timeslot').removeAttribute('required');
		} else {
			all_options.forEach(newOption);
		}
	  
	  function newOption(value)
		{
			const wrapper = document.querySelector('#delivery_timeslot_field');
			var ele = document.getElementsByName('shipping_method[0]');
			for(i = 0; i < ele.length; i++) {
				if(ele[i].checked)
					var currentShippingMethod = ele[i].value;
			}
			if (currentShippingMethod == null) {
				var currentShippingMethod = ele[0].value;
			}
			jQuery.ajax(thisAjaxUrl, {
				async: false,
				data: {
					action: 'bkf_retrieve_session_ts',
				},
				method: 'POST',
				success: function(result){
					currentTs = JSON.parse(result);
				}
			});
			var days = ["sunday", "monday", "tuesday", "wednesday", "thursday", "friday", "saturday"];
			var aDate = document.querySelector('#delivery_date').value;
			var theDate = Date.parse(aDate);
			var theDateObject = new Date(theDate);
			var delDay = days[theDateObject.getDay()];
			if( value.day == delDay && value.method == currentShippingMethod ){
				if(currentTs == value.slot){
					const select = document.querySelector('#delivery_timeslot');
					let newOption = new Option(value.text, value.slot, true, true);
					select.add(newOption, undefined);
				} else {
					const select = document.querySelector('#delivery_timeslot');
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
	  jQuery(select).empty($);
	  if(all_options.length == 0) {
		  const wrapper = document.querySelector('#delivery_timeslot_field');
		  jQuery(wrapper).addClass('bkf-hidden');
		  document.querySelector('#delivery_timeslot').removeAttribute('required');
	  } else {
		  all_options.forEach(newOption);
	  }
	  
	  function newOption(value)
		{
			const wrapper = document.querySelector('#delivery_timeslot_field');
			var ele = document.getElementsByName('shipping_method[0]');
			for(i = 0; i < ele.length; i++) {
				if(ele[i].checked)
					var currentShippingMethod = ele[i].value;
			}
			if (currentShippingMethod == null) {
				var currentShippingMethod = ele[0].value;
			}
			jQuery.ajax(thisAjaxUrl, {
				async: false,
				data: {
					action: 'bkf_retrieve_session_ts',
				},
				method: 'POST',
				success: function(result){
					currentTs = JSON.parse(result);
				}
			});
			var days = ["sunday", "monday", "tuesday", "wednesday", "thursday", "friday", "saturday"];
			var aDate = document.querySelector('#delivery_date').value;
			var theDate = Date.parse(aDate);
			var theDateObject = new Date(theDate);
			var delDay = days[theDateObject.getDay()];
			if( value.day == delDay && value.method == currentShippingMethod ){
				if(currentTs == value.slot){
					const select = document.querySelector('#delivery_timeslot');
					let newOption = new Option(value.text, value.slot, true, true);
					select.add(newOption, undefined);
				} else {
					const select = document.querySelector('#delivery_timeslot');
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