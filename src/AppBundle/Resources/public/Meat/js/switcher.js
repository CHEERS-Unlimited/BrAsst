define([
	"jquery"
], function($){

	return switcher = {
		initialize: function(href){

			var menu = $("#menu"),
				id = "";

			this.items = menu.find("li");
			this.sections = $(".section");

			menu.on("click", "a", $.proxy(function(e){
				id = this.getID($(e.target).attr("href"));

				this.switchClasses(id);
			}, this));

			if(href) id = this.getID(href);
			this.switchClasses(id);
		},
		getID: function(href){
			return href.replace(/\!/, "");
		},
		switchClasses: function(id){
			var	num = id ? $(id).index() - 1 : 0;
			id = id || "#main";

			this.sections.removeClass("active").filter(id).addClass("active");
			if(num >= 0){
				this.items.removeClass("active").eq(num).addClass("active");
			}
		}
	}
});