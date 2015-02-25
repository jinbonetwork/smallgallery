var $container = {};
var $control = {};
var $slideshow = {
	domain: location.protocol+'//'+location.hostname+(location.port ? ':'+location.port: ''),
	open_help: true,
	position: {
		current: null,
		pprev: null,
		prev: null,
		next: null,
		nnext: null,
	},
	direction: null,
	animation: null,
	popup: {
		width: 800,
		height: 600,
		autoSize: false,
		href: false,
		content: false,
		scrolling: 'no',
		iframe: {
			preload: false
		}
	},
	garbage: []
};

function init(){

	// check current entry
	$slideshow.position.current = jQuery('.entry.current');

	// update browser informations
	update_page_meta();

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

function toggle_flag($trigger,flag){
	flag = flag || null;

	if(flag==null){
		flag = $trigger.attr('data-flag')!='true'?true:false;
	}

	if(flag){
		$trigger.attr('data-flag','true');
	}else{
		$trigger.attr('data-flag','false');
	}

	init_flag($trigger);
	return flag;
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
		var $resolution = jQuery(document).width()+'x'+jQuery(document).height();
		var $entry = jQuery(this);
		var $box = $entry.children('.wrap');
		var $feature = $box.find('.feature');
		var $img = $feature.find('img');

		// get default size
		$entry.availableWidth = $entry.width();
		$entry.availableHeight = $entry.height();
		$box.availableWidth = $entry.availableWidth;
		$box.availableHeight = $entry.availableHeight;
		console.log('RESIZE: '+flag+' is in the box => '+$box.availableWidth+'x'+$box.availableHeight);

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
			// check natural size of image
			$feature.givenWidth = $feature.width();
			$feature.givenHeight = $feature.height();
			console.log('RESIZE: '+flag+' has a featured image => '+$feature.givenWidth+'x'+$feature.givenHeight);

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
        console.log('RESIZE: '+flag+' => available resolution is '+$box.availableWidth+'x'+$box.availableHeight);

		// calculate size
		$box.actualWidth = $box.availableWidth;
		$box.actualHeight = $box.availableHeight;
        console.log('RESIZE: '+flag+' => actual resolution is '+$box.actualWidth+'x'+$box.actualHeight);

		// apply size
		$box.cssWidth = Math.floor($box.actualWidth);
		$box.cssHeight = Math.floor($img.length?$entry.availableHeight:$box.actualHeight);
		$box.css({
			width: $box.cssWidth,
			height: 'auto',
			maxHeight: $box.cssHeight
		});

		/*
		// update perfect-scrollbar
		if($entry.is('.page')||$entry.is('.archive')){
			$entry.perfectScrollbar();
		}
		if($entry.is('.post')){
			$entry.children('.wrap').perfectScrollbar();
		}
		*/

		console.log('RESIZE: '+flag+' => content resolution is '+$box.width()+'x'+$box.height());
	});

	fullscreenchange(); // over do for MSIE
}

function build_url(url,attributes){
	attributes = attributes || {};

	var filtered = url;
	var index = 0;
	var glue = '';
	for(key in attributes){
		index ++;
		glue = index>1&&url.search(/\?/)?'&':'?';
		value = attributes[key];
		filtered = filtered+glue+key+'='+value;
	}

	return filtered;
}

function load(source,position){
	var flag = '['+position+']';
	console.log('BEGIN: load '+flag+' item -- '+source);

	if(!jQuery('[data-permalink="'+source+'"]').length){
		source_filtered = build_url(source,{format:'json'});
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
	id = id || jQuery('section.entry.current').attr('id');
	var $entry = jQuery('#'+id);

	$entry.imagesLoaded(function(e){
		resize($entry.attr('data-position'),'imagesLoaded');
	});

	$entry.find('.popup_link a').on('click',function(e){
		e.preventDefault();
		var $trigger = jQuery(this);
		var $content = jQuery($trigger.attr('href')).clone();
		var $popup;
		var $options;
		if(!$content.length){
			return;
		}

		$content.attr('id',$content.attr('id')+'-content');
		$popup = jQuery('<div></div>').append($content).html();
		$options = jQuery.extend({},$slideshow.popup,{
			type: 'html',
			wrapCSS: 'content-popup',
			content: $popup,
			afterLoad:function(){
				jQuery('.fancybox-inner').scrollTop($content.offset().top);
			}
		});
		jQuery.fancybox.open($options);
	});

	$entry.find('.comment_link a').on('click',function(e){
		e.preventDefault();
		e.stopPropagation();
		var $trigger = jQuery(this);
		var $options;

		$options = jQuery.extend({},$slideshow.popup,{
			type: 'iframe',
			wrapCSS: 'comment-popup',
			href: $trigger.attr('href'),
			afterClose: function(){
				location.reload();
			}
		});
		jQuery.fancybox.open($options);
	});

	$entry.find('.social a').on('click',function(e){
		e.preventDefault();
		e.stopPropagation();
		var $trigger = jQuery(this);
		open_social_link($trigger.attr('href'));
	});

	/*
	if($entry.is('.page')||$entry.is('.archive')){
		$entry.perfectScrollbar();
	}
	if($entry.is('.post')){
		$entry.children('.wrap').perfectScrollbar();
	}
	*/

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

function update_page_meta(){
	if(!$slideshow.position.current.attr('data-permalink')){
		return;
	}

	// update browser history
	var current = {
		url: document.location.href
	};
	var history = {
		title: $slideshow.position.current.attr('data-title')+$smallgallery.title.separator+$smallgallery.title.text,
		html: '<!DOCTYPE html><html>'+jQuery('html').html()+'</html>',
		url: $slideshow.position.current.attr('data-permalink')
	};

	console.log('HISTORY: check '+current.url+' (current) => '+history.url+' (update)');
	if(current.url!=history.url){
		window.history.pushState(
			{
				'html': history.html,
				'pageTitle': history.title
			},
			history.title,
			history.url
		);
		document.title = history.title; // fallback
		console.log('HISTORY: (updated) '+history.title+' -- '+current.url+' => '+history.url);
	}else{
		console.log('HISTORY: (unchanged) '+history.title+' -- '+current.url+' => '+history.url);
	}

	// update social metatags
	var social = {
		og: {},
		twitter: {},
		format: $slideshow.position.current.attr('data-format'),
		title: history.title,
		url: encodeURI(history.url),
		description: $slideshow.position.current.attr('data-description'),
		image: $slideshow.position.current.find('.feature img').length?encodeURI($slideshow.position.current.find('.feature img').attr('src')):'',
		time: {
			published: $slideshow.position.current.attr('data-time-published'),
			modified: $slideshow.position.current.attr('data-time-modified')
		},
		author: {
			url: $slideshow.position.current.attr('data-author-url')
		}
	};
	switch(social.format){
		case 'image':
			social.og.type = 'article';
			social.twitter.card = 'photo';
			social.twitter.imagetag = 'image';
		break;
		default:
			social.og.type = 'article';
			social.twitter.card = 'summary';
			social.twitter.imagetag = 'image:src';
		break;
	}

	// facebook open graph
	jQuery('meta[property="og:type"]').attr('content',social.og.type);
	jQuery('meta[property="og:title"]').attr('content',social.title);
	jQuery('meta[property="og:url"]').attr('content',social.url);
	jQuery('meta[property="og:description"]').attr('content',social.description);
	jQuery('meta[property="og:image"]').attr('content',social.image);

	jQuery('meta[property="article:published_time"]').attr('content',social.time.published);
	jQuery('meta[property="article:modified_time"]').attr('content',social.time.modified);
	jQuery('meta[property="article:author"]').attr('content',social.author.url);

	// twitter cards
	jQuery('meta[name="twitter:card"]').attr('content',social.twitter.card);
	jQuery('meta[name="twitter:description"]').attr('content',social.description);
	jQuery('meta[name="twitter:image"]').remove();
	jQuery('meta[name="twitter:image:src"]').remove();

	jQuery('<meta name="twitter:'+social.twitter.imagetag+'" content="'+social.image+'">').appendTo('head');
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
	flag = flag || !is_fullscreen();
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
		} else if(document.msExitFullscreen){
			document.msExitFullscreen();
		}
	}
}

function fullscreenchange(){
	var $trigger = jQuery('#toggle-fullscreen');
	var flag = is_fullscreen();
	toggle_flag($trigger,flag);
}

function is_fullscreen(){
	var is_fullscreen;

	if(typeof document.fullscreen!='undefined'){
		is_fullscreen = document.fullscreen;
	}else if(typeof document.mozFullScreen!='undefined'){
		is_fullscreen = document.mozFullScreen;
	}else if(typeof document.webkitIsFullScreen!='undefined'){
		is_fullscreen = document.webkitIsFullScreen;
	}else{
		is_fullscreen = Math.abs(screen.width-window.innerWidth)<10?true:false;
	}

	return is_fullscreen;
}
	
function open_help(flag){
	var $options;

	if(typeof flag=='undefined'){
		flag = jQuery.cookie('open_help');
		if(typeof flag == 'undefined'){
			jQuery.cookie('open_help','0',{expires:30,path:'/'});
			flag = $slideshow.open_help;
		}
	}
	if(typeof flag=='string'){
		flag = parseInt(flag)?true:false;
	}

	if(flag){
		$options = jQuery.extend({},$options,{
			type: 'html',
			wrapCSS: 'help-popup',
			content: jQuery('#help-container').html(),
		});
		jQuery.fancybox.open($options);
	}

	//jQuery.removeCookie('open_help',{path:'/'});
}

function open_social_link(url){
	var name = '_blank';
	var options = 'width='+$slideshow.popup.width+',height='+$slideshow.popup.height+',menubar=no,resizable=yes,scrollable=no,status=no,titlebar=yes,toolbar=no';
	window.open(url,name,options);
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

	/* messed up with MSIE
	jQuery(document).on('fullscreenchange webkitfullscreenchange mozfullscreenchange MSFullscreenChange',function(e){
		fullscreenchange();
	});
	*/

	jQuery('.toggler').on('click',function(e){
		e.preventDefault();
		var $trigger = jQuery(this);

		switch($trigger.attr('id')){
			case 'toggle-navigation':
				toggle_flag($trigger);
			break;
			case 'toggle-help':
				open_help(true);
			break;
			case 'toggle-fullscreen':
				fullscreen();
			break;
		}
	});

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
        var context = e.target;
        var pressed = e.charCode || e.keyCode || e.which;
        var is_popup = jQuery('.fancybox-overlay').length?true:false;

        if(context=='input'||context=='textarea'){
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
                fullscreen();
            break;
            case 67: // c
                if(!is_popup){
                    jQuery('section.entry.current .comment_link a').click();
                }
            break;
            case 80: // p
                if(!is_popup){
                    jQuery('section.entry.current .popup_link a').click();
                }
            break;
            case 72: // h
				if(!is_popup){
					open_help(true);
				}
            break;
            case 27: // esc
                return false;
            break;
        }
    });

	/*
    jQuery('a').on('click',function(e){
    	var $trigger = jQuery(this);
    	var $href = $trigger.attr('href');

    	if(
    		!$trigger.attr('class')&&!$trigger.attr('id')
    		&& !$trigger.parent().attr('class')&&!$trigger.parent().attr('id')
    		&& ($href.search(/^\//)||$href.search($slideshow.domain))
    		($href.search(/^\//)||$href.search($slideshow.domain))
    	){
			e.preventDefault();
			alert('ajax');
			window.location = $trigger.attr('href');
		}
    });
	*/

	if(is_fullscreen()){
		jQuery('#toggle-fullscreen').attr('data-flag','true');
	}else{
		jQuery('#toggle-fullscreen').attr('data-flag','false');
	}

	resize();
	bind_entry_events();
	init_flags();
	init();

	if($slideshow.open_help){
		open_help();
	}
});
