(function(){
	tinymce.create("tinymce.plugins.bkf_site_title", {
		init: function(editor, url) {
			editor.addButton("bkf_site_title", {
				title: "Site Title",
				icon: "icon bkf_site_title bkf-mce-icon dashicons-admin-site",
				onclick: function(){
					editor.windowManager.open({
						title: "Site Title",
						bodyType: "tabpanel",
						body: [
							{
								title: "About",
								type: "form",
								items:[
									{type: "panel",html:"<h4 style='font-size:24px;font-weight:600;'>Site Title</h4><br/><p style='font-size:12px;'>Created by <a style='font-size:12px;cursor:pointer;font-weight:600;' href='https://www.bakkbone.com.au' target='_blank'>BAKKBONE Australia</a></p>"},
								]
							}
						],
						onsubmit: function(e){
							var shortcode = "";
							shortcode += "[bkf_site_title /]";
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
				longname: "BKF - Site Title",
				author: "BAKKBONE Australia",
				authorurl: "https://www.bakkbone.com.au/",
				infourl: "https://www.floristwebsites.au/",
				version: "1.0.1"
			};
		}
	});
	tinymce.PluginManager.add("bkf_site_title", tinymce.plugins.bkf_site_title);
})();
