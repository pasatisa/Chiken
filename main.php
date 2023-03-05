<?php
    include_once('db/config.php');
    if (isset($_GET["readingsCount"])){
     $dados = $_GET["readingsCount"];
     $dados = trim($dados);
     $dados = stripslashes($dados);
     $dados = htmlspecialchars($dados);
      $readings_count = $_GET["readingsCount"];
    }
   
    else {
      $readings_count = 20;
    } 

    $last_reading = getLastReadings();
    $last_reading_temp = $last_reading["temp"];
    $last_reading_humi = $last_reading["humidade"];
    $last_reading_time =$last_reading["reading_time"];



    $last_reading_time = date("Y-m-d H:i:s");


    $min_temp = minReading($readings_count, 'temp');
    $max_temp = maxReading($readings_count, 'temp');
    $avg_temp = avgReading($readings_count, 'temp');

    $min_humi = minReading($readings_count, 'humidade');
    $max_humi = maxReading($readings_count, 'humidade');
    $avg_humi = avgReading($readings_count, 'humidade');
?>

<!DOCTYPE html>
<html>
    <head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <link rel="stylesheet" type="text/css" href="css/style.css">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    </head>
    <div id="ring"></div>
    <header class="header">
        <h1>📊 Chiken informações</h1>
        <form method="get">
            <input type="number" name="readingsCount" min="1" placeholder="Número de leituras (<?php echo $readings_count; ?>)">
            <input type="submit" value="UPDATE">
        </form>
    </header>
<body>
    <p>Ultima leitura ás: <?php echo $last_reading_time; ?></p>
    <section class="content">
	    <div class="box gauge--1">
	    <h3>TEMPERATURA</h3>
              <div class="mask">
			  <div class="semi-circle"></div>
			  <div class="semi-circle--mask"></div>
			</div>
		    <p style="font-size: 30px;" id="temp">--</p>
		    <table cellspacing="5" cellpadding="5">
		        <tr>
		            <th colspan="3"><?php echo $readings_count; ?> leituras de Temperaturas  </th>
	            </tr>
		        <tr>
		            <td>Min</td>
                    <td>Max</td>
                    <td>Média</td>
                </tr>
                <tr>
                    <td><?php echo $min_temp; ?> &deg;C</td>
                    <td><?php echo $max_temp; ?> &deg;C</td>
                    <td><?php echo round($avg_temp, 2); ?> &deg;C</td>
                </tr>
            </table>
        </div>
        <div class="box gauge--2">
            <h3>HUMIDADE</h3>
            <div class="mask">
                <div class="semi-circle"></div>
                <div class="semi-circle--mask"></div>
            </div>
            <p style="font-size: 30px;" id="humi">--</p>
            <table cellspacing="5" cellpadding="5">
                <tr>
                    <th colspan="3"> <?php echo $readings_count; ?> leituras de humidade</th>
                </tr>
                <tr>
                    <td>Min</td>
                    <td>Max</td>
                    <td>Média</td>
                </tr>
                <tr>
                    <td><?php echo $min_humi; ?> %</td>
                    <td><?php echo $max_humi; ?> %</td>
                    <td><?php echo round($avg_humi, 2); ?> %</td>
                </tr>
            </table>
        </div>
    </section>
<?php
    echo   '<h2> Ver as Últimas ' . $readings_count . '  Leituras </h2>
            <table cellspacing="5" cellpadding="5" id="tableReadings">
                <tr>
                    <th>ID</th>
                    <th>aquecimento</th>
                    <th>sensorluz</th>
                    <th>Temperatura</th>
                    <th>humidade</th>
                    <th>luzes</th>
                    <th>telhado</th>
                    <th>Data/horas</th>
                </tr>';
        $sql = 'SELECT TOP '.$readings_count.' * FROM info ORDER BY reading_time DESC' ;
         $conn= connection_db();
         $resultado = sqlsrv_query($conn, $sql) ;
        if ($resultado) {   
        while ($row=sqlsrv_fetch_array( $resultado, SQLSRV_FETCH_ASSOC)) {
            $row_id =$row["id"];
            $row_temp = $row["temp"];
            $row_humidade = $row["humidade"];
            $row_reading_time = $row["reading_time"];
            $row_reading_time =date("Y-m-d H:i:s");

            if(intval($row['aquecimento']==0)){
                $aquecimento="off";
            }else{
                $aquecimento="on";
            }
    
            //
            if(intval($row['sensorLuz']==0)){
                $sensorluz="off";
            }else{
                $sensorluz="on";
            }
    
            //
            if(intval($row['luzes']==0)){
                $luzes="off";
            }else{
                $luzes="on";
            }
    
            //
            if(intval($row['telhado']==0)){
                $telhado="off";
            }else{
                $telhado="on";
            }
           
     

            echo '<tr>
                    <td>' . $row_id . '</td>
                    <td>' . $aquecimento . '</td>
                    <td>' . $sensorluz . '</td>
                    <td>' . $row_temp . '</td>
                    <td>' . $row_humidade . '</td>
                    <td>' . $luzes . '</td>
                    <td>' . $telhado. '</td>
                    <td>' . $row_reading_time . '</td>
                  </tr>';
        }
        echo '</table>';
        sqlsrv_free_stmt($resultado);
        sqlsrv_close($conn);    
    }
?>

<script>
    window.onload = function(){ document.getElementById("ring").style.display = "none" ;document.getElementById("page").style.display = "block" }
    var temp = <?php echo $last_reading_temp; ?>;
    var humidade = <?php echo $last_reading_humi; ?>;
    setTemperature(temp);
    setHumidity(humidade);

    function setTemperature(curVal){
    	//set range for Temperature in Celsius -5 Celsius to 38 Celsius
    	var minTemp = -5.0;
    	var maxTemp = 38.0;
        //set range for Temperature in Fahrenheit 23 Fahrenheit to 100 Fahrenheit
    	//var minTemp = 23;
    	//var maxTemp = 100;

    	var newVal = scaleValue(curVal, [minTemp, maxTemp], [0, 180]);
    	$('.gauge--1 .semi-circle--mask').attr({
    		style: '-webkit-transform: rotate(' + newVal + 'deg);' +
    		'-moz-transform: rotate(' + newVal + 'deg);' +
    		'transform: rotate(' + newVal + 'deg);'
    	});
    	$("#temp").text(curVal + ' ºC');
    }

    function setHumidity(curVal){
    	//set range for Humidity percentage 0 % to 100 %
    	var minHumi = 0;
    	var maxHumi = 100;

    	var newVal = scaleValue(curVal, [minHumi, maxHumi], [0, 180]);
    	$('.gauge--2 .semi-circle--mask').attr({
    		style: '-webkit-transform: rotate(' + newVal + 'deg);' +
    		'-moz-transform: rotate(' + newVal + 'deg);' +
    		'transform: rotate(' + newVal + 'deg);'
    	});
    	$("#humi").text(curVal + ' %');
    }

    function scaleValue(value, from, to) {
        var scale = (to[1] - to[0]) / (from[1] - from[0]);
        var capped = Math.min(from[1], Math.max(from[0], value)) - from[0];
        return ~~(capped * scale + to[0]);
    }
</script>
</body>
</html>