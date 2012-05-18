<?php
  //new smarty
  $smarty = new SmartyProfile;

  $smarty->assign('title',_('Profiles'));

  //final display
  $smarty->display('front_page.tpl');
?>  
