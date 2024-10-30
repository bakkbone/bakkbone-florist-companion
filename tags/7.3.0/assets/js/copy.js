jQuery('#bkfCopyBtn').on("click", function($){
	var copyText = document.getElementById("bkfCopy");
	copyText.select();
	copyText.setSelectionRange(0, 99999);
	navigator.clipboard.writeText(copyText.value);
	
	var tooltip = document.getElementById("bkfpTooltip");
	tooltip.innerHTML = copied;
});

jQuery('#bkfCopyBtn').on("mouseout", function(){
	var tooltip = document.getElementById("bkfpTooltip");
	tooltip.innerHTML = toCopy;
});

window.addEventListener(
	'keyup',
	(event) => {
		if (event.code == 'Escape' && jQuery('#bkf_ordersearch_results').is(':visible')) {
			jQuery('#bkf_adminbar_ordersearch').empty();
			jQuery('#bkf_ordersearch_results').empty();
			jQuery('#bkf_ordersearch_results').hide();
		}
	}
);

let bkfTimeoutId = 0;

function bkfOrderSearch(event, id) {
	const ele = jQuery('#bkf_ordersearch_results');
	const wrap = jQuery('#wpwrap');
	searchString = jQuery('#bkf_adminbar_ordersearch').val();
	const currentProcess = id;
	if (searchString.length) {
		jQuery(ele).hide();
		jQuery(ele).empty();
		jQuery(wrap).block({
			css: {
				top: '20vh',
				padding: '20px'
			},
			message: copyVars.processingText,
			centerY: false
		});
		jQuery.ajax({
			url: ajaxurl,
			data: {
				action: 'bkf_order_search',
				input: searchString
			},
			success: function(result){
				if ( bkfTimeoutId > currentProcess) {
					return;
				}
				var item = JSON.parse(result);
				var results = Object.entries(item);
				results.sort(function(a, b){
					let compResult;
					if (a.created < b.created) {
						compResult = -1;
					} else if (a.created == b.created) {
						compResult = 0;
					} else if (a.created > b.created) {
						compResult = 1;
					}
					
					return compResult;
				});
				results.reverse();
				if (results.length) {
					for (const [key, value] of results) {
						var id = value.id;
						var dd = value.dd;
						var pickup = value.pickup;
						var physical = value.physical;
						var delsuburb = value.delsuburb;
						var recipient = value.recipient;
						var sender = value.sender;
						var editlink = value.editlink;
						var wslink = value.wslink;
						
						var resultHtml = '<div class="bkf_ordersearch_result" id="bkf_ordersearch_result_' + id + '"><h2>' + copyVars.orderTitle.replace('%s', id) + '</h2>';
						if (physical && pickup) {
							resultHtml += '<p>' + copyVars.pickupText.replace('%s', dd) + '</p><p class="bkf_links"><a href="' + wslink + '">' + copyVars.wsText + '</a>&nbsp;|&nbsp;<a href="' + editlink + '">' + copyVars.editText + '</a></p>';
						} else if (physical) {
							resultHtml += '<p>' + copyVars.delText.replace('%s', dd) + '<br>' + copyVars.recText.replace('%s', recipient) + '<br>' + copyVars.subText.replace('%s', delsuburb) + '</p><p class="bkf_links"><a href="' + wslink + '">' + copyVars.wsText + '</a>&nbsp;|&nbsp;<a href="' + editlink + '">' + copyVars.editText + '</a></p>';
						} else {
							resultHtml += '<p>' + copyVars.cusText.replace('%s', sender) + '</p><p class="bkf_links"><a href="' + editlink + '">' + copyVars.editText + '</a></p>';
						}
						resultHtml += '</div>';
						jQuery(ele).append(resultHtml);
					}
					jQuery(ele).show();
					jQuery(wrap).unblock();
				} else {
					var noResultsHtml = '<div id="bkf_ordersearch_noresults"><p>' + copyVars.noResults.replace('%s', searchString) + '</p></div>';
					jQuery(ele).append(noResultsHtml);
					jQuery(ele).show();
					jQuery(wrap).unblock();
				}
			}
		});
	} else {
		jQuery(ele).empty();
		jQuery(ele).hide();
		jQuery(wrap).unblock();
	}
}

jQuery('#bkf_adminbar_ordersearch').on('input click focus change', function(event) {
	bkfTimeoutId++;
	currentId = setTimeout(bkfOrderSearch, 0, event, bkfTimeoutId);
});