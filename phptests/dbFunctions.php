<?php

	function conectarBBDD($con)
	{
		//BEGIN
		// Create connection
		$con=mysqli_connect("localhost","palvji","palvjipass","palvji");

		// Check connection
		if (mysqli_connect_errno())
		{
		  echo "Failed to connect to MySQL: " . mysqli_connect_error();
		}
		else {
			//echo "Connection successfull!";
		}
		return $con;
	}

	function desconectarBBDD($con)
	{
		mysqli_close($con);
	}

	function addUser($nombre, $email, $cp)
	{
		$con = conectarBBDD($con);
		if($cp == '')
			$result = mysqli_query($con, "INSERT INTO name_email SET nombre='" . $nombre ."', email='" . $email . "'");
		else
			$result = mysqli_query($con, "INSERT INTO name_email SET nombre='" . $nombre ."', email='" . $email . "', cp='" . $cp . "'");
		desconectarBBDD($con);
		return $result;
		
	}

	function deleteUser($id)
	{
		$con = conectarBBDD($con);
		$result = mysqli_query($con, "DELETE FROM name_email WHERE id=" . $id);
		desconectarBBDD($con);
		return $result;
	}

	function loadUsers($filtro)
	{
		$con = conectarBBDD($con);

		//tengo filtro de nombre
		if($filtro['nombre'])
		{
			//tengo ambos filtros
			if($filtro['cp'])
			{
				$result = mysqli_query($con, "SELECT * FROM name_email WHERE nombre like '%" . $filtro['nombre'] . "%' AND cp like '%" . $filtro['cp'] . "%'");
			}
			//solo tengo el de nombre
			else
			{
				$result = mysqli_query($con, "SELECT * FROM name_email WHERE nombre like '%" . $filtro['nombre'] . "%'");
			}
		}
		//solo tengo el de CP
		else if($filtro['cp'])
		{
			$result = mysqli_query($con, "SELECT * FROM name_email WHERE cp like '%" . $filtro['cp'] . "%'");
		}
		//no tengo ningun filtro
		else
		{
			$result = mysqli_query($con, "SELECT * FROM name_email");
		}
		/*
		if(is_numeric($filtro))
			$result = mysqli_query($con, "SELECT * FROM name_email WHERE cp =" . $filtro);
		else
			$result = mysqli_query($con, "SELECT * FROM name_email WHERE nombre like '%" . $filtro . "%'");
		//$ids = array();*/
		$elementos = array();
		
		while($row = mysqli_fetch_array($result))
		{
		  	//array_push($ids, $row['id']);
		  	//$aux = array("nombre" => $row['nombre'], "email" => $row['email']);
		  	$aux = array("id" => $row['id'], "nombre" => $row['nombre'], "email" => $row['email'], "cp" => $row['cp']);
		  	array_push($elementos, $aux);

		}

		//$resultado = array_combine($ids, $elementos);
		desconectarBBDD($con);

		//return $resultado;
		return $elementos;
	}

	function loadUsersAjax($filtroNombre, $filtroCP, $pag, $elem)
	{
		$con = conectarBBDD($con);

		//tengo filtro de nombre
		if($filtroNombre)
		{
			//tengo ambos filtros
			if($filtroCP)
			{
				$result = mysqli_query($con, "SELECT * FROM name_email WHERE nombre like '%" . $filtroNombre . "%' AND cp like '%"
				 . $filtroCP . "%' LIMIT " . $pag . "," . $elem);
			}
			//solo tengo el de nombre
			else
			{
				$result = mysqli_query($con, "SELECT * FROM name_email WHERE nombre like '%" . $filtroNombre
					. "%'" . " LIMIT " . $pag . "," . $elem);
			}
		}
		//solo tengo el de CP
		else if($filtroCP)
		{
			$result = mysqli_query($con, "SELECT * FROM name_email WHERE cp like '%" . $filtroCP
				. "%'" . " LIMIT " . $pag . "," . $elem);
		}
		//no tengo ningun filtro
		else
		{
			$result = mysqli_query($con, "SELECT * FROM name_email LIMIT " . $pag . "," . $elem);
		}
		/*
		if(is_numeric($filtro))
			$result = mysqli_query($con, "SELECT * FROM name_email WHERE cp =" . $filtro);
		else
			$result = mysqli_query($con, "SELECT * FROM name_email WHERE nombre like '%" . $filtro . "%'");
		//$ids = array();*/
		$elementos = array();
		
		while($row = mysqli_fetch_array($result))
		{
		  	//array_push($ids, $row['id']);
		  	//$aux = array("nombre" => $row['nombre'], "email" => $row['email']);
		  	$aux = array("id" => $row['id'], "nombre" => $row['nombre'], "email" => $row['email'], "cp" => $row['cp']);
		  	array_push($elementos, $aux);

		}

		//$resultado = array_combine($ids, $elementos);
		desconectarBBDD($con);

		//return $resultado;
		return $elementos;
	}

	function getNumEltos($filtroNombre, $filtroCP, $pag, $elem)
	{
		$con = conectarBBDD($con);

		//tengo filtro de nombre
		if($filtroNombre)
		{
			//tengo ambos filtros
			if($filtroCP)
			{
				$result = mysqli_query($con, "SELECT * FROM name_email WHERE nombre like '%" . $filtroNombre . "%' AND cp like '%"
				 . $filtroCP . "%'");
			}
			//solo tengo el de nombre
			else
			{
				$result = mysqli_query($con, "SELECT * FROM name_email WHERE nombre like '%" . $filtroNombre
					. "%'");
			}
		}
		//solo tengo el de CP
		else if($filtroCP)
		{
			$result = mysqli_query($con, "SELECT * FROM name_email WHERE cp like '%" . $filtroCP
				. "%'");
		}
		//no tengo ningun filtro
		else
		{
			$result = mysqli_query($con, "SELECT * FROM name_email");
		}
		/*
		if(is_numeric($filtro))
			$result = mysqli_query($con, "SELECT * FROM name_email WHERE cp =" . $filtro);
		else
			$result = mysqli_query($con, "SELECT * FROM name_email WHERE nombre like '%" . $filtro . "%'");
		//$ids = array();*/
		$elementos = array();
		
		while($row = mysqli_fetch_array($result))
		{
		  	//array_push($ids, $row['id']);
		  	//$aux = array("nombre" => $row['nombre'], "email" => $row['email']);
		  	$aux = array("id" => $row['id'], "nombre" => $row['nombre'], "email" => $row['email'], "cp" => $row['cp']);
		  	array_push($elementos, $aux);

		}

		//$resultado = array_combine($ids, $elementos);
		desconectarBBDD($con);

		//return $resultado;
		return count($elementos);
	}

?>