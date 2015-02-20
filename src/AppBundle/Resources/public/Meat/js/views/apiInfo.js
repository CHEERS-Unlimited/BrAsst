define([
	"jquery",
	"stratum"
], function($, Stratum){

	var ESC_CODE = 27;

	return Stratum.View.extend({
		el: $("#api"),
		events: {
			"click .close": "handleClose"
		},
		initialize: function(){

			this.infoOverlays = $(".apInfoOverlay");
			this.close = this.infoOverlays.find(".close");
			this._which = "";

			this.on("which", $.proxy(function(){
				this.activateInfo();
			}, this));

			//external events which doesnt binding to this.el
			$(document).on("keyup", $.proxy(this.escClose, this));
		},
		handleClose: function(e){
			if(!this._which) return;
			this.deactivateInfo();
		},
		escClose: function(e){
			if(e.which !== ESC_CODE || !this._which) return;
			this.deactivateInfo();
		},
		setWhich: function(which){
			this._which = which;
			this.trigger("which");
		},
		activateInfo: function(){
			this.infoOverlays.removeClass("active").filter("." + this._which).addClass("active");
		},
		deactivateInfo: function(){
			this.infoOverlays.removeClass("active");
			this.trigger("deactivate");
		}
	});
});