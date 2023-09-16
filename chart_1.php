<?php
	session_start();
	require_once('../../model/require_login.php');
	$Device = new Device();
	$device_name = $Device->get_sensor_data('',$_POST['device_code']);
	if($_POST['query_date_start'] != ""){
		$device_load = $Device->get_sensor_load_table_val('',$_POST['device_code'],strtotime($_POST['query_date_start']),strtotime($_POST['query_date_end']));
		$start_date = strtotime($_POST['query_date_start']);
 		$end_date = strtotime($_POST['query_date_end']);
	}else{
		switch($_POST['type']){
			case "date":
				$start_date = strtotime(date("Y-m-d 00:00:00",time()));
 				$end_date = strtotime(date("Y-m-d H:i:s",time()));
			break;
			
			case "week":
				$start_date = strtotime(date("Y-m-d 00:00:00",time()-604800));
 				$end_date = strtotime(date("Y-m-d H:i:s",time()));
			break;
			
			case "month":
				$start_date = strtotime(date("Y-m-d 00:00:00",time()-2592000));
 				$end_date = strtotime(date("Y-m-d H:i:s",time()));
			break;
			
			case "season":
				$start_date = strtotime(date("Y-m-d 00:00:00",strtotime("-3 months",time())));
 				$end_date = strtotime(date("Y-m-d H:i:s",time()));
			break;
			
			case "year":
				$start_date = strtotime(date("Y-m-d 00:00:00",strtotime("-1 years",time())));
 				$end_date = strtotime(date("Y-m-d H:i:s",time()));
			break;
		
		}	
		$device_load = $Device->get_sensor_load_table_val('',$_POST['device_code'],$start_date,$end_date);
		
	}
?>
            
			<?php 
				$code = $_POST['device_code'];
				$name = $_POST[$code];
				$limit_data = $Device->get_sensor_data('',$code);
				$device_name = $Device->get_device_data($_POST['device_code']);
				foreach($limit_data as $kk => $vv){
						$upper_limit[$vv['sensor_id']] = $vv['limit_uper'];
						$lower_limit[$vv['sensor_id']] = $vv['limit_lower'];
				}
				if($device_load != ""){
					
					 
		                foreach($device_load as $key => $value){
							$date_xAxis[] = "'".date('Y-m-d H:00',$value['date'])."'";
							$device_date[date('Y-m-d H:00',$value['date'])][$value['name']] = ($value['formula_val'] != "") ? $value['formula_val']:'null';
							$device_temperature[date('Y-m-d H:00',$value['date'])]['temperature'] = ($value['temperature'] != "") ? $value['temperature']:'null';	
		                }               
	                    $xAxis = implode(',',array_unique($date_xAxis)); //列軸時間換算
						//將sensor的值 存為陣列
	                    foreach($device_date as $key => $value){
				            foreach($name as $k => $v){
				                $device_name_1[$v][] = ($value[$v] != "") ? $value[$v]:'null';
				            }
				        }
						
						switch($_POST['other']){
							case "temperature":
								//溫度解析
								foreach($device_temperature as $key => $value){
									$temperature[] = ($value['temperature'] != "") ? $value['temperature']:'null';
								}
								$Y_temp = ",{name: '溫度',color: 'red',tooltip: {valueSuffix: ' °C'}, data: [".implode(',',$temperature)."],tooltip: {valueSuffix: '°C'}}";
							break;
							
							case "rain":
							$rain_sql = "select * from column where name = '' and date between '".$start_date."' and '".$end_date."' GROUP BY DATE ORDER BY ID DESC";
							$rain_data = $Device->custom($rain_sql);

							//溫度解析
								foreach($rain_data as $key => $value){
									$rain[] = ($value['formula_val'] != "") ? $value['formula_val']:'null';
								}
								$Y_temp = ",{name: '時雨量',type: 'column',color: 'red',tooltip: {valueSuffix: 'mm'}, data: [".implode(',',$rain)."],tooltip: {valueSuffix: 'mm'}}";
							break;
							
							default:
								$Y_temp = "";
							break;
						}
						
	                    //解析sensor的陣列 提供給圖內的查詢
	                    $i = 0;
	                    foreach($device_name_1 as $key => $value){
	                        if(in_array($key,$name)){
	                            $Y_Axisp[] = "{name: '".$key."', yAxis: 1, marker: {symbol: '".$chart_symbol[$i]."'}, data: [".implode(',',$value)."]}";                    
	                            $i++;
	                        }
	                    }
	                    
	                    $series = implode(',',$Y_Axisp);  //值的時間換算
	                    $unit =  "'".$device_name['0']['unit']."'";
	                   	
	                }else{
	                	echo "請選取儀器";
	                }
						$total_number = count($date_xAxis);
		                $x_tickInterval  = round($total_number/20);
		                $y_tickInterval  = ($_POST['y_tickInterval'] != "") ? ",tickInterval:".$_POST['y_tickInterval']:"";
		                $y_max  = ($_POST['max'] != "") ? ",max:".$_POST['max']:"";
		                $y_min  = ($_POST['min'] != "") ? ",min:".$_POST['min']:"";
						$interval = ($_POST['int'] != "") ? $_POST['int']:"0.5";
					$sql = "insert into record_txt (user, record) values ('".$_SESSION['user']['user_name']."','chart/".$device_name['device_name']."')";
					$Device->custom($sql);
						
			?>



<script>
Highcharts.chart('container', {
    chart: {
		height: 500,
        zoomType: 'xy'
    },
    title: {
        text: ''
    },
    subtitle: {
        text: '曲線圖查詢'
    },
    xAxis: [{
        categories: [<? echo $xAxis;?>],
        crosshair: true,
		tickInterval:<?=$x_tickInterval;?>
    }],
    yAxis: [
	<?php
		if($_POST['other'] == 'temperature'){
	?>
	{ // Secondary yAxis
        labels: {
            format: '{value}°C',
            style: {
                color: 'red'
            }
        },
        title: {
            text: 'Temperature',
            style: {
                color: 'red'
            }
        },
        opposite: true
    },
	<?php
		}else{
	?>
	{ // Secondary yAxis
        labels: {
            format: '{value}°mm',
            style: {
                color: 'red'
            }
        },
        title: {
            text: 'Rain',
            style: {
                color: 'red'
            }
        },
        opposite: true
    },
	<?php
		}
	?>	
	{ // Primary yAxis
		gridLineColor: '#e0e0e0',
		gridLineWidth: 1,
        labels: {
            style: {
                color: Highcharts.getOptions().colors[0]
            }
        },
        title: {
            text: '<?php echo $limit_data['0']['unit'];?>',
            style: {
                color: Highcharts.getOptions().colors[0]
            }
        },
		tickInterval: <?php echo $interval; ?>
		<?php
			echo $y_max.$y_min;
		?>
    }],
    tooltip: {
        shared: true
    },
    series: [
	<? echo $series.$Y_temp;?>
	]
});

		$('#buttonExport').click(function() {
	        var e = document.getElementById("ExportOption");
	        var ExportAs = e.options[e.selectedIndex].value;   
	        
	        if(ExportAs == 'PNG')
	        {
	            chart.exportChart({type: 'image/png', filename: 'my-png'}, {subtitle: {text:''}});
	        }
	        if(ExportAs == 'JPEG')
	        {
	            chart.exportChart({type: 'image/jpeg', filename: 'my-jpg'}, {subtitle: {text:''}});
	        }
	        if(ExportAs == 'PDF')
	        {
	            chart.exportChart({type: 'application/pdf', filename: 'my-pdf'}, {subtitle: {text:''}});
	        }
	        if(ExportAs == 'SVG')
	        {
	            chart.exportChart({type: 'image/svg+xml', filename: 'my-svg'}, {subtitle: {text:''}});
	        }
	    });
	    
	    $('#buttonPrint').click(function() {
	        chart.setTitle(null, { text: ' ' });
	        chart.print();
	        chart.setTitle(null, { text: '' });
	    });


</script>				