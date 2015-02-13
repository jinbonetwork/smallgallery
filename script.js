var $container = {};
var $control = {};
var $slideshow = {
	position: {
		current: null,
		pprev: null,
		prev: null,
		next: null,
		nnext: null,
	},
	direction: null,
	animation: null,
	garbage: []
};

function init(){

	// check current entry
	$slideshow.position.current = jQuery('.entry.current');

	// update browser informations
	update_history();

	// load prev/next entries
	var _next_source = $slideshow.position.current.attr('data-next_permalink');
	var _prev_source = $slideshow.position.current.attr('data-prev_permalink');
	var _nnext_source = $slideshow.position.current.attr('data-nnext_permalink');
	var _pprev_source = $slideshow.position.current.attr('data-pprev_permalink');

	if(_next_source){
		load(_next_source,'next');
	}
	if(_prev_source){
		load(_prev_source,'prev');
	}
	if(_nnext_source){
		load(_nnext_source,'nnext');
	}
	if(_pprev_source){
		load(_pprev_source,'pprev');
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

function resize(position,context){
	position = position || '';
    position = 'section.entry'+(position!=''?'.'+position:'');
    context = context || '';

    var flag = '['+position+']';
    var padding = $smallgallery.padding;
    var padnum = $smallgallery.padding.replace(/(\%|px)$/,'');

    console.log('RESIZE: redrawing '+flag+(context?' ('+context+')':''));

	jQuery(position).each(function(index){
		var $entry = jQuery(this);
		var $box = $entry.children('.wrap');
		var $feature = $box.find('.feature');
		var $img = $feature.find('img');

		// get default size
		$box.availableWidth = $entry.width();
		$box.availableHeight = $entry.height();

		// adjust size with given padding value
		if(padding.search(/\%$/)>0){
			$box.availableWidth = $box.availableWidth-(($entry.width()/padnum)*2);
			$box.availableHeight = $box.availableHeight-(($entry.height()/padnum)*2);
		}
		if(padding.search(/px$/)>0){
			$box.availableWidth = $box.availableWidth-(padnum*2);
			$box.availableHeight = $box.availableHeight-(padnum*2);
		}

		// consider feature image size
		if($img.length){
			console.log('RESIZE: '+flag+' has a featured image => '+$feature.width()+'x'+$feature.height());

			// check natural size of image
			$feature.givenWidth = $feature.width();
			$feature.givenHeight = $feature.height();

			// check aspect ratios of image and container
			$feature.ratio = $feature.givenWidth/$feature.givenHeight;
			$box.ratio = $box.availableWidth/$box.availableHeight;

			if($feature.ratio>$box.ratio){
				// if image is wider, limit it's width
				$feature.availableWidth = $box.availableWidth;
				$feature.availableHeight = $feature.givenHeight*$feature.availableWidth/$feature.givenWidth;
			}else{
				// if container is wider, limit image's height
				$feature.availableHeight = $box.availableHeight;
				$feature.availableWidth = $feature.givenWidth*$feature.availableHeight/$feature.givenHeight;
			}
			$box.availableWidth = $feature.availableWidth;
			$box.availableHeight = $feature.availableHeight;
		}

		// apply calculated size
        console.log('RESIZE: '+flag+' => available resolution is '+$box.availableWidth+'x'+$box.availableHeight);
		$box.css({
			width: $box.availableWidth,
			//height: $img.length?'auto':$box.availableHeight,
			height: 'auto',
			maxHeight: $img.length?$entry.height():$box.availableHeight
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
		jQuery.getJSON(source_filtered)
			.done(function(data){
				console.log('_DATA LOADED: '+flag+' using '+source_filtered+' => #'+data.ID);

				_markup = jQuery(data.filtered_post).removeClass('current').addClass('fresh '+position);
				switch(position){
					case 'pprev':
					case 'prev':
						$container.prepend(_markup);
					break;
					case 'nnext':
					case 'next':
						$container.append(_markup);
					break;
				}
				bind_entry_events(_markup.attr('id'));
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
	var $this = jQuery('#'+id);

	$this.imagesLoaded(function(e){
		resize($this.attr('data-position'),'imagesLoaded');
	});

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
	var _pprev_permalink = $slideshow.position.current.attr('data-pprev_permalink');
	var _prev_permalink = $slideshow.position.current.attr('data-prev_permalink');
	var _next_permalink = $slideshow.position.current.attr('data-next_permalink');
	var _nnext_permalink = $slideshow.position.current.attr('data-nnext_permalink');

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
	$slideshow.position.next = jQuery('[data-permalink="'+_next_permalink+'"]');
	if($slideshow.position.next.length){
		console.log('ENTRY: new [next] => #'+$slideshow.position.next.attr('data-ID'));

		$control.next.removeClass('disabled');
		console.log('CONTROL: [next] button enabled');
	}

	$slideshow.position.prev = jQuery('[data-permalink="'+_prev_permalink+'"]');
	if($slideshow.position.prev.length){
		console.log('ENTRY: new [prev] => #'+$slideshow.position.prev.attr('data-ID'));

		$control.prev.removeClass('disabled');
		console.log('CONTROL: [prev] button enabled');
	}

	$slideshow.position.nnext = jQuery('[data-permalink="'+_nnext_permalink+'"]');
	if($slideshow.position.nnext.length){
		console.log('ENTRY: new [nnext] => #'+$slideshow.position.nnext.attr('data-ID'));
	}

	$slideshow.position.pprev = jQuery('[data-permalink="'+_pprev_permalink+'"]');
	if($slideshow.position.pprev.length){
		console.log('ENTRY: new [pprev] => #'+$slideshow.position.pprev.attr('data-ID'));
	}
}

function update_history(){
	if(!$slideshow.position.current.attr('data-permalink')){
		return;
	}

	var currentURL = document.location.href;
	var historyHTML = '<!DOCTYPE html><html>'+jQuery('html').html()+'</html>';
	var historyTitle = $slideshow.position.current.attr('data-title')+$smallgallery.title.separator+$smallgallery.title.text;
	var historyURL = $slideshow.position.current.attr('data-permalink');

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
		var position = $box.attr('data-position');
		var _current;
		var _next;
		var _prev;
		var _nnext;
		var _pprev;
		var _garbage;

		if($box.hasClass('disabled')){
			return;
		}else{
			// disable controls to prevent overhead
			$control.prev.addClass('disabled');
			console.log('CONTROL: [prev] button disabled');
			$control.next.addClass('disabled');
			console.log('CONTROL: [prev] button disabled');

			// redefine positions and determine things to do
			$slideshow.direction = position;
			_current = $slideshow.position[position];
			switch(position){
				case 'prev':
					$slideshow.animation = $smallgallery.animation.name[$slideshow.position.current.attr('data-animation')];
					_prev = $slideshow.position.pprev;
					_nnext = $slideshow.position.next;
					_next = $slideshow.position.current;
					_garbage = $slideshow.position.nnext.addClass('expired').attr('data-ID');
				break;
				case 'next':
					$slideshow.animation = $smallgallery.animation.name[$slideshow.position.next.attr('data-animation')];
					_next = $slideshow.position.nnext;
					_pprev = $slideshow.position.prev;
					_prev = $slideshow.position.current;
					_garbage = $slideshow.position.pprev.addClass('expired').attr('data-ID');
				break;
				default:
					_garbage = null;
				break;
			}
			for(pos in $slideshow.position){
				$slideshow.position[pos].attr('data-position',pos);
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
			var classes_to_remove = 'pprev prev current next nnext';
			$slideshow.position.current = _current.removeClass(classes_to_remove+' fresh').addClass('current');
			if(_next){
				$slideshow.position.next = _next.removeClass(classes_to_remove).addClass('next');
			}
			if(_prev){
				$slideshow.position.prev = _prev.removeClass(classes_to_remove).addClass('prev');
			}
			if(_nnext){
				$slideshow.position.nnext = _nnext.removeClass(classes_to_remove).addClass('nnext');
			}
			if(_pprev){
				$slideshow.position.pprev = _pprev.removeClass(classes_to_remove).addClass('pprev');
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
				default:
					return;
				break;
			}
		},
		allowPageScroll: 'vertical',
		threadhold: 75
	});
    jQuery(document).keydown(function(e){
        var domain = e.target;
        var pressed = e.charCode || e.keyCode || e.which;
        var is_popup = jQuery('.fancybox-overlay').length?true:false;

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

	jQuery.fancybox.open({
		type: 'html',
		wrapCSS: 'intro',
		content: jQuery('#intro-container').html(),
		scrolling: 'no'

	});
});
