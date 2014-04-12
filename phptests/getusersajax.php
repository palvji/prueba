<?php
include 'dbFunctions.php';

// get the q parameter from URL
$query = $_REQUEST["query"];
$queryCP = $_REQUEST["queryCP"];
$pag = $_REQUEST["pag"];
$nElem = $_REQUEST["nElem"];

//sleep(1);

$vector = loadUsersAjax($query, $queryCP, $pag, $nElem);
$num_eltos = getNumEltos($query, $queryCP, $pag, $nElem);

$result = Array ("vector" => $vector, "numTotal" => $num_eltos);
echo json_encode($result);
//echo "query =" . $query . "/ $queryCP = " . $queryCP . "/ pag = " . $pag . "/ nElem = " . $nElem;

?>