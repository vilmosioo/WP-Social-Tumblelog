'use strict';

var WP_SOCIAL_TUMBLELOG = (function (app, $, window) {

	var _list , _button, _spinner, _error, _input, _delete, _error_primary,
	_social_client_key, _social_client_secret, _social_spinner, _social_error_primary, _social_error, _social_submit, _social_select;

	var _assign = function(){
		_list = $('#wpsocial-tumblelog-options-list');
		_button = $('#wpsocial-tumblelog-options-add_feed');
		_spinner = $('#wpsocial-tumblelog-options-spinner');
		_error = $('#wpsocial-tumblelog-options-error');
		_input = $('#wpsocial-tumblelog-options-feed');
		_delete = $('#wpsocial-tumblelog-options-list .delete');
		_error_primary = $('#wpsocial-tumblelog-options-error-primary');

		_social_client_key = $('#wpsocial-client-key');
		_social_client_secret = $('#wpsocial-client-secret');
		_social_spinner = $('#wpsocial-spinner');
		_social_error_primary = $('#wpsocial-error-primary');
		_social_error = $('#wpsocial-error');
		_social_submit = $('#wpsocial-submit');
		_social_select = $('#wpsocial-social');
	};

	var _addFeed = function(){
		_spinner.show();
		jQuery.post(
	    ajaxurl, 
	    {
        'action': 'wp_social_tumblelog_add_feed',
        'feed': _input.val()
	    }, 
	    function(response){
	    	response = JSON.parse(response);

	    	_spinner.hide();

	    	if(response.code === 200){
	    		_error.hide();
	    		_input.removeClass('error').val('');
	    		var _new = $("<li style='display:none;'><i class='fa fa-minus-circle delete'></i><div class='feed'><a target='_blank' href='" + response.feed.url + "'>" + (response.feed.title || respons.feed.url) + "</a> - <span class='description'>" + response.feed.description + "</span></div></li>");
	    		_list.append(_new);
	    		_new.fadeIn();	    		
	    	} else {
	    		_input.addClass('error');
	    		_error.show().text(response.feed);
	    	}
	    }
		);
	};

	var _removeFeed = function(el){
		el.hide();
		jQuery.post(
	    ajaxurl, 
	    {
        'action': 'wp_social_tumblelog_remove_feed',
        'feed': el.find('a').attr('href')
	    }, 
	    function(response){
	    	response = JSON.parse(response);

	    	if(response.code === 200){
	    		el.remove();	    		
	    		_error_primary.hide();
	    	} else {
	    		el.show();
	    		_error_primary.show().text(response.feed);
	    	}
	    }
		);
	};

	var _connect = function(){
		_social_spinner.show();
		jQuery.post(
	    ajaxurl, 
	    {
        'action': 'wp_social_tumblelog_connect',
        'social': _social_select.val(),
        'key': _social_client_key.val(),
        'secret': _social_client_secret.val()
	    }, 
	    function(response){
	    	response = JSON.parse(response);
	    	_social_spinner.hide();
	    	if(response.code === 200){
	    		_social_error.hide();
	    	} else {
	    		_social_error.show().text(response.error);
	    	}
	    }
		);
	};

	var _handlers = function(){
		_input.on('keypress', function(){
			if(event.which === 13){
				_addFeed();
			}
		});
		_button.on('click', _addFeed);
		_list.on('click', '.delete', function(){
			_removeFeed($(this).parent());
		});
		_social_submit.on('click', _connect);
	};

	app.init = function(){
		_assign();
		_handlers();
	}

	return app;
}(WP_SOCIAL_TUMBLELOG || {}, jQuery, window));

jQuery(document).ready(WP_SOCIAL_TUMBLELOG.init);