jQuery(document).ready(function(e){
	var $slideshow = {};
	var $container = jQuery('#container');
	var $control = jQuery('#control');
		$control.prev = $control.find('li.prev');
		$control.next = $control.find('li.next');

	function init(){
		// reset environmental settings
		$slideshow = {
			current: null,
			prev: null,
			next: null,
			direction: null,
			animation: null,
			garbage: []
		};

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
					_markup = jQuery(data.filtered_post).removeClass('current').addClass(position);
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
		if($slideshow.garbage.length){
			for(index in $slideshow.garbage){
				var $ID = $slideshow.garbage[index];
				jQuery('.entry[data-ID="'+$ID+'"]').remove();
				console.log('CLEANUP: #'+$ID+' removed');
			}
			$slideshow.garbage = [];
		}
	}

	jQuery('#control a').on('click',function(e){
		e.preventDefault();
		var $trigger = jQuery(this);
		var $box = $trigger.closest('li');
		var position;
		var _current;
		var _prev;
		var _next;

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
				$slideshow.animation = $slideshow.current.attr('data-animation');
				$slideshow.garbage.push($slideshow.next.attr('data-ID'));
			break;
			case 'next':
				_prev = $slideshow.current;
				$slideshow.animation = $slideshow.next.attr('data-animation');
				$slideshow.garbage.push($slideshow.prev.attr('data-ID'));
			break;
		}

		// do animation

		// reset classes
		$slideshow.current = _current.removeClass('current prev next').addClass('current');
		if(_next){
			$slideshow.next = _next.removeClass('current prev next').addClass('next');
		}
		if(_prev){
			$slideshow.prev = _prev.removeClass('current prev next').addClass('prev');
		}

		// garbage collection
		cleanup();

		// init
		init();
	});
	
	init();
});
