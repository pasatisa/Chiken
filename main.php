<?php
    include_once('db/config.php'); // Inclui o arquivo config.php que contém as informações de configuração do banco de dados.
    if (isset($_GET["readingsCount"])){ // Verifica se a variável readingsCount foi definida na URL.
     $dados = $_GET["readingsCount"]; // Atribui o valor de readingsCount a uma variável.
     $dados = trim($dados); // Remove espaços em branco do início e do fim da string.
     $dados = stripslashes($dados); // Remove barras invertidas adicionadas por addslashes().
     $dados = htmlspecialchars($dados); // Converte caracteres especiais em entidades HTML.
      $readings_count = $_GET["readingsCount"]; // Atribui o valor de readingsCount a uma variável.
    }
   
    else {
      $readings_count = 20; // Define o valor padrão para readingsCount se não foi definido na URL.
    } 

    $last_reading = getLastReadings(); // Obtém a última leitura da tabela info do banco de dados.
    $last_reading_temp = $last_reading["temp"]; // Obtém o valor da temperatura da última leitura.
    $last_reading_humi = $last_reading["humidade"]; // Obtém o valor da umidade da última leitura.
    $last_reading_time = $last_reading["reading_time"]; // Obtém o valor da data/hora da última leitura.

    // Converte a variável $last_reading_time em uma string PHP.
    $last_reading_time = $last_reading_time->format("Y-m-d H:i:s");

    // Obtém as estatísticas de temperatura e umidade para o número de leituras especificado.
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
        <title>Chiken</title>
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
// Exibe um título indicando a quantidade de leituras a serem mostradas
echo '<h2> Ver as Últimas ' . $readings_count . '  Leituras </h2>';

// Inicia a criação de uma tabela HTML para exibir as leituras
echo '<table cellspacing="5" cellpadding="5" id="tableReadings">';
echo '<tr>
        <th>ID</th>
        <th>aquecimento</th>
        <th>sensorluz</th>
        <th>Temperatura</th>
        <th>humidade</th>
        <th>luzes</th>
        <th>telhado</th>
        <th>Data/horas</th>
      </tr>';

// Prepara a query SQL para selecionar as leituras mais recentes
$sql = 'SELECT TOP '.$readings_count.' * FROM info ORDER BY reading_time DESC';

// Estabelece uma conexão com o banco de dados
$conn = connection_db();

// Executa a query SQL e armazena o resultado em uma variável
$resultado = sqlsrv_query($conn, $sql);

// Verifica se a query foi executada com sucesso
if ($resultado) {
    // Percorre cada linha do resultado e exibe as informações na tabela HTML
    while ($row = sqlsrv_fetch_array($resultado, SQLSRV_FETCH_ASSOC)) {
        // Armazena os valores de cada coluna em variáveis separadas para facilitar o acesso
        $row_id = $row["id"];
        $row_temp = $row["temp"];
        $row_humidade = $row["humidade"];
        $row_reading_time = $row["reading_time"]->format("Y-m-d H:i:s");

        // Converte os valores de 0/1 em "on" ou "off" para exibir na tabela
        $aquecimento = intval($row['aquecimento']) == 0 ? "off" : "on";
        $sensorluz = intval($row['sensorLuz']) == 0 ? "off" : "on";
        $luzes = intval($row['luzes']) == 0 ? "off" : "on";
        $telhado = intval($row['telhado']) == 0 ? "off" : "on";

        // Exibe os valores na tabela HTML
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
    
    // Fecha a tag da tabela HTML
    echo '</table>';
    
    // Libera o resultado da query e fecha a conexão com o banco de dados
    sqlsrv_free_stmt($resultado);
    sqlsrv_close($conn);    
}
?>
<script>
// Definição da função a ser executada quando a página é carregada
window.onload = function(){ 
    // Esconde o elemento com o ID "ring"
    document.getElementById("ring").style.display = "none"; 
    // Exibe o elemento com o ID "page"
    
    document.body.style.display = "block";
}

// Leitura dos valores da temperatura e umidade obtidos via PHP
var temp = <?php echo $last_reading_temp; ?>;
var humidade = <?php echo $last_reading_humi; ?>;

// Chamada das funções para exibir os valores obtidos na tela
setTemperature(temp);
setHumidity(humidade);

// Definição da função para exibir o valor da temperatura
function setTemperature(curVal){
    // Definição dos valores mínimo e máximo para a escala da temperatura em Celsius
    var minTemp = -5.0;
    var maxTemp = 38.0;
    // Definição dos valores mínimo e máximo para a escala da temperatura em Fahrenheit
    // var minTemp = 23;
    // var maxTemp = 100;

    // Escalonamento do valor da temperatura obtido para a escala de exibição na tela
    var newVal = scaleValue(curVal, [minTemp, maxTemp], [0, 180]);

    // Atualização da exibição da temperatura na tela
    $('.gauge--1 .semi-circle--mask').attr({
        style: '-webkit-transform: rotate(' + newVal + 'deg);' +
        '-moz-transform: rotate(' + newVal + 'deg);' +
        'transform: rotate(' + newVal + 'deg);'
    });
    $("#temp").text(curVal + ' ºC');
}

// Definição da função para exibir o valor da umidade
function setHumidity(curVal){
    // Definição dos valores mínimo e máximo para a escala da umidade
    var minHumi = 0;
    var maxHumi = 100;

    // Escalonamento do valor da umidade obtido para a escala de exibição na tela
    var newVal = scaleValue(curVal, [minHumi, maxHumi], [0, 180]);

    // Atualização da exibição da umidade na tela
    $('.gauge--2 .semi-circle--mask').attr({
        style: '-webkit-transform: rotate(' + newVal + 'deg);' +
        '-moz-transform: rotate(' + newVal + 'deg);' +
        'transform: rotate(' + newVal + 'deg);'
    });
    $("#humi").text(curVal + ' %');
}

// Definição da função para escalonar o valor da temperatura ou umidade
function scaleValue(value, from, to) {
    // Cálculo do fator de escala para o valor a ser escalonado
    var scale = (to[1] - to[0]) / (from[1] - from[0]);
    // Limitação do valor a ser escalonado dentro dos limites de escala definidos
    var capped = Math.min(from[1], Math.max(from[0], value)) - from[0];
    // Escalonamento do valor propriamente dito
    return ~~(capped * scale + to[0]);
}
</script>
</body>
</html>