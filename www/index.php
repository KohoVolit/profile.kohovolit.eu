<?php

require '../config/settings.php';
require '../setup.php';

$api = new ApiDirect('data');

$page = isset($_GET['page']) ? $_GET['page'] : null;

switch ($page)
{
	case 'about':
		static_page($page);
		break;

	case 'mp':
	case 'group':
	case 'parliament':
	case 'term':
	case 'country':
	case 'constituency':
		require_once("./page/{$page}.php");
		break;
		
	default:
		front_page();
}



function attribute($smarty,$entity,$idef,$data) {
  global $api,$locale;
  
  if(count($data) == 1) {
	  $attribute = array();
	  //$parls = array();
	  $attribute_db = $api->read(ucfirst($entity).'Attribute',array($entity.'_'.$idef => $_GET[$idef]));

	  if (count($attribute_db) > 0)
		foreach ($attribute_db as $ad) {
		  if (($ad['lang'] == '-') or ($locale['lang'] == $ad['lang'])) {
			$label = ucfirst(str_replace('_',' ',$ad['name']));
			if (isset($ad['parl']) and (!isset($parls[$ad['parl']]))) {
			  $parl_db = $api->readOne('Parliament',array('code' => $ad['parl']));
			  $parls[$ad['parl']] = $parl_db['name'];
			}
			$attribute[$ad['name']] = array(
			  'label'=>$label, 
			  'value' => $ad['value'], 
			  //'parl' => $ad['parl'], 			  
			  'since' => format_date_infinity($ad['since']), 
			  'until' => format_date_infinity($ad['until'])
			);
			if (isset($ad['parl'])) $attribute[$ad['name']]['parliament'] = l('parliament',$ad['parl'],$parls[$ad['parl']],'code');
			if (($ad['name'] == 'image') or ($ad['name'] == 'logo')) {
			  $attribute['image']['format'] = 'image';
			  $attribute['image']['image_url'] = API_FILES_URL . '/' . $ad['parl'] . "/images/{$entity}/" . $ad['value'];
			}
		  }
		}
	  if (count($attribute) > 0) {
		$smarty->assign('attribute',$attribute);
		$smarty->assign('api_call_attribute',API_DOMAIN . '/data/' . ucfirst($entity) . 'Attribute?'.$entity.'_'.$idef . '=' . $_GET[$idef]);
	  }
  }
  return $smarty;
}



function constituency_page() {
  global $api, $locale;
  
  $input = array(
    'entity' => array(
      array(
        'name' => 'constituency',
        'columns' => array('id','name','short_name','description','since','until'),
        'pkey_columns' => array('id'),
        //'filter' => array('id' => '20'),
      ),
      array(
        'name' => 'parliament',
        'columns' => array('code','name','short_name','description'),
        'pkey_columns' => array('code'),
        'on' => '"code" = "constituency"."parliament_code"',
      ),
    ),
    'lang' => $locale['lang'],
  );
  
  
  
  $data = $api->read('EntityInfo',$input);

}


/*function country_page() {
  global $api, $locale;
  
  $input = array(
    'entity' => array(
      array(
        'name' => 'country',
        'columns' => array('code','name','short_name','description'),
        'pkey_columns' => array('code'),
      ),
    ),
    'lang' => $locale['lang'],
  );
  $data = $api->read('EntityInfo',$input);
  print_r($data);
}*/

/*function parliament_page() {
  global $api, $locale;
  
  $data = array(
    'entity' => array(
      array(
        'name' => 'parliament',
        'columns' => array('code','name','short_name','description'),
        'pkey_columns' => array('code'),
      ),
      array(
        'name' => 'parliament_kind',
        'columns' => array('code','name','short_name','description'),
        'on' => '"code" = "parliament"."parliament_kind_code"',
        'pkey_columns' => array('code'),
      ),
      array(
        'name' => 'country',
        'columns' => array('code','name','short_name','description'),
        'on' => '"code" = "parliament"."country_code"',
        'pkey_columns' => array('code'),
      ),
    ),
    /*'filter' => array(
      'code' => 'cz/psp',
    ),
    'lang' => 'cs'
  );
  
  $res = $api->read('EntityInfo',$data);
  print_r($res);
}*/


function term_page() {
error_reporting(E_ALL);
  global $api, $locale;
  $smarty = new SmartyProfile;
  
  $individual = true;
  
  if (isset($_GET['id'])) {
    $term_db = $api->readOne('Term',array('id' => $_GET['id']));
    $smarty->assign('id',$_GET['id']);
  } else
    $individual = false;
  $smarty->assign('individual',$individual); 
  
  if (isset($term_db['id'])) {
  
  
  }

}
/*
function parliament_page() {
error_reporting(E_ALL);
  global $api, $locale;
  $smarty = new SmartyProfile;
  
  $individual = true;
  
  if (isset($_GET['code'])) {
    $parliament_db = $api->readOne('ParliamentInfo',array('code' => $_GET['code']));
    $smarty->assign('code',$_GET['code']);
  } else
    $individual = false;
  $smarty->assign('individual',$individual); 
   
  if (isset($parliament_db['parliament_code'])) {
    //parliament
    $parliament = array(
      'name' => array('value' => $parliament_db['parliament_name'], 'label' => 'Name'),
      'short_name' => array('value' => $parliament_db['parliament_short_name'], 'label' => 'Short name'),
      'description' => array('value' => $parliament_db['parliament_description'], 'label' => 'Description'),
      'parliament_kind_name' => array('value' => $parliament_db['parliament_kind_name'], 'label' => 'Parliament kind name'),
      'country_name' => array('value' => l('country',$parliament_db['country_code'],$parliament_db['country_name'],'code'), 'label' => 'Country name'),
    );
    $smarty->assign('parliament',$parliament);
	$smarty->assign('parliament_api',API_DOMAIN . '/data/ParliamentInfo?code=' . $_GET['code']);
    
    
    $terms = array();
    $term_db = $api->read('Term',array('parliament_kind_code' => $parliament_db['parliament_kind_code'],'country_code' => $parliament_db['country_code'], '_order' => array(array('since','DESC'))));
    if (count($term_db) > 0)
      foreach ($term_db as $td)
		  $terms[] = array(
		    'name' => $td['name'],
		    'short_name' => $td['short_name'],
		    'description' => $td['description'],
		    'since' => format_date_infinity($td['since']),
		    'until' => format_date_infinity($td['until']),
		    'link_only' => "/term?id={$td['id']}&parliament_code={$code}",
		  );
    $smarty->assign('terms',$terms);
	$smarty->assign('term_api',API_DOMAIN . '/data/Term?country_code=' . $parliament_db['country_code'] . '&parliament_kind_code=' . $parliament_db['parliament_kind_code']);
    
    $smarty->display('parliament.tpl');
  } else {
    $parliaments_db = $api->read('Parliament',array());
    //order locale awared
    foreach ($parliaments_db as $key => $row)
      $parl_order[$key] = $row['name'];
    uasort ($parl_order,'strcoll');

    foreach ($parliaments_db as $key => $pd) {
      $parliaments[$key] = array(
        'name' => $pd['name'],
        'short_name' => $pd['short_name'],
		'description' => $pd['description'],
		'link_only' => "/parliament?code={$pd['code']}",
      );
    }
    $smarty->assign('parl_order',$parl_order);
    $smarty->assign('parliaments',$parliaments);
	$smarty->assign('parliament_api',API_DOMAIN . '/data/Parliament');
    $smarty->display('parliaments.tpl');
  }
  
}
*/
function mp_page () {
  global $api, $locale;
  $smarty = new SmartyProfile;
  
  //mp
  $mp = array();
  if (isset($_GET['id']))
    $mp_db = $api->readOne('Mp',array('id' => $_GET['id']));
  if (isset($mp_db['id'])) {
	  $mp = array(
			'last_name' => array('value' => $mp_db['last_name'],'label' => 'Last name'),
			'first_name' => array('value' => $mp_db['first_name'],'label' => 'First name'),
			'middle_names' => array('value' => $mp_db['middle_names'],'label' => 'Middle names'),
			'disambiguation' => array('value' => $mp_db['disambiguation'],'label' => 'Disambiguation'),
			'sex' => array('value' => ($mp_db['sex'] == 'm' ? 'Male' : ($mp_db['sex'] == 'f' ? 'Female' : '')), 'label' => 'Sex', 'format' => 'translate'),
			'pre_title' => array('value' => $mp_db['pre_title'],'label' => 'Title (pre)'),
			'post_title' => array('value' => $mp_db['post_title'],'label' => 'Title (post)'),
			'born_on' => array('value' => format_date_infinity($mp_db['born_on']),'label' => 'Born on'),
			'died_on' => array('value' => format_date_infinity($mp_db['died_on']),'label' => 'Died on'),
		  );
	  $smarty->assign('mp',$mp);
	  $smarty->assign('mp_api',API_DOMAIN . '/data/Mp?id=' . $_GET['id']);
	 
	  
	  //attributes
	  $mp_attribute = array();
	  $parls = array();
	  $mp_attribute_db = $api->read('MpAttribute',array('mp_id' => $_GET['id']));
	  
	  if (count($mp_attribute_db) > 0)
		foreach ($mp_attribute_db as $mad) {
		  if (($mad['lang'] == '-') or ($locale['lang'] == $mad['lang'])) {
		    $label = ucfirst(str_replace('_',' ',$mad['name']));
		    if (!isset($parls[$mad['parl']])) {
		      $parl_db = $api->readOne('Parliament',array('code' => $mad['parl']));
		      $parls[$mad['parl']] = $parl_db['name'];
		    }
		    $mp_attribute[$mad['name']] = array(
		      'label'=>$label, 
		      'value' => $mad['value'], 
		      'parl' => $mad['parl'], 
		      'parliament_link' => l('parliament',$mad['parl'],$parls[$mad['parl']],'code'),
		      'since' => format_date_infinity($mad['since']), 
		      'until' => format_date_infinity($mad['until'])
		    );
		    if ($mad['name'] == 'image') {
		      $mp_attribute['image']['format'] = 'image';
		      $mp_attribute['image']['image_url'] = API_FILES_URL . '/' . $mad['parl'] . '/images/mp/' . $mad['value'];
		    }
		  }
		}
	  $smarty->assign('mp_attribute',$mp_attribute);
	  $smarty->assign('mp_attribute_api',API_DOMAIN . '/data/MpAttribute?mp_id=' . $_GET['id']);


	  //mp in group
	  $mp_in_group = array();
	  $order = array(
		array('term_since','DESC'),
		array('since','ASC')
	  );
	  $mp_in_group_db = $api->read('MpInGroupInfo',array('mp_id'=>$_GET['id'],'_order' => $order));

	  if (count($mp_in_group_db) > 0) {
		foreach ($mp_in_group_db as $migd) {
		   $new_mig = array(
		    'group_link' => l('group',$migd['group_id'],$migd['group_name']),
		    'role_code' => $migd['role_code'],
		    'since' => format_date_infinity ($migd['since']),
		    'until' => format_date_infinity ($migd['until']),
		    'group_id' => $migd['group_id'],
		    'group_name' => $migd['group_name'],
		  );
		  if ($migd['constituency_id'] != '') 
		    $new_mig['constituency_link'] = l('constituency',$migd['constituency_id'],$migd['constituency_name']);
		  if ($migd['sex'] == 'f')
		    $new_mig['role'] = $migd['role_female_name'];
		  else
		    $new_mig['role'] = $migd['role_male_name'];
		  
		  if (!isset($mp_in_group[$migd['term_id']][$migd['parliament_code']]['parliament_link']))
		    $mp_in_group[$migd['term_id']][$migd['parliament_code']]['parliament_link'] = l('parliament',$migd['parliament_code'],$migd['parliament_name'],'code');
		  if (!isset($mp_in_group[$migd['term_id']][$migd['parliament_code']]['term_link']))
		    $mp_in_group[$migd['term_id']][$migd['parliament_code']]['term_link'] = l('term',$migd['term_id'],$migd['term_name']);
		  if (!isset($mp_in_group[$migd['term_id']][$migd['parliament_code']]['country_link']))
		    $mp_in_group[$migd['term_id']][$migd['parliament_code']]['country_link'] = l('country',$migd['country_code'],$migd['country_name'],'code');
		  $mp_in_group[$migd['term_id']][$migd['parliament_code']]['mp_in_group'][] = $new_mig;
		}
	  }
	  //print_r($mp_in_group);die();
	  $smarty->assign('mp_in_group',$mp_in_group);
	  $smarty->assign('mp_in_group_api',API_DOMAIN . '/data/MpInGroupInfo?mp_id=' . $_GET['id']);
	  
	  $smarty->display('mp.tpl');
  } else {
    //list all mps
  }
  
}

function format_date_infinity ($date) {
  if (($date == 'infinity') or ($date == '-infinity') or $date == '')
    return '...';
  else
    return strftime('%x',strtotime($date));
}

/**
*
*/
function l($entity,$param,$label,$id_code = 'id', $full = true) {
  return "<a href='/{$entity}?{$id_code}={$param}'>" . ($full ? "{$label}</a>" : ''); 
}


function smarty_individual ($param){

  $smarty = new SmartyProfile;
  
  $individual = true;
  if (isset($_GET[$param])) 
    $smarty->assign($param,$_GET[$param]);
  else
    $individual = false;
  $smarty->assign('individual',$individual);
  
  return $smarty;
  
}

function translate(array $data) {
  $str = '_translated';
  $strlen = strlen($str);
  $out = array();
  
  if (count($data > 0)) {
    foreach ($data as $rkey=>$row) 
      foreach ($row as $ikey=>$item) 
        if (substr($ikey, -1*$strlen) != $str) 
          if (isset($row[$ikey.$str]) and ($row[$ikey.$str] != ''))
            $out[$rkey][$ikey] = $row[$ikey.$str];
          else
            $out[$rkey][$ikey] = $item;
  }
  else return null;
           
  return $out;
}

/**
*
*/
function _sort($data,$array,$restart_key=true) {
  $out=array();
  uasort ($array,'strcoll');
  foreach ($array as $key=>$row) {
    if (isset($data[$key]))
      if ($restart_key)
        $out[] = $data[$key];
      else
        $out[$key] = $data[$key];
  }
  return $out;
}

?>
