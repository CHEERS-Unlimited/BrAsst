define([
	"jquery",
	"stratum",
	"library/connection",
	"social",
	"views/browser",
	"views/api"
], function($, Stratum, connection, social, Browser, Api){

	return Stratum.Router.extend({
		routes: {
			"!browsers": "browsers",
			"!api": "api",
			"*default": "_default"
		},
		browsers: function(){
			new Browser();
		},
		api: function(){
			new Api();
		},
		_default: function(){
			$("#connection").connection({
				particle: {
					radius: 1.5,
					line: {
						enabled: false,
						color: "#dee1b6",
						thickness: 1
					},
					shadow: {
						enabled: false,
						color: "#000",
						blur: 20,
						offsetX: 0,
						offsetY: 0
					},
					fill: {
						enabled: true,
						color: "#373B44",
						opacity: 1
					},
					velocityX: 0.001,
					velocityY: 0.001
				},
				lineBetweenParticles: {
					color: "#373B44",
					thickness: 0.7
				},
				particlesCount: 120,
				minDistance: 75
			});
		}
	});
});