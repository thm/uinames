<?php
	
	$data = file_get_contents('api/names.json');
	$names = json_decode($data);
	
	$data = file_get_contents('api/stats.json');
	$stats = json_decode($data);
	
	$regions = $stats->regions;
	$available = $stats->names;
	
	require_once('dependables.php');

?>

<!DOCTYPE html>

<html lang="en">
<head>

	<meta charset="utf-8" />
	
	<title>uinames.com: Randomly Generate Fake Names</title>
	<meta name="description" content="Generate random fake names for use in designs and mockups. Supports 45+ regions with over 1 million possible combinations. Completely open-source." />
	<meta name="keywords" content="uinames, dummy, random, names, fake, generator, name" />
	
	<link rel="shortcut icon" href="assets/img/favicon.ico" />
	<script>
		var isRetina = (window.devicePixelRatio > 1 || (window.matchMedia && window.matchMedia("(-webkit-min-device-pixel-ratio: 1.5),(-moz-min-device-pixel-ratio: 1.5),(min-device-pixel-ratio: 1.5)").matches)); if (isRetina) document.write('<link rel="shortcut icon" href="assets/img/favicon@2x.ico" />');
	</script>
	
	<!--[if lt IE 9]><script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
	
	<link href="assets/css/styles.css" rel="stylesheet" />
	
	<!--viewport-->
	<meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=no" />
	
	<!--identity-->
	<meta name="apple-mobile-web-app-capable" content="yes" />
	<meta name="apple-mobile-web-app-title" content="uinames">
	<meta name="apple-mobile-web-app-status-bar-style" content="black" />
	<link rel="apple-touch-icon" href="assets/img/ios-precomposed.png" />
	
	<!--Facebook Card-->
	<meta property="og:title" content="Randomly Generate Fake Names" />
	<meta property="og:type" content="website" />
	<meta property="og:image" content="http://cl.ly/1o2k1Z422Z2T/icon-250.png" />
	<meta property="og:url" content="http://uinames.com" />
	<meta property="og:description" content="Generate random fake names for use in designs and mockups. Supports 45+ regions with over 1 million possible combinations. Completely open-source." />
	
	<!--Twitter Card-->
	<meta name="twitter:card" content="summary" />
	<meta name="twitter:site" content="@thomweerd" />
	<meta name="twitter:title" content="Randomly Generate Fake Names" />
	<meta name="twitter:description" content="Generate random fake names for use in designs and mockups. Supports 45+ regions with over 1 million possible combinations. Completely open-source." />
	<meta name="twitter:image" content="http://cl.ly/1o2k1Z422Z2T/icon-250.png" />

</head>

<body class="no-js" data-tab="info">

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

	<div id="name" aria-live="polite" aria-atomic="true"><h1><em><script>document.write(action)</script></em></h1></div>
	<p id="specs" style="display: none"></p>
	<p id="help" style="display: none">Press <span>C</span> to select, then <span><script>document.write(c)</script></span> to copy!</p>
	
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
		</span>
		<span id="regionSelect">
			<a class="icon region active" href="#region" title="Select Region">
				<span class="flag"><img src="assets/img/flags/random.gif" /></span>
				<b class="ac">Random region selected</b>
			</a>
		</span>
		<span id="bulk">
			<a role="button" class="icon bulk" href="#bulk" title="Bulk mode" aria-label="Bulk mode" aria-pressed="false">
				<span class="b1"></span><span class="b2"></span><span class="b3"></span>
			</a>
		</span>
	</div>
	
	<div id="details">
		<button type="button" class="icon info" data-href="info" title="More Information" aria-label="More Information"><span class="i1"></span><span class="i2"></span><span class="i3"></span><span class="i4"></span></button>
	</div>
	<div id="info" class="clearfix">
		<div id="tabs">
			<a id="info-tab" href="#info-panel" data-set="info">Info <b class="ac">(Selected)</b></a>
			<a id="api-tab" href="#api-panel" data-set="api">API <b class="ac"></b></a>
			<a id="shortcuts-tab" href="shortcuts-panel" data-set="shortcuts">Shortcuts <b class="ac"></b></a>
		</div>
		<div id="info-panel">
			<h2>About</h2>
			<p><a href="http://uinames.com">uinames.com</a> is a simple tool to generate names for use in designs and mockups. Made by <a href="http://twitter.com/thomweerd" target="_blank">Thom</a>.</p>
			<h2>Elsewhere</h2>
			<p>Numerous blogs and websites have published about the project including <a href="http://tympanus.net/codrops/collective/collective-96/" target="_blank">Codrops</a>, <a href="http://speckyboy.com/2014/01/30/40-tiny-web-based-apps-tools-web-designers/" target="_blank">Speckyboy</a>, <a href="http://www.smashingmagazine.com/smashing-newsletter-issue-new-year-special-edition-2013/" target="_blank">Smashing Magazine</a>, <a href="http://sidebar.io/2013/12/26" target="_blank">Sidebar</a>, <a href="http://www.webdesignerdepot.com/2014/02/whats-new-for-designers-february-2014/" target="_blank">Webdesigner Depot</a>, <a href="http://www.cssauthor.com/weekly-web-development-resources-tools-35/" target="_blank">CSS Author</a>, <a href="https://www.producthunt.com/tech/uinames" target="_blank">Product Hunt</a>, <a href="https://news.layervault.com/stories/12511-ui-names" target="_blank">LayerVault</a>, <a href="http://oozled.com/resources/just-handy" target="_blank">Oozled</a> and <a href="http://thenextweb.com/dd/2015/02/18/300-awesome-free-things-massive-list-free-resources-know/" target="_blank">The Next Web</a>.</p>
			<h2>Feedback</h2>
			<p>Feedback can be tweeted directly to <a href="http://twitter.com/thomweerd" target="_blank">@thomweerd</a>.</p>
			<h2>Contribute</h2>
			<p>This massive collection of names wouldn’t have been as complete without the help of these wonderful people. If you would like to contribute to the project too, then send a pull-request on <a href="http://github.com/thm/uinames" target="_blank">Github</a>!</p>
			<p class="clearfix">
				<?php
				
					// render github contributors
					echo github(
						'<a href="$href" class="contributor">
							<span class="popup"><span>$user</span> +$contributions</span>
							<img class="lazy" src="assets/img/blank.png" data-src="$img" />
						</a>'
					);
					
				?>
			</p>
			<p>Check out <a href="http://uifaces.com" target="_blank">uifaces.com</a> as well!</p>
		</div>
		<div id="api-panel">
			<h2>Overview</h2>
			<p>All responses are returned as JSON(P). There is currently no request limit. However, please keep the amount of requests to a minimum, and cache responses whenever possible.</p>
			<h2>Usage</h2>
			<p>Basic usage:</p>
<pre>http://uinames.com/api/
---
{
  "name":"John",
  "surname":"Doe",
  "gender":"male",
  "region":"United States"
}
</pre>
			<h2>Optional Parameters</h2>
			<p>Amount of names to return, between <code>1</code> and <code>500</code>:</p>
			<pre><span>http://uinames.com/api/</span>?amount=25</pre>
			<p>Limit results to the <code>male</code> or <code>female</code> gender:</p>
			<pre><span>http://uinames.com/api/</span>?gender=female</pre>
			<p>Region-specific results:</p>
			<pre><span>http://uinames.com/api/</span>?region=germany</pre>
			<p>Require a minimum number of characters in a name:</p>
			<pre><span>http://uinames.com/api/</span>?minlen=25</pre>
			<p>Require a maximum number of characters in a name:</p>
			<pre><span>http://uinames.com/api/</span>?maxlen=75</pre>
			<p>For JSONP, specify a callback function to wrap results in:</p>
			<pre><span>http://uinames.com/api/</span>?callback=example</pre>
			<!-- no region currently supports different languages
			<p>Results for a language within a region:</p>
			<pre><span>http://uinames.com/api/</span>?region=switzerland&amp;language=german</pre>
			-->
			<h2>Exception Handling</h2>
			<p>Error messages have the following format:</p>
			<pre>{"error":"Region or language not found"}</pre>
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
		<div id="facts">
			<div><?php print $regions; ?><p>Regions Available</p></div>
			<div><?php print number_format($available, 0, ',', '.'); ?><p>Names Available</p></div>
			<div title="<?php print number_format($stats->api->calculated, 0, ',', '.'); ?> names generated by API"><?php print number_format($stats->web->calculated, 0, ',', '.'); ?><p>Names Generated</p></div>
		</div>
	</div>

	<div id="region" class="clearfix">
		<div>
			<label for="rsearch" id="regionSearchLabel" class="ac">Search regions</label>
			<span class="regionCount" aria-live="polite" aria-atomic="true"><?php echo (count($names) + 1) . '/' . (count($names) + 1); ?></span>
			<input class="search" type="text" placeholder="Type to search..." role="combobox" aria-expanded="true" aria-autocomplete="list" id="rsearch" aria-labelledby="regionSearchLabel" aria-owns="regionList" aria-activedescendant="region-0" />
		</div>
		<ul id="regionList" role="listbox">
			<li id="region-0" class="pos-0" role="option" tabindex="-1"><span class="flag"><img src="assets/img/flags/random.gif" /></span><span class="region-label">Random</span></li>
			<?php
				
				$newRegions = array("Slovakia");
				$favRegions = array("United States", "Germany", "France", "Russia", "India");
				
				$total = count($names);
				
				for ($i = 0; $i < $total; $i++) {
					$region = $names[$i]->region;
					
					$new = '';
					if (in_array($region, $newRegions)) {
						$new = ' new';
					}
					
					$fav = '';
					if (in_array($region, $favRegions)) {
						$fav = ' fav';
						if ($region == $favRegions[0]) {
							$fav = ' fav active';
						}
					}
					
					echo '<li id="region-' . ($i + 1) . '" class="pos-' . ($i + 1) . $new . $fav . '" role="option" tabindex="-1"><span class="flag"><img class="lazy" src="assets/img/blank.png" data-src="assets/img/flags/' . str_replace(' ', '-', strtolower($region)) . '.png" /></span><span class="region-label">' . $region . '</span></li>';
				}
				
			?>
			<span class="contribute" style="display: none;">
				<a href="https://github.com/thm/uinames" target="_blank" class="clearfix">
					<?php echo file_get_contents('assets/img/crying-octocat.svg'); ?>
					<p>No region could be found. Consider contributing on Github!</p>
				</a>
			</span>
		</ul>
	</div>
	
	<div id="overlay"></div>
	
	
	<div id="share-box">
		<iframe src="//www.facebook.com/plugins/like.php?href=http%3A%2F%2Fuinames.com&amp;width=100px&amp;layout=button_count&amp;action=like&amp;show_faces=false&amp;share=false&amp;height=65" scrolling="no" frameborder="0" style="border:none; overflow: hidden; width: 87px; height: 20px;" allowTransparency="true"></iframe>
		
		<iframe src="http://ghbtns.com/github-btn.html?user=thm&repo=uinames&type=watch&count=true" allowtransparency="true" frameborder="0" scrolling="0" width="86" height="20"></iframe>

		<a href="http://twitter.com/share?text=Generate%20random%20names%20for%20use%20in%20designs%20and%20mockups%20on&url=http://uinames.com&via=thomweerd&related=thomweerd" class="twitter-button" onclick="window.open(this.href, 'Tweet', 'scrollbars=no,width=500,height=280'); return false;">
			<span class="share-button"><span class="share-icon"><?php echo file_get_contents('assets/img/share-icon-twitter.svg'); ?></span>Tweet</span>
			<span class="share-count">1.8k</span>
		</a>
	</div>
	
	<script src="assets/js/site.js"></script>

</body>
</html>
