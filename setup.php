<?php
// set autoloading function for classes
function profile_autoload($class_name)
{
    if (file_exists(P_DIR . "/classes/$class_name.php"))
		require_once P_DIR . "/classes/$class_name.php";
}
spl_autoload_register('profile_autoload');

// set locale
if (defined('NOT_USE_SESSION'))
	$locale = reset($locales);
else
{
	// store URL parameters that should be persistent to the session
	session_start();
	if (isset($_GET['locale']))
		$_SESSION['locale'] = $_GET['locale'];

	// choose locale according to locale stored in session
	if (isset($_SESSION['locale']) && array_key_exists($_SESSION['locale'], $locales))
		$locale = $locales[$_SESSION['locale']];
	else
		$locale = reset($locales);
}

mb_internal_encoding('UTF-8');
date_default_timezone_set($locale['time_zone']);

putenv('LC_ALL=' . $locale['system_locale']);
setlocale(LC_ALL, $locale['system_locale']);
bindtextdomain('messages', P_DIR . '/www/locale');
textdomain('messages');

?>
