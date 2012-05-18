<?php
  global $api, $locale;
  
  $entity = 'parliament';
  $idef = 'code';
  
  //new smarty
  $smarty = smarty_individual($idef);
  $smarty->assign('entity',$entity);
  
  //BASIC INFORMATION - TO READ FROM DB
  $input = array(
    'entity' => array(
      array(
        'name' => 'parliament',
        'columns' => array('code','name','short_name','description'),
        'pkey_columns' => array($idef),
        'filter' => ((isset($_GET[$idef]) and ($_GET[$idef] != '')) ? array($idef => htmlspecialchars($_GET[$idef])) : null),
      ),
      array(
        'name' => 'parliament_kind',
        'columns' => array('code','name','short_name','description'),
        'pkey_columns' => array('code'),
        'on' => '"code" = "parliament"."parliament_kind_code"'
      ),
      array(
        'name' => 'country',
        'columns' => array('code', 'name', 'short_name', 'description'),
        'pkey_columns' => array('code'),
        'on' => '"code" = "parliament"."country_code"'
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
          'value' => l('parliament',$row['parliament_code'],$row['parliament_name'],'code'),
          //'link' => l('group',$row['group_id'],$row['group_name'],'id',false),
          'details' => array(
            $row['parliament_short_name'],
            $row['parliament_description'],
            l('country',$row['country_code'],$row['country_name'],'code'),
            $row['parliament_kind_name'],  
          ),
        ),
      );
      $sort[] = $row['parliament_name'];
    }
    $output = _sort($output,$sort);
  } else if (count($data) == 1) {
    //individual
    $output = array( array(
      'name' => array('value' => $data[0]['parliament_name'], 'label' => _('Name')),
      'short_name' => array('value' => $data[0]['parliament_short_name'], 'label' => _('Short name')),
      'description' => array('value' => $data[0]['parliament_description'], 'label' => _('Description')),
      'parliament_kind' => array('value' => $data[0]['parliament_kind_name'], 'label' => _('Parliament kind'), 'details' => array($data[0]['parliament_kind_short_name'], $data[0]['parliament_kind_description'])),
      'country' => array('value' => $data[0]['country_name'], 'label' => _('Country'), 'details' => array($data[0]['country_short_name'], $data[0]['country_description']), 'link' => l('country',$data[0]['country_code'],'','code',false),
      'link' => l('country',$data[0]['country_code'],'','code',false)),      
    )); 
  } else $output = null;

  $smarty->assign('data',$output);

  //title
  if(count($data) == 1)
    $smarty->assign('title',$data[0]['parliament_name']);
  else if (isset($_GET[$idef]))
    $smarty->assign('title',htmlspecialchars($_GET[$idef]));
  else
    $smarty->assign('title',_('Parliaments'));
  
  //api address
  $api_call = API_DOMAIN . "/data/Parliament" . (isset($_GET[$idef]) ? "?{$idef}={$_GET[$idef]}" : '');
  $smarty->assign('api_call',$api_call);
  
  //is it individual?
  if(count($data) != 1) $individual = false;
  else {
    $individual = true;      


  //ATTRIBUTES
  $smarty = attribute($smarty,$entity,$idef,$data);

	  
  //country specific = parliaments
  //GROUP SPECIFIC - TO READ FROM DB
  $input_specific = array(
    'entity' => array(
      array(
        'name' => 'term',
        'columns' => array('id', 'name', 'short_name', 'description','since','until'),
        'pkey_columns' => array('id'),
      ),
      array(
        'name' => 'parliament_kind',
        'columns' => array('code', 'name', 'short_name', 'description'),
        'pkey_columns' => array('code'),
        'on' => '"code" = "term"."parliament_kind_code"',
      ),
      array(
        'name' => 'parliament',
        'columns' => array('code', 'name', 'short_name', 'description'),
        'pkey_columns' => array('code'),
        'on' => '"parliament_kind_code" = "parliament_kind"."code"
        AND "parliament"."country_code" = "term"."country_code"',
      ),
    ),
    'lang' => $locale['lang'],
    //'filter' => array('_limit' => '10'),
    'filter' => array(
      '"parliament"."code"' => htmlspecialchars($_GET[$idef]),
      '_order' => array(array('"term"."since"','DESC')),
    ),
    
  );

  $data_specific = $api->read('EntityInfo',$input_specific);
  $data_specific = translate($data_specific);

  //GROUP SPECIFIC - TO DISPLAY
  foreach($data_specific as $row) {
      $output_specific[] = array(
          'value' => $row['term_name'],
          'link' => l('term',$row['term_id'],'','id',false),
          'details' => array(
            '(' . format_date_infinity($row['term_since']) . ' - ' .format_date_infinity($row['term_until']) . ')',
          ),
      );
  }

  $smarty->assign('data_specific',$output_specific);
  $smarty->assign('data_specific_api_call',API_DOMAIN . '/data/Term?parliament_kind_code=' . $data_specific[0]['parliament_kind_code'] . '&country_code='.$data[0]['country_code']);
  
  }
  $smarty->assign('individual',$individual);  
  
  //final display
  $smarty->display('entity.tpl');

?>
