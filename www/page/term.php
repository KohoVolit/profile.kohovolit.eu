<?php
  global $api, $locale;
  
  $entity = 'term';
  $idef = 'id';
  
  //new smarty
  $smarty = smarty_individual($idef);
  $smarty->assign('entity',$entity);
  
  //BASIC INFORMATION - TO READ FROM DB
  $input = array(
    'entity' => array(
      array(
        'name' => 'term',
        'columns' => array('id','name','short_name','description','since','until'),
        'pkey_columns' => array($idef),
        'filter' => ((isset($_GET[$idef]) and ($_GET[$idef] != '')) ? array($idef => htmlspecialchars($_GET[$idef])) : null),
      ),
      array(
        'name' => 'parliament_kind',
        'columns' => array('code', 'name', 'short_name', 'description'),
        'pkey_columns' => array('code'),
        'on' => '"code" = "term"."parliament_kind_code"',
      ),
      array(
        'name' => 'country',
        'columns' => array('code', 'name', 'short_name', 'description'),
        'pkey_columns' => array('code'),
        'on' => '"code" = "term"."country_code"',
      ),
    ),
    'lang' => $locale['lang'],
    'filter' => array('_order' => array(array('"term"."since"','DESC'))),
  );
  
  $data = $api->read('EntityInfo',$input);
  $data = translate($data);
  
  //BASIC INFORMATION - TO DISPLAY
  if (count($data) > 1) {
    //list
    foreach($data as $row) {
      $output[] = array(
        'row' => array(
          'value' => l('term',$row['term_id'],$row['term_name'],'code'),
          //'link' => l('group',$row['group_id'],$row['group_name'],'id',false),
          'details' => array(
            '('.format_date_infinity($row['term_since']).' - '.format_date_infinity($row['term_until']).')',
            $row['term_short_name'],
            $row['term_description'], 
            $row['parliament_kind_name'],
            l('country',$row['country_code'],$row['country_name'],'id'),
          ),
        ),
      );
    }
  } else if (count($data) == 1) {
    //individual
    $output = array( array(
      'name' => array('value' => $data[0]['term_name'], 'label' => _('Name')),
      'short_name' => array('value' => $data[0]['term_short_name'], 'label' => _('Short name')),
      'description' => array('value' => $data[0]['term_description'], 'label' => _('Description')),
      'date' => array('value' => format_date_infinity($data[0]['term_since']).' - '.format_date_infinity($data[0]['term_until']), 'label' => _('Date')),
      'parliament_kind' => array('value' => $data[0]['parliament_kind_name'], 'label' => _('Parliament kind'), 'details' => array($data[0]['parliament_kind_short_name'], $data[0]['parliament_kind_description'])),
      'country' => array('value' => $data[0]['country_name'], 'label' => _('Country'), 'details' => array($data[0]['country_short_name'], $data[0]['country_description']), 'link' => l('country',$data[0]['country_code'],'','code',false), 
      ),   
    )); 
  } else $output = null;

  $smarty->assign('data',$output);

  //title
  if(count($data) == 1)
    $smarty->assign('title',$data[0]['term_name']);
  else if (isset($_GET[$idef]))
    $smarty->assign('title',htmlspecialchars($_GET[$idef]));
  else
    $smarty->assign('title',_('Terms'));
  
  //api address
  $api_call = API_DOMAIN . "/data/Term" . (isset($_GET[$idef]) ? "?{$idef}={$_GET[$idef]}" : '');
  $smarty->assign('api_call',$api_call);
  
   
  //ATTRIBUTES
  $smarty = attribute($smarty,$entity,$idef,$data);
	  
  //term specific = parliaments->groups
  //TERM SPECIFIC - TO READ FROM DB
  $input_specific = array(
    'entity' => array(
      array(
        'name' => 'group',
        'columns' => array('id','name','short_name'),
        'pkey_columns' => array('id'),
      ),
      array(
        'name' => 'term',
        'columns' => array('id', 'name', 'short_name', 'description'),
        'pkey_columns' => array('id'),
        'on' => '"id" = "group"."term_id"'
      ),
      array(
        'name' => 'parliament',
        'columns' => array('code', 'name', 'short_name', 'description'),
        'pkey_columns' => array('code'),
        'on' => '"code" = "group"."parliament_code"'
      ),
    ),
    'lang' => $locale['lang'],
    //'filter' => array('_limit' => '10'),
    'filter' => array(
      '"term"."id"' => htmlspecialchars($_GET[$idef]),
    ),    
  );

  $data_specific = $api->read('EntityInfo',$input_specific);
  $data_specific = translate($data_specific);

  //TERM SPECIFIC - TO DISPLAY
  $sort = array();
  foreach($data_specific as $row) {
  	  $output_specific_title[$row['parliament_code']] = $row['parliament_name'];
      $output_specific[$row['parliament_code']][] = array(
          'value' => $row['group_name'],
          'link' => l('group',$row['group_id'],'','id',false),
          'details' => array(
            $row['group_short_name']
          ),
      );
      $sort[$row['parliament_code']][] = $row['group_name'];
      $data_specific_api_call[$row['parliament_code']] = API_DOMAIN . '/data/Group?parliament_code=' . $row['parliament_code'] . '&term_id=' . htmlspecialchars($_GET[$idef]);
  }
  uasort ($output_specific_title,'strcoll');
  foreach ($output_specific as $key=>$os)
     $output_specific[$key] = _sort($output_specific[$key],$sort[$key]);

  $smarty->assign('data_specific_title',$output_specific_title);
  $smarty->assign('data_specific',$output_specific);
  
  $smarty->assign('data_specific_api_call',$data_specific_api_call);
  
  
  
  //final display
  $smarty->display('entity.tpl');

?>
