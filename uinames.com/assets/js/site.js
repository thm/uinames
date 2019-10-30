function capitalize(string) {
	return string.charAt(0).toUpperCase() + string.slice(1);
}

function selectThis(elem) {
	var range, selection,
		elem = (elem instanceof Element) ? elem : this;
	if (document.body.createTextRange) {
		range = document.body.createTextRange();
		range.moveToElementText(elem);
		range.select();
	} else if (window.getSelection) {
		selection = window.getSelection();
		range = document.createRange();
		range.selectNodeContents(elem);
		selection.removeAllRanges();
		selection.addRange(range);
	}
}

function randomNum(min, max) {
	return Math.floor(Math.random() * (max - min + 1)) + min;
}

(function() {
	var $body = $('body'),
		$genderToggles = $('#genderSelect'),
		$regionToggle = $('.icon.region:first'),
		$bulkToggle = $('#bulk .icon'),
		$infoToggle = $('.info:first'),
		$regionPopup = $('#region'),
		$searchInput = $regionPopup.find('input:first'),
		$availableRegions = $regionPopup.find('li'),
		$dataContainer = $('#data'),
		$tips = $('#help p');
	
	// MOUSE DETECTION
	$body.on('mousedown keydown', function(e) {
		$body.toggleClass('mouseDetected', e.type == 'mousedown');
	});
	
	// REGION SWITCHER
	$availableRegions.click(function() {
		var $this = $(this);
		
		$availableRegions.removeClass('active');
		$this.addClass('active');
		
		// switch to new region
		var regionName = $this.find('.region-label:first').html();
		localStorage.setItem('region', regionName);
		
		// update the flag in the region icon
		var randName = $this.attr('id') == 'region-0' ? 1 : 0;
		$regionToggle.find('img')
			.attr('src', 'assets/img/' + (randName ? 'flag-random.gif' : 'flags.png'))
			.css({
				'height': randName ? '20px' : 'auto',
				'top': randName ? '0' : ('-' + ($this.attr('id').replace('region-','')-1)*20 + 'px'),
				'border-radius': randName ? '' : '0'
			});
		
		// update the textual name in the region icon
		$regionToggle.find('b').innerHTML = regionName + ' selected';
		
		// wait 250ms so the user can see the region being switched
		setTimeout(function() {
			closePopup();
		}, 250);
	});
	
	// select region from local storage if saved
	var storedRegion = localStorage.getItem('region');
	if (storedRegion) {
		$availableRegions.each(function() {
			if (storedRegion == $(this).find('.region-label:first').html()) {
				$(this).click(); return false;
			}
		});
	} else {
		// US is selected by default, uncomment this if we want the icon to update too
		// $availableRegions.filter('.active:first').click();
	}
	
	// CONNECTION TO THE API
	var namesRequested = 0, maxNamesRequested = 25, data, oldRegion, oldGender;
	function getName() {
		// set parameters based on settings
		var region = $regionPopup.find('.active:first .region-label:first').html().toLowerCase().replace(/ /g, '+'),
			gender = $genderToggles.find('.active:first').attr('href'),
			gender = gender.substr(gender.indexOf('#')+1);
		
		// function that injects all the data on the page
		function injectData(data, offset) {
			
			// secondary data
			var printPhoto = '<div id="photo-container"><a href="' + data[offset]['photo'] + '" class="photo" target="_blank"><img src="' + data[offset]['photo'].replace(/http:\/\/uinames\.com\//, '') + '" /></a><a role="button" class="icon refresh active" title="Refresh" aria-label="Refresh" aria-pressed="true"><span class="r1"></span><span class="r2"></span><span class="r3"></span><span class="r4"></span><span class="r5"></span><span class="r6"></span><span class="r7"></span></a></div>',
				printName = '<h1 id="name" class="click-to-select">' + data[offset]['name'] + ' ' + data[offset]['surname'] + '</h1>',
				printData = '';
			
			$.each({
					'👤': capitalize(data[offset]['gender']),
					'🌐': data[offset]['region'],
					'📞': data[offset]['phone'],
					'🎂': data[offset]['birthday']['dmy'],
					'✉️': data[offset]['email'],
					'🔑': data[offset]['password']
				}, function(key, val) {
				printData += '<li>' + key + '&nbsp;&nbsp;<span class="click-to-select">' + val + '</span></li>';
			});
			
			// print all onto page
			$dataContainer.html('<div>' + printPhoto + printName + '<ul class="printData">' + printData + '</ul></div>');
			if (!$body.hasClass('touch-device')) {
				$tips.hide().parent().find('.tip-' + randomNum(1,$tips.length)).show();
			}
			
			// if bulk mode, add the other 24
			if ($bulkToggle.hasClass('active')) {
				$body.addClass('bulk bulkPause'); // 'bulkPause' pauses requests to stop overly excited users
				
				// inject names, genders and regions into page
				var bulkNames = '';
				for (var i = 0; i < maxNamesRequested; i++) {
					bulkNames += '<h1 class="click-to-select">' + data[i]['name'] + ' ' + data[i]['surname'] + '</h1><p><span class="click-to-select">' + capitalize(data[i]['gender']) + '</span> from <span class="click-to-select">' + data[i]['region'] + '</span></p>';
				}
				$dataContainer.html(bulkNames);

				// bulk toggle count-down
				var bulkToggleCache = $bulkToggle.html(),
					count = 10,
					bulkCountDown = setInterval(function() {
						if (count === 0 || !$bulkToggle.hasClass('active')) {
							$body.removeClass('bulkPause');
							$bulkToggle.html(bulkToggleCache).css({lineHeight: ''});
							clearInterval(bulkCountDown);
						} else {
							$bulkToggle.html(count--).css({lineHeight: '237.5%'});
						}
					}, 650);
			} else {
				$body.removeClass('bulk');
			}
			
			// enable any selectable elements
			$('.click-to-select').click(selectThis);
			$('#photo-container .refresh').click(function() {
				getName();
			});
		}
		
		function injectNewData() {
			// get new names from api
			var getJSON = new XMLHttpRequest();
			getJSON.onreadystatechange = function() {
				if (getJSON.readyState == 4 && getJSON.status == 200) {
					data = JSON.parse(getJSON.responseText);
					injectData(data, 0);
				}
			};
			
			// send the request
			getJSON.open('GET', 'api/?ext&amount=' + maxNamesRequested + '&region=' + region + '&gender=' + gender, true);
			getJSON.send();
			
			// reset counter
			namesRequested = 0;
		}
		
		// detect end of road or a change in settings
		if (namesRequested == 0 || namesRequested >= maxNamesRequested || oldRegion != region || oldGender != gender || $bulkToggle.hasClass('active')) {
			injectNewData();
		} else {
			injectData(data, namesRequested);
		}
		
		// update flags
		namesRequested++;
		oldRegion = region;
		oldGender = gender;
	}
	
	// GENDER TOGGLE
	$genderToggles.find('a').click(function(e) {
		e.preventDefault();
		e.stopPropagation();
		
		$genderToggles.find('a').removeClass('active').attr('aria-pressed', 'active');
		$(this).addClass('active');
	});
	
	// BULK TOGGLE
	$bulkToggle.click(function(e) {
		e.preventDefault();
		e.stopPropagation();
		
		if ($(this).hasClass('bulk')) {
			$(this).toggleClass('active').attr('aria-pressed', $(this).hasClass('active'));
		}
	});
	
	// whoever opens popups, to get the focus when they're closed
	var trigger;
	
	// POPUP CLOSER
	function closePopup() {
		if ($('#region').hasClass('visible')) {
			$regionPopup.find('input').blur();
			$('#destination').focus();
			$searchInput.off('keyup');
		}
		// no popup onload, prevent tabbing through invisibles
		$('.popover').hide().removeClass('visible');
		$('#overlay').removeClass('visible');
		$('.popover-trigger').removeClass('active');
		
		// no trigger onload, prevent hocus-focus
		if (trigger && !$body.hasClass('mouseDetected')) {
			trigger.focus();
		}
	}
	
	// POPUP TOGGLE
	function togglePopup(e) {
		e.preventDefault();
		e.stopPropagation();
		
		// reassign each time
		trigger = this;
		var name = (this.hash) ? this.hash.substr(1) : $(this).attr('data-href');
		
		if ($('#'+name).is(':visible')) {
			closePopup();
		} else {
			// lazy load images
			$.each($('#' + name + ' .lazy'), function(i, elem) {
				setTimeout(function() {
					$(elem).attr('src', $(elem).attr('data-src'));
				}, i*15);
			});
			
			// stopPropagation isn't really the right tool, and sometimes still passes on to search input
			// which now listens to 'Enter'. So this hack instead:
			if (name == 'region') {
				setTimeout(function() {
					$searchInput.keyup(search);
				}, 150);
			}
			
			$('#overlay').addClass('visible');
			$('.popover').fadeOut(250).filter('#'+name).fadeIn(250);
			$(this).addClass('active');
			
			// let's focus the input field if it's the region popup
			if (name == 'region') $regionPopup.find('input:first').focus();
		}
		
		$('#overlay').click(closePopup);
	}
	
	$('.popover-trigger').click(togglePopup);

	// INFO TABS TOGGLE
	var $tabs = $('#tabs a');
	$tabs.click(function(e) {
		e.preventDefault();
		e.stopPropagation();

		// re-set tab contents
		$("[id$='-panel']").hide();
		$('#' + this.hash.substr(1)).show();
		
		// re-set active tab
		$tabs.removeClass('active').find('b').html('');
		$(this).addClass('active').find('b').html('(Selected)');
	});

	// REGION SEARCH
	function search(e) {
		var n = e.which || e.keyCode || 0,
			$filteredRegions = $availableRegions.not('.inactive'),
			$current = $filteredRegions.filter('.highlight');
		
		if (n == 13) { // enter
			$current.click();
		} else if (n == 35 || n == 36 || n == 38 || n == 40) {
			$availableRegions.removeClass('highlight');
			
			if ($filteredRegions.length >= 1) {
				if (n == 36) { // home
					$filteredRegions.first().addClass('highlight');
				} else if (n == 35) { // end
					$filteredRegions.last().addClass('highlight');
				} else if (n == 38) { // up arrow
					$current.length > 0 ? $current.prev().addClass('highlight') : $filteredRegions.last().addClass('highlight');
				} else if (n == 40) { // down arrow
					$current.length > 0 ? $current.next().addClass('highlight') : $filteredRegions.first().addClass('highlight');
				}
			}
			if ($current.length > 0) {
				$searchInput.attr('aria-activedescendant', $current[0].id);
			}
		} else {
			$availableRegions.each(function() {
				var searchMatch = $(this).find('.region-label').text().toLowerCase().indexOf($searchInput.val().toLowerCase(), 0) !== 0;
				$(this).toggleClass('inactive', searchMatch);
			});
			
			var regionMatches = $availableRegions.not('.inactive').length;
			$regionPopup.find('.regionCount').html(regionMatches + '/' + $availableRegions.length);
			$regionPopup.find('.contribute').toggle(regionMatches === 0);
		}
	}

	// SHORTCUTS
	$(window).keyup(function(e) {
		var n = e.which || e.keyCode || 0;
		
		if (!$('.popover').is(':visible') || n == 27) {	
			if (n == 27) {
				closePopup();
			} else if (n == 32 && !$body.hasClass('bulkPause')) {
				getName();
			} else if (n == 48) {
				$infoToggle.click();
			} else if (n == 49) {
				$genderToggles.find('a').first().click();
			} else if (n == 50) {
				$genderToggles.find('a:eq(1)').click();
			} else if (n == 51) {
				$genderToggles.find('a').last().click();
			} else if (n == 52) {
				$regionToggle.click();
			} else if (n == 53) {
				$bulkToggle.click();
			} else if (n == 67 && !$body.hasClass('touch-device') && !$body.hasClass('bulk') && $dataContainer.find('em').length == 0) {
				$dataContainer.find('#name').click();
			}
		}
	});
	
	$('#name').on('touchend', function() {
		getName();
	});
	
})();

// SNOW
(function() {
	
	var particles = 5, // max visible particles at any given moment
		particleLife = 5000, // in ms
		$confetti = $('<div/>');
	
	$confetti
		.appendTo('body')
		.css({
			'top': 0,
			'right': 0,
			'bottom': 0,
			'left': 0,
			'position': 'absolute',
			'z-index': -1,
			'overflow': 'hidden'
		});
	
	setInterval(function() {
		$confetti.find('div:hidden').remove();
		
		var dimensions = Math.random() < .5 ? 5 : 10;
		
		$('<div/>')
			.appendTo($confetti)
			.css({
				'top': Math.round(Math.random() * 100) + '%',
				'left': Math.round(Math.random() * 100) + '%',
				'background-color': '#fff',
				'position': 'absolute',
				'border-radius': '100%',
				'z-index': Math.random() < .3 ? 1 : -1
			})
			.animate({
				'height': dimensions + 'px',
				'width': dimensions + 'px',
				'margin': '-' + dimensions / 2 + 'px'
			}, 250)
			.css({
				'transform': 'translate(' + randomNum(-200,-150) + 'px, ' + randomNum(150,200) + 'px)',
				'transition': 'transform ' + particleLife/1000 + 's linear'
			})
			.delay(particleLife*0.85)
			.fadeOut();
	}, particleLife / particles);

})();