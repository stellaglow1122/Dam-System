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
	  if ($rowNumber > 2)
      {
          $dataArray = array();
          $dataArray = explode(',', $lineData);
          $date_Axis[] = "'".$dataArray[0]."'";
          $aDataValue[] = $dataArray[1];
          $bDataValue[] = $dataArray[2];   
      }
	  ++$rowNumber;
}
$total_number = count($date_Axis);

$a_tickInterval = round($total_number/6);
$seriesA = "{name: '風速', yAxis: 0, data: [".implode(',',$aDataValue)."], tooltip: {shared: true,valueSuffix: 'mm/s'} ,connectNulls: true }";
$aAxis = implode(',',$date_Axis);

$b_tickInterval = round($total_number/6);
$seriesB = "{name: '風向', yAxis: 1, data: [".implode(',',$bDataValue)."], tooltip: {shared: true,valueSuffix: '°'} ,connectNulls: true }";


fclose($file);

?>

<script>
Highcharts.chart('containerWind', {
    chart: {
		height: 500,
        zoomType: 'xy',
        alignTicks:false
    },
    
    title: {
        text: '曲線圖'
    },
    xAxis: [{
        categories: [<?php echo $aAxis;?>],
        crosshair: true,
		gridLineWidth: 1,
        tickInterval:<?=$a_tickInterval;?>
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
            text: '°',
            style: {
                color: '#000000'
            }
        }
    }, { // Secondary yAxis
        gridLineColor: '#e0e0e0',
        gridLineWidth: 1,
        labels: {
            style: {
                color: 'blue'
            }
        },
        title: {
            text: 'mm/s',
            style: {
                color: 'blue'
            }
        },
        opposite: true
    }
	]
	,tooltip: {shared: true}
    ,series: [
	
	<?php echo $seriesA.','.$seriesB;?>

	]
});
</script>