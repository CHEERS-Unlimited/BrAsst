define([
	"jquery"
], function($){

	return switcher = {
		initialize: function(href){
			var page = $("#page"),
				menu = $("#menu"),
				target;

			this.sections = $(".section");

			page.on("click", "a.toPage", $.proxy(this.clickHandler, this));

			if(!href) href="#!main";
			page.find("[href='"+ href +"']").parent().addClass("active");
			this.switchPage(this.getID(href));
		},
		clickHandler: function(e){
			target = $(e.target);

			$(menu).find("[href='"+ target.attr("href") +"']").parent().addClass("active").siblings("li").removeClass("active");
			this.switchPage(this.getID(target.attr("href")));
		},
		getID: function(href){
			return href.replace(/\!/, "");
		},
		switchPage: function(id){
			if(!id) return;
			this.sections.removeClass("active").filter(id).addClass("active");
		}
	}
});