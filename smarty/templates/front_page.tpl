{extends file="main_html.tpl"}

{block name=title}{$title} - profile.KohoVolit.eu{/block}

{block name=pageTitle}{$title} - profile.KohoVolit.eu{/block}

{block name=h1}{$title}{/block}

{block name=content}

<!-- front page specific -->
<ul data-role="listview" data-inset="true">
  <li data-role="list-divider">{t}Select{/t}</li>
  <li><a href="/mp"><h3>{t}MPs{/t}</h3></a></li>
  <li><a href="/group"><h3>{t}Groups{/t}</h3></a></li>
  <li><a href="/constituency"><h3>{t}Constituencies{/t}</h3></a></li>
  <li><a href="/term"><h3>{t}Parliamentary terms{/t}</h3></a></li>
  <li><a href="/parliament"><h3>{t}Parliaments{/t}</h3></a></li>
  <li><a href="/country"><h3>{t}Countries{/t}</h3></a></li>
  <li data-role="list-divider">{t}API{/t}</li>
  <li><a href="http://community.kohovolit.eu/doku.php/api"><h3>{t}Documentation{/t}</h3></a></li>
</ul>
{/block}
