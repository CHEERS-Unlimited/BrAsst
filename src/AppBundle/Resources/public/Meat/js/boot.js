require.config({

	baseUrl: "/web/bundles/app/Meat/js",
	paths: {
		"jquery": "//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min",
		"jquery-ui": "//cdnjs.cloudflare.com/ajax/libs/jqueryui/1.11.2/jquery-ui.min",
		"modernizr": "//cdnjs.cloudflare.com/ajax/libs/modernizr/2.8.3/modernizr.min",
		"stratum": 	"library/stratum",
		"social": "//cdnjs.cloudflare.com/ajax/libs/social-likes/3.0.12/social-likes.min"
	}
});

requirejs(["app"]);
