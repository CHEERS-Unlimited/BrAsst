define([
	"modernizr",
	"jquery",
	"stratum",
	"router"
], function(Modernizr, $, stratum, Router){

	$(function(){
		new Router({pushState: false});
	});
});