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

		.error {color: #FF0000;}
	</style>
</head>  
<body>
	<?php

	function checkCP ($cp)
	{
		if($cp != '')
		{
			if (!preg_match("/^[0-9]{5}$/",$cp))
	 		{
	  			return false;
	  		}
	  		else
	  			return true;
  		}
  		else
  			return true;
	}
	function listUsers($data, $pagina)
	{
		//la página para el usuario empieza en la 1, pero los datos empiezan en la 0
		$pagina = $pagina - 1;

		$copiaLocalData = $data;
		$nuevoData = array_slice($copiaLocalData, $pagina*10);
		
		echo '<table style="width:400px">';
	  	echo '<tr><td>ID</td><td>NAME</td><td>EMAIL</td><td>CP</td><td>DELETE</td></tr>';
	  	
	  	$total = min (10, count($nuevoData));
	  	for($i = 0; $i < $total; $i++)
	  	{
	  		echo '<tr>';
	  		echo '<td>' . $nuevoData[$i]['id'] . '</td>';
			echo '<td>' . $nuevoData[$i]['nombre'] . '</td>';
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

	session_start();
	include 'loadData.php';

	//establezco si hay página o no
	if($_GET['page'])
	{
		$pagina = $_GET['page'];
	}
	//si no la tengo, estamos en la página
	else
	{
		$pagina = 1;
	}

	//si el filtro de la sesion tiene valor, lo guardo en $filtro
	if(isset($_SESSION['filtro']))
	{
		$filtro = $_SESSION['filtro']['nombre'];
		$filtroCP = $_SESSION['filtro']['cp'];
	}

	//miro si tengo algun filtro de busqueda
	if ($_POST["action"] == "search")
	{
		$filtroCP = $_POST["queryCP"];
		$filtro = $_POST["query"];
		
		//si el campo de CP no cumple la comprobacion, elimino filtros de busqueda y muestro el error
		if (!checkCP($filtroCP))
		{
			$cpError = '* El CP deben ser 5 digitos!';
			$filtro = $_SESSION['filtro']['nombre']; //dejo el filtro de nombre que tuviera antes la sesion
		}
		else
		{
			$cpError = '';
			$_SESSION['filtro']['nombre'] = $filtro;
			$_SESSION['filtro']['cp'] = $filtroCP;
		}		
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
		  			<ul>
		  			<li>Nombre: <input type="text" name="query" value=' . $filtro . '></li>
		  			<li>CP: <input type="text" name="queryCP" value=' . $filtroCP . '>
		  			<span class="error"> ' . $cpError . '</span></li>
		  			<br/><input type="submit" value="Buscar">
		  			</ul>
		  		</form>';

	
	echo "<h1> Here's your name-email list </h1>";
	//Cargo los usuarios que cumplen el filtro (si hay)
	$data = cargarDatos($_SESSION['filtro']);

	//calculo el número de páginas totales y muestro página actual/páginas totales
	$paginasTotales = ceil(count($data)/10);
	echo "Pagina ". $pagina . "/" . $paginasTotales . ". Num elementos: " . count($data);

	//llamo a la función de mostrar usuarios, y la página a mostrar
	listUsers($data, $pagina);

	//muestro enlaces a página anterior/siguiente si es que las hay
	if($pagina>1)
		echo '<a href ="' . $_SERVER['PHP_SELF'] . '?page=' . ($pagina - 1) .'"> << Anterior </a>';
	if($pagina<$paginasTotales)
		echo '<a href ="' . $_SERVER['PHP_SELF'] . '?page=' . ($pagina + 1) .'"> Siguiente >> </a>';

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
