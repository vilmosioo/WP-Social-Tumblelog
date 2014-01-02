'use strict';

var WP_SOCIAL_TUMBLELOG = (function (app, $, window) {

	var _list , _button, _spinner, _error, _input;

	var _assign = function(){
		_list = $('#WPSocialTumblelog_options_list');
		_button = $('#WPSocialTumblelog_options_add_feed');
		_spinner = $('#WPSocialTumblelog_options_spinner');
		_error = $('#WPSocialTumblelog_options_error');
		_input = $('#WPSocialTumblelog_options_feed');
	};

	var _addFeed = function(){
		_spinner.show();
		jQuery.post(
	    ajaxurl, 
	    {
        'action': 'wp_social_tumblelog',
        'feed': _input.val()
	    }, 
	    function(response){
	    	response = JSON.parse(response);

	    	_spinner.hide();

	    	if(response.code === 200){
	    		_error.hide();
	    		_input.removeClass('error');
	    		_list.append($("<li><a target='_blank' href='" + response.feed + "'>" + response.feed + "</a></li>"));
	    	} else {
	    		_error.show();
	    		_input.addClass('error');
	    		_error.text(response.feed);
	    	}
	    }
		);
	
	};

	var _handlers = function(){
		_input.bind('keypress', function(){
			if(event.which === 13){
				event.stopPropagation();
				_addFeed();
				return false;
			}
		});
		_button.click(_addFeed);
	};

	app.init = function(){
		_assign();
		_handlers();
	}

	return app;
}(WP_SOCIAL_TUMBLELOG || {}, jQuery, window));

jQuery(document).ready(WP_SOCIAL_TUMBLELOG.init);