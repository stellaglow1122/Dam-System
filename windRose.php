<?php

function degToCompass($num)
{
    $val=intval(($num/22.5)+.5);
    $direction = array("N","NNE","NE","ENE","E","ESE", "SE", "SSE","S","SSW","SW","WSW","W","WNW","NW","NNW");
    return $direction[($val % 16)];
}
function calculateWindPower($ms)
{
	$windPowerArray = array(0.2, 1.5, 3.3, 5.4, 7.9, 10.7, 13.8, 17.1, 20.7, 24.4, 28.4, 32.6, 36.9, 41.4, 46.1, 50.9, 56.0, 61.2);
	$windPower = 0;
	foreach ($windPowerArray as $key => $value) {
		if ($ms > $value)
		{
			++$windPower;
		}
	}
	--$windPower;
	if ($windPower > 17)
	{
		$windPower = 17;
	}
	if ($windPower < 0)
	{
	    $windPower = 0;
	}
	return $windPower;
}
$windPowerArray = array(0.2, 1.5, 3.3, 5.4, 7.9, 10.7, 13.8, 17.1, 20.7, 24.4, 28.4, 32.6, 36.9, 41.4, 46.1, 50.9, 56.0, 61.2);
$maxWindPower = 0;
$dir = "/AppServ/www/fubonland/rec/";
$fileName = $_GET['fileName'];
$file = fopen($dir.$fileName, "rt");

$totalNumber = 0;
$data = array();
while( $lineData=fgets($file) )
{
	if ($totalNumber != 0)
	{
		$dataArray = array();
		$dataArray = explode(',', $lineData);
		if ($dataArray[1] > $windPowerArray[$maxWindPower])
		{
			++$maxWindPower; // 最後maxWindPower的值決定最大風力的級數
		}
		$data[degToCompass($dataArray[2])][calculateWindPower($dataArray[1])][] = 1; //ex. data['W']['2']指的是方向west風力級數2
	}
	++$totalNumber;
    
}
--$totalNumber; //總資料個數



$drawChart = '<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/highcharts-more.js"></script>
<script src="https://code.highcharts.com/modules/data.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>

<div id="container" style="min-width: 420px; max-width: 600px; height: 600px; margin: 0 auto"></div>

<div style="display:none">
  <!-- Source: http://or.water.usgs.gov/cgi-bin/grapher/graph_windrose.pl -->
  <table id="freq" border="0" cellspacing="0" cellpadding="0">
    <tr nowrap bgcolor="#CCCCFF">
      <th colspan="9" class="hdr">Table of Frequencies (percent)</th>
    </tr>';
$drawChart .= '<tr nowrap bgcolor="#CCCCFF">';
$drawChart .= '<th class="freq">Direction</th>';
$windPowerChinese = array('無風','軟風','輕風','微風','和風','清風','強風','疾風','大風','烈風','狂風','暴風','颶風','颶風','颶風','颶風','颶風','颶風');
for ($i=0; $i <= $maxWindPower; $i++) { 
	if ($i == 0)
	{
		$drawChart .= '<th class="freq">'.$windPowerChinese[0].'0~'.$windPowerArray[$i].' m/s</th>';
	}
	else
	{
		$drawChart .= '<th class="freq">'.$windPowerChinese[$i].($windPowerArray[$i - 1] + 0.1).'~'.$windPowerArray[$i].' m/s</th>';
	}
}
$drawChart .= '</tr>';


$directionArray = array("N","NNE","NE","ENE","E","ESE", "SE", "SSE","S","SSW","SW","WSW","W","WNW","NW","NNW");
foreach ($directionArray as $key => $direction) {
	$drawChart .= '<tr nowrap>';
	$drawChart .= '<td class="dir">'.$direction.'</td>';
	$total = 0;
	for ($i=0; $i <= $maxWindPower; $i++) { 
		if ($data[$direction][$i])
		{
			$drawChart .= '<td class="data">'.(count($data[$direction][$i]) / $totalNumber * 100).'</td>';
			$total += count($data[$direction][$i]) / $totalNumber * 100;
		}
		else
		{
			$drawChart .= '<td class="data">0.00</td>';
		}
	}
	$drawChart .= '<td class="data">'.$total.'</td>';
	$drawChart .= '</tr>';
}
$drawChart .= '</table>';
$drawChart .= '</div>';
echo $drawChart;
$href = "'../index.php?item=history'";
echo '<div style="margin: auto;
  width: 50%;
  "><button type="button" onClick="location.href='.$href.'">
                          <i class="fa fa-check-square-o"></i> 回到歷時紀錄查詢
                        </button></div>';

echo "<script>Highcharts.chart('container', {
  data: {
    table: 'freq',
    startRow: 1,
    endRow: 17,
    endColumn: ".($maxWindPower + 1)."
  },

  chart: {
    polar: true,
    type: 'column'
  },

  title: {
    text: '".substr($fileName, 4, 4).'/'.substr($fileName, 8, 2).'/'.substr($fileName, 10, 2)."'
  },

  pane: {
    size: '85%'
  },

  legend: {
    align: 'right',
    verticalAlign: 'top',
    y: 100,
    layout: 'vertical'
  },

  xAxis: {
    tickmarkPlacement: 'on'
  },

  yAxis: {
    min: 0,
    endOnTick: false,
    showLastLabel: true,
    title: {
      text: 'Frequency (%)'
    },
    labels: {
      formatter: function () {
        return this.value + '%';
      }
    },
    reversedStacks: false
  },

  tooltip: {
    valueSuffix: '%'
  },

  plotOptions: {
    series: {
      stacking: 'normal',
      shadow: false,
      groupPadding: 0,
      pointPlacement: 'on'
    }
  }
});</script>";

?>

