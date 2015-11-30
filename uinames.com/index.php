<?php
	
	$data = file_get_contents('api/names.json');
	$names = json_decode($data, true);
	
	$data = file_get_contents('api/stats.json');
	$stats = json_decode($data, true);
	
	$countries = $stats['countries'];
	$available = $stats['names'];

?>

<!DOCTYPE html>

<html>
<head>

	<meta charset="utf-8" />
	
	<title>uinames.com</title>
	<meta name="description" content="A simple tool to generate random names for use in designs and mockups." />
	
	<link rel="shortcut icon" href="assets/img/favicon.ico" />
	<script>
		var isRetina = (window.devicePixelRatio > 1 || (window.matchMedia && window.matchMedia("(-webkit-min-device-pixel-ratio: 1.5),(-moz-min-device-pixel-ratio: 1.5),(min-device-pixel-ratio: 1.5)").matches)); if (isRetina) document.write('<link rel="shortcut icon" href="assets/img/favicon@2x.ico" />');
	</script>
	
	<!--[if lt IE 9]><script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
	
	<link href="assets/css/styles.css" rel="stylesheet" />
	
	<!-- viewport -->
	<meta name="viewport" content="user-scalable=no, initial-scale=1" />
	
	<!-- identity -->
	<meta name="apple-mobile-web-app-capable" content="yes" />
	<meta name="apple-mobile-web-app-title" content="uinames">
	<meta name="apple-mobile-web-app-status-bar-style" content="black" />
	<link rel="apple-touch-icon" href="assets/img/ios-precomposed.png" />

</head>

<body class="no-js">

	<script>
		var body = document.getElementsByTagName('body')[0],
			action = 'Press Spacebar', c = 'CTRL + C';
		
		body.className = body.className.replace(/no\-js/, '');
		
		if ('ontouchstart' in window || 'onmsgesturechange' in window) {
			body.className += ' touch-device';
			action = 'Tap here';
		}
		
		if (navigator.appVersion.indexOf('Mac')!=-1) {
			c = '&#8984; + C';
		}
	</script>

	<h1><em><script>document.write(action)</script></em></h1>
	<p id="specs" style="display: none"></p>
	<p id="help" style="display: none">Press <span>C</span> to select, then <span><script>document.write(c)</script></span> to copy!</p>
	
	<div id="info" class="clearfix" data-tab="info">
		<div id="tabs">
			<a href="#info" class="active">Info</a>
			<a href="#api">API</a>
		</div>
		<div id="tab-info">
			<h2>About</h2>
			<p><a href="http://uinames.com">uinames.com</a> is a simple tool to generate names for use in designs and mockups. Made by <a href="http://twitter.com/thomweerd" target="_blank">Thom</a>.</p>
			<h2>Elsewhere</h2>
			<p>Numerous websites and blogs have published about the project including <a href="http://tympanus.net/codrops/collective/collective-96/" target="_blank">Codrops</a>, <a href="http://speckyboy.com/2014/01/30/40-tiny-web-based-apps-tools-web-designers/" target="_blank">Speckyboy</a>, <a href="http://www.smashingmagazine.com/smashing-newsletter-issue-new-year-special-edition-2013/" target="_blank">Smashing Magazine</a>, <a href="http://sidebar.io/2013/12/26" target="_blank">Sidebar</a>, <a href="http://www.webdesignerdepot.com/2014/02/whats-new-for-designers-february-2014/" target="_blank">Webdesigner Depot</a>, <a href="http://www.cssauthor.com/weekly-web-development-resources-tools-35/" target="_blank">CSS Author</a>, <a href="https://www.producthunt.com/tech/uinames" target="_blank">Product Hunt</a>, <a href="https://news.layervault.com/stories/12511-ui-names" target="_blank">LayerVault</a>, <a href="http://oozled.com/resources/just-handy" target="_blank">Oozled</a> and <a href="http://thenextweb.com/dd/2015/02/18/300-awesome-free-things-massive-list-free-resources-know/" target="_blank">The Next Web</a>.</p>
			<h2>Contribute</h2>
			<p>You can contribute to the project through <a href="http://github.com/thm/uinames" target="_blank">Github</a>. Feedback can be tweeted to <a href="http://twitter.com/thomweerd" target="_blank">@thomweerd</a> directly.</p>
			<h2>Other</h2>
			<p>Check out <a href="http://uifaces.com" target="_blank">uifaces.com</a> as well!</p>
		</div>
		<div id="tab-api">
			<h2>Overview</h2>
			<p>All responses are returned as JSON, and there is no request limit. Please keep the amount of calls to a minimum though, and cache responses whenever possible.</p>
			<h2>Usage</h2>
			<p>Basic usage:</p>
<pre>http://api.uinames.com
---
{
  "name":"John",
  "surname":"Doe",
  "gender":"male",
  "country":"United States"
}
</pre>
			<h2>Optional Parameters</h2>
			<p>The amount of names to return:</p>
			<pre><span>http://api.uinames.com/</span>?amount=25</pre>
			<br />
			<p>The gender of names to return (male or female):</p>
			<pre><span>http://api.uinames.com/</span>?gender=female</pre>
			<br />
			<p>Country specific results:</p>
			<pre><span>http://api.uinames.com/</span>?country=germany</pre>
			<br />
			<p>Require a minimum number of characters in a name:</p>
			<pre><span>http://api.uinames.com/</span>?minlen=25</pre>
			<br />
			<p>Require a maximum number of characters in a name:</p>
			<pre><span>http://api.uinames.com/</span>?maxlen=75</pre>
			<!--
			<br />
			<p>Results for a language within a country:</p>
			<pre><span>http://api.uinames.com/</span>?country=switzerland&amp;language=german</pre>
			-->
		</div>
		<div id="facts">
			<div><?php print $countries; ?><p>Countries</p></div>
			<div><?php print number_format($available, 0, ',', '.'); ?><p>Available Names</p></div>
		</div>
	</div>

	<div id="country" class="clearfix">
		<ul>
			<span class="countryCount"><?php echo (count($names) + 1) . '/' . (count($names) + 1); ?></span>
			<input class="search" type="text" placeholder="Type to search..." />
			<li class="pos-0"><span class="flag"><img src="assets/img/flags/random.gif" /></span><span class="country-label">Random</span></li>
			<?php
				
				$newCountries = array("Pakistan");
				$favCountries = array("United States","Germany","France");
				
				$total = count($names);
				
				for ($i = 0; $i < $total; $i++) {
					$country = $names[$i]['country'];
					
					$new = '';
					if (in_array($country, $newCountries)) {
						$new = ' new';
					}
					
					$fav = '';
					if (in_array($country, $favCountries)) {
						$fav = ' fav';
						if ($country == $favCountries[0]) {
							$fav = ' fav active';
						}
					}
					
					echo '<li class="pos-' . ($i + 1) . $new . $fav . '"><span class="flag"><img src="assets/img/flags/' . str_replace(' ', '-', strtolower($country)) . '.png" /></span><span class="country-label">' . $country . '</span></li>';
				}
				
			?>
			<span class="contribute" style="display: none;">
				<a href="https://github.com/thm/uinames" target="_blank" class="clearfix">
					<?php echo file_get_contents('assets/img/crying-octocat.svg'); ?>
					<p>No country could be found. Consider contributing on Github!</p>
				</a>
			</span>
		</ul>
	</div>
	
	<div id="overlay"></div>
	
	<div id="options">
		<span id="genderSelect">
			<a class="icon random active" href="#random" title="Random Gender"><span class="r1"></span><span class="r2"></span><span class="r3"></span><span class="r4"></span><span class="r5"></span><span class="r6"></span><span class="r7"></span></a>
			<a class="icon male" href="#male" title="Male Only"><span class="m1"></span><span class="m2"></span><span class="m3"></span><span class="m4"></span></a>
			<a class="icon female" href="#female" title="Female Only"><span class="f1"></span><span class="f2"></span><span class="f3"></span></a>
		</span>
		<span id="countrySelect">
			<a class="icon country" href="#country" title="Select Country"><span class="r1"></span><span class="r2"></span><span class="r3"></span><span class="r4"></span></a>
		</span>
	</div>
	
	<div id="details">
		<a class="icon info" href="#info" title="More Information"><span class="i1"></span><span class="i2"></span><span class="i3"></span></a>
	</div>
	
	<div id="share-box">
		<iframe src="//www.facebook.com/plugins/like.php?href=http%3A%2F%2Fuinames.com&amp;width=100px&amp;layout=button_count&amp;action=like&amp;show_faces=false&amp;share=false&amp;height=65" scrolling="no" frameborder="0" style="border:none; overflow: hidden; width: 86px; height: 20px;" allowTransparency="true"></iframe>
		
		<iframe src="http://ghbtns.com/github-btn.html?user=thm&repo=uinames&type=watch&count=true" allowtransparency="true" frameborder="0" scrolling="0" width="87" height="20"></iframe>
		
		<a href="https://twitter.com/share" class="twitter-share-button" data-url="http://uinames.com" data-text="Generate random names for use in designs and mockups on" data-via="thomweerd" data-size="small" data-related="thomweerd" data-dnt="true" data-lang="en"></a>
		<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>
	</div>
	
	<script src="assets/js/site.js"></script>

</body>
</html>