{extends file="main_html.tpl"}

{block name=title}{$title} - profile.KohoVolit.eu{/block}

{block name=pageTitle}{$title} - profile.KohoVolit.eu{/block}

{block name=h1}{$title}{/block}

{block name=content}
  <!-- group -->
  <ul data-role="listview" data-inset="true" {if $data|@count > 10}data-filter="true"{/if}>
    
      <li data-role="list-divider">
        {if ($data|@count == 1)}{t}Basic information{/t}
        {else}{$title}   
        {/if}
      </li>
        {foreach from=$data item=row}
          {foreach from=$row key=key item=item}
		    {if ($item.value != '') and ($item.value != '...')}
			  <li>
			    {if (isset($item.link))}{$item.link}{/if}
		   		  {if (isset($item.label))}<p>{t}{$item.label}{/t}:</p>{/if}
					<h3>
			  		  {$item.value}
		   		   </h3>
		   		   {if isset($item.details)}
		   		     {foreach from=$item.details item=detail}
		   		       {if ($detail != '')}<p>{$detail}</p>{/if}
		   		     {/foreach}
		   		   {/if}
		   		 {if (isset($item.link))}</a>{/if}
			  </li>
			{/if}
          {/foreach}
        {/foreach}
  </ul>
  
  <label for="basic">API:</label>
  <input data-theme="e" data-type="text" name="name" id="basic" value="{$api_call}" readonly='readonly' />
  
  <!-- attributes -->
  {if isset($attribute)}
      <ul data-role="listview" data-inset="true">
		<li data-role="list-divider">{t}Further information{/t}</li>
		{foreach from=$attribute key=key item=item}
		  <li>
			{if (isset($item.format) and ($item.format == 'image'))}
			  <img src="{$item.image_url}" />
			{/if}
			<p>{t}{$item.label}{/t}:</p>
			<h3>
			  {if (isset($item.format) and ($item.format == 'translate'))}
			    {t}{$item.value}{/t}
			  {else}
			    {$item.value}
			  {/if}
			</h3>
			<p>({if isset($item.parliament)}{$item.parliament}, {/if}{$item.since} - {$item.until})</p>
		  </li>
		{/foreach}
	  </ul> 
	  <label for="basic">API:</label>
	  <input data-theme="e" data-type="text" name="name" id="basic" value="{$api_call_attribute}" readonly='readonly' />   
  {/if}
  
  <!-- {$entity} specific -->
  {include file="$entity.tpl"}


{/block}
