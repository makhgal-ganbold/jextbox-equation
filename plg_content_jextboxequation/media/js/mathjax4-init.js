document.addEventListener("DOMContentLoaded", function() {
	window.MathJax = {
		tex: {
			tags: "ams",
			autoload: {
				color: [],
				colorv2: ["color"]
			},
			packages: {"[+]": ["noerrors"]}
		},
		options: {
			enableTabOrder: false,
			enableMenu: false,
			renderActions: {
				addMenu: []
			},
			ignoreHtmlClass: "tex2jax_ignore",
			processHtmlClass: "tex2jax_process"
		},
		loader: {
			load: ["input/asciimath", "[tex]/noerrors"]
		},
		startup: {
			pageReady() {
				return MathJax.startup.defaultPageReady().then(function () {
					document.querySelectorAll("mjx-container").forEach(function(node) {
						node.setAttribute("tabindex", "-1");
						node.style.outline = "none";
						node.style.pointerEvents = "none";
					});
				});
			}
		}
	};
});
