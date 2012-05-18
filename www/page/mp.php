<?php
  global $api, $locale;
  
  $entity = 'mp';
  $idef = 'id';
  
  //new smarty
  $smarty = smarty_individual($idef);
  $smarty->assign('entity',$entity);
  
  //BASIC INFORMATION - TO READ FROM DB
  $input = array(
    'entity' => array(
      array(
        'name' => 'mp',
        'columns' => array('id','first_name','last_name','middle_names','disambiguation','sex', 'pre_title','post_title','born_on','died_on'),
        'pkey_columns' => array($idef),
        'filter' => ((isset($_GET[$idef]) and ($_GET[$idef] != '')) ? array($idef => htmlspecialchars($_GET[$idef])) : null),
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
    
    if (isset($_GET['letter'])) {
      $letter = htmlspecialchars($_GET['letter']);
      foreach($data as $row) {
		if (mb_substr($row['mp_last_name'],0,1) == $letter) {
		  $output[] = array(
		    'row' => array(
		      'value' => "{$row['mp_last_name']} {$row['mp_first_name']}" . 
		      (($row['mp_middle_names'] != '') ? ', ' . $row['mp_middle_names']:'') . 
		      (($row['mp_pre_title'] != '') ? ', ' . $row['mp_pre_title']:'') . 
		      (($row['mp_post_title'] != '') ? ', ' . $row['mp_post_title']:'') . (($row['mp_disambiguation'] != '') ? ' ('.$row['mp_disambiguation'].')':''),
		      'link' => l('mp',$row['mp_id'],'','id',false),
		    ),
		  );
		  $sort0[] = $row['mp_first_name'];
		  $sort1[] = $row['mp_last_name'];
		}
	  }
	  $output = _sort($output,$sort0,false);
	  $output = _sort($output,$sort1);
	} else {
	  $output = array();
	  foreach($data as $row) {
	    if (!isset($output[mb_substr($row['mp_last_name'],0,1)])) {
	      $output[mb_substr($row['mp_last_name'],0,1)] = array(
	        'row' => array(
		      'value' => mb_substr($row['mp_last_name'],0,1) . ' ...',
		      'link' => l('mp',mb_substr($row['mp_last_name'],0,1),'','letter',false),
		    ),
		  );
	      $sort[mb_substr($row['mp_last_name'],0,1)] = mb_substr($row['mp_last_name'],0,1);
	    } 
	  }
	  $output = _sort($output,$sort);
	}
  } else if (count($data) == 1) {
    //individual
    $output = array( array(
		'last_name' => array('value' => $data[0]['mp_last_name'],'label' => _('Last name')),
		'first_name' => array('value' => $data[0]['mp_first_name'],'label' => _('First name')),
		'middle_names' => array('value' => $data[0]['mp_middle_names'],'label' => _('Middle names')),
		'disambiguation' => array('value' => $data[0]['mp_disambiguation'],'label' => _('Disambiguation')),
		'sex' => array('value' => ($data[0]['mp_sex'] == 'm' ? _('Male') : ($data[0]['mp_sex'] == 'f' ? _('Female') : '')), 'label' => _('Sex')),
		'pre_title' => array('value' => $data[0]['mp_pre_title'],'label' => _('Title (pre)')),
		'post_title' => array('value' => $data[0]['mp_post_title'],'label' => _('Title (post)')),
		'born_on' => array('value' => format_date_infinity($data[0]['mp_born_on']),'label' => _('Born on')),
		'died_on' => array('value' => format_date_infinity($data[0]['mp_died_on']),'label' => _('Died on')),
    )); 
  } else $output = null;

  $smarty->assign('data',$output);
  
  //title
  if(count($data) == 1)
    $smarty->assign('title',$data[0]['mp_first_name'] . ' ' . $data[0]['mp_last_name']);
  else if (isset($_GET[$idef]))
    $smarty->assign('title',htmlspecialchars($_GET[$idef]));
  else
    $smarty->assign('title',_('MPs'));
  
  //api address
  $api_call = API_DOMAIN . "/data/Mp" . (isset($_GET[$idef]) ? "?{$idef}={$_GET[$idef]}" : '');
  $smarty->assign('api_call',$api_call);
  
  //is it individual?
  if(count($data) != 1) $individual = false;
  else {
    $individual = true;   
   
  //ATTRIBUTES
  $smarty = attribute($smarty,$entity,$idef,$data);

	  
  //mp specific = memberships
  //MP SPECIFIC - TO READ FROM DB
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
      array(
        'name' => 'constituency',
        'columns' => array('id', 'name', 'short_name', 'description'),
        'pkey_columns' => array('id'),
        'on' => '"id" = "mp_in_group"."constituency_id"',
      ),
    ),
    'lang' => $locale['lang'],
    //'filter' => array('_limit' => '10'),
     'filter' => array('"mp"."id"' => htmlspecialchars($_GET[$idef]),
       '_order' => array(array('parliament_name','ASC'),array('term_since','DESC')),
    )
  );

  $data_specific = $api->read('EntityInfo',$input_specific);
  $data_specific = translate($data_specific);

  //MP SPECIFIC - TO DISPLAY
  foreach($data_specific as $row) {
    $output_specific_title[$row['parliament_code']][$row['term_id']] = $row['parliament_name'] . ', ' . $row['term_name'];
    if (!isset($output_specific[$row['parliament_code']][$row['term_id']][$row['group_id']])) {
      $output_specific[$row['parliament_code']][$row['term_id']][$row['group_id']] = array(
          'value' => $row['group_name'],
          'link' => l('group',$row['group_id'],'','id',false),
          'details' => array(
            ($row['mp_sex'] == 'f' ? $row['role_female_name'] : $row['role_male_name']) . ' (' . format_date_infinity($row['mp_in_group_since']) . ' - ' .format_date_infinity($row['mp_in_group_until']) . ')',
          ),
      );
      
       $sort[$row['parliament_code']][$row['term_id']][$row['group_id']] = $row['group_name'];
       
      //constituency
      if ($row['constituency_id'] != '') {  
        $output_specific[$row['parliament_code']][$row['term_id']]['c-'.$row['constituency_id']] = array(
          'value' => $row['constituency_name'],
          'link' => l('constituency',$row['constituency_id'],'','id',false),
          'details' => array(
            $row['constituency_short_name'],
            $row['constituency_description']
          ),
        );
         $sort[$row['parliament_code']][$row['term_id']]['c-'.$row['constituency_id']] = $row['constituency_name'];
      }
      
    } else {
      $output_specific[$row['parliament_code']][$row['term_id']][$row['group_id']]['details'][] .= 
            ($row['mp_sex'] == 'f' ? $row['role_female_name'] : $row['role_male_name']) . ' (' . format_date_infinity($row['mp_in_group_since'])  . ' - ' .format_date_infinity($row['mp_in_group_until']) . ')' ;
    }
  }
//print_r($output_specific);die();
  //sort
  uksort ($output_specific_title,'strcoll');
  /*foreach ($output_specific_title as $key=>$ost)
    ksort ($output_specific_title[$key]);*/
  foreach ($output_specific as $key0=>$os0)
    foreach ($os0 as $key=>$os)
      $output_specific[$key0][$key] = _sort($output_specific[$key0][$key],$sort[$key0][$key],false);

  $smarty->assign('data_specific_title',$output_specific_title);
  $smarty->assign('data_specific',$output_specific);
  $smarty->assign('data_specific_api_call',API_DOMAIN . '/data/MpInGroupInfo?'.$entity.'_'.$idef . '=' . $_GET[$idef]);
  
  }
  $smarty->assign('individual',$individual);
  
  //final display
  $smarty->display('entity.tpl');
?>
