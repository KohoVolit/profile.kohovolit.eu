{extends file="main_html.tpl"}

{block name=title}{if $individual}{$code}{else}{t}Parliaments{/t}{/if} - profile.KohoVolit.eu{/block}

{block name=pageTitle}{if $individual}{$code}{else}{t}Parliaments{/t}{/if} - profile.KohoVolit.eu{/block}

{block name=h1}{if $individual}{$code}{else}{t}Parliaments{/t}{/if}{/block}

{block name=content}
  <!-- individual -->
  {if $individual}
    {t}The parliament with code{/t} <em>{$code}</em> {t}is not in our database. Try find it manually.{/t}
  {/if}

  <!-- parliament -->
  <ul data-role="listview" data-inset="true">
    <li data-role="list-divider">{t}Parliaments{/t}</li>
    {foreach from=$parl_order key=key item=item}
	  <li><a href="{$parliaments.$key.link_only}">
	    <h3>{$item}</h3>
	    <p>{$parliaments.$key.short_name}</p>
	    <p>{$parliaments.$key.description}</p>
	  </a></li>
    {/foreach}
  </ul>
  
  <label for="basic">API:</label>
  <input data-theme="e" data-type="text" name="name" id="basic" value="{$parliament_api}" readonly='readonly' />
 
{/block}
