'use strict';

var WP_SOCIAL_TUMBLELOG = (function (app, $, window) {

	var _list , _button, _spinner, _error, _input, _delete, _error_primary;

	var _assign = function(){
		_list = $('#wpsocial-tumblelog-options-list');
		_button = $('#wpsocial-tumblelog-options-add_feed');
		_spinner = $('#wpsocial-tumblelog-options-spinner');
		_error = $('#wpsocial-tumblelog-options-error');
		_input = $('#wpsocial-tumblelog-options-feed');
		_delete = $('#wpsocial-tumblelog-options-list .delete');
		_error_primary = $('#wpsocial-tumblelog-options-error-primary');
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
	    		var _new = $("<li style='display:none;'><a target='_blank' href='" + response.feed + "'>" + response.feed + "</a><i class='fa fa-minus-circle delete'></i></li>");
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
        'feed': el.text()
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
	};

	app.init = function(){
		_assign();
		_handlers();
	}

	return app;
}(WP_SOCIAL_TUMBLELOG || {}, jQuery, window));

jQuery(document).ready(WP_SOCIAL_TUMBLELOG.init);