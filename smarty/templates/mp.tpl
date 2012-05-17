<!-- parliament specific -->
{if $data_specific|@count > 0}
  {foreach from=$data_specific_title key=key0 item=dst0}
  {foreach from=$dst0 key=key item=dst}
  <ul data-role="listview" data-inset="true" {if $data_specific.$key0.$key|@count > 10}data-filter="true"{/if}>
    
      <li data-role="list-divider">{$dst}</li>
        {foreach from=$data_specific.$key0.$key item=item}
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

  {/foreach}
  {/foreach}
  
{/if}
