<?php
  global $api, $locale;
  
  $entity = 'country';
  $idef = 'code';
  
  //new smarty
  $smarty = smarty_individual($idef);
  $smarty->assign('entity',$entity);
  
  //BASIC INFORMATION - TO READ FROM DB
  $input = array(
    'entity' => array(
      array(
        'name' => 'country',
        'columns' => array('code','name','short_name','description'),
        'pkey_columns' => array($idef),
        'filter' => ((isset($_GET[$idef]) and ($_GET[$idef] != '')) ? array($idef => htmlspecialchars($_GET[$idef])) : null),
      ),
    ),
    'lang' => $locale['lang'],
  );
  
  $data = $api->read('EntityInfo',$input);
  $data = translate($data);
  
  //BASIC INFORMATION - TO DISPLAY
  if (count($data) > 1) {
    //list
    foreach($data as $row) {
      $output[] = array(
        'row' => array(
          'value' => $row['country_name'],
          'link' => l('country',$row['country_code'],$row['country_name'],'code',false),
          'details' => array(
            $row['country_short_name'],
            $row['country_description'], 
          ),
        ),
      );
      $sort[] = $row['country_name'];
    }
    $output = _sort($output,$sort);
  } else if (count($data) == 1) {
    //individual
    $output = array( array(
      'name' => array('value' => $data[0]['country_name'], 'label' => _('Name')),
      'short_name' => array('value' => $data[0]['country_short_name'], 'label' => _('Short name')),
      'description' => array('value' => $data[0]['country_description'], 'label' => _('Description')),     
    )); 
  } else $output = null;

  $smarty->assign('data',$output);

  //title
  if(count($data) == 1)
    $smarty->assign('title',$data[0]['country_name']);
  else if (isset($_GET[$idef]))
    $smarty->assign('title',htmlspecialchars($_GET[$idef]));
  else
    $smarty->assign('title',_('Countries'));
  
  //api address
  $api_call = API_DOMAIN . "/data/Country" . (isset($_GET[$idef]) ? "?{$idef}={$_GET[$idef]}" : '');
  $smarty->assign('api_call',$api_call);


  //is it individual?
  if(count($data) != 1) $individual = false;
  else {
    $individual = true;   
   
  //ATTRIBUTES
  $smarty = attribute($smarty,$entity,$idef,$data);

	  
  //country specific = parliaments
  //COUNTRY SPECIFIC - TO READ FROM DB
  $input_specific = array(
    'entity' => array(
      array(
        'name' => 'parliament',
        'columns' => array('code', 'name', 'short_name', 'description','country_code'),
        'pkey_columns' => array('code'),
      ),
    ),
    'lang' => $locale['lang'],
    //'filter' => array('_limit' => '10'),
    'filter' => array(
      '"parliament"."country_code"' => htmlspecialchars($_GET[$idef]),
    ),    
  );

  $data_specific = $api->read('EntityInfo',$input_specific);
  $data_specific = translate($data_specific);

  //COUNTRY SPECIFIC - TO DISPLAY
  $sort = array();
  foreach($data_specific as $row) {
      $output_specific[] = array(
          'value' => $row['parliament_name'],
          'link' => l('parliament',$row['parliament_code'],'','code',false),
          'details' => array(
            $row['parliament_short_name'],
            $row['parliament_description']
          ),
      );
      $sort[] = $row['parliament_name'];
  }
  $output_specific = _sort($output_specific,$sort);

  $smarty->assign('data_specific',$output_specific);
  $smarty->assign('data_specific_api_call',API_DOMAIN . '/data/Parliament?country_code=' . $data[0]['country_code']);
  
  }
  $smarty->assign('individual',$individual);    
  
  //final display
  $smarty->display('entity.tpl');

?>
