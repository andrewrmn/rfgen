
function SOPremiumFontsImporter(contextDocument) {
	var fontModules = soPremiumFonts.font_modules;

	contextDocument = contextDocument || window.document;
	this.importFonts = function () {

		// Get all the SO Premium Web Fonts we need to import.
		var toImport = {};
		contextDocument.querySelectorAll('.so-premium-web-font').forEach(function (element) {
			var fontInfo = JSON.parse( element.getAttribute('data-font-info') );
			if (fontInfo.font && fontInfo.module !== 'web_safe') {
				var fontName = fontInfo.font.replace(/\s/g, '+');

				if (!toImport.hasOwnProperty(fontInfo.module)) {
					toImport[fontInfo.module] = {fonts: {}};
				}
				var module = toImport[fontInfo.module];

				if (!module.fonts.hasOwnProperty(fontName)) {
					module.fonts[fontName] = {
						name: fontName,
						variants: [],
					};
				}
				var curFont = module.fonts[fontName];
				if (curFont.variants.indexOf(fontInfo.variant) === -1) {
					curFont.variants.push(fontInfo.variant);
				}
			}
		});

		// Build the URL for the web fonts.
		for (var moduleName in toImport) {
			var moduleInfo = fontModules[moduleName];
			var mod = toImport[moduleName];
			var fontImports = [];

			for( var fontName in mod.fonts ) {
				var fontImport = fontName;
				var font = mod.fonts[fontName];
				if (font.hasOwnProperty('variants')) {
					fontImport += ':' + font.variants.join(',');
				}
				fontImports.push(fontImport);
			}

			// Remove existing import.
			var existingImports = contextDocument.querySelectorAll('head link[rel="stylesheet"][href*="' + moduleInfo.base_url + '"]');
			if (existingImports.length) {
				existingImports.forEach(function (linkElt) {
					linkElt.parentNode.removeChild(linkElt);
				});
			}

			// Import the fonts.
			var importUrl = moduleInfo.base_url + '?family=' + fontImports.join('|');
			var importElt = contextDocument.createElement('link');
			importElt.setAttribute('rel', 'stylesheet');
			importElt.setAttribute('media', 'all');
			importElt.setAttribute('href', importUrl);
			contextDocument.head.appendChild(importElt);
		}
	};
}

window.addEventListener('DOMContentLoaded', function () {

	// To ensure the import isn't run too early for widget previews.
	setTimeout(
		function () {
			new SOPremiumFontsImporter().importFonts();
		},
		100
	);

});
