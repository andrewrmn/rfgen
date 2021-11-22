jQuery(function (jQuery) {
	jQuery(document).on('before_panels_setup', function (event, builderView) {

		var searchParams = new URLSearchParams(window.location.search);
		if (!searchParams.has('tab')) {
			searchParams.set('tab', 'content-single-product');
			window.history.replaceState({}, '', window.location.pathname + '?' + searchParams);
		}
		var curPageTab = searchParams.get('tab');
		var curPageWidgetsTab = 'woocommerce_' + curPageTab.replace(/\-/g, '_');
		jQuery(document).on('open_dialog', function (event, dialog) {
			if (dialog instanceof panels.dialog.widgets) {
				var curWidgetsTab = dialog.el.querySelector('.so-sidebar-tabs a[href="#' + curPageWidgetsTab + '"]');
				curWidgetsTab.trigger( 'click' );
			}
		});

		var previewIframeId = 1;
		var previewIframe;
		var postToIframe = function( data, url, target ) {
			if( previewIframe ) {
				previewIframe.remove();
			}

			var iframeId = 'so-wc-template-preview-' + previewIframeId;

			previewIframe = document.createElement('iframe');
			previewIframe.setAttribute('src', 'javascript:false;');
			previewIframe.setAttribute('id', iframeId);
			previewIframe.setAttribute('name', iframeId);
			target.appendChild(previewIframe);

			var tempForm = document.createElement('form');
			tempForm.setAttribute('id', 'soPostToPreviewFrame');
			tempForm.setAttribute('method', 'post');
			tempForm.setAttribute('id', iframeId);
			tempForm.setAttribute('target', iframeId);
			tempForm.setAttribute('action', url);
			document.body.appendChild(tempForm);

			for (var name in data) {
				var input = document.createElement('input');
				input.setAttribute('type', 'hidden');
				input.setAttribute('name', name);
				input.setAttribute('value', data[name]);
				tempForm.appendChild(input);
			}

			tempForm.submit();
			tempForm.remove();

			previewIframeId++;

			return previewIframe;
		};
		var previewButton = document.getElementById('so-wc-preview-template');
		if (previewButton) {
			var previewDialog;
			previewButton.addEventListener('click', function () {
				if (!previewDialog) {
					var previewDialogHTML = document.getElementById('so-premium-wc-template-preview').innerHTML;
					var tmpDiv = document.createElement('div');
					tmpDiv.innerHTML = previewDialogHTML;
					previewDialog = tmpDiv.querySelector('#so-premium-wc-template-preview-dialog');
					document.body.append(previewDialog);

					var closeButtons = previewDialog.querySelectorAll('.so-close');
					closeButtons.forEach(function (button) {
						button.addEventListener('click', function (event) {
							event.preventDefault();
							previewDialog.style.display = 'none';
						});
					});
				}

				var templateBuilderForm = document.getElementById('so-premium-wc-template-form');

				var previewData = {
					template_preview_panels_data: JSON.stringify(builderView.model.getPanelsData()),
					preview_template_post_id: templateBuilderForm.getAttribute('data-post-id'),
					siteorigin_premium_template_preview: true,
					tab: curPageTab,
				};
				var previewUrl = previewButton.getAttribute('data-preview-url');
				postToIframe(previewData, previewUrl, previewDialog.querySelector('.so-content'));
				previewDialog.style.display = 'block';
			});
		}
		if (curPageTab === 'content-single-product' || curPageTab === 'content-product' ) {
			var selectTemplate = document.getElementById('so-wc-selected-template');
			if (selectTemplate) {
				selectTemplate.addEventListener('change', function (event) {
					searchParams.set('template_post_id', selectTemplate.value);
					window.location.href = window.location.pathname + '?' + searchParams;
				});
			}
			var deleteButton = document.getElementById('so-wc-delete-template');
			if (deleteButton) {
				deleteButton.addEventListener('click', function (event) {
					if (!confirm(soPremiumWcTemplateBuilder.confirm_delete_template)) {
						event.preventDefault();
					}
				});
			}
		}
		var canHover = window.matchMedia( '(hover:hover)' ).matches;
		if ( ! canHover ) {
			jQuery( '.so-wc-templates-tooltip' ).hide();
		}
	});
});

