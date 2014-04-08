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
	<script>
		function showHint(str)
		{
		if (str.length==0)
		  {
		  document.getElementById("txtHint").innerHTML="";
		  return;
		  }
		var xmlhttp=new XMLHttpRequest();
		xmlhttp.onreadystatechange=function()
		  {
		  if (xmlhttp.readyState==4 && xmlhttp.status==200)
		    {
		    document.getElementById("txtHint").innerHTML=xmlhttp.responseText;
		    }
		  }
		xmlhttp.open("GET","gethint.php?q="+str,true);
		xmlhttp.send();
		}

		function listaAjax(pag,nElem)
		{

			var xmlhttp=new XMLHttpRequest();
			xmlhttp.onreadystatechange=function()
			{

			  if (xmlhttp.readyState==4 && xmlhttp.status==200)
			    {
				    var respuesta = xmlhttp.responseText;
      				var respParseada = JSON.parse(respuesta);
      				var vectorUsuarios = respParseada["vector"];

      				var tablaNueva = "<tr><td>ID</td><td>NAME</td><td>EMAIL</td><td>CP</td><td>DELETE</td></tr>";
      				for (var i = 0; i < vectorUsuarios.length; i++) {
      					tablaNueva += "<tr><td>" + vectorUsuarios[i]["id"] + "</td>";
      					tablaNueva += "<td>" + vectorUsuarios[i]["nombre"] + "</td>";
      					tablaNueva += "<td>" + vectorUsuarios[i]["email"] + "</td>";
      					tablaNueva += "<td>" + vectorUsuarios[i]["cp"] + "</td>";
      					tablaNueva += "<td><input type='button' value='Eliminar' onClick='borrarAjax(" + vectorUsuarios[i]["id"] + "); listaAjax(" + pag + "," + nElem + ")' ></td></tr>";
      				};
      				document.getElementById("idTabla").innerHTML = tablaNueva;
      				document.getElementById("idNumEltos").innerHTML = "Num elementos: " + respParseada["numTotal"];
      				var totalPags = Math.ceil(respParseada["numTotal"] / nElem);
   					document.getElementById("idPag").innerHTML = "Pagina: " + ((pag / nElem) + 1) + "/" + totalPags;

   					//Si no estoy en la primera pagina, muestro "Anterior"
   					if(pag > 0)
   					{
   						var enlacePagAnterior = "<a href=# onClick='listaAjax(" + (pag - nElem) + "," + nElem + ")'> << ANTERIOR </a>";
   						document.getElementById("idPagAnt").innerHTML = enlacePagAnterior;
   					}
   					else
   					{
   						document.getElementById("idPagAnt").innerHTML = "";
   					}


   					//Si no estoy en la ultima pagina, muestro "Siguiente"
   					if(((pag/nElem) + 1) != totalPags)
   					{
	      				var enlacePagSiguiente = "<a href=# onClick='listaAjax(" + (pag + nElem) + "," + nElem + ")'> SIGUIENTE >> </a>";
	      				document.getElementById("idPagSig").innerHTML= enlacePagSiguiente;
	      			}
	      			else
	      			{
	      				document.getElementById("idPagSig").innerHTML= "";
	      			}
			    }
			}

			var query = document.getElementById("idNombre").value;
			var queryCP = document.getElementById("idCp").value;

			var cadenaUrl = "?query=" + query + "&queryCP=" + queryCP + "&pag=" + pag + "&nElem=" + nElem;
			
			//var cadenaUrl = "?q=" + "M";
			xmlhttp.open("GET","getusersajax.php"+cadenaUrl,true);
			//xmlhttp.open("GET","getHint.php"+cadenaUrl,true);
			xmlhttp.send();

		}

		function borrarAjax(id)
		{
			var xmlhttp=new XMLHttpRequest();
			xmlhttp.onreadystatechange=function()
			{
			  if (xmlhttp.readyState==4 && xmlhttp.status==200)
			  {
			    //document.getElementById("idPrueba").innerHTML=xmlhttp.responseText;
			   return true;
			  }
			}
			xmlhttp.open("GET","deleteuserajax.php?id="+id,true);
			xmlhttp.send();
		}
	</script>
</head>  
<body>
	<?php

	function muestra($cadena)
	{
		echo $cadena;
	}
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
		
		echo '<table id="idTabla" style="width:400px">';
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
		
		$_SESSION['filtro']['nombre'] = $filtro;
		$_SESSION['filtro']['cp'] = $filtroCP;
	
	}

	if ($_GET["action"] == "delete"){
		delete($_GET["id"]);
	}

	if ($_POST["action"] == "insert")
	{
		if(checkCP($_POST["cp"]))
		{
			insert($_POST["nombre"], $_POST["email"], $_POST["cp"]);
			$nombreInsertar = '';
			$emailInsertar = '';
			$cpInsertar = '';
		}
		else
		{
			$nombreInsertar = $_POST["nombre"];
			$emailInsertar = $_POST["email"];
			$cpInsertar = $_POST["cp"];
			$cpError = '* El CP deben ser 5 digitos!';
		}
	}
/*
	echo "<h1> Search an user: </h1>";
	echo '<form action="list.php" method="post">
		  			<input type="hidden" name="action" value="search">
		  			<ul>
		  			<li>Nombre: <input type="text" name="query" value="' . $filtro . '" onkeyup="showHint(this.value)">
		  			<span id="txtHint"> </span></li>
		  			<li>CP: <input type="text" name="queryCP" value=' . $filtroCP . '></li>
		  			<br/><input type="submit" value="Buscar">
		  			</ul>
		  		</form>';
*/

	echo "<h1> Search an user: </h1>";
	echo '<input type="hidden" name="action" value="search">
		  	Nombre: <input id="idNombre" type="text" name="query" value="' . $filtro . '" onkeyup="showHint(this.value)">
		  	<span id="txtHint"> </span>
		  	<br/>CP: <input id="idCp" type="text" name="queryCP" value=' . $filtroCP . '>
		  	<br/><input type="button" value="Buscar" onClick="listaAjax(0,10)">';
	
	echo "<h1> Here's your name-email list </h1>";
	//Cargo los usuarios que cumplen el filtro (si hay)
	$data = cargarDatos($_SESSION['filtro']);

	//calculo el número de páginas totales y muestro página actual/páginas totales
	$paginasTotales = ceil(count($data)/10);
	//echo "Pagina ". $pagina . "/" . $paginasTotales;
	echo "<div id=idPag> Pagina: " . $pagina . "/" . $paginasTotales . "</div>";
	echo "<div id=idNumEltos>Num elementos: " . count($data) . "</div>";

	//llamo a la función de mostrar usuarios, y la página a mostrar
	listUsers($data, $pagina);

	//muestro enlaces a página anterior/siguiente si es que las hay
	//if($pagina>1)
		//echo '<div id=idPagAnt><a href ="' . $_SERVER['PHP_SELF'] . '?page=' . ($pagina - 1) .'"> << Anterior </a></div>';
	//if($pagina<$paginasTotales)
		//echo '<div id=idPagSig><a href ="' . $_SERVER['PHP_SELF'] . '?page=' . ($pagina + 1) .'"> Siguiente >> </a></div>';
	echo '<div id=idPagAnt></div>';
	echo '<div id=idPagSig><a href=# onClick="listaAjax(10,10)"> SIGUIENTE >></a></div>';
		//echo '<a onclick="listaAjax(10,10)" href="#"> Siguiente </a>';

	echo "<h1> Insert an user </h1>";
	echo '<form action="list.php" method="post">
				<input type="hidden" name="action" value="insert">
				Nombre:<input type="text" name="nombre" value="' . $nombreInsertar . '">
				email:<input type="text" name="email" value="' . $emailInsertar . '">
				CP:<input type="text" name="cp" value="' . $cpInsertar . '">
				<span id="idPrueba" class="error"> ' . $cpError . '</span>
				<input type="submit" value="Insertar">
			</form>';



	?>
	
	<br/>
	<a href="index.html"> Back </a>
	<br/>
	<a href="ajax.php"> ajax </a>


</body>  
</html>
