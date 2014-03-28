<!DOCTYPE html>   
<html> 
<head>   
	<title>Name - Email Table</title>  
	<style>
		table,th,td
		{
			border:1px solid black;
			border-collapse:collapse;
		}
	</style>
</head>  
<body>
	<?php

	session_start();

	//si tengo esta variable, es porque el usuario esta navegando con los botones
	//de pagina anterior y siguiente. EL filtro de la sesion se queda como esta
	if($_GET['page'])
	{
		$pagina = $_GET['page'];
	}
	//si no la tengo, es porque ha buscado, borrado o filtrado, por lo que reseteo el filtro de la sesion
	else
	{
		$pagina = 1;
		unset($_SESSION['filtro']);
	}

	//si el filtro de la sesion tiene valor, lo guardo en $filtro
	if(isset($_SESSION['filtro']))
		$filtro = $_SESSION['filtro'];

	include 'loadData.php';


	//miro si tengo algun filtro de busqueda
	if ($_POST["query"])
	{
		$filtro = $_POST["query"];
		$_SESSION['filtro'] = $filtro;
	}

	if ($_GET["action"] == "delete"){
		delete($_GET["id"]);
	}

	if ($_POST["action"] == "insert"){
		insert($_POST["nombre"], $_POST["email"], $_POST["cp"]);
	}


	echo "<h1> Search an user: </h1>";
	echo '<form action="list.php" method="post">
		  			<input type="hidden" name="action" value="search">
		  			<input type="text" name="query" value=' . $filtro . '>
		  			<input type="submit" value="Buscar">
		  		</form>';

	
	echo "<h1> Here's your name-email list </h1>";
	

	//Cargo los usuarios que cumplen el filtro (si hay)
	$data = cargarDatos($filtro);
	
	$paginasTotales = ceil(count($data)/10);

	echo "Pagina ". $pagina . "/" . $paginasTotales . " .";

	listUsers($data, $pagina);

	function listUsers($data, $pagina)
	{
		$pagina = $pagina - 1;

		$copiaLocalData = $data;
		$nuevoData = array_slice($copiaLocalData, $pagina*10);
		
		echo '<table style="width:400px">';
	  	echo '<tr><td>ID</td><td>NAME</td><td>EMAIL</td><td>CP</td><td>DELETE</td></tr>';
	  	
	  	$total = min (10, count($nuevoData));
	  	//foreach($nuevoData as $id=>$usuario)
	  	for($i = 0; $i < $total; $i++)
	  	{
	  		echo '<tr>';
	  		//echo '<td>' . $id . '</td>';
	  		//echo '<td>' . $i . '</td>';
	  		echo '<td>' . $nuevoData[$i]['id'] . '</td>';
	  		//echo '<td>' . $usuario['nombre'] . '</td>';
			echo '<td>' . $nuevoData[$i]['nombre'] . '</td>';
	  		//echo '<td>' . $usuario['email'] . '</td>';
	  		echo '<td>' . $nuevoData[$i]['email'] . '</td>';
	  		echo '<td>' . $nuevoData[$i]['cp'] . '</td>';
	  		echo '<td>';
	  		echo '<form action="list.php" method="get">';
			echo '<input type="submit" value="Eliminar">
					<input type="hidden" name="action" value="delete">
	  				<input type="hidden" name="id" value="' . $nuevoData[$i]['id'] . '">
	  				</form>';
	  		echo '</td>';
	  		echo '</tr>';
	  	}
		
		echo '</table>';
	}

	if($pagina>1)
		echo '<a href ="' . $_SERVER['PHP_SELF'] . '?page=' . ($pagina - 1) .'"> << Anterior </a>';
	if($pagina<$paginasTotales)
		echo '<a href ="' . $_SERVER['PHP_SELF'] . '?page=' . ($pagina + 1) .'"> Siguiente >> </a>';

	echo "<br/><br/>data tiene " . count($data) . " elementos";

	echo "<h1> Insert an user </h1>";
	echo '<form action="list.php" method="post">
				<input type="hidden" name="action" value="insert">
				Nombre:<input type="text" name="nombre">
				email:<input type="text" name="email">
				CP:<input type="text" name="cp">
				<input type="submit" value="Insertar">
			</form>';

	?>
	
	<br/>
	<a href="index.html"> Back </a>


</body>  
</html>
