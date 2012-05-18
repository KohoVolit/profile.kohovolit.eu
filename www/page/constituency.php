<?php
  global $api, $locale;
  
  $entity = 'constituency';
  $idef = 'id';
  
  //new smarty
  $smarty = smarty_individual($idef);
  $smarty->assign('entity',$entity);
  
  //BASIC INFORMATION - TO READ FROM DB
  $input = array(
    'entity' => array(
      array(
        'name' => 'constituency',
        'columns' => array('id','name','short_name', 'description','since','until'),
        'pkey_columns' => array($idef),
        'filter' => ((isset($_GET[$idef]) and ($_GET[$idef] != '')) ? array($idef => htmlspecialchars($_GET[$idef])) : null),
      ),
      array(
        'name' => 'parliament',
        'columns' => array('code', 'name', 'short_name', 'description'),
        'pkey_columns' => array('code'),
        'on' => '"code" = "constituency"."parliament_code"'
      ),
    ),
    'lang' => $locale['lang'],
    //'filter' => array('_limit' => '10'),
  );
  
  $data = $api->read('EntityInfo',$input);
  $data = translate($data);
  
  
  //BASIC INFORMATION - TO DISPLAY
  if (count($data) > 1) {
    //list
    foreach($data as $row) {
      $output[] = array(
        'row' => array(
          'value' => l('constituency',$row['constituency_id'],$row['constituency_name'],'id'),
          //'link' => l('group',$row['group_id'],$row['group_name'],'id',false),
          'details' => array(
            $row['constituency_short_name'],
            $row['constituency_description'],
            '('.format_date_infinity($row['constituency_since']).' - '.format_date_infinity($row['constituency_until']).')',
            l('parliament',$row['parliament_code'],$row['parliament_name'],'code'),
          ),
        ),
      );
      $sort[] = $row['group_name'];
    }
    $output = _sort($output,$sort);
  } else if (count($data) == 1) {
    //individual
    $output = array( array(
      'name' => array('value' => $data[0]['constituency_name'], 'label' => _('Name')),
      'short_name' => array('value' => $data[0]['constituency_short_name'], 'label' => _('Short name')),
      'description' => array('value' => $data[0]['constituency_description'], 'label' => _('Description')),
      'since' => array('value' => format_date_infinity($data[0]['constituency_since']), 'label' => _('Since')),
      'until' => array('value' => format_date_infinity($data[0]['constituency_until']), 'label' => _('Until')),
      'parliament' => array('value' => $data[0]['parliament_name'], 'label' => _('Parliament'), 'details' => array($data[0]['parliament_short_name'], $data[0]['parliament_description']), 'link' => l('parliament',$data[0]['parliament_code'],'','code',false)),    
    )); 
  } else $output = null;

  $smarty->assign('data',$output);
    
  //title
  if(count($data) == 1)
    $smarty->assign('title',$data[0]['constituency_name']);
  else if (isset($_GET[$idef]))
    $smarty->assign('title',htmlspecialchars($_GET[$idef]));
  else
    $smarty->assign('title',_('constituencies'));
    
  //api address
  $api_call = API_DOMAIN . "/data/Constituency" . (isset($_GET[$idef]) ? "?{$idef}={$_GET[$idef]}" : '');
  $smarty->assign('api_call',$api_call);
  

  //is it individual?
  if(count($data) != 1) $individual = false;
  else {
    $individual = true; 

  
  //ATTRIBUTES
  $smarty = attribute($smarty,$entity,$idef,$data);

	  
  //constituency specific = nothing
  
  }
  $smarty->assign('individual',$individual);   
  
  //final display
  $smarty->display('entity.tpl');
  
