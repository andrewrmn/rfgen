WLM3ThirdPartyIntegration.constantcontact.fxn = {
	test_keys : function(x) {
		var c = $('#thirdparty-provider-container-constantcontact'); 
		c.find('.api-status').html('<div class="text-warning"><p><em>Checking...</em></p></div>');
		var b = c.find('.save-keys').first();
		if(x.save) {
			b.text(b.data('saving'));
		}
		b.addClass('disabled');
		$.post(
			WLM3VARS.ajaxurl,
			{
				action: 'wlm3_constantcontact_test_keys',
				data: x
			},
			function(result) {
				if(result.status) {
					c.removeClass('api-fail');
					c.find('.api-status').html('<div class="text-success"><p>' + get_integration_api_message(1, 'Constant Contact') + '</p></div>');
					WLM3ThirdPartyIntegration.constantcontact.lists = result.lists;
					WLM3ThirdPartyIntegration.constantcontact.fxn.set_list_options();
				} else {
					c.addClass('api-fail');
					var msg = (x.ccusername.trim() && x.ccpassword.trim()) ? get_integration_api_message(2, result.message) : get_integration_api_message(3);
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
		var $me = $('#thirdparty-provider-container-constantcontact');
// 		if(!$me.hasClass('api-fail')) {
// 			obj.find('.-integration-keys :input').val('');
// 		}
		var x = {};
		obj.find('.-integration-keys :input').each(function(i,v) {
			x[v.name] = v.value;
		});
		return x;
	},
	set_list_options : function() {
		var $me = $('#thirdparty-provider-container-constantcontact');
		var selects = $me.find('select.constantcontact-lists-select');
		selects.empty().append($('<option/>', {value : '', text : '- None -'}))
		$.each(WLM3ThirdPartyIntegration.constantcontact.lists, function(index, list) {
			selects.append($('<option/>', {value : list.id, text : list.Name}));
		});
		$me.set_form_data(WLM3ThirdPartyIntegration.constantcontact);
	}
}
integration_before_open['constantcontact'] = function(obj) {
	var fxn = this;
	obj = $(obj);
	var $me = $('#thirdparty-provider-container-constantcontact');

	fxn.save_keys = function(){
		var x = $.extend({save : true},WLM3ThirdPartyIntegration.constantcontact.fxn.get_keys(obj));
		WLM3ThirdPartyIntegration.constantcontact.fxn.test_keys(x);
	};

	$me.off('click', '.save-keys', fxn.save_keys);
	$me.on('click', '.save-keys', fxn.save_keys);

	$me.addClass('api-fail'); 
}
integration_after_open['constantcontact'] = function(obj) {
	var fxn = this;
	obj = $(obj);

	WLM3ThirdPartyIntegration.constantcontact.fxn.test_keys(
		WLM3ThirdPartyIntegration.constantcontact.fxn.get_keys(obj)
	);
}