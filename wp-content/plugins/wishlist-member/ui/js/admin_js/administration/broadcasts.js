var queue_ticker = null;
var queue_cnt = 0;
var queue_interval = 2000;
jQuery(function($){
	canspam_modal = new wlm3_modal( '#canspam-modal', update_canspam );
	send_queue_modal = new wlm3_modal( '#send-queue-modal', send_emails_in_queue );
	check_status_modal = new wlm3_modal( '#check-status-modal' );

	$('.create-broadcast-btn').click(show_create_broadcast_modal);
	$('.preview-broadcast-btn').click(preview_broadcast);
	$('.edit-broadcast-btn').click(edit_broadcast);
	$('.cancel-broadcast-btn').click(cancel_broadcast);
	$('.save-broadcast-btn').click(create_broadcast);
	$('#broadcast_use_custom_sender_info').change(toggle_sender_info);

	$('.update-canspam-btn').click(show_canspam_modal);
	$('.wlm-send-to').change(send_to_changed);

	$('.html-text-h').click(html_text_click);

	$('.html-text-t').do_confirm({placement: 'right', confirm_message : wlm.translate( 'This will remove all formatting. Are you sure?' ), yes_button : wlm.translate( 'Yes' )}).on("yes.do_confirm", html_text_click ).on('no.do_confirm', function() { $('.html-text-h, .html-text-t').toggleClass('active') });
	

	$('.broadcast-queued-btn').click( broadcast_change_status );
	$('.broadcast-paused-btn').click( broadcast_change_status );
	$('.broadcast-delete-btn').do_confirm({placement: 'right', confirm_message : wlm.translate( 'Delete Email Broadcast?' ), yes_button : wlm.translate( 'Delete' )}).on("yes.do_confirm", confirm_broadcast_delete );

	$('.send-queue-btn').click( show_send_queue_modal );
	$('.check-status-btn').click( show_check_status_modal );

	$('.duplicate-broadcast-btn').click( show_duplicate_broadcast_modal );

	if ( !$('.wlm-mergecodes').data('select2') ) $('.wlm-mergecodes').select2({theme:"bootstrap"});
	if ( !$('.wlm-levels').data('select2') ) $('.wlm-levels').select2({theme:"bootstrap",placeholder: wlm.translate( 'Select Membership Levels' )});

	$('.wlm-levels').allow_select_all();

	queue_cnt = parseInt($('.emails-in-queue-cnt').html());
	queue_cnt = queue_cnt ? queue_cnt : 0;
	if ( queue_cnt > 0 ) {
		clearInterval( queue_ticker );
		queue_ticker = setInterval( update_queue_counter, queue_interval );
	}
});

var load_email_editor = function( msg ) {
	if ( tinymce.editors['broadcast-message'] ) tinymce.remove('.broadcast-message');
	$('.broadcast-sentas').val("html");
	$('.html-text-h, .html-text-t').toggleClass('active');
	tinymce.init({
	  selector: '.broadcast-message',
	  height: 300,
	  branding: false,
	  menubar: 'edit insert format table',
  	  plugins: 'searchreplace link table charmap hr pagebreak nonbreaking anchor advlist lists textcolor contextmenu colorpicker textpattern paste image imagetools',
	  toolbar1: 'formatselect | bold italic strikethrough forecolor backcolor | link image | alignleft aligncenter alignright alignjustify  | numlist bullist outdent indent  | removeformat',
	  paste_data_images: true,
		convert_urls:true,
		relative_urls:false,
		remove_script_host:false,
	  browser_spellcheck: true,
		setup: function (editor) {
		    editor.on('init', function () {
		      editor.setContent( wlm.text2html( msg ) );
		    });
		}
	});
}

var update_queue_counter = function () {
	var settings_data = {
		action : "admin_actions",
		WishListMemberAction : "get_emails_in_queue",
	};
	var x = $('.emails-in-queue-cnt').save_settings({
		data: settings_data,
		show_loader: false,
	    on_success: function( $me, $result) {
	    	queue_cnt = $result.cnt;
	    	$('.emails-in-queue-cnt').html(queue_cnt);
			if ( queue_cnt <= 0 ) {
				clearInterval( queue_ticker );
				$('.emails-in-queue-cnt').html(0);
				$('.send-queue-status').addClass("d-none").hide();
				$(this).reload_screen();
			} else {
				$('.send-queue-status').removeClass("d-none").show();
			}
	    },
	    on_fail: function( $me, $data) {
	    	console.log(WLM3VARS.request_failed);
	    },
	    on_error: function( $me, $error_fields) {
	    	console.log(WLM3VARS.request_error);
	    }
	});
}

var show_create_broadcast_modal = function( e ) {
	e.preventDefault();
	if ( $(".broadcast-list-holder").hasClass("no-can-spam") ){
		show_canspam_modal(e);
		return;
	}
	$(".broadcast-new-holder").show();
	$(".broadcast-list-holder").hide();
	if ( !$('.wlm-mergecodes').data('select2') ) $('.wlm-mergecodes').select2({theme:"bootstrap"});
	if ( !$('.wlm-levels').data('select2') ) $('.wlm-levels').select2({theme:"bootstrap",placeholder: wlm.translate( 'Select Membership Levels' )});
	edit_broadcast( e );
	$(".broadcast-new-holder").find('.html-text-t').popover('disable');
	$(".broadcast-new-holder").find('.html-text-t').trigger("click");
}

var show_canspam_modal = function( e ) {
	e.preventDefault();
	if ( $(".broadcast-list-holder").hasClass("no-can-spam") ){
		$("#" +canspam_modal.data.id).find(".no-canspam-msg").show();
	} else {
		$("#" +canspam_modal.data.id).find(".no-canspam-msg").hide();
	}
	canspam_modal.open();

	$(".can-span-nav").trigger("click");

    wlm.richtext({
        selector: 'textarea.email-message',
        height: 200,
        menubar: false,
    });

    $('#' +canspam_modal.data.id  +' .email-reset-button').each(function() {
        $(this).do_confirm({placement:'right',yes_classes:'-success'})
        .on('yes.do_confirm', function() {
            var type = $(this).data('target');
            var subject = type + '_subject';
            var message = type + '_message';
            $('[name="' + subject + '"]').val(default_data[subject]);
            var target = $('[name="' + message + '"]');
            var editor = tinymce.get(target[0].id);
            editor.setContent(default_data[message]);
            target.val(default_data[message]);
            $('#' +canspam_modal.data.id +' .save-button.-primary').click();
        });
    });

	var select = $("#" +canspam_modal.data.id).find(".wlm-countries");
	if ( !select.data('select2') ) select.select2({theme:"bootstrap"});
	var select = $("#" +canspam_modal.data.id).find(".wlm-mergecodes");
	if ( !select.data('select2') ) select.select2({theme:"bootstrap"});
}

var send_to_changed = function() {
	if ( $(this).val() == "send_mlevels" ) {
		$('.wlm-levels-holder').removeClass("d-none");
		$('.save-searches-holder').addClass("d-none");

		$('.wlm-levels-holder .wlm-levels').prop("required", "required");
		$('.save-searches-holder .wlm-select').removeAttr("required");
	} else {
		$('.wlm-levels-holder').addClass("d-none");
		$('.save-searches-holder').removeClass("d-none");

		$('.save-searches-holder .wlm-select').prop("required", "required");
		$('.wlm-levels-holder .wlm-levels').removeAttr("required");
	}
}

var html_text_click = function() {
	$('.broadcast-sentas').val( $(this).attr("aria-value") );

	if ( $(this).attr("aria-value") == "text" ) {
		var tinymsg = tinymce.editors["broadcast-message"].getContent();
		tinymsg = jQuery.trim(tinymsg);
		tinymce.remove('.broadcast-message');
		$("#broadcast-message").val(wlm.html2text( tinymsg ));
		$("#broadcast-message").focus();
		$(".broadcast-new-holder").find('.html-text-t').popover('disable');
	} else {
		$(".broadcast-new-holder").find('.html-text-t').popover('enable');
		var tinymsg = $("#broadcast-message").val();
		load_email_editor( tinymsg );
	}
}

var edit_broadcast = function( e ) {
	e.preventDefault();
	$(".broadcast-new-holder").find('.edit-broadcast-btn').hide();
	$(".broadcast-new-holder").find('.save-broadcast-btn').hide();
	$(".broadcast-new-holder").find('.cancel-broadcast-btn').show();
	$(".broadcast-new-holder").find('.preview-broadcast-btn').show();
	$(".broadcast-new-holder").find('.form-holder').show();
	$(".broadcast-new-holder").find('.preview-holder').hide();
	$(".broadcast-new-holder").find('.preview-holder').html("");
}

var cancel_broadcast = function( e ) {
	e.preventDefault();
	$(".broadcast-new-holder").hide();
	$(".broadcast-list-holder").show();
}

var preview_broadcast = function( e ) {
	e.preventDefault();
	var $this_button = $(this);
	if ( $this_button.prop("disabled") || $this_button.hasClass("-disable") || $this_button.hasClass("-disabled") ) return false; //if disabled, do nothing

	var form_holder = $(this).closest(".broadcast-new-holder").find(".form-holder");
	var modal_id = $(this).closest(".broadcast-new-holder").prop("id");
	var $save_button = $(this).closest(".broadcast-new-holder").find(".save-button");


	var tinymsg = "";
	if ( $('.broadcast-sentas').val() == "html" ) {
		tinymsg = tinymce.editors["broadcast-message"].getContent();
	} else {
		tinymsg = $("#broadcast-message").val();
	}
	tinymsg = jQuery.trim(tinymsg);

	if ( tinymsg == "" ) {
		$("#broadcast-message").parent().addClass('has-error');
		return false;
	}

	var settings_data = {
		action : "admin_actions",
		WishListMemberAction : "preview_broadcast",
		message: tinymsg
	};
	var x = form_holder.save_settings({
		data: settings_data,
	    on_init: function( $me, $data) {
	    	$this_button.disable_button({disable:true, icon:"update"});
	    },
	    on_success: function( $me, $result) {
	    	if ( $result.success ) {
				$('.edit-broadcast-btn').show();
				$('.save-broadcast-btn').show();
				$this_button.hide();
				$('.form-holder').hide();
				$('.preview-holder').show();
	    		$('.preview-holder').html($result.preview);
	    	} else {
	    		$(".wlm-message-holder").show_message({message:$result.msg, type:$result.msg_type, icon:$result.msg_type});
	    		console.log($result);
	    	}
	    },
	    on_fail: function( $me, $data) {
	    	console.log($data);
	    	alert(WLM3VARS.request_failed);
	    	$this_button.disable_button({disable:false, icon:"search"});
	    },
	    on_error: function( $me, $error_fields) {
	    	$.each( $error_fields, function( key, obj ) {
	    		if ( typeof obj == 'object' ) {
	    			obj.parent().addClass('has-error');
	    			$(window).scrollTop(0);
	    		}
			});
			$this_button.disable_button({disable:false, icon:"search"});
	    },
	    on_done: function( $me, $data) {
	    	$this_button.disable_button({disable:false, icon:"search"});
	    	$this_button.blur();
	    }
	});
}

var create_broadcast = function( e ) {
	e.preventDefault();
	var $this_button = $(this);
	if ( $this_button.prop("disabled") || $this_button.hasClass("-disable") || $this_button.hasClass("-disabled") ) return false; //if disabled, do nothing

	var form_holder = $(this).closest(".broadcast-new-holder").find(".form-holder");

	var tinymsg = "";
	if ( $('.broadcast-sentas').val() == "html" ) {
		tinymsg = tinymce.editors["broadcast-message"].getContent();
	} else {
		tinymsg = $("#broadcast-message").val();
	}
	tinymsg = jQuery.trim(tinymsg);

	var settings_data = {
		action : "admin_actions",
		WishListMemberAction : "create_broadcast",
		message: tinymsg
	};
	var x = form_holder.save_settings({
		data: settings_data,
	    on_success: function( $me, $result) {
	    	if ( $result.success ) {
				queue_broadcast( $this_button, $result.id );
	    	} else {
	    		$(".wlm-message-holder").show_message({message:$result.msg, type:$result.msg_type, icon:$result.msg_type});
	    		console.log($result);
	    	}
	    },
	    on_fail: function( $me, $data) {
	    	console.log($data);
	    	alert(WLM3VARS.request_failed);
	    },
	    on_error: function( $me, $error_fields) {
	    	edit_broadcast();
	    	$.each( $error_fields, function( key, obj ) {
	    		if ( typeof obj == 'object' ) {
	    			obj.parent().addClass('has-error');
	    		}
			});
	    },
	    on_done: function( $me, $data) {
	    	$this_button.blur();
	    }
	});
}

var queue_broadcast = function ( this_button, id ) {
	var $this_button = this_button;
	var form_holder = this_button.closest(".broadcast-new-holder").find(".form-holder");

	var settings_data = {
		action : "admin_actions",
		WishListMemberAction : "queue_broadcast",
		id : id
	};
	var x = this_button.save_settings({
		data: settings_data,
	    on_init: function( $me, $data) {
	    	$this_button.disable_button({disable:true});
	    	$('.preview-holder').html("<p>Queueing your broadcast, please wait...</p>");
	    },
	    on_success: function( $me, $result) {
	    	if ( $result.success ) {
				$(".wlm-message-holder").show_message({message:$result.msg, type:$result.msg_type, icon:$result.msg_type});
	    	} else {
	    		$(".wlm-message-holder").show_message({message:$result.msg, type:$result.msg_type, icon:$result.msg_type});
	    		console.log($result);
	    	}
	    },
	    on_fail: function( $me, $data) {
	    	console.log($data);
	    	alert(WLM3VARS.request_failed);
	    },
	    on_error: function( $me, $error_fields) {
	    	alert(WLM3VARS.request_error);
	    },
	    on_done: function( $me, $data) {
	    	$this_button.disable_button({disable:false});
	    	$(this).reload_screen();
	    	// window.parent.location.reload(true);
	    }
	});
}

var show_duplicate_broadcast_modal = function( e ) {
	e.preventDefault();
	show_create_broadcast_modal(e);

	var settings_data = {
		action : "admin_actions",
		WishListMemberAction : "get_email_broadcast",
		id : $(this).attr("data-id")
	};

	var x = $(this).find(".email-status-holder").save_settings({
		data: settings_data,
	    on_success: function( $me, $result) {
	    	if ( $result.success ) {
	    		$(".broadcast-new-holder").find('input[name="subject"]').val( $result.broadcast.subject );
	    		if ( $result.broadcast.sent_as == "html" ) {
					$(".broadcast-new-holder").find('.html-text-t').popover('enable');
					load_email_editor( $result.broadcast.text_body );
	    		} else {
	    			$(".broadcast-new-holder").find('#broadcast-message').html( $result.broadcast.text_body );
	    		}
	    		$('input[name="from_name"]').val($result.broadcast.from_name);
	    		$('input[name="from_email"]').val($result.broadcast.from_email);
	    		$('#broadcast_use_custom_sender_info').prop('checked', false).change();
	    	}
	    },
	    on_fail: function( $me, $data) {
	    	console.log($data);
	    	alert(WLM3VARS.request_failed);
	    },
	    on_error: function( $me, $error_fields) {
	    	edit_broadcast();
	    	alert(WLM3VARS.request_error);
	    }
	});
}

var broadcast_change_status = function( e ) {
	e.preventDefault();
	var settings_data = {
		action : "admin_actions",
		WishListMemberAction : "changestat_broadcast",
		id : $(this).attr("data-id"),
		status : $(this).attr("data-status")
	};
	var x = $(this).save_settings({
		data: settings_data,
	    on_success: function( $me, $result) {
	    	if ( $result.success ) {
				$(".wlm-message-holder").show_message({message:$result.msg, type:$result.msg_type, icon:$result.msg_type});
				$(".tr-" +$result.data.id ).find(".broadcast-status").html( $result.data.status.toUpperCase() );
				if ( $result.data.status == "Queued" ) {
					$(".tr-" +$result.data.id ).find(".broadcast-queued-btn").addClass("d-none");
					$(".tr-" +$result.data.id ).find(".broadcast-paused-btn").removeClass("d-none");
					$(".tr-" +$result.data.id ).find(".broadcast-delete-btn").addClass("d-none");
				} else {
					$(".tr-" +$result.data.id ).find(".broadcast-queued-btn").removeClass("d-none");
					$(".tr-" +$result.data.id ).find(".broadcast-paused-btn").addClass("d-none");
					$(".tr-" +$result.data.id ).find(".broadcast-delete-btn").removeClass("d-none");
				}
	    	}
	    },
	    on_fail: function( $me, $data) {
	    	console.log($data);
	    	alert(WLM3VARS.request_failed);
	    },
	    on_error: function( $me, $error_fields) {
	    	alert(WLM3VARS.request_error);
	    },
	    on_done: function( $me, $data) {
			clearInterval( queue_ticker );
			queue_ticker = setInterval( update_queue_counter, queue_interval);
	    }
	});
}

var confirm_broadcast_delete = function( e ) {
	e.preventDefault();

	var $this_button = $(this);
	if ( $this_button.prop("disabled") || $this_button.hasClass("-disable") || $this_button.hasClass("-disabled") ) return false; //if disabled, do nothing

	var settings_data = {
		action : "admin_actions",
		WishListMemberAction : "delete_broadcast",
		id : $(this).attr("data-id"),
	};

	var x = $this_button.save_settings({
		data: settings_data,
	    on_success: function( $me, $result) {
	    	if ( $result.success ) {
	    		$(".wlm-message-holder").show_message({message:$result.msg, type:$result.msg_type, icon:$result.msg_type});
	    		$(".tr-" +$result.data.id ).fadeOut(500, function(){ $(this).remove();});
	    	}
	    },
	    on_fail: function( $me, $data) {
	    	console.log($data);
	    	alert(WLM3VARS.request_failed);
	    },
	    on_error: function( $me, $error_fields) {
	    	alert(WLM3VARS.request_error);
	    }
	});
}

var show_send_queue_modal = function( e ) {
	e.preventDefault();
	send_queue_modal.open();
	$("#" +send_queue_modal.data.id).find(".progress-holder").hide();
	$("#" +send_queue_modal.data.id).find(".message-holder").show();
	$("#" +send_queue_modal.data.id).find(".message").html(wlm.translate( 'Are you sure you want to send the emails currently in queue?' ));
	$("#" +send_queue_modal.data.id).find(".save-button").show();
	$("#" +send_queue_modal.data.id).find(".cancel-button").show();
	$("#" +send_queue_modal.data.id).find(".cancel-button").html(wlm.translate( 'Cancel' ));
	$("#" +send_queue_modal.data.id).find('.progress-bar').css("width", "0%");
}

var send_emails_in_queue = function() {
	var $this_button = $(this);
	if ( $this_button.prop("disabled") || $this_button.hasClass("-disable") || $this_button.hasClass("-disabled") ) return false; //if disabled, do nothing

	var message = $(this).closest(".modal").find(".message");
	var progress = $(this).closest(".modal").find(".progress-holder");
	var cancel_button = $(this).closest(".modal").find(".cancel-button");
	var modal_id = $(this).closest(".modal").prop("id");

	var settings_data = {
		action : "admin_actions",
		WishListMemberAction : "get_emails_in_queue",
	};
	var x = message.save_settings({
		data: settings_data,
	    on_init: function( $me, $data) {
	    	$this_button.hide();
	    	cancel_button.hide();
	    	message.html("<p>" +wlm.translate( 'Retrieving emails in queue...' ) +"</p>");
	    	progress.hide();
	    },
	    on_success: function( $me, $result) {
	    	if ( $result.success ) {
	    		var queue_length = $result.data.length;
	    		var total_sent = total_failed = 0;
	    		if ( queue_length > 0 ) {
					message.html(wlm.translate('<p>Processing <strong>$$1</strong> emails in queue, please wait...</p>', [queue_length]) );
					progress.show();
					progress.find('.progress-warning').removeClass("d-none").show();
					progress.find('.progress-warning').html(wlm.translate( 'Do not close your browser while your emails are being sent' ));
					progress.find('.progress-warning').addClass("text-danger").removeClass("text-success");
					jQuery.each( $result.data, function( key, queue_id ) {
						var x = message.save_settings({
							data: { action : "admin_actions", WishListMemberAction : "send_emails_in_queue", id: queue_id },
						    on_success: function( $me, $result) {
						    	if ( $result.success ) total_sent += 1;
						    	else total_failed += 1;
						    },
						    on_fail: function( $me, $data) {
						    	console.log($data);
						    	total_failed += 1;
						    },
						    on_done: function( $me, $data) {
						    	total_processed = total_sent + total_failed;
								progress.find('.progress-bar').attr("aria-valuemax", queue_length );
								progress.find('.progress-bar').attr("aria-valuenow", total_sent );
								progress.find('.progress-bar').css("width", ( ( total_processed / queue_length) * 100) + "%");
								// progress.find('.progress-warning').addClass("d-none").hide();
								$(".emails-in-queue-cnt").html( queue_length - total_processed );
								if ( (queue_length - total_processed) <= 0 ) $(".send-queue-btn").addClass("d-none").hide();
								if ( (total_sent+total_failed) == queue_length ) {
									message.html(wlm.translate('<p>Total emails processed: <strong>$$1</strong><br /> Total emails sent: <strong>$$2</strong><br /> Total emails failed: <strong>$$3</strong> </p>', [queue_length,total_sent,total_failed]));
									progress.find('.progress-warning').html("<i class='wlm-icons' style='vertical-align: -4px'>check_circle_outline</i> " +wlm.translate('Completed'));
									progress.find('.progress-warning').removeClass("text-danger").addClass("text-success");
							    	$(".wlm-message-holder").show_message({message: wlm.translate( 'Email Sent' ), type: 'success', icon: 'success'});
							    	cancel_button.show();
							    	cancel_button.html(wlm.translate( 'Close' ));
								}
						    }
						});
					});
	    		} else {
	    			message.html( wlm.translate( '<p>You have no emails in queue</p>' ) );
			    	cancel_button.show();
			    	cancel_button.html(wlm.translate( 'Close' ));
	    		}
	    	}
	    },
	    on_fail: function( $me, $data) {
	    	console.log($data);
	    	alert(WLM3VARS.request_failed);
	    	cancel_button.show();
	    	cancel_button.html(wlm.translate( 'Close' ));
	    },
	    on_error: function( $me, $error_fields) {
	    	alert(WLM3VARS.request_error);
	    	cancel_button.show();
	    	cancel_button.html(wlm.translate( 'Close' ));
	    }
	});
}

var show_check_status_modal = function( e ) {
	e.preventDefault();
	var modal_body = $("#" +check_status_modal.data.id).find(".modal-body");
	var modal_id = check_status_modal.data.id;
	modal_body.html(wlm.translate( '<p>Retrieving broadcast email status, please wait...</p>' ));

	check_status_modal.open();
	var settings_data = {
		action : "admin_actions",
		WishListMemberAction : "get_broadcast_status",
		id : $(this).attr("data-id")
	};
	var x = modal_body.save_settings({
		data: settings_data,
	    on_success: function( $me, $result) {
	    	modal_body.html($result.html);
	    	modal_body.find(".check-all-failed").click( check_all_failed );
	    	modal_body.find(".failed-emails-action").click( failed_emails_action );
	    },
	    on_fail: function( $me, $data) {
	    	console.log($data);
	    	alert(WLM3VARS.request_failed);
	    },
	    on_error: function( $me, $error_fields) {
	    	edit_broadcast();
	    	alert(WLM3VARS.request_error);
	    }
	});
}

var check_all_failed = function( e ) {
	e.preventDefault();
	var status = $(this).attr("data-check");
	$(this).closest(".email-status-holder").find(':checkbox').each( function() {
		if ( status == "1" ) {
			$(this).prop('checked', true );
		} else {
			$(this).prop('checked', false );
		}
	});
	if ( status == "1" ) {
		$(this).attr("data-check", 0);
	} else {
		$(this).attr("data-check", 1);
	}
}

var failed_emails_action = function( e ) {
	e.preventDefault();
	var body = $(this).closest(".modal").find(".modal-body");
	var action = $(this).attr("data-action");
	var cont = false;
	if ( action == "remove_failed_broadcast_emails" ) {
		cont = confirm(wlm.translate( 'You are about to remove the selected items from your email queue.' ));
	} else {
		cont = confirm(wlm.translate( 'You are about to add the selected items in your email queue.' ));
	}
	if ( !cont ) return  false;

	var settings_data = {
		action : "admin_actions",
		WishListMemberAction : action
	};
	var x = body.find(".email-status-holder").save_settings({
		data: settings_data,
	    on_init: function( $me, $data) {
	    	body.html(wlm.translate( '<p>Processing your request, please wait...</p>' ));
			if ( action == "remove_failed_broadcast_emails" ) {
				body.html(wlm.translate( '<p>Removing the selected items from your email queue, please wait...</p>' ));
			} else {
				body.html(wlm.translate( '<p>Requeuing the selected items, please wait...</p>' ));
			}
	    },
	    on_success: function( $me, $result) {
	    	body.html($result.html);
	    	body.find(".check-all-failed").click( check_all_failed );
	    	body.find(".failed-emails-action").click( failed_emails_action );
	    	if ( $result.data.qid && $result.data.qid.length > 0 ) {
	    		$inq = $(".emails-in-queue-cnt").html();
	    		$inq = parseInt($inq)+0;
				$(".emails-in-queue-cnt").html( $inq + $result.data.qid.length );
				if ( ($inq + $result.data.qid.length) > 0 ) $(".send-queue-btn").removeClass("d-none").show();
	    	}
			if ( action == "remove_failed_broadcast_emails" ) {
				$(".wlm-message-holder").show_message({message: wlm.translate( 'Selected items has been removed from email queue' ), type: 'success', icon: 'success'});
			} else {
				$(".wlm-message-holder").show_message({message: wlm.translate( 'Selected items has been added to queue' ), type: 'success', icon: 'success'});
				// $(this).reload_screen();
				clearInterval( queue_ticker );
				queue_ticker = setInterval( update_queue_counter, queue_interval );
			}
	    },
	    on_fail: function( $me, $data) {
	    	console.log($data);
	    	alert(WLM3VARS.request_failed);
	    },
	    on_error: function( $me, $error_fields) {
	    	edit_broadcast();
	    	alert(WLM3VARS.request_error);
	    }
	});
}

var update_canspam = function() {
	var $this_button = $(this);
	if ( $this_button.prop("disabled") || $this_button.hasClass("-disable") || $this_button.hasClass("-disabled") ) return false; //if disabled, do nothing
	var $save_button = $(this).closest(".modal").find(".save-button");

	var $container = $(this).closest(".modal").find(".modal-body").find("#notification");
	if ( $(this).closest(".modal").find(".modal-body").find("#can-spam").hasClass("active") ) {
		$container = $(this).closest(".modal").find(".modal-body").find("#can-spam");
	}

	var containerid = $container.prop("id");
	var complete_canspam = false;

	var settings_data = {
		action : "admin_actions",
		WishListMemberAction : "save",
	};
	if ( containerid == "notification" ) {
		settings_data['unsubscribe_notice_email_message'] = tinymce.editors["email-message"].getContent();
	}
	$container.save_settings({
		data: settings_data,
	    on_init: function( $me, $data) {
	    	$this_button.disable_button({disable:true, icon:"update"});
	    	$save_button.disable_button({disable:true});
	    },
	    on_success: function( $me, $result) {
	    	if ( containerid == "can-spam" ) {
		    	street1 = $container.find('[name="email_sender_street1"]').val();
				street2 = $container.find('[name="email_sender_street2"]').val();
				city    = $container.find('[name="email_sender_city"]').val();
				state   = $container.find('[name="email_sender_state"]').val();
				zipcode = $container.find('[name="email_sender_zipcode"]').val();
				country = $container.find('[name="email_sender_country"]').val();
				complete_canspam = street1 && city && state && zipcode && country ? true : false;
				if (  complete_canspam ) {
					$(".broadcast-list-holder").removeClass("no-can-spam");
					$("#" +canspam_modal.data.id).find(".no-canspam-msg").hide();
				} else {
					$(".broadcast-list-holder").addClass("no-can-spam");
					$("#" +canspam_modal.data.id).find(".no-canspam-msg").show();
				}
	    	}
			$(".wlm-message-holder").show_message({message:$result.msg, type:$result.msg_type, icon:$result.msg_type});
			if ( $this_button.hasClass("-close") ) canspam_modal.close();
	    },
	    on_fail: function( $me, $data) {
	    	alert(WLM3VARS.request_failed);
	    },
	    on_error: function( $me, $error_fields) {
	    	$.each( $error_fields, function( key, obj ) {
  				obj.parent().addClass('has-error');
			});
	    	$this_button.disable_button( {disable:false, icon:"save"} );
	    	$save_button.disable_button({disable:false});
	    },
	    on_done: function( $me, $data) {
	    	$this_button.disable_button( {disable:false, icon:"save"} );
	    	$save_button.disable_button({disable:false});
	    }
	});
}

var toggle_sender_info = function( e ) {
	if(this.checked) {
		$('.-global-sender').removeClass('d-none');
		$('.-custom-sender').addClass('d-none');
	} else {
		$('.-global-sender').addClass('d-none');
		$('.-custom-sender').removeClass('d-none');		
	}
}

$(function() {
	$('#broadcast_use_custom_sender_info').change();
});