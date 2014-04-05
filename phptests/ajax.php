<?php
	include 'dbFunctions.php';

	$data = loadUsers('');

	$xml = new SimpleXMLElement('<xml/>');

	foreach($data as $id => $user)
	{
		$u = $xml->addChild('user');
		$u->addChild('id', $user['id']);
		$u->addChild('name', $user['nombre']);
		$u->addChild('email', $user['email']);
		$u->addChild('cp', $user['cp']);
	}

	Header('Content-type: text/xml');

	print($xml->asXML());	


?>