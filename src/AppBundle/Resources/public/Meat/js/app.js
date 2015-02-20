define([
	"modernizr",
	"jquery",
	"stratum",
	"router",
	"switcher"
], function(Modernizr, $, stratum, Router){

	$(function(){
		new Router({pushState: false});
		switcher.initialize(window.location.hash);
	});
});