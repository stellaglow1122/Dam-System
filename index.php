<?php
include('cfg/cfg.php');
include('cfg/cfg.inc.php');
include('cls/Project.cls.php');
include('cls/WADB.cls.php');
	include('html/header.html');
	include('html/view_top.html');
	include('html/view_menu.html');
	$file = fopen(IP_PATH,'r');
	while(! feof($file)){
		$IP[] = fgets($file);
	}
	fclose($file);

	switch($_GET['item']){
		case "setting":
			switch($_GET['cat']){
				case "palert":
					include('html/view_setting_palert.html');
				break;
				
				case "":
				break;
			}
			
		break;
		
		case "main":
			include('html/view_content.html');
		break;
		
		case "history":
			include('html/view_history.html');
		break;
		
		case "event":
			if($_GET['data'] != ''){
				include('html/view_event_detail.html');
			}else{
				include('html/view_event.html');
			}
		break;
			
		default:
			include('html/view_content.html');
		break;
	}
	include('html/view_footer.html');
?>