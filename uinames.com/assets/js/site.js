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

(function() {
	var $body = $('body'),
		$genderToggles = $('#genderSelect'),
		$regionToggle = $('.icon.region:first'),
		$bulkToggle = $('#bulk .icon'),
		$infoToggle = $('.info:first'),
		$regionPopup = $('#region'),
		$searchInput = $regionPopup.find('input:first'),
		$availableRegions = $regionPopup.find('li'),
		$name = $('#name'),
		$specs = $('#data');
	
	// so we don't tab into invisible things
	$('#info').hide();
	$regionPopup.hide();
	
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
		$regionToggle.find('img').attr('src', $this.find('img').attr('data-src'));
		
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
			// insert name into page
			$name.html('<h1>' + data[offset]['name'] + ' ' + data[offset]['surname'] + '</h1>');
			
			// insert additional data into page
			var specsData = '';
			$.each({
					'Gender': capitalize(data[offset]['gender']),
					'Region': data[offset]['region'],
					'Phone': data[offset]['phone'],
					'Birthday': data[offset]['birthday']['dmy'],
					'Email': data[offset]['email'],
					'Password': data[offset]['password']
				}, function(key, val) {
				specsData += '<li><span class="label">' + key + ':</span> <span class="click-to-select">' + val + '</span></li>';
			});
			
			var photo = '<div class="photo-container"><a href="' + data[offset]['photo'] + '" target="_blank" class="photo"><img src="' + data[offset]['photo'].replace(/http:\/\/uinames\.com\//, '') + '" /></a><a href="http://uifaces.com" target="_blank">More</a></div>';
			
			$specs.html('<a id="data-open">Show Details</a><div>' + photo + '<ul>' + specsData + '</ul><a id="data-exit"></a></div>');
			
			$('.click-to-select').click(selectThis);
			
			$('#data-exit').click(function() {
				$specs.addClass('closed');
			});
			
			$('#data-open').click(function() {
				$specs.removeClass('closed');
			});
			
			// if bulk mode, add the other 24
			if ($bulkToggle.hasClass('active')) {
				$body.addClass('bulk bulkPause'); // 'bulkPause' pauses requests to stop overly excited users
				
				// inject names, genders and regions into page
				var bulkNames = '';
				for (var i = 0; i < maxNamesRequested; i++) {
					bulkNames += '<h1>' + data[i]['name'] + ' ' + data[i]['surname'] + '</h1><p>' + capitalize(data[i]['gender']) + ' from ' + data[i]['region'] + '</p>';
				}
				$name.html(bulkNames);

				// bulk toggle count-down
				var bulkToggleCache = $bulkToggle.html(),
					count = 10,
					bulkCountDown = setInterval(function() {
						if (count === 0 || !$bulkToggle.hasClass('active')) {
							$body.removeClass('bulkPause');
							$bulkToggle.html(bulkToggleCache).css({lineHeight: ''});
							clearInterval(bulkCountDown);
						} else {
							$bulkToggle.html(count--).css({lineHeight: '200%'});
						}
					}, 650);
			} else {
				$body.removeClass('bulk');
			}
			
			$specs.toggle(!$bulkToggle.hasClass('active'));
			$name
				.show()
				.find('h1').click(selectThis)
				.on('touchend', function() {
					getName();
				});
			
			// count generated name
			/*
			var getJSON = new XMLHttpRequest();
			getJSON.open('POST', 'api/counter.php', true);
			getJSON.send();
			*/
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
		var name = $body.attr('data-popup');
		
		if (name == 'region') {
			$regionPopup.find('input').blur();
			$('#destination').focus();
			$searchInput.off('keyup');
		}
		// no popup onload, prevent tabbing through invisibles
		$('#'+name).hide();
		
		// no trigger onload, prevent hocus-focus
		if (trigger && !$body.hasClass('mouseDetected')) {
			trigger.focus();
		}
		
		$body.removeAttr('data-popup');
	}
	
	// POPUP TOGGLE
	function togglePopup(e) {
		e.preventDefault();
		e.stopPropagation();
		
		// reassign each time
		trigger = this;
		var name = (this.hash) ? this.hash.substr(1) : $(this).attr('data-href');
		
		if ($body.attr('data-popup') == name) {
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
			
			$('#'+name).show();
			
			// show popup
			setTimeout(function() {
				$body.attr('data-popup', name);
			}, 50);
			
			// let's focus the input field if it's the region popup
			if (name == 'region') $regionPopup.find('input:first').focus();
		}
		
		$('#overlay').click(closePopup);
	}
	
	$infoToggle.click(togglePopup);
	$regionToggle.click(togglePopup);

	// INFO TABS TOGGLE
	var $tabs = $('#tabs a');
	$tabs.click(function(e) {
		e.preventDefault();
		e.stopPropagation();

		$body.attr('data-tab', $(this).attr('data-set'));
		$tabs.find('b').html('');
		$(this).find('b').html('(Selected)');
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
		
		if ($body.attr('data-popup') != 'region' || n == 27) {	
			if (n == 27) {
				closePopup();
			} else if (n == 32 && !$body.attr('data-popup') && !$body.hasClass('bulkPause')) {
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
			} else if (n == 67 && !$body.hasClass('touch-device') && !$body.hasClass('bulk') && $name.find('em').length == 0) {
				selectThis($name[0]);
			}
		}
	});
	
	$name.on('touchend', function() {
		getName();
	});
	
})();

(function() {

	var title = document.title;
	$(window).on('blur focus', function() {
		document.title = document.hidden ? '👀 ' + title : title;
	});

})();

// STALKING CODE
var _gaq = _gaq || [];
_gaq.push(['_setAccount', 'UA-46677803-1']);
_gaq.push(['_trackPageview']);

(function() {
	var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
	ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
	var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
})();