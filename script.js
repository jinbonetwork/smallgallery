var $container = {};
var $control = {};
var $slideshow = {
	current: null,
	prev: null,
	next: null,
	direction: null,
	animation: null,
	garbage: []
};

function init(){

	// check current entry
	$slideshow.current = jQuery('.entry.current');

	// update browser informations
	update_history();

	// load prev/next entries
	var _next_source = $slideshow.current.attr('data-next_permalink');
	if(_next_source){
		load(_next_source,'next');
	}
	var _prev_source = $slideshow.current.attr('data-prev_permalink');
	if(_prev_source){
		load(_prev_source,'prev');
	}
}

function init_flag($trigger){
	var $target = jQuery($trigger.attr('href'));
	$trigger.flag = $trigger.attr('data-flag');
	console.log($trigger.attr('id')+' => '+$target.attr('id')+' ('+$trigger.flag+')');

	if($trigger.flag=='true'){
		$target.removeClass($trigger.attr('data-flag-false-class')).addClass($trigger.attr('data-flag-true-class'));
	}else{
		$target.removeClass($trigger.attr('data-flag-true-class')).addClass($trigger.attr('data-flag-false-class'));
	}
}

function init_flags(){
	jQuery('[data-flag]').each(function(index){
		init_flag(jQuery(this));
	});
}

function resize(position){
	position = position || '';
    position = 'section.entry'+(position!=''?'.'+position:'');

    var flag = '['+position+']';
    var padding = $smallgallery.padding;
    var padnum = $smallgallery.padding.replace(/(\%|px)$/,'');

    console.log('RESIZE: redrawing '+flag);

	jQuery(position).each(function(index){
		var $entry = jQuery(this);
		var $box = $entry.children('.wrap');
		var $img = $entry.find('.feature img');

		$box.availableWidth = $entry.width();
		$box.availableHeight = $entry.height();

		if(padding.search(/\%$/)>0){
			$box.availableWidth = $box.availableWidth-(($entry.width()/padnum)*2);
			$box.availableHeight = $box.availableHeight-(($entry.height()/padnum)*2);
		}
		if(padding.search(/px$/)>0){
			$box.availableWidth = $box.availableWidth-(padnum*2);
			$box.availableHeight = $box.availableHeight-(padnum*2);
		}

		if($img.length){
			$img.ratio = $img.attr('width')/$img.attr('height');
			$box.ratio = $box.availableWidth/$box.availableHeight;
			if($img.ratio<$box.ratio){
				$box.availableWidth = $img.attr('width')*$box.availableHeight/$img.attr('height');
			}else{
				$box.availableHeight = $img.attr('height')*$box.availableWidth/$img.attr('width');
			}
		}

        console.log('RESIZE: '+flag+' => available resolution is '+$box.availableWidth+'x'+$box.availableHeight);
		$box.css({
			width: $box.availableWidth,
			height: $box.availableHeight
		});
		console.log('RESIZE: '+flag+' => content resolution is '+$box.width()+'x'+$box.height());
	});
}

function load(source,position){
	var flag = '['+position+']';
	console.log('BEGIN: load '+flag+' item -- '+source);

	if(!jQuery('[data-permalink="'+source+'"]').length){
		source_filtered = source+(source.search(/\?/)>0?'&':'?')+'format=json';
		console.log('_SOURCE FILTERED: '+flag+' using '+source+' => '+source_filtered);

		var _markup;
		var _object;

		jQuery.getJSON(source_filtered)
			.done(function(data){
				_markup = jQuery(data.filtered_post).removeClass('current').addClass('fresh '+position);
				switch(position){
					case 'prev':
						_object = _markup.addClass('prev');
						$container.prepend(_object);
					break;
					case 'next':
						_object = _markup.addClass('next');
						$container.append(_object);
					break;
				}
				bind_entry_events(_object.attr('id'));
				console.log('_DATA LOADED: '+flag+' using '+source_filtered+' => #'+data.ID);
			})
			.error(function(data){
				console.log('_ERROR: '+flag+' using '+source+' => '+data);
			})
			.always(function(data){
				console.log('END: load '+flag+' item -- '+source);
				update_control();
				resize(position);
			});
	}else{
		console.log('ABORT: '+flag+' item already exists');
	}
}

function bind_entry_events(id){
	id = id || jQuery('section.entry.format-image.current').attr('id');
	var $this = jQuery('section.entry.format-image#'+id);

	$this.find('.popup_switch a').on('click',function(e){
		e.preventDefault();
		var $trigger = jQuery(this);
		var $content = jQuery($trigger.attr('href')).clone();
		var $popup;

		$content.attr('id',$content.attr('id')+'-content');
		$popup = jQuery('<div></div>').append($content).html();

		jQuery.fancybox.open({
			type: 'html',
			width: 800,
			height: 600,
			content: $popup
		});
	});
}

function update_control(){
	var _prev_permalink = $slideshow.current.attr('data-prev_permalink');
	var _next_permalink = $slideshow.current.attr('data-next_permalink');

	// update control buttons
	if($control.prev.find('a').attr('href')!=_prev_permalink){
		$control.prev.find('a').attr('href',_prev_permalink);
		console.log('CONTROL: [prev] button updated => '+_prev_permalink);
	}
	if($control.next.find('a').attr('href')!=_next_permalink){
		$control.next.find('a').attr('href',_next_permalink);
		console.log('CONTROL: [next] button updated => '+_next_permalink);
	}

	// check prev/next entries and enable control buttons
	$slideshow.prev = jQuery('[data-permalink="'+_prev_permalink+'"]');
	$slideshow.next = jQuery('[data-permalink="'+_next_permalink+'"]');
	if($slideshow.prev.length){
		console.log('ENTRY: new [prev] => #'+$slideshow.prev.attr('data-ID'));

		$control.prev.removeClass('disabled');
		console.log('CONTROL: [prev] button enabled');
	}
	if($slideshow.next.length){
		console.log('ENTRY: new [next] => #'+$slideshow.next.attr('data-ID'));

		$control.next.removeClass('disabled');
		console.log('CONTROL: [next] button enabled');
	}
}

function update_history(){
	if(!$slideshow.current.attr('data-permalink')){
		return;
	}

	var currentURL = document.location.href;
	var historyHTML = '<!DOCTYPE html><html>'+jQuery('html').html()+'</html>';
	var historyTitle = $slideshow.current.attr('data-title')+$smallgallery.title.separator+$smallgallery.title.text;
	var historyURL = $slideshow.current.attr('data-permalink');

	console.log('HISTORY: check '+currentURL+' (current) => '+historyURL+' (update)');
	if(currentURL!=historyURL){
		window.history.pushState(
			{
				'html': historyHTML,
				'pageTitle': historyTitle
			},
			historyTitle,
			historyURL
		);
		document.title = historyTitle; // fallback
		console.log('HISTORY: (updated) '+historyTitle+' -- '+currentURL+' => '+historyURL);
	}else{
		console.log('HISTORY: (unchanged) '+historyTitle+' -- '+currentURL+' => '+historyURL);
	}
}

function cleanup(){
	console.log('CLEANUP: que => '+$slideshow.garbage);
	if($slideshow.garbage.length){
		for(index in $slideshow.garbage){
			var $ID = $slideshow.garbage[index];
			jQuery('.entry[data-ID="'+$ID+'"]').remove();
			console.log('CLEANUP: #'+$ID+' removed');
		}
		$slideshow.garbage = [];
	}
}

function fullscreen(flag){
	var element = document.documentElement;
	if(flag){
		if(element.requestFullscreen) {
			element.requestFullscreen();
		} else if(element.mozRequestFullScreen) {
			element.mozRequestFullScreen();
		} else if(element.webkitRequestFullscreen) {
			element.webkitRequestFullscreen();
		} else if(element.msRequestFullscreen) {
			element.msRequestFullscreen();
		}
	}else{
		if(document.exitFullscreen) {
			document.exitFullscreen();
		} else if(document.mozCancelFullScreen) {
			document.mozCancelFullScreen();
		} else if(document.webkitExitFullscreen) {
			document.webkitExitFullscreen();
		}
	}
}

function fullscreenchange(){
	var $trigger = jQuery('#toggle-fullscreen');

	if(is_fullscreen()){
		$trigger.attr('data-flag','true');
	}else{
		$trigger.attr('data-flag','false');
	}
	init_flag($trigger);
}

function is_fullscreen(){
	//var is_fullscreen = window.fullScreenApi.isFullScreen();
	var is_fullscreen = !window.screenTop&&!window.screenY; // fallback

	return is_fullscreen;
}
	

jQuery(document).ready(function(e){
	$container = jQuery('#container');
	$control = jQuery('#control');
	$control.prev = $control.find('li.prev');
	$control.next = $control.find('li.next');

	jQuery('#control a').on('click',function(e){
		e.preventDefault();
		var $trigger = jQuery(this);
		var $box = $trigger.closest('li');
		var position;
		var _current;
		var _prev;
		var _next;
		var _garbage;

		if($box.hasClass('disabled')){
			return;
		}else{
			// disable controls to prevent overhead
			$control.prev.addClass('disabled');
			console.log('CONTROL: [prev] button disabled');
			$control.next.addClass('disabled');
			console.log('CONTROL: [prev] button disabled');

			// check trigger position
			position = $box.hasClass('prev')?'prev':position;
			position = $box.hasClass('next')?'next':position;

			// determine things to do
			$slideshow.direction = position;
			_current = $slideshow[position];
			switch(position){
				case 'prev':
					_next = $slideshow.current;
					$slideshow.animation = $smallgallery.animation.name[$slideshow.current.attr('data-animation')];
					_garbage = $slideshow.next.addClass('expired').attr('data-ID');
				break;
				case 'next':
					_prev = $slideshow.current;
					$slideshow.animation = $smallgallery.animation.name[$slideshow.next.attr('data-animation')];
					_garbage = $slideshow.prev.addClass('expired').attr('data-ID');
				break;
				default:
					_garbage = null;
				break;
			}

			// do animation
			$container.attr('data-animation',$slideshow.animation);

			// garbage collection
			if(_garbage){
				$slideshow.garbage.push(_garbage);
				console.log('CLEANUP QUE: #'+_garbage+' will be deleted');
				setTimeout(function(){cleanup();},$smallgallery.animation.duration);
			}

			// reset classes
			$slideshow.current = _current.removeClass('current prev next fresh').addClass('current');
			if(_next){
				$slideshow.next = _next.removeClass('current prev next').addClass('next');
			}
			if(_prev){
				$slideshow.prev = _prev.removeClass('current prev next').addClass('prev');
			}

			// init
			init();
		} // endif
	});

	jQuery(window).on('resize',function(e){
		resize();
	});

	jQuery('.toggler').on('click',function(e){
		e.preventDefault();
		var $trigger = jQuery(this);
		var flag = null;

		if($trigger.attr('data-flag')=='true'){
			$trigger.attr('data-flag','false');
			flag = false;
		}else{
			$trigger.attr('data-flag','true');
			flag = true;
		}
		init_flag($trigger);

		if($trigger.attr('id')=='toggle-fullscreen'){
			fullscreen(flag);
		}
	});

	jQuery(document).on('fullscreenchange',function(e){fullscreenchange();});
	jQuery(document).on('webkitfullscreenchange',function(e){fullscreenchange();});
	jQuery(document).on('mozfullscreenchange',function(e){fullscreenchange();});

	jQuery('#container').swipe({
		swipe:function(e,direction,distance,duration,fingerCount,fingerData){
			switch(direction){
				case 'right':
					jQuery('#control .prev a').click();
				break;
				case 'left':
					jQuery('#control .next a').click();
				break;
			}
		},
		threadhold: 75
	});
    jQuery(document).keydown(function(e){
        var domain = e.target;
        var pressed = e.charCode || e.keyCode || e.which;
        var is_popup = jQuery('.popup-content').length?true:false;

        if(domain=='input'||domain=='textarea'){
            return;
        }

        console.log('KEY PRESSED: #'+pressed);

        switch(pressed){
            case 37: // left
                if(!is_popup){
                    jQuery('#control .prev a').click();
                }
            break;
            case 39: // right
                if(!is_popup){
                    jQuery('#control .next a').click();
                }
            break;
            case 38: // up
            break;
            case 40: // down
            break;
            case 70: // f
                jQuery('#toggle-fullscreen').click();
            break;
            case 80: // p
                if(is_popup){
                    jQuery.fancybox.close();
                }else{
                    jQuery('section.entry.current .popup_switch a').click();
                }
            break;
            case 27: // esc
                return false;
            break;
        }
    });

	if(is_fullscreen()){
		jQuery('#toggle-fullscreen').attr('data-flag','true');
	}else{
		jQuery('#toggle-fullscreen').attr('data-flag','false');
	}

	resize();
	bind_entry_events();
	init_flags();
	init();
});
