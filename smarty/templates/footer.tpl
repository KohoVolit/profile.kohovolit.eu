<!--footer -->
  <a href="/about" data-icon="info" data-ajax="false">{t}About{/t}</a>
  <a href="/settings" data-icon="gear" data-ajax="false">{t}Settings{/t}</a>
  <!--social plugins-->
     {literal}
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
		<a><div class="fb-like" data-send="false" data-layout="button_count" data-width="130" data-show-faces="true"></div></a>

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
{/literal}

