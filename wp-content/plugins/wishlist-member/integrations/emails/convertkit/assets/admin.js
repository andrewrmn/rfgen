WLM3ThirdPartyIntegration.convertkit.fxn = {
	test_keys : function(x) {
		var c = $('#thirdparty-provider-container-convertkit'); 
		c.find('.api-status').html('<div class="text-warning"><p><em>Checking...</em></p></div>');
		var b = c.find('.save-keys').first();
		if(x.save) {
			b.text(b.data('saving'));
		}
		b.addClass('disabled');
		$.post(
			WLM3VARS.ajaxurl,
			{
				action: 'wlm3_convertkit_test_keys',
				data: x
			},
			function(result) {
				var msg;
				if(result.status) {
					c.removeClass('api-fail');
					c.find('.api-status').html('<div class="text-success"><p>' + get_integration_api_message(1, 'ConvertKit') + '</p></div>');
					WLM3ThirdPartyIntegration.convertkit.fxn.set_list_options(result.lists);
				} else {
					c.addClass('api-fail');
					var msg = x.ckapi.trim() ? get_integration_api_message(2, result.message) : get_integration_api_message(3);
					c.find('.api-status').html('<div class="text-danger"><p>' + msg + '</p></div>');
				}
				if(x.save) {
					b.text(b.data('saved'));
				}
				b.removeClass('disabled');
			},
			'json'
		);
	},
	get_keys : function(obj) {
		var $me = $('#thirdparty-provider-container-convertkit');
// 		if(!$me.hasClass('api-fail')) {
// 			obj.find('.-integration-keys :input').val('');
// 		}
		var x = {};
		obj.find('.-integration-keys :input').each(function(i,v) {
			x[v.name] = v.value;
		});
		return x;
	},
	set_list_options : function(options) {
		var $me = $('#thirdparty-provider-container-convertkit');
		var selects = $me.find('.convertkit-lists-select');
		selects.empty().append($('<option/>', {value : '', text : '- None -'}))
		$.each(options, function (index, option) {
			selects.append($('<option />', option));
		});
		$('#thirdparty-provider-container-convertkit').set_form_data(WLM3ThirdPartyIntegration.convertkit);
	}
}
integration_before_open['convertkit'] = function(obj) {
	var fxn = this;
	obj = $(obj);
	var $me = $('#thirdparty-provider-container-convertkit');

	fxn.save_keys = function(){
		var x = $.extend({save : true},WLM3ThirdPartyIntegration.convertkit.fxn.get_keys(obj));
		WLM3ThirdPartyIntegration.convertkit.fxn.test_keys(x);
	};

	$me.off('click', '.save-keys', fxn.save_keys);
	$me.on('click', '.save-keys', fxn.save_keys);

	$me.addClass('api-fail'); 
}
integration_after_open['convertkit'] = function(obj) {
	var fxn = this;
	obj = $(obj);

	WLM3ThirdPartyIntegration.convertkit.fxn.test_keys(
		WLM3ThirdPartyIntegration.convertkit.fxn.get_keys(obj)
	);
}