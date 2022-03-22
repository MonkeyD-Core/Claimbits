<?php 
	define('BASEPATH', true);
	include('../../../system/init.php');
	if($is_online && $data['admin'] == 0 || !$is_online){exit;}

	if(isset($_POST['change_design']))
	{
		ob_start();
		include(BASE_PATH.'/template/'.$config['theme'].'/static/theme.php');
		$cssContent = ob_get_contents();
		ob_end_clean();
		
		if(!empty($cssContent))
		{
			$theme = fopen(BASE_PATH.'/template/'.$config['theme'].'/static/theme.css', 'w');
			fwrite($theme, $cssContent);
			fclose($theme);
		}
	}
?>