define([
	"jquery",
	"stratum"
], function($, Stratum){

	return Stratum.View.extend({
		el: $("#browsers"),
		events: {
			"click .browser": "handleBrowser",
			"click .close": "closeBrowserInfo",
			"dragstart .browser": "startBrowserDrag",
			"dragend .browser": "endBrowserDrag",
			"dragenter #dropZone": "enterDropZone",
			"dragover #dropZone": "overDropZone",
			"drop #dropZone": "dropInDropZone",
			"dragleave #dropZone": "leaveDropZone"
		},
		initialize: function(){
			
			this.browsers = this.el.find(".browser");
			this.dropZone = this.el.find("#dropZone");
			this.browsersInfo = this.dropZone.find(".browserInfo");
			this.closeButton = this.dropZone.find(".close");
			this.dragIcon = null;
			this.currentBrowser = null;
			this.currentBrowserName = "";

			this.createDragIcon();
		},
		createDragIcon: function(){
			this.dragIcon = new Image();
			this.dragIcon.src = "/web/bundles/app/Meat/images/dragIcon.png";
		},
		handleBrowser: function(e){
			this.currentBrowser = $(e.target).hasClass("browser") ? $(e.target) : $(e.target).closest(".browser");
			this.setCurrentBrowserName(this.currentBrowser.attr("class"));

			this.activateBrowser();
			this.activateClose();
			this.showBrowserInfo();
		},
		closeBrowserInfo: function(e){
			this.browsers.removeClass("active");
			this.browsersInfo.removeClass("active dragEnter").filter(".default").addClass("active");
			this.closeButton.removeClass("active");
		},
		startBrowserDrag: function(e){
			this.currentBrowser = $(e.target);
			this.setCurrentBrowserName(this.currentBrowser.attr("class"));

			e.originalEvent.dataTransfer.setData('text/html', e.target.id); //set type of data for firefox
			e.originalEvent.dataTransfer.setDragImage(this.dragIcon, 20, 20); //custom icon when dragging

			this.browsers.removeClass("dragging"); 
			this.currentBrowser.addClass("dragging");
		},
		endBrowserDrag: function(e){
			e.stopPropagation();
			this.browsers.removeClass("dragging");
		},
		enterDropZone: function(e){
			e.preventDefault();
			this.browsersInfo.filter(".default").addClass("dragEnter");
		},
		overDropZone: function(e){
			e.originalEvent.dataTransfer.dropEffect = 'move';
			this.browsersInfo.filter(".default").addClass("dragEnter");
			return false;
		},
		dropInDropZone: function(e){
			e.preventDefault();
			
			this.activateBrowser();
			this.activateClose();

			this.setCurrentBrowserName(this.currentBrowser.attr("class"));
			this.showBrowserInfo();
		},
		leaveDropZone: function(e){
			e.preventDefault();
			this.browsersInfo.filter(".default").removeClass("dragEnter");
		},
		setCurrentBrowserName: function(classNames){
			var match = classNames.match(/(chrome|internet_explorer|firefox|safari|opera)/gi);
			if(match) this.currentBrowserName = match[0];
		},
		activateBrowser: function(){
			this.browsers.removeClass("active");
			this.currentBrowser.addClass("active");
		},
		activateClose: function(){
			this.closeButton.addClass("active");
		},
		showBrowserInfo: function(){
			this.browsersInfo.removeClass("active").filter("." + this.currentBrowserName).addClass("active");
		}
	});
});