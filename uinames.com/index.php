<?php
	

	$data = file_get_contents('api/names.json');
	$names = json_decode($data);

	/*	
	$data = file_get_contents('api/stats.json');
	$stats = json_decode($data);
	
	$regions = $stats->regions;
	$available = $stats->names;
	*/
	
	require_once('dependables.php');

?>

<!DOCTYPE html>

<html lang="en">
<head>

	<!-- Copryight (c) 2017 Thom van der Weerd. All rights reserved. -->

	<meta charset="utf-8" />
	
	<title>Generate Random Fake Names</title>
	<meta name="description" content="Generate random fake names for use in designs and mockups. Supports 50+ regions with more than 1.2 million possible combinations. Completely open-source." />
	<meta name="keywords" content="uinames, dummy, random, names, fake, generator, name, personas" />
	
	<link rel="shortcut icon" href="assets/img/favicon@2x.ico" />
	
	<link href="assets/css/styles.css" rel="stylesheet" />
	
	<!--viewport-->
	<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
	
	<!--identity-->
	<meta name="apple-mobile-web-app-capable" content="yes" />
	<meta name="apple-mobile-web-app-title" content="uinames">
	<meta name="apple-mobile-web-app-status-bar-style" content="black" />
	<link rel="apple-touch-icon" href="assets/img/ios-precomposed.png" />
	
	<!--Facebook Card-->
	<meta property="og:title" content="Randomly Generate Fake Names" />
	<meta property="og:type" content="website" />
	<meta property="og:image" content="https://cl.ly/1o2k1Z422Z2T/icon-250.png" />
	<meta property="og:url" content="https://uinames.com" />
	<meta property="og:description" content="Generate random fake names for use in designs and mockups. Supports 48+ regions with over 1.2 million possible combinations. Completely open-source." />
	
	<!--Twitter Card-->
	<meta name="twitter:card" content="summary" />
	<meta name="twitter:site" content="@thomweerd" />
	<meta name="twitter:title" content="Randomly Generate Fake Names" />
	<meta name="twitter:description" content="Generate random fake names for use in designs and mockups. Supports 48+ regions with over 1.2 million possible combinations. Completely open-source." />
	<meta name="twitter:image" content="https://cl.ly/1o2k1Z422Z2T/icon-250.png" />

</head>

<body class="no-js">
	<script>
		var body = document.getElementsByTagName('body')[0]
			action = 'Press Spacebar', c = 'CTRL+C';
		
		body.className = body.className.replace(/no\-js/, '');
		
		if ('ontouchstart' in window || 'onmsgesturechange' in window) {
			body.className += ' touch-device';
			action = 'Tap here';
		}
		
		if (navigator.appVersion.indexOf('Mac')!=-1) {
			c = '&#8984;+C';
		}
	</script>

	<div id="data" aria-live="polite" aria-atomic="true">
		<div>
			<h1 id="name"><script>document.write(action)</script></h1>
		</div>
	</div>
	
	<p class="ac" id="destination" tabindex="-1">In options menu</p>
	
	<div id="options">
		<span id="genderSelect">
			<a role="button" class="icon random active" href="#random" title="Gender: Random" aria-label="Gender: Random" aria-pressed="true">
				<span class="r1"></span><span class="r2"></span><span class="r3"></span><span class="r4"></span><span class="r5"></span><span class="r6"></span><span class="r7"></span>
			</a>
			<a role="button" class="icon male" href="#male" title="Gender: Male" aria-label="Gender: Male" aria-pressed="false">
				<span class="m1"></span><span class="m2"></span><span class="m3"></span><span class="m4"></span>
			</a>
			<a role="button" class="icon female" href="#female" title="Gender: Female" aria-label="Gender: Female" aria-pressed="false">
				<span class="f1"></span><span class="f2"></span><span class="f3"></span>
			</a>
			<span class="label">Gender</span>
		</span>
		<span id="regionSelect">
			<a class="icon region active popover-trigger" href="#region" title="Select Region">
				<span class="flag"><img src="assets/img/flag-random.gif" /></span>
				<b class="ac">Random region selected</b>
			</a>
			<span class="label">Region</span>
		</span>
		<span id="bulk">
			<a role="button" class="icon bulk" href="#bulk" title="Bulk mode" aria-label="Bulk mode" aria-pressed="false">
				<span class="b1"></span><span class="b2"></span><span class="b3"></span>
			</a>
			<span class="label">Bulk</span>
		</span>
	</div>
	
	<div id="details">
		<a role="button" class="icon info popover-trigger" href="#info" title="More Information" aria-label="More Information">
			<span class="i1"></span><span class="i2"></span><span class="i3"></span><span class="i4"></span>
		</a>
		<span class="label">Info</span>
	</div>
	
	<div id="help">
		<p class="tip-1">Share = ❤️</p>
		<p class="tip-2">Press <strong>C</strong>, then <strong><script>document.write(c)</script></strong> to easily copy names! 😎</p>
		<p class="tip-3">All results, including <a href="https://unsplash.com/license" target="_blank">photos</a>, are free for all purposes. 🤘</p>
		<p class="tip-4">Send <a href="https://twitter.com/thomweerd" target="_blank">@thomweerd</a> your feedback. 👋️</p>
		<p class="tip-5">Contribute through <a href="https://github.com/thm/uinames" target="_blank">Github</a>! 👋</p>
		<p class="tip-6"><a href="https://paypal.me/thomweerd" target="_blank">Donate</a> if this site is useful! 💸</p>
	</div>
	
	<div id="share">
		<a href="https://twitter.com/share?text=Generate%20random%20names%20for%20use%20in%20designs%20and%20mockups%20on&url=https://uinames.com&via=thomweerd&related=thomweerd" class="twitter-button" onclick="window.open(this.href, 'Tweet', 'scrollbars=no,width=500,height=280'); return false;">
			<span class="share-button"><?php echo file_get_contents('assets/img/share-tw.svg'); ?>Tweet</span>
			<span class="share-count">2.1k</span>
			<!-- Conservative guess based on increased FB count: (NewFB/oldFB) * oldTw or (1.7/1.5) * 1.9 -->
		</a>
		
		<a href="https://www.facebook.com/sharer/sharer.php?u=https://uinames.com" class="facebook-button" onclick="window.open(this.href, 'Share', 'scrollbars=no,width=500,height=280'); return false;">
			<span class="share-button"><?php echo file_get_contents('assets/img/share-fb.svg'); ?>Share</span>
			<span class="share-count">1.7k</span>
			<!-- Source (Dec 5, 2018): https://graph.facebook.com/?id=http://uinames.com + https://graph.facebook.com/?id=https://uinames.com -->
		</a>
		
		<a href="https://github.com/thm/uinames/" class="github-button" target="_blank">
			<span class="share-button"><?php echo file_get_contents('assets/img/share-gh.svg'); ?>Star</span>
			<span class="share-count">0.5k</span>
		</a>
	</div>
	
	<div id="info" class="popover clearfix">
		<div id="tabs">
			<a id="info-tab" href="#info-panel" class="active">Info <b class="ac">(Selected)</b></a>
			<a id="api-tab" href="#api-panel">API <b class="ac"></b></a>
			<a id="shortcuts-tab" href="#shortcuts-panel">Shortcuts <b class="ac"></b></a>
		</div>
		<div id="info-panel" style="display: block;">
			<h2>About</h2>
			<p><a href="https://uinames.com">uinames.com</a> is a simple tool to generate fake names for use in designs and mockups. Made by <a href="https://twitter.com/thomweerd" target="_blank">Thom</a>.</p>
			
			<h2>Elsewhere</h2>
			<p>Many have published about the project, including <a href="https://tympanus.net/codrops/collective/collective-96/" target="_blank">Codrops</a>, <a href="https://speckyboy.com/40-tiny-web-based-apps-tools-web-designers/" target="_blank">Speckyboy</a>, <a href="https://www.smashingmagazine.com/smashing-newsletter-issue-new-year-special-edition-2013/" target="_blank">Smashing Magazine</a>, <a href="https://sidebar.io/2013/12/26" target="_blank">Sidebar</a>, <a href="https://www.webdesignerdepot.com/2014/02/whats-new-for-designers-february-2014/" target="_blank">Webdesigner Depot</a>, <a href="https://www.producthunt.com/tech/uinames" target="_blank">Product Hunt</a>, <a href="https://news.layervault.com/stories/12511-ui-names" target="_blank">LayerVault</a>, <a href="https://oozled.com/resources/just-handy" target="_blank">Oozled</a> and <a href="https://thenextweb.com/dd/2015/02/18/300-awesome-free-things-massive-list-free-resources-know/" target="_blank">The Next Web</a>.</p>
			
			<h2>Contribute</h2>
			<p>Contribute to the project through <a href="https://github.com/thm/uinames" target="_blank">Github</a>! This collection of names would not have been as complete without these wonderful people:</p>
			<p class="clearfix">
				<?php
					// render github contributors
					echo github(
						'<a href="$href" class="contributor">
							<span class="popup"><span>$user</span> +$contributions</span>
							<img class="lazy" src="assets/img/replacement.png" data-src="$img" />
						</a>'
					);
				?>
			</p>
		</div>
		<div id="api-panel">
			<h2>Overview</h2>
			<p>All responses are returned as JSON(P) over HTTP(S). There is currently no request limit. However, please keep the amount of requests to a minimum, and cache responses whenever possible.</p>
			<h2>Basic Usage</h2>
<pre>https://uinames.com/api/
<span>---</span>
{
  <span class="key">"name"</span>: <span class="val">"John"</span>,
  <span class="key">"surname"</span>: <span class="val">"Doe"</span>,
  <span class="key">"gender"</span>: <span class="val">"male"</span>,
  <span class="key">"region"</span>: <span class="val">"United States"</span>
}
</pre>
			<h2>Optional Parameters</h2>
			<p>Number of names to return, between <code>1</code> and <code>500</code>:</p>
			<pre><span>https://uinames.com/api/</span>?amount=25</pre>
			<p>Limit results to the <code>male</code> or <code>female</code> gender:</p>
			<pre><span>https://uinames.com/api/</span>?gender=female</pre>
			<p>Region-specific results:</p>
			<pre><span>https://uinames.com/api/</span>?region=germany</pre>
			<p>Require a minimum number of characters in a name:</p>
			<pre><span>https://uinames.com/api/</span>?minlen=25</pre>
			<p>Require a maximum number of characters in a name:</p>
			<pre><span>https://uinames.com/api/</span>?maxlen=75</pre>
			<p>For JSONP, specify a callback function to wrap results in:</p>
			<pre><span>https://uinames.com/api/</span>?callback=example</pre>
			<!-- no region currently supports different languages
			<p>Results for a language within a region:</p>
			<pre><span>https://uinames.com/api/</span>?region=switzerland&amp;language=german</pre>
			-->
			<h2>Extra Data</h2>
			<p>Additional random data is served to requests passing an <code>ext</code> parameter. However, response times may be slower, especially when requesting larger quantities of data.</p>
			<p>All photos are hand-picked from <a href="https://unsplash.com" target="_blank">Unsplash</a> (<a href="https://unsplash.com/license" target="_blank">license</a>):</p>
<pre><span>https://uinames.com/api/</span>?ext
<span>---</span>
{
  <span class="key">"name"</span>: <span class="val">"John"</span>,
  <span class="key">"surname"</span>: <span class="val">"Doe"</span>,
  <span class="key">"gender"</span>: <span class="val">"male"</span>,
  <span class="key">"region"</span>: <span class="val">"United States"</span>,
  <span class="key">"age"</span>: <span class="int">29</span>,
  <span class="key">"title"</span>: <span class="val">"mr"</span>,
  <span class="key">"phone"</span>: <span class="val">"(123) 456 7890"</span>,
  <span class="key">"birthday"</span>: {
    <span class="key">"dmy"</span>: <span class="val">"19/06/1987"</span>, <span>// day, month, year</span>
    <span class="key">"mdy"</span>: <span class="val">"06/19/1987"</span>, <span>// month, day, year</span>
    <span class="key">"raw"</span>: <span class="int">551062610</span> <span>// UNIX timestamp</span>
  },
  <span class="key">"email"</span>: <span class="val">"john.doe@example.com"</span>,
  <span class="key">"password"</span>: <span class="val">"Doe87(!"</span>,
  <span class="key">"credit_card"</span>: {
    <span class="key">"expiration"</span>: <span class="val">"12/20"</span>,
    <span class="key">"number"</span>: <span class="val">"1234-5678-1234-5678"</span>,
    <span class="key">"pin"</span>: <span class="int">1234</span>,
    <span class="key">"security"</span>: <span class="int">123</span>
  },
  <span class="key">"photo"</span>: <span class="val">"https://uinames.com/api/photos/male/1.jpg"</span>
}
</pre>
			<h2>Exception Handling</h2>
			<p>Error messages have the following format:</p>
			<pre>{<span class="key">"error"</span>:<span class="val">"Region or language not found"</span>}</pre>
		</div>
		<div id="shortcuts-panel">
			<h2>Names</h2>
			<p><code>Spacebar</code> to generate a new name</p>
			<p><code>C</code> to highlight the current name</p>
			<h2>Options</h2>
			<p><code>1</code> to set the gender to random</p>
			<p><code>2</code> to set the gender to male</p>
			<p><code>3</code> to set the gender to female</p>
			<p><code>5</code> switch bulk-mode on or off</p>
			<h2>Panels</h2>
			<p><code>4</code> to change the region</p>
			<p><code>0</code> to view the info panel</p>
			<p><code>Esc</code> to close the current panel</p>
		</div>
	</div>

	<div id="region" class="popover clearfix">
		<div>
			<label for="rsearch" id="regionSearchLabel" class="ac">Search regions</label>
			<span class="regionCount" aria-live="polite" aria-atomic="true"><?php echo (count($names) + 1) . '/' . (count($names) + 1); ?></span>
			<input class="search" type="text" placeholder="Type to search..." role="combobox" aria-expanded="true" aria-autocomplete="list" id="rsearch" aria-labelledby="regionSearchLabel" aria-owns="regionList" aria-activedescendant="region-0" />
		</div>
		<ul id="regionList" role="listbox">
			<li id="region-0" role="option" tabindex="-1"><span class="flag"><img src="assets/img/flag-random.gif" /></span><span class="region-label">Random</span></li>
			<?php
				
				$newRegions = ["Costa Rica", "Indonesia", "Croatia", "Czech Republic", "Ireland", "Kyrgyz Republic", "Tunisia"];
				$favRegions = ["United States", "Germany", "France", "Russia", "India"];
				
				$total = count($names);
				
				for ($i = 0; $i < $total; $i++) {
					$region = $names[$i]->region;
					$new = !in_array($region, $newRegions) ? '' : ' new';
					
					$fav = '';
					if (in_array($region, $favRegions)) {
						$fav = ($region == $favRegions[0]) ? ' fav active' : ' fav';
					}
					
					echo '<li id="region-' . ($i + 1) . '" class="' . $new . $fav . '" role="option" tabindex="-1"><span class="flag" style="background-position: 0 -' . $i*20 . 'px;"></span><span class="region-label">' . $region . '</span></li>';
				}
				
			?>
			<span class="contribute" style="display: none;">
				<a href="https://github.com/thm/uinames" target="_blank" class="clearfix">
					<?php echo file_get_contents('assets/img/crying-octocat.svg'); ?>
					<p>No such region found. Consider contributing through Github!</p>
				</a>
			</span>
		</ul>
	</div>
	
	<div id="overlay"></div>
	
	<script><?php echo file_get_contents('assets/js/jquery.min.js'); ?></script>
	<script><?php echo file_get_contents('assets/js/site.js'); ?></script>

</body>
</html>
