<?php

include '../db.php';


$sql='SELECT * FROM Afincco.Condicao_status;';

$res=exec_query($sql);
print_r($res);


?>
