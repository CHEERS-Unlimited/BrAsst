define([
	"jquery",
	"stratum",
	"views/apiInfo"
], function($, Stratum, ApiInfo){

	var getVendor = function(){
		var s = document.createElement("div"),
			v = ["Webkit", "Moz", "O", "ms"], i;

		for(i = 0; i < v.length; i++){
			if(v[i] + "Transition" in s){	
				return v[i];
			}
		}
		return "";
	}

	var View = Stratum.View.extend({
		el: $("#apiType"),
		events: {
			"mousedown .dragger": "activateDragger",
			"mousemove": "handleMove",
			"mouseup": "deactivateDragger"
		},
		apiInfo: new ApiInfo(),
		initialize: function(){
			this.circles = this.el.find(".circle");
			this.circlesData = [];
			this.dragger = this.el.find(".dragger");
			this._isActiveDragger = false;
			this._direction = null;
			this._difference = 0;

			this.ensureData();

			this.apiInfo.on("deactivate", $.proxy(function(){
				this.moveDragger(0);
				this.deactivateCircles();
			}, this));
		},
		ensureData: function(){
			this.circlesData["distance"] = this.getDifference(this.circles);
		},
		getDifference: function(el){
			return this.dragger.offset().left  - (el.offset().left + el.width());
		},
		activateDragger: function(e){
			this._isActiveDragger = true;
			this.draggerOffset = this.dragger.offset();
		},
		handleMove: function(e){
			if(!this._isActiveDragger) return;

			var xPos = e.pageX - this.draggerOffset.left - this.dragger.width() / 2;

			this._direction = this.getDirection(xPos);
			this._difference = this.checkDiff(xPos);
			
			if(this._difference >= 0){
				this.moveDragger(xPos);
			}
		},
		getDirection: function(x){
			return (x >= 0) ? "right" : "left";
		},
		checkDiff: function(x){
			return this.circlesData["distance"] - 10 - Math.abs(x);
		},
		deactivateDragger: function(e){
			if(this._difference <= 70 && this._difference !== 0){

				if(this._direction === "right"){
					this.moveDragger(this.circlesData["distance"] - 10);
					this.activateCircle("json");
					this.apiInfo.setWhich("json");
				} else{
					this.moveDragger((this.circlesData["distance"] - 10) * -1);
					this.activateCircle("widget");
					this.apiInfo.setWhich("widget");
				}
			} else{
				this.moveDragger(0);
				this.deactivateCircles();
			}
			this.clearAll();
		},
		moveDragger: function(x){
			var transform = getVendor() + "Transform";
			this.dragger.css({
				transform: "translateX("+ x +"px)"
			});
		},
		activateCircle: function(which){
			this.circles.removeClass("active").filter("." + which).addClass("active");
		},
		deactivateCircles: function(){
			this.circles.removeClass("active");
		},
		clearAll: function(){
			this._isActiveDragger = false;
			this._difference = 0;
			this._direction = 0;
		}
	});
	return View;
});