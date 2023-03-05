
<?php

  // Função para estabelecer conexão com o banco de dados
function connection_db() {
   
    $serverName = "192.168.1.100\sqlexpress,1433"; // nome do servidor e instância
    $connectionInfo = array( "Database"=>"chiken", "UID"=>"SA", "PWD"=>"Mafra2@23"); // informações de login do sql server
    $conn = sqlsrv_connect( $serverName, $connectionInfo); // estabelece a conexão
    
    // Verifica se a conexão foi estabelecida com sucesso
    if( !$conn ) {
        echo "Não foi possível estabelecer uma conexão com o banco de dados.<br />";
        die( print_r( sqlsrv_errors(), true));
    }
    
    return $conn; // retorna o objeto de conexão
}

  

// Função para obter a última leitura
function getLastReadings() {
    $conn=connection_db(); // estabelece a conexão
    $sql = "SELECT TOP 1 * FROM info ORDER BY reading_time DESC"; // consulta SQL para obter a última leitura
    $resultado = sqlsrv_query($conn, $sql); // executa a consulta SQL
    
    // Verifica se a consulta foi bem sucedida
    if($resultado === false) {
        die(print_r(sqlsrv_errors(), true));
    }
    
    $last_reading = sqlsrv_fetch_array($resultado, SQLSRV_FETCH_ASSOC); // obtém a última leitura
    sqlsrv_free_stmt($resultado); // liberta a memória usada pelo resultado da consulta
    sqlsrv_close($conn); // fecha a conexão
    return $last_reading; // retorna a última leitura
}


// Função para obter a menor leitura
function minReading($limit, $value) {
    $conn=connection_db(); // estabelece a conexão
    $sql = 'SELECT TOP 1 MIN('.$value.') AS min_amount FROM (SELECT TOP '.$limit.' '.$value.' FROM info ORDER BY reading_time DESC) AS min'; // consulta SQL para obter a menor leitura
    $resultado = sqlsrv_query($conn, $sql) or die(print_r(sqlsrv_errors())); // executa a consulta SQL
    $row=sqlsrv_fetch_array($resultado, SQLSRV_FETCH_ASSOC); // obtém a menor leitura
    sqlsrv_free_stmt($resultado); // liberta a memória usada pelo resultado da consulta
    sqlsrv_close($conn); // fecha a conexão
    return $row['min_amount']; // retorna a menor leitura
}

// Função para obter a maior leitura
function maxReading($limit, $value) {
    $conn=connection_db(); // estabelece a conexão
    $sql = 'SELECT MAX(' .$value.') AS max_amount FROM (SELECT TOP ' .$limit . ' ' . $value. ' FROM info ORDER BY reading_time DESC) AS max'; // consulta SQL para obter a maior leitura
    
    $resultado = sqlsrv_query($conn, $sql) or die(print_r(sqlsrv_errors())); // executa a consulta SQL
    
    $row=sqlsrv_fetch_array($resultado, SQLSRV_FETCH_ASSOC); // obtém a maior leitura
    sqlsrv_free_stmt($resultado); // liberta a memória usada pelo resultado da consulta
    sqlsrv_close($conn ); // fecha a conexão
    return $row['max_amount']; // retorna a maior leitura
}

  function avgReading($limit, $value) {
    $conn=connection_db();// executa a consulta SQL
    $sql = 'SELECT AVG(' .$value. ') AS avg_amount FROM (SELECT TOP ' .$limit. ' ' .$value. ' FROM info ORDER BY reading_time DESC) AS avg';// Monta a consulta SQL que irá buscar os valores e calcular a média.
    $resultado = sqlsrv_query($conn, $sql) or die(print_r(sqlsrv_errors())); // executa a consulta SQL
    $row=sqlsrv_fetch_array($resultado, SQLSRV_FETCH_ASSOC); // obtém a maior leitura
    sqlsrv_free_stmt($resultado); // liberta a memória usada pelo resultado da consulta
    sqlsrv_close($conn ); // fecha a conexão
    return $row['avg_amount']; // retorna a maior leitura
  }
?>
