<?PHP
	include('file_name.php');
	
	
	
	//加入群組內的重複名需再這判斷
	if(count($_POST) > 1 && $_POST['MATH'] == "" && $_POST['SERVER_IP'] == "" && $_POST['SERVER_PASSWORD'] == ""){
		foreach($_POST as $key => $value){
			$name[$key] = $value;
		}
	}else{
		
		foreach($_POST as $key => $value){
			//加入群組內的重複名需再這判斷 如果名稱有底線的要加這再
			if($_POST['SERVER_IP'] != ""){
				$name = $key;
				$name_value = $value;
			}
			elseif($_POST['SERVER_PASSWORD'] != ""){
				$name = $key;
				$name_value = $value;
			}
			else{
				$name = $key;	
				$name_value = $value;
			}
		}
	}
	
	$nn = 0;
	$handle = @fopen($open_file,"r");
	$i==1;
	while(($data = fgetcsv($handle,1000,"\n")) != false){
		foreach($data as $key => $value){
			$value = str_replace('[','',$value);
			$value = str_replace(']','',$value);
			if($value[0] == '#'){
				$value = str_replace('#','',$value);
				if(in_array($value,$file_name1)){
					$value = '#'.$value;
				}
			}
			if(in_array($value,$file_name1)){
				$str_check = $value;
			}else{
				$str = explode(' ',$value);
				$str_v = $str[1];
				$str_check = ($str[0] != "") ? $str[0]:$value;
			}
			
			
			if(count($name) > 1 && $value == 'PLAY_FILE'){
				$aa[$nn][] = $value."\n";
					foreach($name as $device_name => $set_value){
						$aa[$nn][] = $device_name." ".$set_value."\n";
					}
			}
			elseif($str_check == $name){
				$str_check = '['.$str_check.']';	
				$aa[$nn][] = $str_check;
				if(in_array($value,$file_name1)){
					$aa[$nn][] = "\n".$name_value."\n";
				}else{
					$aa[$nn][] = "\n".$str_check." ".$name_value."\n";
				}
				
			//加入群組內的重複名需再這判斷
			}
			elseif($str_check == 'MATH'){
				if($str_v == ''){
					$aa[$nn][] = '#'.$value."\n";
				}
				else{
					$aa[$nn][] = $value."\n";
				}
				$i++;
				if($i == $_POST['total_MATH']){
					$aa[$nn][] = $str_check." ".$_POST['MATH']."\n";
				}
				if($_POST['total_MATH'] == ''){
					$i = 0;
				}
				
			}
			elseif($str_check == 'SERVER_IP'){
				if($str_v == ''){
					$aa[$nn][] = '#'.$value."\n";
				}
				else{
					$aa[$nn][] = $value."\n";
				}
				$i++;
				if($i == $_POST['total_SERVER_IP'] && $i < 3){
					$aa[$nn][] = $str_check." ".$_POST['SERVER_IP']."\n";
				}
				if($_POST['total_SERVER_IP'] == ''){
					$i = 0;
				}
				
			}
			elseif($str_check == 'SERVER_PASSWORD'){
				if($str_v == ''){
					$aa[$nn][] = '#'.$value."\n";
				}
				else{
					$aa[$nn][] = $value."\n";
				}
				$i++;
				if($i == $_POST['total_SERVER_PASSWORD'] && $i < 3){
					$aa[$nn][] = $str_check." ".$_POST['SERVER_PASSWORD']."\n";
				}
				if($_POST['total_SERVER_PASSWORD'] == ''){
					$i = 0;
				}

			}
			else{
				if(in_array($value,$file_name) || in_array($value,$file_name1) ){
					$aa[$nn][] = '['.$value.']'."\n";
				}
				elseif($value[0] == '#'){
					$value = str_replace('#','',$value);
					if(in_array($value,$file_name) || in_array($value,$file_name1) ){
						$aa[$nn][] = '#'.'['.$value.']'."\n";
					}
					else{
						$aa[$nn][] = '#'.'['.$value.']'."\n";
					}
				}
				else{
					$aa[$nn][] = $value."\n";
				}
			}
												
		}
		$nn++;
	}
	fclose($handle);
	$fp = @fopen($open_file,'w')  or die("Unable to open file!");
	foreach($aa as $key => $value){
		foreach($value as $k => $v){
			fwrite($fp, $v);
		}
	}
	fclose($fp);
	
	echo "<script>location.href='main.php';</script>";
	?>
