define([
	"jquery"
], function($){

	return switcher = {
		initialize: function(hash){

			var id;
			this.$menu = $("#menu");
			this.$sections = $(".section");

			this.$menu.on("click", "a", $.proxy(function(e){
				
				id = this.getID($(e.target).attr("href"));
				this.switchClasses(id);
			}, this));

			if(!hash) return;
			
			id = this.getID(hash);
			this.switchClasses(id);
		},
		getID: function(hash){
			return hash.replace(/\!/, "");
		},
		switchClasses: function(id){
			this.$sections.removeClass("active").filter($(id)).addClass("active");
		}
	}
});