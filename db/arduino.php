<?php
include_once('config.php');

// Obter os parâmetros da requisição GET
$temp=$_GET['temp'];
$humidade=$_GET['humidade'];
$sensorLuz=$_GET['sensorLuz'];
$luzes=$_GET['luzes'];
$aquecimento=$_GET['aquecimento'];
$telhado=$_GET['telhado'];

// Prepara a query SQL para selecionar as leituras mais recentes
$sql = 'INSERT INTO info(temp,humidade,sensorLuz,luzes,aquecimento,telhado) VALUES(' .$temp. ',' .$humidade. ',' .$sensorLuz. ',' .$luzes. ',' .$aquecimento. ',' .$telhado. ')';

// Estabelece uma conexão com o banco de dados
$conn = connection_db();

// Executa a query SQL e armazena o resultado em uma variável
$resultado = sqlsrv_query($conn, $sql);
sqlsrv_free_stmt($resultado);
sqlsrv_close($conn); 

//?temp=55&humidade=55&sensorLuz=0&luzes=0&aquecimento=0&telhado=0
?>