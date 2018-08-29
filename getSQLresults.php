
<?php

$entity = $_GET['entity'];
$sqlsName = getenv('sqlsName');
if(empty($sqlsName)){
    $sqlsName = "qu-dev-sqls-qeddbkp";
}

try {
    $conn = new PDO ( "sqlsrv:server = tcp:".$sqlsName.".database.windows.net,1433; Database = qu-dev-sqlw-initialinsight", "phpReader", "340\$Uuxwp7Mcxo7Khy");
    $conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
}
catch ( PDOException $e ) {
    print( "Error connecting to SQL Server." );
    die(print_r($e));
}
 

$statement = $conn->prepare("SELECT * FROM dbo.Devices WHERE deviceId = '".$entity."'");

if($statement->execute()) {
    while($row = $statement->fetch()) {
        echo '{"row_num": '.$row['row_num'].'}';
    }    
} else {
    echo "SQL Error <br />";
    echo $statement->queryString."<br />";
    echo $statement->errorInfo()[2];
}

?>



