(function($) {

	var module = {};
	var loadedFonts = {};

	$.fn.webfontselector = function() {
		// Convert args to real Array
		var args = Array.prototype.slice.call(arguments);
		if (typeof args[0] === 'string') {
			var methodName = args[0];
			if (module.hasOwnProperty(methodName) && typeof module[methodName] === 'function') {
				if (args.length > 1) {
					module[methodName].apply(this, args.slice(1));
				} else {
					module[methodName].apply(this);
				}
			}

		} else if (typeof args[0] === 'object') {
			module.init.apply(this, args);
		} else {
			console.log('No default options yet! Please call webfontselector with a method name or an options object.')
		}

	};

	module.init = function(options) {
		options = module.setDefaultOptions( options );
		return this.each(
			function() {

				var control = this;

				var $f = $(control);
				var $container = $f.parent();

				// Chosen will revert to a standard select on mobile devices
				// How the placeholder is handled changes when that happens.
				if ( ! window.matchMedia( '(max-width: 468px)' ).matches ) {	
					// Placeholder is handled elsewhere.
					$f.append('<option value="" data-webfont="false"></option>');
				} else {
					// Add placeholder as first item
					$f.append('<option value="" data-webfont="false" disabled>' + soPremiumFonts.placeholder_text + '</option>');
				}
				
				if ( options.useInherit ) {
					// Inherit option
					$f.append('<option value="inherit" data-webfont="false">Inherit</option>');
				}
				
				if ( options.useVariants ) {
					// Add variant dropdown
					$container.append(
						'<div class="field-wrapper">' +
						'<label>Variant</label>' +
						'<select class="font-variant"></select>' +
						'</div>'
					);
					var $v = $container.find('select.font-variant');
					$v.parent().hide();
				}

				if ( options.useSubsets ) {
					// Add subset dropdown
					$container.append(
						'<div class="field-wrapper">' +
						'<label>Subset</label>' +
						'<select class="font-subset"></select>' +
						'</div>'
					);
					var $s = $container.find('select.font-subset');
					$s.parent().hide();
				}
				
				var hasSetup = false;

				// Populate with font family options
				for (var moduleName in options.modules ) {
					var module = options.modules[moduleName];
					var label = module.hasOwnProperty( 'label' ) ? module.label : moduleName;
					var $grp = $('<optgroup label="' + label + '"></optgroup>');
					$grp.data( 'module', moduleName );
					$f.append($grp);
					for (var familyName in module.fonts) {
						var family = module.fonts[familyName];
						var $opt = $('<option>' + familyName + '</option>');
						$opt.val(familyName);
						if ( family.hasOwnProperty('variants') && family.variants.length ) {
							$opt.data('variants', family.variants.join(','));
						}
						if (family.hasOwnProperty('subsets') && family.subsets.length) {
							$opt.data('subsets', family.subsets.join(','));
						}
						$opt.data('category', family.category);
						$opt.data('webfont', (moduleName !== 'web_safe'));
						$opt.attr('style', "font-family: '" + familyName + "', " + family.category + ", __" + moduleName);
						$grp.append($opt);
					}
				}

				$f.on( 'change', function() {
					var $fs = $(this).find('option:selected');
					
					if ( options.useVariants ) {
						$v.empty().val( '' );
						if ( $fs.data( 'variants' ) !== undefined ) {
							// Lets populate the variants and subsets
							var variants = $fs.data( 'variants' ).split( ',' );
							$v.append( $( "<option></option>" ) ).val( '' );
							$.each( variants, function ( i, v ) {
								$v.append( $( "<option></option>" ).html( v ) );
							} );
							
							// If there's no regular or 400 variant, just select the first one.
							if ( variants.indexOf( 'regular' ) > -1 ) {
								$v.val( 'regular' );
							} else if ( variants.indexOf( '400' ) > -1 ) {
								$v.val( '400' );
							} else {
								$v.val( variants[ 0 ] );
							}
							
							if ( $v.find( 'option' ).length > 2 ) {
								$v.parent().show();
							} else {
								$v.parent().hide();
							}
						} else {
							$v.parent().hide();
						}
					}

					if ( options.useSubsets ) {
						$s.empty().val( '' );
						if ( $fs.data( 'subsets' ) !== undefined ) {
							// Lets populate the variants and subsets
							$s.append( $( "<option></option>" ) );
							$.each( $fs.data( 'subsets' ).split( ',' ), function ( i, v ) {
								$s.append( $( "<option></option>" ).html( v ) );
							} );
							$s.val( 'latin' );
							
							if ( $s.find( 'option' ).length > 2 ) {
								$s.parent().show();
							} else {
								$s.parent().hide();
							}
						} else {
							$s.parent().hide();
						}
					}
				});

				var changeValue = function(event, args) {
					if (!hasSetup) {
						return;
					}
					var val = {};
					val.font = $f.val();
					var $selectedOption = $f.find('option:selected');
					val.webfont = $selectedOption.data('webfont');
					val.category = $selectedOption.data('category');
					if ( options.useVariants ) {
						val.variant = $v.val();
					}
					if ( options.useSubsets ) {
						val.subset = $s.val();
					}
					
					val.module = $selectedOption.parent().data( 'module' );
					// Sets the style on the selected item in chosen.
					var style = $selectedOption.attr( 'style' );
					if ( style ) {
						if ( val.webfont && ( ! loadedFonts.hasOwnProperty( val.font ) || ! loadedFonts[ val.font ] ) ) {
							WebFont.load({
								google: {
									families: [ val.font ],
								}
							});
							loadedFonts[ val.font ] = true;
						}
					} else {
						style = null;
					}
					$f.siblings( '.chosen-container' )
					  .find( '.chosen-single > span' )
					  .attr( 'style', style );
					var oldValue = $(this).data('currentValue');
					if (!args || !args.silent) {
						$(this).trigger('font_change', [val, oldValue]);
					}
					$(this).data('currentValue', val)
				}.bind(this);

				$container.find( 'select' ).on( 'change', changeValue );

				var chosen = null;

				// Setup this field for the first time
				if (chosen === null) {
					var timeout = null;
					$f.on('chosen:ready', function(e, params) {
						var dropdown = params.chosen.dropdown;
						var results = dropdown.find('.chosen-results');

						dropdown.find('.chosen-results').on('scroll', function() {
							clearTimeout(timeout);
							timeout = setTimeout(function() {
								// These are the fonts we'll load
								var loadFonts = {}, module, font, match;

								results.find('li').each(function() {
									var $$ = $(this),
										offset = $$.position().top;

									// Check that this element is in the viewport and not a web safe font
									if ($$.attr('style') !== undefined &&
										$$.attr('style') !== '' &&
										$$.attr('style').indexOf('__web_safe') === -1 &&
										offset > -10 &&
										offset < results.outerHeight() + 30
									) {
										match = $$.attr('style').match(/font-family: ([^,]+),.*__([^,]+);/);
										font = match[1].replace(/['"]/g, '').trim();
										module = match[2];
										if (typeof loadFonts[module] === 'undefined') {
											loadFonts[module] = [];
										}
										if (typeof loadedFonts[font] === 'undefined') {
											loadFonts[module].push(font);
											loadedFonts[font] = true;
										}
									}
								});

								// Load the fonts
								// Only doing Google web fonts for now.
								if (loadFonts.hasOwnProperty('google') && loadFonts.google.length > 0) {
									WebFont.load({
										google: {
											families: loadFonts.google,
										}
									});
								}

							}, 500);
						});

						// Trigger a fake scroll after a short timeout
						setTimeout(function() {
							results.trigger('scroll');
						}, 500);

						// After the user searches, trigger a scroll
						params.chosen.search_field.on('keyup', function() {
							setTimeout(function() {
								results.trigger('scroll');
							}, 500);
						});
					})
					.on('chosen:showing_dropdown', function(e, params) {
						params.chosen.dropdown.find('.chosen-results').trigger('scroll');
					});
					var chosenSettings = {
						allow_single_deselect: true,
						search_contains: true,
					};
					if ( options.hasOwnProperty( 'width' ) ) {
						chosenSettings.width = options.width;
					}
					$f.chosen( chosenSettings );
					chosen = true;
				}
				hasSetup = true;

			}
		);
	};
	
	module.setDefaultOptions = function (options) {
		var defaults = {
			useVariants: true,
			useSubsets: true,
		};
		
		for ( var key in defaults ) {
			if ( !options.hasOwnProperty( key ) ) {
				options[ key ] = defaults[ key ];
			}
		}
		
		return options;
	};

	module.update = function(value, variant) {
		var fontFam = value;
		if (fontFam && fontFam !== 'inherit') {
			fontFam = fontFam.split( ',' )[0];
			var famMatch = fontFam.match( /[\'\"]?([^\'\"]+)[\'\"]?/ );
			if (famMatch != null) {
				if (famMatch.length > 1) {
					fontFam = famMatch[1];
				} else {
					fontFam = famMatch[0];
				}
			}
		}
		if ( this.val() !== fontFam ) {
			this.val(fontFam);
			this.trigger("chosen:updated");
			this.trigger("change", {silent: true});
		}
		var $v = this.parent().find('select.font-variant');
		if ($v.length && variant) {
			$v.val(variant);
		}
	};
	
})(jQuery);
