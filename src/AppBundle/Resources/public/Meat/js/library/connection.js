;(function(root, factory){

	if(typeof define === 'function' && define.amd){
		define(['jquery'], factory);
	} else{
		factory(root.jQuery, root);
	}

}(this, function($){

	var pluginName = "connection",
		defaults = {
			particle: {
				radius: 3,
				line: {
					enabled: true,
					color: "#5ad3e1",
					thickness: 1
				},
				shadow: {
					enabled: true,
					color: "red",
					blur: 20,
					offsetX: 0,
					offsetY: 0
				},
				fill: {
					enabled: true,
					color: "black",
					opacity: 1
				},
				scaleX: 1,
				scaleY: 1,
				velocityX: 0.001,
				velocityY: 0.001
			},
			lineBetweenParticles: {
				color: "#990000",
				thickness: 1
			},
			particlesCount: 150,
			minDistance: 100
		};

	function Connection(element, options){
		
		this.el = element;
		this.$el = $(element);

		this.options = $.extend({}, defaults, options);

		this._pluginName = pluginName;
		this._defaults = defaults;
		
		this.initialize();
	}
	Connection.prototype = {
		initialize: function(){

			this.context = this.el.getContext("2d");
			this.particles = [];
			this.particlesCount = this.options.particlesCount,
			this.minDistance = this.options.minDistance,
			this.springFactor = 0.0001;

			this.dimension = this.getDimension();
			this.setDimension();

			$(window).on("resize", $.proxy(function(e){
				this.dimension = this.getDimension();
				this.setDimension();
			}, this));

			this.createParticles();
			this.render();	
		},
		getDimension: function(){
			return {
				w: window.innerWidth,
				h: window.innerHeight
			}
		},
		setDimension: function(){
			this.el.width = this.dimension.w;
			this.el.height = this.dimension.h;
		},
		createParticles: function(){
			var key;

			for(key = 0; key < this.particlesCount; key++){

				this.particles[key] = new Particle(this.options.particle);
				this.particles[key].x = Math.random() * this.el.width;
				this.particles[key].y = Math.random() * this.el.height;
			}
		},
		spring: function(particleA, particleB){
			var dx, dy, ax, ay, alpha, distance;
			
			dx = particleB.x - particleA.x;
			dy = particleB.y - particleA.y;
			distance = Math.sqrt(dx * dx + dy * dy);

			if(distance < this.minDistance){

				alpha = 1 - distance / this.minDistance;
				this.context.strokeStyle = hexToRgb(this.options.lineBetweenParticles.color, alpha);
				this.context.lineWidth = this.options.lineBetweenParticles.thickness || 1;
				this.context.beginPath();

				this.context.moveTo(particleA.x, particleA.y);
				this.context.lineTo(particleB.x, particleB.y);
				this.context.stroke();

				ax = dx * this.springFactor;
				ay = dy * this.springFactor;

				particleA.vx += ax / particleA.mass;
				particleA.vy += ay / particleA.mass;
				particleB.vx -= ax / particleB.mass;
				particleB.vy -= ay / particleB.mass;
			}
		},
		motion: function(particleA, i){

			particleA.x += particleA.vx;
			particleA.y += particleA.vy;

			if(particleA.x > this.el.width){
				particleA.x = 0;
			} else if(particleA.x < 0){
				particleA.x = this.el.width;
			}

			if(particleA.y > this.el.height){
				particleA.y = 0;
			} else if(particleA.y < 0){
				particleA.y = this.el.height;
			}

			for(var j = i + 1; j < this.particlesCount; j++){
				this.spring(particleA, this.particles[j]);
			}
		},
		draw: function(particle){
			particle.draw(this.context);
		},
		render: function(){
			window.requestAnimationFrame($.proxy(this.render, this));
			this.context.clearRect(0, 0, this.el.width, this.el.height);

			this.particles.forEach($.proxy(this.motion, this));
			this.particles.forEach($.proxy(this.draw, this));
		}
	}

	function Particle(options){
		this.x = 0;
		this.y = 0;
		this.radius = options.radius || 3;

		this.rotation = 0;
		this.scaleX = options.scaleX || 1;
		this.scaleY = options.scaleY || 1;

		this.isLine = options.line.enabled || true;
		this.lineColor = options.line.color || "#fff";
		this.lineWidth = options.line.thickness || 0;

		this.isFill = options.fill.enabled || false;
		this.fillColor = hexToRgb(options.fill.color, options.fill.opacity) || hexToRgb("#333", 1);

		this.isShadow = options.shadow.enabled || false;
		this.shadowColor = options.shadow.color || "#222";
		this.shadowBlur = options.shadow.blur || 10;
		this.shadowX = options.shadow.offsetX || 0;
		this.shadowY = options.shadow.offsetY || 0;

		this.vx = options.velocityX || 0.001;
		this.vy = options.velocityY || 0.001;
		this.mass = options.radius || this.radius;
	}
	Particle.prototype = {
		draw: function(context){
			context.save();

			context.translate(this.x, this.y);
			context.rotate(this.rotation);
			context.scale(this.scaleX, this.scaleY);

			context.lineWidth = this.lineWidth;
			context.fillStyle = this.fillColor;

			context.beginPath();
			context.arc(0, 0, this.radius, 0, (Math.PI * 2), true);
			context.closePath();

			if(this.isLine){
				context.strokeStyle = this.lineColor;
				context.stroke();
			}
			if(this.isShadow && this.isFill){
				context.shadowColor = this.shadowColor;
				context.shadowBlur = this.shadowBlur;
				context.shadowOffsetX = this.shadowX;
				context.shadowOffsetY = this.shadowY;
			}
			if(this.isFill){
				context.fill();
			}
			context.restore();
		}
	}

	window.requestAnimationFrame = (function(){

		if(!window.requestAnimationFrame){
			requestAnimationFrame = window.webkitRequestAnimationFrame || 
										window.mozRequestAnimationFrame || 
										window.oRequestAnimationFrame ||
										window.msRequestAnimationFrame || 
										function(callback){
											return setTimeout(callback, 1000/60);
										}
		}
		return requestAnimationFrame;
	})();

	function hexToRgb(hex, alpha){
		var result;

		hex = hex.replace(/^#?([a-f\d])([a-f\d])([a-f\d])$/i, function(m, r, g, b){
			return r + r + g + g + b + b;
		});

		result = hex.match(/^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i);
		
		if(alpha >= 1){
			return "rgb(" +parseInt(result[1], 16) +","+ +parseInt(result[2], 16) +","+ +parseInt(result[3], 16) +")";
		} else{
			return "rgba("+ parseInt(result[1], 16) +","+ parseInt(result[2], 16) +","+ parseInt(result[3], 16) +","+ alpha +")"
		}
	}

	$.fn[pluginName] = function(options){

		return this.each(function(){
			if(!$.data(this, pluginName)){
				$.data(this, pluginName, new Connection(this, options));
			}
		});
	}

}));