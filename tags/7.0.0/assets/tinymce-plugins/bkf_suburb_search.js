(function(){
	tinymce.create("tinymce.plugins.bkf_suburb_search", {
		init: function(editor, url) {
			editor.addButton("bkf_suburb_search", {
				title: "Suburb Search",
				icon: "icon bkf_suburb_search bkf-mce-icon dashicons-search",
				onclick: function(){
					editor.windowManager.open({
						title: "Suburb Search",
						bodyType: "tabpanel",
						body: [
							{
								title: "General",
								type: "form",
								items:[
								
									{type: "textbox", name: "placeholder", label: "Placeholder", tooltip: "optional" },
									{type: "textbox", name: "noresults", label: "No Results", tooltip: "optional" },
									{type: "textbox", name: "header", label: "Results Header", tooltip: "optional" }								
								]
							}
						],
						onsubmit: function(e){
							var shortcode = "";
							shortcode += "[bkf_suburb_search";
							if(e.data.placeholder !== ""){
								shortcode +=" placeholder=\""  + e.data.placeholder + "\"" ;
							}
							if(e.data.noresults !== ""){
								shortcode +=" noresults=\""  + e.data.noresults + "\"" ;
							}
							if(e.data.header !== ""){
								shortcode +=" header=\""  + e.data.header + "\"" ;
							}
							shortcode += "]";
							editor.insertContent(shortcode);
						}
					});
				}
			});
		},
		createControl: function(n, cm) {
			return null;
		},
		getInfo: function() {
			return {
				longname: "FloristPress - Suburb Search",
				author: "BAKKBONE Australia",
				authorurl: "https://www.bakkbone.com.au/",
				infourl: "https://www.floristwebsites.com.au/",
				version: "1.0.1"
			};
		}
	});
	tinymce.PluginManager.add("bkf_suburb_search", tinymce.plugins.bkf_suburb_search);
})();