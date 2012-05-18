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
		
	case 'settings':
		settings_page();
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
		require_once("./page/front_page.php");
}


function settings_page() {
  global $api_data, $api_votewiki, $locale, $locales; 
  
  $smarty = new SmartyProfile;

  asort($locales);
  $smarty->assign('current_locale', $locale);
  $smarty->assign('locales', $locales); 

  $smarty->assign('h1', 'Settings');
  $smarty->assign('page_id', 'settings');
  $smarty->assign('locale',$locale);
  $smarty->display('settings.tpl'); 

}

function static_page($page)
{
	$smarty = new SmartyProfile;
	$smarty->assign('h1', $page);
	$smarty->assign('page_id', $page);
	$smarty->display($page . '.tpl');
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
