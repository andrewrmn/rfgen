WLM3ThirdPartyIntegration.activecampaign.fxn = {
	test_keys : function(x) {
		var c = $('#thirdparty-provider-container-activecampaign'); 
		c.find('.api-status').html('<div class="text-warning"><p><em>Checking...</em></p></div>');
		var b = c.find('.save-keys').first();
		if(x.save) {
			b.text(b.data('saving'));
		}
		b.addClass('disabled');
		$.post(
			WLM3VARS.ajaxurl,
			{
				action: 'wlm3_activecampaign_test_keys',
				data: x
			},
			function(result) {
				if(result.status) {
					c.removeClass('api-fail');
					c.find('.api-status').html('<div class="text-success"><p>' + get_integration_api_message(1, 'ActiveCampaign') + '</p></div>');
					WLM3ThirdPartyIntegration.activecampaign.lists = result.lists;
					WLM3ThirdPartyIntegration.activecampaign.fxn.load_lists();
				} else {
					c.addClass('api-fail');
					var msg = (x.api_url.trim() && x.api_key.trim()) ? get_integration_api_message(2, result.message) : get_integration_api_message(3);
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
		var $me = $('#thirdparty-provider-container-activecampaign');
		// if(!$me.hasClass('api-fail')) {
		// 	obj.find('.-integration-keys :input').val('');
		// }
		var x = {};
		obj.find('.-integration-keys :input').each(function(i,v) {
			x[v.name] = v.value;
		});
		return x;
	},
	load_lists : function() {
		var $me = $('#thirdparty-provider-container-activecampaign');
		$('select.activecampaign-lists').select2({"data" : WLM3ThirdPartyIntegration.activecampaign.lists}, true);
		$me.set_form_data(WLM3ThirdPartyIntegration.activecampaign);
	}
}
integration_before_open['activecampaign'] = function(obj) {
	var fxn = this;
	obj = $(obj);
	var $me = $('#thirdparty-provider-container-activecampaign');

	fxn.save_keys = function(){
		var x = $.extend({save : true},WLM3ThirdPartyIntegration.activecampaign.fxn.get_keys(obj));
		WLM3ThirdPartyIntegration.activecampaign.fxn.test_keys(x);
	};

	$me.off('click', '.save-keys', fxn.save_keys);
	$me.on('click', '.save-keys', fxn.save_keys);

	$me.addClass('api-fail'); 
}
integration_after_open['activecampaign'] = function(obj) {
	var fxn = this;
	obj = $(obj);

	WLM3ThirdPartyIntegration.activecampaign.fxn.test_keys(
		WLM3ThirdPartyIntegration.activecampaign.fxn.get_keys(obj)
	);
}