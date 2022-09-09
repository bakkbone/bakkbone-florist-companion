(function(){
	tinymce.create("tinymce.plugins.bkf_page_title", {
		init: function(editor, url) {
			editor.addButton("bkf_page_title", {
				title: "Page Title",
				icon: "icon bkf_page_title bkf-mce-icon dashicons-admin-post",
				onclick: function(){
					editor.windowManager.open({
						title: "Page Title",
						bodyType: "tabpanel",
						body: [
							{
								title: "General",
								type: "form",
								items:[
								
		
		
								
								]
							},
							{
								title: "About",
								type: "form",
								items:[
									{type: "panel",html:"<h4 style='font-size:24px;font-weight:600;'>Page Title v.1.0.1</h4><br/><p style='font-size:12px;'>Created by <a style='font-size:12px;cursor:pointer;font-weight:600;' href='https://www.bakkbone.com.au'>BAKKBONE Australia</a></p><p style='font-size:12px;cursor:pointer;font-weight:600;'>Powered by <a style='cursor:pointer;font-weight:600;' href='https://codecanyon.net/item/iwp-devtoolz/13581496'>iWP-DevToolz</a></p>"},
								]
							}
						],
						onsubmit: function(e){
							var shortcode = "";
							shortcode += "[bkf_page_title ";
							shortcode += "]";
							shortcode += "[/bkf_page_title]";
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
				longname: "BKF - Page Title",
				author: "BAKKBONE Australia",
				authorurl: "https://www.bakkbone.com.au",
				infourl: "https://www.floristwebsites.au",
				version: "1.0.1"
			};
		}
	});
	tinymce.PluginManager.add("bkf_page_title", tinymce.plugins.bkf_page_title);
})();
