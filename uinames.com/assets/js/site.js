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

(function() {
	
	var body = document.getElementsByTagName('body')[0],
		h1 = document.getElementsByTagName('h1')[0],
		genders = document.getElementById('genderSelect').getElementsByTagName('a'),
		infoToggle = document.getElementsByClassName('info')[0],
		countrySelect = document.getElementsByClassName('icon country')[0],
		countryBox = document.getElementById('country'),
		availableCountries = countryBox.getElementsByTagName('li'),
		infoBox = document.getElementById('info');
	
	// NAME RANDOMIZER
	function randomizer() {
		if (req.readyState == 4) {
			var data = eval('(' + req.responseText + ')');
			
			function countryToggle() {
				for (var j = 0; j < availableCountries.length; j++) {
					availableCountries[j].className = availableCountries[j].className.replace(/\b ?active\b/g, '');
				}
				this.className += ' active';
				
				countrySelect.className = countrySelect.className.replace(/\b ?active\b/g, '');
				localStorage.setItem('country', this.innerHTML);
				
				setTimeout(function() {
					body.removeAttribute('data-popup');
				}, 300);
			}
			
			for (var i = 0; i < availableCountries.length; i++) {
				addListener(availableCountries[i], 'click', countryToggle);
			}
			
			// select country from local storage if saved
			var storedcountry = localStorage.getItem('country');
			if (storedcountry != null) {
				for (var i = 1, n = availableCountries.length; i < n; i++) {
					if (availableCountries[i].innerHTML == storedcountry) {
						availableCountries[i].click();
						break;
					}
				}
			}
						
			function getName(e) {
				if (e.keyCode == 32 || e.which == 32 || e.type == 'touchend') {
					
					var country = countryBox.getElementsByClassName('active')[0].className.replace(/[^0-9]/g, '') - 1,
						gender = genders[0].parentNode.getElementsByClassName('active')[0].hash.substr(1);
					
					if (country < 0) {
						country = Math.floor(Math.random() * data.length);
					}
					
					if (gender == 'random') {
						if (Math.floor(Math.random() * 2) == 0) {
							gender = 'male';
						} else {
							gender = 'female';
						}
					}
					
					// generating html
					var first = data[country][gender][Math.floor(Math.random() * data[country][gender].length)],
						last = data[country]['surnames'][Math.floor(Math.random() * data[country]['surnames'].length)];
					
					// russian exceptions
					if (gender == 'female' && data[country]['country'].toLowerCase() == 'russia' && /[в|н]/.test(last[last.length - 1])) {
						 last += 'a';
					}
					
					// set name order as per http://en.wikipedia.org/wiki/Personal_name#Name_order
					if (data[country]['country'].match(/(Japan|Hungary|China|Korea|Singapore|Taiwan|Vietnam)/)) {
						h1.innerHTML = last + ' ' + first;
					} else {
						h1.innerHTML = first + ' ' + last;
					}
					
					h1.style.display = '';
					
					var specs = document.getElementById('specs'),
						help = document.getElementById('help');
					
					specs.innerHTML = '<span>' + gender.charAt(0).toUpperCase() + gender.slice(1) + '</span> from <span>' + data[country]['country'] + '</span><a class="bg-check" href="http://duckduckgo.com/?q=' + first + '+' + last + '" target="_blank">Background Check</a>';
					
					body.removeAttribute('data-popup');
					
					infoToggle.className = infoToggle.className.replace(/\b ?active\b/g, '');
					countrySelect.className = countrySelect.className.replace(/\b ?active\b/g, '');
					
					specs.style.display = '';
					help.style.display = '';
					help.className += ' animate';
					
					setTimeout(function() {
						help.className = 'help';
					}, 250);
					
				}
			}
			
			addListener(document, 'keyup', getName);
			addListener(h1, 'touchend', getName);
			
			function select(e) {
				if (e.keyCode == 67 || e.which == 67) {
				
					if (!hasClass(body, 'touch-device') && !h1.getElementsByTagName('em')[0]) {
						var range, selection;
						if (document.body.createTextRange) {
							range = document.body.createTextRange();
							range.moveToElementText(h1);
							range.select();
						} else if (window.getSelection) {
							selection = window.getSelection();
							range = document.createRange();
							range.selectNodeContents(h1);
							selection.removeAllRanges();
							selection.addRange(range);
						}
					}
				
				}
			}
			
			addListener(document, 'keyup', select);
		}
	}
	
	var req = new XMLHttpRequest();
	req.open("GET", "./api/names.json", true);
	req.onreadystatechange = randomizer;
	req.send(null);
	
	// GENDER TOGGLE
	function toggleGender(e) {
	    e.preventDefault();
	    e.stopPropagation();
	
        if (!this.className.match(/active/)) {
            for (var i = 0; i < genders.length; i++) {
                genders[i].className = genders[i].className.replace(/\b ?active\b/g, '');
            }
            this.className += ' active';
        }
    }
    
    for (var i = 0; i < genders.length; i++) {
    	addListener(genders[i], 'click', toggleGender);
    }
	
	// POPUP TOGGLE
	function togglePopup(e) {
	    e.preventDefault();
	    e.stopPropagation();
	    
	    var hash = this.hash.substr(1);
		
		if (body.getAttribute('data-popup') == hash) {
			body.removeAttribute('data-popup');
		} else {
			body.setAttribute('data-popup', hash);
		}
	}
	
	addListener(infoToggle, 'click', togglePopup);
	addListener(countrySelect, 'click', togglePopup);
	
	// INFO TABS TOGGLE
	var tabs = document.getElementById('tabs').getElementsByTagName('a');
	
	function toggleTab(e) {
	    e.preventDefault();
	    e.stopPropagation();
	
        if (!this.className.match(/active/)) {
            for (var i = 0; i < tabs.length; i++) {
                tabs[i].className = tabs[i].className.replace(/\b ?active\b/g, '');
            }
            this.className += ' active';
            infoBox.setAttribute('data-tab', this.hash.substr(1));
        }
    }
    
    for (var i = 0; i < tabs.length; i++) {
    	addListener(tabs[i], 'click', toggleTab);
    }
	
})();
	
// GOOGLE'S STALKING CODE
var _gaq = _gaq || [];
_gaq.push(['_setAccount', 'UA-46677803-1']);
_gaq.push(['_trackPageview']);

(function() {
	var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
	ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
	var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
})();