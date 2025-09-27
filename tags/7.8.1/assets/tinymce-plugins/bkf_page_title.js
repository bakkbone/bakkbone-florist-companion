(function(){
	tinymce.create("tinymce.plugins.bkf_page_title", {
		init: function(editor, url) {
			editor.addButton("bkf_page_title", {
				title: "Page Title",
				icon: "icon bkf_page_title bkf-mce-icon dashicons-admin-post",
				onclick: function(){
					var shortcode = "[bkf_page_title]";
					editor.insertContent(shortcode);
				}
			});
		},
		createControl: function(n, cm) {
			return null;
		},
		getInfo: function() {
			return {
				longname: "FloristPress - Page Title",
				author: "BAKKBONE Australia",
				authorurl: "https://www.bakkbone.com.au/",
				infourl: "https://www.floristwebsites.com.au/",
				version: "1.0.1"
			};
		}
	});
	tinymce.PluginManager.add("bkf_page_title", tinymce.plugins.bkf_page_title);
})();
