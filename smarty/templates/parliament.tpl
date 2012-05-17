<!-- parliament specific -->
{if $data_specific|@count > 0}
  <ul data-role="listview" data-inset="true" {if $data_specific|@count > 10}data-filter="true"{/if}>
    
      <li data-role="list-divider">{t}Terms{/t}</li>
        {foreach from=$data_specific item=item}
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
  </ul>
  
  <label for="basic">API:</label>
  <input data-theme="e" data-type="text" name="name" id="basic" value="{$data_specific_api_call}" readonly='readonly' />
{/if}
