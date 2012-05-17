<?php
  global $api, $locale;
  
  $entity = 'group';
  $idef = 'id';
  
  //new smarty
  $smarty = smarty_individual($idef);
  $smarty->assign('entity',$entity);
  
  //BASIC INFORMATION - TO READ FROM DB
  $input = array(
    'entity' => array(
      array(
        'name' => 'group',
        'columns' => array('id','name','short_name'),
        'pkey_columns' => array($idef),
        'filter' => ((isset($_GET[$idef]) and ($_GET[$idef] != '')) ? array($idef => htmlspecialchars($_GET[$idef])) : null),
      ),
      array(
        'name' => 'group_kind',
        'columns' => array('code','name','short_name','description'),
        'pkey_columns' => array('code'),
        'on' => '"code" = "group"."group_kind_code"'
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
  );
  
  $data = $api->read('EntityInfo',$input);
  $data = translate($data);
  
  //BASIC INFORMATION - TO DISPLAY
  if (count($data) > 1) {
    //list
    foreach($data as $row) {
      $output[] = array(
        'row' => array(
          'value' => l('group',$row['group_id'],$row['group_name'],'id'),
          //'link' => l('group',$row['group_id'],$row['group_name'],'id',false),
          'details' => array(
            $row['group_short_name'],
            l('parliament',$row['parliament_code'],$row['parliament_name'],'code'),
            l('term',$row['term_id'],$row['term_name']),
            $row['group_kind_name'],  
          ),
        ),
      );
      $sort[] = $row['group_name'];
    }
    $output = _sort($output,$sort);
  } else if (count($data) == 1) {
    //individual
    $output = array( array(
      'name' => array('value' => $data[0]['group_name'], 'label' => _('Name')),
      'short_name' => array('value' => $data[0]['group_short_name'], 'label' => _('Short name')),
      'parliament' => array('value' => $data[0]['parliament_name'], 'label' => _('Parliament'), 'details' => array($data[0]['parliament_short_name'], $data[0]['parliament_description']), 'link' => l('parliament',$data[0]['parliament_code'],'','code',false)),
      'term' => array('value' => $data[0]['term_name'], 'label' => _('Term'), 'details' => array($data[0]['term_short_name'], $data[0]['term_description']), 'link' => l('term',$data[0]['term_id'],'','id',false)),
      'group_kind_code' => array('value' => $data[0]['group_kind_name'], 'label' => _('Group kind'), 'details' => array($data[0]['group_kind_short_name'], $data[0]['group_kind_description']), 
      'link' => l('group_kind',$data[0]['group_kind_code'],'','code',false)),      
    )); 
  } else $output = null;

  $smarty->assign('data',$output);
    
  //title
  if(count($data) == 1)
    $smarty->assign('title',$data[0]['group_name']);
  else if (isset($_GET[$idef]))
    $smarty->assign('title',htmlspecialchars($_GET[$idef]));
  else
    $smarty->assign('title',_('Groups'));
  
  //api address
  $api_call = API_DOMAIN . "/data/Group" . (isset($_GET[$idef]) ? "?{$idef}={$_GET[$idef]}" : '');
  $smarty->assign('api_call',$api_call);
  
   
  //ATTRIBUTES
  $smarty = attribute($smarty,$entity,$idef,$data);

	  
  //group specific = memberships
  //GROUP SPECIFIC - TO READ FROM DB
    $input_specific = array(
    'entity' => array(
      array(
        'name' => 'mp',
        'columns' => array('id','first_name','last_name','middle_names','disambiguation','sex', 'pre_title','post_title','born_on','died_on'),
        'pkey_columns' => array('id'),
      ),
      array(
        'name' => 'mp_in_group',
        'columns' => array('mp_id','group_id','role_code','party_id','constituency_id','since','until'),
        'pkey_columns' => array('mp_id','group_id','role_code','since'),
        'on' => '"mp_id" = "mp"."id"',
        'no_translation' => true,
      ),   
      array(
        'name' => 'group',
        'columns' => array('id','name','short_name'),
        'pkey_columns' => array('id'),
        'on' => '"id" = "mp_in_group"."group_id"',
      ),
      array(
        'name' => 'group_kind',
        'columns' => array('code','name','short_name','description'),
        'pkey_columns' => array('code'),
        'on' => '"code" = "group"."group_kind_code"'
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
      array(
        'name' => 'role',
        'columns' => array('male_name','female_name','description'),
        'pkey_columns' => array('code'),
        'on' => '"code" = "mp_in_group"."role_code"',
      ),
    ),
    'lang' => $locale['lang'],
    //'filter' => array('_limit' => '10'),
     'filter' => array('"group"."id"' => htmlspecialchars($_GET[$idef])
    )
  );

  $data_specific = $api->read('EntityInfo',$input_specific);
  $data_specific = translate($data_specific);

  //GROUP SPECIFIC - TO DISPLAY
  foreach($data_specific as $row) {
    if (!isset($output_specific[$row['mp_id']])) {
      $output_specific[] = array(
          'value' => 
          "{$row['mp_last_name']} {$row['mp_first_name']}" . 
          (($row['mp_middle_names'] != '') ? ', ' . $row['mp_middle_names']:'') . 
          (($row['mp_pre_title'] != '') ? ', ' . $row['mp_pre_title']:'') . 
          (($row['mp_post_title'] != '') ? ', ' . $row['mp_post_title']:'') . (($row['mp_disambiguation'] != '') ? ' ('.$row['mp_disambiguation'].')':''),
          'link' => l('mp',$row['mp_id'],'','id',false),
          'details' => array(
            ($row['mp_sex'] == 'f' ? $row['role_female_name'] : $row['role_male_name']) . ' (' . format_date_infinity($row['mp_in_group_since']) . ' - ' .format_date_infinity($row['mp_in_group_until']) . ')',
          ),
      );
      $sort0[] = $row['mp_first_name'];
      $sort1[] = $row['mp_last_name'];
    } else {
      $output_specific[$row['mp_id']]['details'][] .= 
            ($row['mp_sex'] == 'f' ? $row['role_female_name'] : $row['role_male_name']) . ' (' . format_date_infinity($row['mp_in_group_since'])  . ' - ' .format_date_infinity($row['mp_in_group_until']) . ')' ;
    }
  }
  //sort
  $output_specific = _sort($output_specific,$sort0,false);
  $output_specific = _sort($output_specific,$sort1);

  $smarty->assign('data_specific',$output_specific);
  $smarty->assign('data_specific_api_call',API_DOMAIN . '/data/MpInGroupInfo?'.$entity.'_'.$idef . '=' . $_GET[$idef]);
  
  
  
  //final display
  $smarty->display('entity.tpl');
?>
