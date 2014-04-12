<?php
include 'dbFunctions.php';

$id = $_REQUEST["id"];
deleteUser($id);

echo "Vamos a borrar el user " . $id;
//echo "query =" . $query . "/ $queryCP = " . $queryCP . "/ pag = " . $pag . "/ nElem = " . $nElem;

?>