<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
	<title>{block name=title}{/block}</title>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
	<meta name="viewport" content="width=device-width, initial-scale=1"> 
	<meta name="Description" content="{t}MSGID_HTML_HEAD_DESCRIPTION{/t}" />
	<link rel="shortcut icon" href="/images/favicon.ico" type="image/x-icon">
	<link rel="icon" href="/images/favicon.ico" type="image/x-icon">
	<link rel="stylesheet" href="http://code.jquery.com/mobile/1.1.0/jquery.mobile-1.1.0.min.css" />
	<!--<link rel="stylesheet" href="http://jeromeetienne.github.com/jquery-mobile-960/css/jquery-mobile-fluid960.min.css" />-->
	
	<script src="http://code.jquery.com/jquery-1.7.1.min.js"></script>
	<!--<script type="text/javascript">
	  $(document).bind("mobileinit", function(){
 		  //apply overrides here
 		  //disabling all ajax:
		    $.extend(  $.mobile , {
                  ajaxEnabled: false
             });
	  });  
	</script>-->
	<script src="http://code.jquery.com/mobile/1.1.0/jquery.mobile-1.1.0.min.js"></script>
	
	<!-- google analytics -->
	<script type="text/javascript">
		var _gaq = _gaq || [];
		(function() {
		  var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
		  ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
		  var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
		})();
    </script>
	
	<link rel="stylesheet" type="text/css" href="/css/votewiki.css" />
	<!--<link rel="stylesheet" type="text/css" href="/css/jqm-icon-pack-1.1-original.css" />-->

	{block name=head}{/block}

</head>

<body>
  <div data-role="page" id="{block name=pageId}{/block}" data-title="{block name=pageTitle}{/block}">
    
	<div data-role="header" data-theme="e" {block name=headerDataTheme}{/block}>
		<h1>{block name=h1}{/block}</h1>
		<a href="/" data-role="button" data-icon="home" data-iconpos="notext">{t}Home{/t}</a>
		<a href="http://community.kohovolit.eu/doku.php/api" data-role="button">{t}API{/t}</a>
	</div><!-- /header -->
	<div data-role="content">
	  
		{block name=content}{/block}
	</div><!-- /content -->
	<div data-role="footer" {block name=footerDataTheme}{/block} data-tap-toggle="false" data-iconpos="top">
	  <!--footer -->
	  <a href="/about" data-icon="info" data-ajax="false">{t}About{/t}</a>
	  <a href="/settings" data-icon="gear" data-ajax="false">{t}Settings{/t}</a>
	  <!--social plugins-->
		 {literal}
			
			<!--google+-->
			<a>
			<g:plusone annotation="inline" size="small" width="220"></g:plusone>
			</a>
			<!-- Place this render call where appropriate -->
			<script type="text/javascript">
				window.___gcfg = {lang: 'sk'};
				(function() {
					var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
					po.src = 'https://apis.google.com/js/plusone.js';
					var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
				})();
			</script>
			<!--twitter-->
			<a href="https://twitter.com/share" class="twitter-share-button" data-via="KohoVolitEU">Tweet</a>

			<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
			
			
			<!--facebook-->
			<span id="fb-root"></span>
			<script>(function(d, s, id) {
				var js, fjs = d.getElementsByTagName(s)[0];
				if (d.getElementById(id)) return;
				js = d.createElement(s); js.id = id;
				js.src = '//connect.facebook.net/sk_SK/all.js#xfbml=1';
				fjs.parentNode.insertBefore(js, fjs);
				}(document, 'script', 'facebook-jssdk'));
			</script>
			<div class="fb-like" data-send="false" data-layout="button_count" data-width="130" data-show-faces="true"></div>
		{/literal}

	</div><!-- /footer -->
	<script type="text/javascript">
	  	$('[data-role=page]').live('pageshow', function (event, ui) {
			try {
				_gaq.push(['_setAccount', '{$smarty.const.GOOGLE_ANALYTICS_KEY}']);
				hash = location.hash;
				if (hash) {
				    _gaq.push(['_trackPageview', hash.substr(1)]);
				} else {
				    _gaq.push(['_trackPageview']);
				}
			} catch(err) {
			}
		});
</script>
  </div><!-- /page -->
</body>
</html>
