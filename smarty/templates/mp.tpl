<!-- parliament specific -->
{if isset($data_wtt)}
  <ul data-role="listview" data-inset="true">
    <li>
      <h3>{t}Received public messages{/t}</h3><p>{$data_wtt.received_public_messages}</p>
    </li>
    <li>
      <h3>{t}Answered public messages{/t}</h3><p>{$data_wtt.sent_public_replies}</p>
    </li>
    <li>
      <h3>{t}Received private messages{/t}</h3><p>{$data_wtt.received_private_messages}</p>
    </li>
  </ul>
  
  <label for="wtt">API:</label>
  <input data-theme="e" data-type="text" name="name" id="wtt" value="{$data_wtt_api_call}" readonly='readonly' />
{/if}

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
