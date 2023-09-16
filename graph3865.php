<?php
$dir = '../event/';
$fileName = $_POST['fileName'];
$file = fopen($dir.$fileName, "rt");

//read file 3865


$rowNumber = 0;
$date_Axis = array();
$aDataValue = array();
$bDataValue = array();
$cDataValue = array();

while( $lineData=fgets($file) )
{
	if ($rowNumber >= 13)
	{
		$dataArray = array();
        $dataArray = explode(',', $lineData);
        $date_Axis[] = $dataArray[0];
        $aDataValue[] = $dataArray[1];
        $bDataValue[] = $dataArray[2];
        $cDataValue[] = $dataArray[3];
	}
	++$rowNumber;
}
$total_number = count($date_Axis);

$a_tickInterval = round($total_number/4);
$seriesA = "{name: 'a軸', yAxis: 0, data: [".implode(',',$aDataValue)."], tooltip: {shared: true,valueSuffix: 'gal'} ,connectNulls: true }";
$aAxis = implode(',',array_unique($date_Axis));

$b_tickInterval = round($total_number/4);
$seriesB = "{name: 'b軸', yAxis: 0, data: [".implode(',',$bDataValue)."], tooltip: {shared: true,valueSuffix: 'gal'} ,connectNulls: true }";
$bAxis = implode(',',array_unique($date_Axis));

$c_tickInterval = round($total_number/4);
$seriesC = "{name: 'c軸', yAxis: 0, data: [".implode(',',$cDataValue)."], tooltip: {shared: true,valueSuffix: 'gal'} ,connectNulls: true }";
$cAxis = implode(',',array_unique($date_Axis));
fclose($file);

?>

<script>
Highcharts.chart('container3865_a', {
    chart: {
		height: 500,
        zoomType: 'xy',
        alignTicks:false
    },
    
    title: {
        text: 'a軸'
    },
    xAxis: [{
        categories: [<?php echo $aAxis;?>],
        crosshair: true,
		gridLineWidth: 1
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
	
	<?php echo $seriesA;?>

	]
});

Highcharts.chart('container3865_b', {
    chart: {
		height: 500,
        zoomType: 'xy',
        alignTicks:false
    },
    
    title: {
        text: 'b軸'
    },
    xAxis: [{
        categories: [<?php echo $bAxis;?>],
        crosshair: true,
		gridLineWidth: 1,
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
	
	<?php echo $seriesB;?>

	]
});

Highcharts.chart('container3865_c', {
    chart: {
		height: 500,
        zoomType: 'xy',
        alignTicks:false
    },
    
    title: {
        text: 'c軸'
    },
    xAxis: [{
        categories: [<?php echo $cAxis;?>],
        crosshair: true,
		gridLineWidth: 1,
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
	
	<?php echo $seriesC;?>

	]
});
</script>