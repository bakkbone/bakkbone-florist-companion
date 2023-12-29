(function(){
	tinymce.create("tinymce.plugins.bkf_site_title", {
		init: function(editor, url) {
			editor.addButton("bkf_site_title", {
				title: "Site Title",
				icon: "icon bkf_site_title bkf-mce-icon dashicons-admin-site",
				onclick: function(){
					var shortcode = "[bkf_site_title]";
					editor.insertContent(shortcode);
				}
			});
		},
		createControl: function(n, cm) {
			return null;
		},
		getInfo: function() {
			return {
				longname: "FloristPress - Site Title",
				author: "BAKKBONE Australia",
				authorurl: "https://www.bakkbone.com.au/",
				infourl: "https://www.floristwebsites.com.au/",
				version: "1.0.1"
			};
		}
	});
	tinymce.PluginManager.add("bkf_site_title", tinymce.plugins.bkf_site_title);
})();
