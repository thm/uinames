function addListener(li, ev, fn) {
    var ev = ev.split(' ');
    
    for (var i = 0, l = ev.length; i < l; i++) {
        if (li && window.addEventListener) {
            li.addEventListener(ev[i], fn, false);
        } else if (li && window.attachEvent) {
            li.attachEvent('on' + ev[i], fn);
        }
    }
}

function hasClass(elem, klass) {
     return (' ' + elem.className + ' ').indexOf(' ' + klass + ' ') > -1;
}

function capitalize(string) {
	return string.charAt(0).toUpperCase() + string.slice(1);
}

(function() {
	
	var body = document.getElementsByTagName('body')[0],
		nameContainer = document.getElementById('name'),
		genders = document.getElementById('genderSelect').getElementsByTagName('a'),
		infoToggle = document.getElementsByClassName('info')[0],
		regionSelect = document.getElementsByClassName('icon region')[0],
		regionBox = document.getElementById('region'),
		availableRegions = regionBox.getElementsByTagName('li'),
		infoBox = document.getElementById('info');
	
	// REGION SWITCHER
	function regionToggle() {
		for (var j = 0; j < availableRegions.length; j++) {
			availableRegions[j].className = availableRegions[j].className.replace(/\b ?active\b/g, '');
		}
		this.className += ' active';
		
		// switch to new region
		localStorage.setItem('region', this.getElementsByClassName('region-label')[0].innerHTML);
		
		// update the flag in the region icon
		regionSelect.getElementsByTagName('img')[0].src = this.getElementsByTagName('img')[0].src;
		
		// wait 250ms so the user can see the region switched
		setTimeout(function() {
			body.removeAttribute('data-popup');
		}, 250);
	}
	
	for (var i = 0; i < availableRegions.length; i++) {
		addListener(availableRegions[i], 'click', regionToggle);
	}
	
	// select region from local storage if saved
	var storedRegion = localStorage.getItem('region');
	if (storedRegion) {
		for (var i = 0, n = availableRegions.length; i < n; i++) {
			if (storedRegion == availableRegions[i].getElementsByClassName('region-label')[0].innerHTML) {
				availableRegions[i].click();
				break;
			}
		}
	}
	
	// CONNECTION TO THE API
	var namesRequested = 0,
		maxNamesRequested = 25,
		data, oldRegion, oldGender;
	
	function getName(e) {
		// set parameters based on settings
		var region = regionBox.getElementsByClassName('active')[0].getElementsByClassName('region-label')[0].innerHTML.toLowerCase().replace(/ /g, '+'),
			gender = genders[0].parentNode.getElementsByClassName('active')[0].hash.substr(1),
			bulkToggle = document.getElementById('bulk').getElementsByClassName('icon')[0];
		
		// function that injects all the data on the page
		function injectData(data, offset) {
			var specs = document.getElementById('specs'),
				help = document.getElementById('help');
			
			// inject name into page
			nameContainer.innerHTML = '<h1>' + data[offset]['name'] + ' ' + data[offset]['surname'] + '</h1>';
			// inject gender and region into page
			specs.innerHTML = capitalize(data[offset]['gender']) + ' from ' + data[offset]['region'];
			
			// if bulk mode, add the other 24
			if (hasClass(bulkToggle, 'active')) {
				namesRequested = maxNamesRequested;
				nameContainer.innerHTML = '';
				
				if (!hasClass(body, 'bulk')) {
					body.className += ' bulk';
				}
				
				// inject names, genders and regions into page
				for (var i = 0; i < namesRequested; i++) {
					nameContainer.innerHTML += '<h1>' + data[i]['name'] + ' ' + data[i]['surname'] + '</h1><p>' + capitalize(data[i]['gender']) + ' from ' + data[i]['region'] + '</p>';
				}
				
				// pause requests to stop overly excited users
				body.className += ' bulkPause';
				
				// bulk toggle count-down
				var bulkCache = bulkToggle.innerHTML,
					count = 11;
				
				var interval = setInterval(function() {
					bulkToggle.innerHTML = count - 1;
					bulkToggle.style.lineHeight = '200%';
					if (count <= 0 || !hasClass(bulkToggle, 'active')) {
						bulkToggle.innerHTML = bulkCache;
						body.className = body.className.replace(/\b ?bulkPause\b/g, '');
						bulkToggle.style.lineHeight = '';
						clearInterval(interval);
					}
					count--;
				}, 600);
				
				// reset name node and count
				namesRequested = maxNamesRequested;
			} else {
				body.className = body.className.replace(/\b ?bulk\b/g, '');
			}
			
			nameContainer.style.display = '';
			
			// surface help elements
			specs.style.display = '';
			help.style.display = '';
			help.className = 'animate';
			
			setTimeout(function() {
				help.className = '';
			}, 250);
			
			// count generated name
			var getJSON = new XMLHttpRequest();
			getJSON.open('POST', 'api/counter.php', true);
			getJSON.send();
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
			getJSON.open('GET', 'api/?amount=' + maxNamesRequested + '&region=' + region + '&gender=' + gender, true);
			getJSON.send();
			
			// reset counter
			namesRequested = 0;
		}
		
		// detect end of road or a change in settings
		if (namesRequested == 0 || namesRequested >= maxNamesRequested || oldRegion != region || oldGender != gender || hasClass(bulkToggle, 'active')) {
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
	function toggleGender(e) {
	    e.preventDefault();
	    e.stopPropagation();
	
        if (!hasClass(this, 'active')) {
            for (var i = 0; i < genders.length; i++) {
                genders[i].className = genders[i].className.replace(/\b ?active\b/g, '');
            }
            this.className += ' active';
        }
    }
    
    for (var i = 0; i < genders.length; i++) {
    	addListener(genders[i], 'click', toggleGender);
    }
	
	// BULK TOGGLE
	var bulk = document.getElementById('bulk').getElementsByClassName('icon bulk')[0];
	
	function toggleBulk(e) {
	    e.preventDefault();
	    e.stopPropagation();
	
        if (hasClass(bulk, 'active')) {
            bulk.className = bulk.className.replace(/\b ?active\b/g, '');
		} else {
            bulk.className += ' active';
        }
    }
    
	addListener(bulk, 'click', toggleBulk);
    	
	// POPUP CLOSER
	function closePopup() {
		regionBox.getElementsByTagName('input')[0].blur();
		body.removeAttribute('data-popup');
	}
	
	// POPUP TOGGLE
	function togglePopup(e) {
		e.preventDefault();
		e.stopPropagation();
		
		var hash = this.hash.substr(1),
			overlay = document.getElementById('overlay');

		if (body.getAttribute('data-popup') == hash) {
			closePopup();
		} else {
			// lazy load images
			var lazyImages = document.getElementById(hash).getElementsByClassName('lazy');
			for (var i = 0; i < lazyImages.length; i++) {
				(function(i) {
					setTimeout(function() {
						lazyImages[i].src = lazyImages[i].getAttribute('data-src');
					}, i*15);
				})(i);
			}
			
			// show popup
			body.setAttribute('data-popup', hash);
			
			// let's focus the input field if it's the region popup
			if (hash == 'region') {
				regionBox.getElementsByTagName('input')[0].focus();
			}
		}
		
		addListener(overlay, 'click', closePopup);
		
	}
	
	addListener(infoToggle, 'click', togglePopup);
	addListener(regionSelect, 'click', togglePopup);
	
	// INFO TABS TOGGLE
	var tabs = document.getElementById('tabs').getElementsByTagName('a');
	
	function toggleTab(e) {
	    e.preventDefault();
	    e.stopPropagation();
	    
		body.setAttribute('data-tab', this.hash.substr(1));
    }
    
    for (var i = 0; i < tabs.length; i++) {
    	addListener(tabs[i], 'click', toggleTab);
    }
    
    // REGION SEARCH
    var regions = regionBox.getElementsByTagName('li'),
    	searchInput = document.getElementsByTagName('input')[0],
    	regionCount = regionBox.getElementsByClassName('regionCount')[0],
    	contribute = regionBox.getElementsByClassName('contribute')[0];
    
    function search(e) {
    	var regionMatches = regions.length;
    	
    	for (var i = 0; i < regions.length; i++) {
    		var region = regions[i],
    			regionValue = region.getElementsByClassName('region-label')[0].innerHTML.toLowerCase();
    		
    		if (regionValue.indexOf(searchInput.value.toLowerCase(), 0) == 0) {
    			region.className = region.className.replace(/\b ?inactive\b/g, '');
    			regionMatches = regionMatches + 1;
    		} else {
    			if (!hasClass(region, 'inactive')) {
	    			region.className += ' inactive';
	    		}
    			regionMatches = regionMatches - 1;
    		}
    	}
    	
		regionCount.innerHTML = regionMatches / 2 + '/' + regions.length;
		
		if (regionMatches == 0) {
			contribute.style.display = '';
		} else {
			contribute.style.display = 'none';
		}
    }
    
    addListener(searchInput, 'keyup', search);
    
    // SHORTCUTS
	addListener(window, 'keyup touchstart', function(e) {
		var num = e.which || e.keyCode || e.type == 'touchstart' || 0;
		
		if (body.getAttribute('data-popup') != 'region' || num == 27) {
			if (num == 27) {
				closePopup();
			} else if (num == 32 && !body.getAttribute('data-popup') && !hasClass(body, 'bulkPause')) {
				getName(e);
			} else if (e.type == 'touchstart') {
				if (/(EM|H1)/.test(e.target.tagName) || e.target == body || e.target == nameContainer || e.target == specs) {
					getName(e);
				}
			} else if (num == 48) {
				infoToggle.click();
			} else if (num == 49) {
				document.getElementsByClassName('icon random')[0].click();
			} else if (num == 50) {
				document.getElementsByClassName('icon male')[0].click();
			} else if (num == 51) {
				document.getElementsByClassName('icon female')[0].click();
			} else if (num == 52) {
				regionSelect.click();
			} else if (num == 53) {
				document.getElementsByClassName('icon bulk')[0].click();
			} else if (e.keyCode == 67 || e.which == 67) {
				if (!hasClass(body, 'touch-device') && !hasClass(body, 'bulk') && !nameContainer.getElementsByTagName('em')[0]) {
					var range, selection;
					if (document.body.createTextRange) {
						range = document.body.createTextRange();
						range.moveToElementText(nameContainer);
						range.select();
					} else if (window.getSelection) {
						selection = window.getSelection();
						range = document.createRange();
						range.selectNodeContents(nameContainer);
						selection.removeAllRanges();
						selection.addRange(range);
					}
				}
			}
		}
	});
	
	// GRAPH POPUP
	var graph = document.getElementById('graph'),
		bars = graph.getElementsByClassName('bar'),
		popup = graph.getElementsByClassName('popup')[0];
	
	// update contents of popup and display when bar is hovered
	for (var i = 0; i < bars.length; i++) {
		(function(i) {
			addListener(bars[i], 'mouseover', function() {
				var web = bars[i].getAttribute('data-web'),
					api = bars[i].getAttribute('data-api');
				
				// set values in popup
				popup.getElementsByTagName('span')[0].innerHTML = web;
				popup.getElementsByTagName('span')[1].innerHTML = api;
				
				// set distance from left of graph
				var leftOffset = bars[i].offsetLeft + document.getElementById('bars').offsetLeft - 13;
				popup.style.left = leftOffset + 'px';
				
				// add class if visible if it doesn't already have it
				if (!hasClass(graph, 'visible')) {
					graph.className += ' visible';
				}
				
				// add inflated class
				if (hasClass(bars[i], 'inflated') && !hasClass(popup, 'inflated')) {
					popup.className += ' inflated';
				} else if (!hasClass(bars[i], 'inflated')) {
					popup.className = popup.className.replace(/\b ?inflated\b/g, '');
				}
			});
		})(i);
	}
	
	// hide popup when pointer leaves graph
	addListener(graph, 'mouseleave', function() {
		graph.className = graph.className.replace(/\b ?visible\b/g, '');
	});
	
	// make api legend clickable
	addListener(document.getElementById('graph-legend').getElementsByTagName('a')[0], 'click', toggleTab);
	
})();

(function() {

	var title = document.title;
	addListener(document, 'visibilitychange webkitvisibilitychange mozvisibilitychange msvisibilitychange', function() {
		if (document.hidden) {
			document.title = '👀 ' + title;
		} else {
			document.title = title;
		}
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