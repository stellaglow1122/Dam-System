<?php

 include('../../model/ajax_model.php');
	$project = new Project();
    $sql = "SELECT * FROM DataBase.dbo.EarthQuake_Set";	
	$data = $project->custosql($sql);
     foreach($data as $inLocation)
	{
      if ($_POST['location_code']==$inLocation['install_Location']) {

		$nowfilename=str_replace('\\\\','/',addslashes($inLocation['txt_backpath']));
		if (strlen($inLocation['msd_backpath'])<2) {
			
			    $nowfiletxt=".csv";
			
		} else {
		
		        $nowfiletxt=".txt";
		}
       }
	}

$nowfilename = str_replace('C:','/mnt/hgfs',$nowfilename);    //linux 用

$file = fopen($nowfilename."/".$_POST['timeName'].$nowfiletxt, "rt");

$firstLineArray = array();
$xData = array();
$yData = array();
$zData = array();
$n = 0;
$xStart = 1;
$yStart = -1;
$zStart = -1;
$end = -1;
while( $lineData=fgets($file) )
{
	if ($n == $xStart - 1)
	{
		$firstLineArray = explode(",", $lineData);
		foreach ($firstLineArray as $key => $value) {
			if (strpos($value, 'samples') !== false)
			{
				$xNumber = str_replace('samples','',$value);
				$yStart = (int)$xNumber + 2 + $n;
				//echo $xNumber;
				
			}
		}
	}
	else if ($n < $yStart - 1)
	{
		$xData[] = $lineData;
	}
	else if ($n == $yStart - 1)
	{
		
		$firstLineArray = explode(",", $lineData);
		foreach ($firstLineArray as $key => $value) {
			if (strpos($value, 'samples') !== false)
			{
				$yNumber = str_replace('samples','',$value);
				$zStart = (int)$yNumber + 2 + $n;
				//echo $yNumber;
			}
		}
	}
	else if ($n < $zStart - 1)
	{
		$yData[] = $lineData;
	}
	else if ($n == $zStart - 1)
	{
		$firstLineArray = explode(",", $lineData);
		foreach ($firstLineArray as $key => $value) {
			if (strpos($value, 'samples') !== false)
			{
				$zNumber = str_replace('samples','',$value);
				$end = (int)$zNumber + 2 + $n;
				//echo $zNumber;
			}
		}
	}
	else if ($n < $end - 1)
	{
		$zData[] = $lineData;
	}
	$n = $n + 1;


}
$date_xAxis = array();
$X_Axisp = array();
	$xDataValue = '';
	$x_max = -999999999;
foreach ($xData as $key => $value) {
	if (abs(floatval(substr($value, 28))) > $x_max)
	{
		$x_max = abs(floatval(substr($value, 28)));
	}
	$date_xAxis[] = "'".date("Y-m-d H:i:s",strtotime(substr($value, 0, 26))+28800)."'";
	$xDataValue = $xDataValue.(round(floatval(substr($value, 28)) * 0.00025961131,5)).',';

}
$total_numberX = count($date_xAxis);
	$x_tickInterval = round($total_numberX/4);
	$xDataValue = substr($xDataValue, 0, strlen($xDataValue)-1);
	$interval = ($_POST['int'] != "") ? $_POST['int']:"0.5";

	$seriesX = "{name: 'X軸', yAxis: 0, data: [".$xDataValue."], tooltip: {shared: true,valueSuffix: 'gal'} ,connectNulls: true }";
	$xAxis = implode(',',array_unique($date_xAxis));



	$date_yAxis = array();
$Y_Axisp = array();
	$yDataValue = '';
	$y_max = -999999999;
foreach ($yData as $key => $value) {
	if (abs(floatval(substr($value, 28))) > $y_max)
	{
		$y_max = abs(floatval(substr($value, 28)));
	}
	$date_yAxis[] = "'".date("Y-m-d H:i:s",strtotime(substr($value, 0, 26))+28800)."'";
	$yDataValue = $yDataValue.(round(floatval(substr($value, 28)) * 0.00025961131,5)).',';

}
$total_numberY = count($date_yAxis);
	$y_tickInterval = round($total_numberY/4);
	$yDataValue = substr($yDataValue, 0, strlen($yDataValue)-1);
	$interval = ($_POST['int'] != "") ? $_POST['int']:"0.5";

	$seriesY = "{name: 'Y軸', yAxis: 0, data: [".$yDataValue."], tooltip: {shared: true,valueSuffix: 'gal'} ,connectNulls: true }";
	$yAxis = implode(',',array_unique($date_yAxis));




	$date_zAxis = array();
$Z_Axisp = array();
	$zDataValue = '';
	$z_max = -999999999;
foreach ($zData as $key => $value) {
	if (abs(floatval(substr($value, 28))) > $z_max)
	{
		$z_max = abs(floatval(substr($value, 28)));
	}
	$date_zAxis[] = "'".date("Y-m-d H:i:s",strtotime(substr($value, 0, 26))+28800)."'";
	$zDataValue = $zDataValue.(round(floatval(substr($value, 28)) * 0.00025961131,5)).',';

}
$total_numberZ = count($date_zAxis);
	$z_tickInterval = round($total_numberZ/4);
	$zDataValue = substr($zDataValue, 0, strlen($zDataValue)-1);
	$interval = ($_POST['int'] != "") ? $_POST['int']:"0.5";

	$seriesZ = "{name: 'Z軸', yAxis: 0, data: [".$zDataValue."], tooltip: {shared: true,valueSuffix: 'gal'} ,connectNulls: true }";
	$zAxis = implode(',',array_unique($date_zAxis));


	$x_max = round($x_max * 0.00025961131,5);
	$y_max = round($y_max * 0.00025961131,5);
	$z_max = round($z_max * 0.00025961131,5);
	$maxGal = $x_max;
	if ($y_max > $maxGal)
	{
		$maxGal = $y_max;
	}
	if ($z_max > $maxGal)
	{
		$maxGal = $z_max;
	}
	if ($maxGal >= 400)
	{
		$text = '7';
	}
	else
	{
		if ($maxGal >= 250)
		{
			$text = '6';
		}
		else
		{
			if ($maxGal >= 80)
			{
				$text = '5';
			}
			else
			{
				if ($maxGal >= 25)
				{
					$text = '4';
				}
				else
				{
					if ($maxGal >= 8)
					{
						$text = '3';
					}
					else
					{
						if ($maxGal >= 2.5)
						{
							$text = '2';
						}
						else
						{
							if ($maxGal >= 0.8)
							{
								$text = '1';
							}
							else
							{
								$text = '0';
							}
						}
					}
				}
			}
		}
	}
	echo $text.','.$x_max.','.$y_max.','.$z_max.','.str_replace("'","",$date_xAxis[0]).'|';
	fclose($file);
?>

<script>
Highcharts.chart('container', {
    chart: {
		height: 500,
        zoomType: 'xy',
        alignTicks:false
    },
    
    title: {
        text: 'X軸'
    },
    xAxis: [{
        categories: [<?php echo $xAxis;?>],
        crosshair: true,
		gridLineWidth: 1,
		tickInterval:<?=$x_tickInterval;?>
    }],
	
    yAxis: [	
	{ // Primary yAxis
		gridLineColor: '#e0e0e0',
		gridLineWidth: 1,
        labels: {
            style: {
                color: '#000000'
            }
        },
        title: {
            text: 'gal',
            style: {
                color: '#000000'
            }
        },
		tickInterval: 0.5
    } 
	]
	,tooltip: {shared: true}
	<?php echo $str;?>
    ,series: [
	
	<?php echo $seriesX;?>

	]
});

		
</script>

<script>
Highcharts.chart('container2', {
    chart: {
		height: 500,
        zoomType: 'xy',
        alignTicks:false
    },
    
    title: {
        text: 'Y軸'
    },
    xAxis: [{
        categories: [<?php echo $yAxis;?>],
        crosshair: true,
		gridLineWidth: 1,
		tickInterval:<?=$y_tickInterval;?>
    }],
	
    yAxis: [	
	{ // Primary yAxis
		gridLineColor: '#e0e0e0',
		gridLineWidth: 1,
        labels: {
            style: {
                color: '#000000'
            }
        },
        title: {
            text: 'gal',
            style: {
                color: '#000000'
            }
        },
		tickInterval: 0.5
    } 
	]
	,tooltip: {shared: true}
	<?php echo $str;?>
    ,series: [
	
	<?php echo $seriesY;?>

	]
});

		
</script>

<script>
Highcharts.chart('container3', {
    chart: {
		height: 500,
        zoomType: 'xy',
        alignTicks:false
    },
    
    title: {
        text: 'Z軸'
    },
    xAxis: [{
        categories: [<?php echo $zAxis;?>],
        crosshair: true,
		gridLineWidth: 1,
		tickInterval:<?=$z_tickInterval;?>
    }],
	
    yAxis: [	
	{ // Primary yAxis
		gridLineColor: '#e0e0e0',
		gridLineWidth: 1,
        labels: {
            style: {
                color: '#000000'
            }
        },
        title: {
            text: 'gal',
            style: {
                color: '#000000'
            }
        },
		tickInterval: 0.5
    } 
	]
	,tooltip: {shared: true}
	<?php echo $str;?>
    ,series: [
	
	<?php echo $seriesZ;?>

	]
});

		
</script>






