;(function(root, factory){

	if(typeof define === "function" && define.amd){
		define(["jquery", "exports"], function($, exports){ root.Stratum = factory(root, exports, $); });
	} else{
		root.Stratum = factory(root, {}, (root.jQuery || root.$));
	}

}(this, function(root, Stratum, $){

	Stratum.version = "1.0.0";
	Stratum.$ = $;

	var Events = Stratum.Events = {
		topics: {},
		on: function(topic, handler){
			
			if(!this.topics[topic]) this.topics[topic] = {};
			this.topics[topic] = {handler: handler};
		},
		off: function(topic){
			if(!this.topics[topic]) return;
			delete this.topics[topic];
		},
		trigger: function(topic, args){
			if(!this.topics[topic] || this.topics[topic].length < 1) return;

			if(args){
				args = Array.prototype.slice.call(args, 0);
			}	
			this.topics[topic].handler.apply(this, args);
		},
		clearAll: function(){
			this.topics = {};
		}
	}

	function isRegExp(obj){
		return obj.constructor === RegExp;
	}
	function isArray(obj){
		return obj.constructor === Array;
	}
	function isFunction(obj){
		return typeof obj === "function";
	}
	function isEmptyString(str){
		return str.length <= 0;
	}
	function trimSlashs(str){
		return str.toString().replace(/^\//, "").replace(/\/$/, "");
	}

	var namedParam = /\:\w+/g,
		optionalParam = /\(\/?\:\w+\)/,
		splatParam = /\*\w+/;

	var Router = Stratum.Router = function(options){
		options = options || {};

		this._routes = [];
		this.root = options.root || "/";

		this.initialize.apply(this, arguments);
		this.start(options);
	}

	Router.triggered = false;

	$.extend(Router.prototype, Events, {
		_interval: 50,
		initialize: function(){},
		start: function(options){
			this.hasPushState = !!(window.history && history.pushState);

			this.wantsPushState = !!(options.pushState && options.pushState === true);
			this.wantsHashChange = !!(!options.pushState || options.pushState === false);

			this.fragment = this.getFragment();

			var _urlCheckerID = setInterval($.proxy(this.checkUrl, this), this._interval);

			this.bindRoutes();
		},
		getFragment: function(){
			var fragment;

			if(this.wantsPushState){
				fragment = decodeURI(window.location.pathname + window.location.search);
				fragment = fragment;
			} else if(this.wantsHashChange){
				fragment = window.location.hash.match(/^\#?(.*)/);
				if(fragment !== null) fragment = fragment[1];
			}
			return trimSlashs(fragment || this.root);
		},
		checkUrl: function(){
			var fragment = this.fragment, matches, key;

			if(!Router.triggered || fragment !== this.getFragment()){
				fragment = this.fragment = this.getFragment();

				for(key in this._routes){
					matches = fragment.match(this._routes[key].regex);
					if(matches !== null){
						this._routes[key].callback.apply(this, matches[0].split("/"));					
						Router.triggered = true;
					}
				}
			}	
			return this;
		},
		bindRoutes: function(){
			if(!this.routes) throw new Error("Routes doesn't exist, set them up");
			var regex, key;

			for(key in this.routes){
				if(!isRegExp(key)) regex = this.regexify(trimSlashs(key));
				
				if(isFunction(this[this.routes[key]])){
					this._routes.push({
						url: key,
						regex: regex,
						callback: this[this.routes[key]]
					});
				}
			}
			return this;
		},
		regexify: function(route){
			route = route.replace(optionalParam, "\\/?(\\w+)?")
						   .replace(namedParam, "(\\w+)\/?")
						   .replace(splatParam, "(.*)")
						   .replace("/", "\\/");

			return new RegExp("^\\/?" + route + "$", "g");
		},
		navigate: function(route){
			route = route ? trimSlashs(route) : "";

			if(this.hasPushState && this.wantsPushState && window.history){
				window.history.pushState({}, null, route);
			} else if(this.wantsHashChange){
				window.location.href = window.location.href.replace(/#(.*)$/, "") + "#" + route;
			}
		}
	});

	var View = Stratum.View = function(options){
		options || (options = {});

		this.el = this.el ? this.el : options.el;
		this.tagName = this.tagName ? this.tagName : options.tagName;

		this.idName = this.idName ? this.idName : options.idName;
		this.className = this.className ? this.className : options.className;

		this._ensureElements();
		this.initialize.apply(this, arguments);
		this.bindEvents();
	}

	var eventSplitter = /\s+/g;

	$.extend(View.prototype, Events, {
		initialize: function(){},
		render: function(){ return this; },
		bindEvents: function(){
			if(!this.events) return;
			var matches, eventName, selector, callback, key;

			for(key in this.events){
				if(!isFunction(this[this.events[key]])) throw new Error("event callback " + this.events[key] + " doesn't exist");
				
				matches = key.split(eventSplitter);
				eventName = matches[0],
				selector = matches[1];
				callback = this[this.events[key]];

				selector ? this.$el.on(eventName, selector, $.proxy(callback, this)) : this.$el.on(eventName, $.proxy(callback, this));
			}
			return this;
		},
		$: function(selector){
			return this.$el.find(selector);
		},
		_ensureElements: function(){
			if(!this.el) this.el = "body";
			this.tagName = this.tagName ? "<" + this.tagName + ">" : "<div>";

			this.setElement();
		},
		setElement: function(){
			this.$el = this.el instanceof Stratum.$ ? this.el : Stratum.$(this.el);
			this.$tagName = Stratum.$(this.tagName, {id: this.idName, class: this.className});

			return this;
		}
	});
	
	var Model = Stratum.Model = function(attrs){
		attrs || (attrs = {});

		this._records = this._records ? this._records : attrs ? attrs : {};
		this.initialize.apply(this, arguments);
	}
	$.extend(Model.prototype, Events, {
		initialize: function(){}
	});

	var extend = function(proto, stat){
		var parent = this, _s;

		if(proto && proto.hasOwnProperty("constructor")){
			_s = proto.constructor;
		} else{
			_s = function(){ return parent.apply(this, arguments); }
		}
		_s = $.extend(_s, parent, stat);

		var Surrogate = function(){ this.constructor = _s; }
		Surrogate.prototype = parent.prototype;

		_s.prototype = new Surrogate;
		_s.__super__ = parent.prototype;

		if(proto){ _s.prototype = $.extend(_s.prototype, proto); }

		return _s;
	}


	Stratum.extend = Router.extend = View.extend = Model.extend = extend;
	
	return Stratum;
}));


