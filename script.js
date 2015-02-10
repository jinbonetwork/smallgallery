console.log = function(){};

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
	var _prev_source = $slideshow.current.attr('data-prev_permalink');
	load(_next_source,'next');
	load(_prev_source,'prev');
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

function resize(){
	/*
	$slideshow.style = {
		entry: {
			width: $container.width(),
			height: $container.height()
		}
	};
	jQuery('section.entry').css($slideshow.style.entry);
	if($slideshow.next){
		$slideshow.next.css($slideshow.style.cache);
	}
	if($slideshow.prev){
		$slideshow.prev.css($slideshow.style.cache);
	}
	*/

	jQuery('section.entry').each(function(index){
		var $entry = jQuery(this);
		var $box = $entry.children('.wrap');
		var $img = $entry.find('.feature img');

		$box.availableWidth = $entry.width();
		$box.availableHeight = $entry.height();

		var padding = $smallgallery.padding.replace(/(\%|px)$/,'');

		if($smallgallery.padding.search(/\%$/)>0){
			$box.availableWidth = $box.availableWidth-(($entry.width()/padding)*2);
			$box.availableHeight = $box.availableHeight-(($entry.height()/padding)*2);
		}
		if($smallgallery.padding.search(/px$/)>0){
			$box.availableWidth = $box.availableWidth-(padding*2);
			$box.availableHeight = $box.availableHeight-(padding*2);
		}

		$box.css({
			width: $box.availableWidth,
			height: $box.availableHeight
		});

		if($img.length){
			if($img.attr('width')/$img.attr('height')<$box.width()/$box.height()){
				$img.css({
					width: 'auto',
					height: $box.height()
				});
			}else{
				$img.css({
					width: $box.width(),
					height: 'auto'
				});
			}
			$box.width($img.width());
		}
	});
}

function load(source,position){
	var flag = '['+position+']';
	console.log('BEGIN: load '+flag+' item -- '+source);

	if(!jQuery('[data-permalink="'+source+'"]').length){
		source_filtered = source+(source.search(/\?/)>0?'&':'?')+'format=json';
		console.log('_SOURCE FILTERED: '+flag+' using '+source+' => '+source_filtered);

		var _markup;
		var _prev;
		var _next;

		jQuery.getJSON(source_filtered)
			.done(function(data){
				_markup = jQuery(data.filtered_post).removeClass('current').addClass('fresh '+position);
				switch(position){
					case 'prev':
						_prev = _markup.addClass('prev');
						$container.prepend(_prev);
					break;
					case 'next':
						_next = _markup.addClass('next');
						$container.append(_next);
					break;
				}
				console.log('_DATA LOADED: '+flag+' using '+source_filtered+' => #'+data.ID);
			})
			.error(function(data){
				console.log('_ERROR: '+flag+' using '+source+' => '+data);
			})
			.always(function(data){
				console.log('END: load '+flag+' item -- '+source);
				update_control();
				resize();
			});
	}else{
		console.log('ABORT: '+flag+' item already exists');
	}
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
	var historyHTML = '<!DOCTYPE html><html>'+jQuery('html').html()+'</html>';
	var historyTitle = $slideshow.current.attr('data-title')+$smallgallery.title.separator+$smallgallery.title.text;
	var historyURL = $slideshow.current.attr('data-permalink');

	window.history.pushState(
		{
			'html': historyHTML,
			'pageTitle': historyTitle
		},
		historyTitle,
		historyURL
	);
	document.title = historyTitle; // fallback

	console.log('HISTORY: '+historyTitle+' => '+historyURL);
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

	jQuery('#container').on('swipeleft',function(e){
		jQuery('#control .prev a').click();
	});

	jQuery('#container').on('swiperight',function(e){
		jQuery('#control .next a').click();
	});
	
	jQuery(window).on('resize',function(e){
		resize();
	});

	jQuery('.toggler').on('click',function(e){
		e.preventDefault();
		var $trigger = jQuery(this);
		$trigger.flag = $trigger.attr('data-flag');
		if($trigger.flag=='true'){
			$trigger.attr('data-flag','false');
		}else{
			$trigger.attr('data-flag','true');
		}
		init_flag($trigger);
	});

	init_flags();
	init();
});
