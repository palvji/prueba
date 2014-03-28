<?php
	
	include 'dbFunctions.php';

	function insert($nombre, $email, $cp) {
		if(addUser($nombre, $email, $cp))
		{
			//echo 'Insertado con éxito';
		}
		else
		{
			//echo 'Error en la inserción';
		}
	}

	function delete($id) {
		if(deleteUser($id))
		{
			//echo 'Eliminado con éxito';
		}
		else
		{
			//echo 'Error en la eliminación';
		}
	}

	//YA NO SE USA, SE LISTA EN list.php recorriendo los vectores
	function listar($filtro) {

		if(!$filtro) {
			$result = mysqli_query($con,"SELECT * FROM name_email");
		}
		else {
			$result = mysqli_query($con,"SELECT * FROM name_email WHERE nombre like '%" . $filtro ."%' or email like '%" . $filtro . "%'");
		}
		echo "<ul>";

		while($row = mysqli_fetch_array($result))
		  {
		  echo "<li>";
		  $id = $row['id'];
		  echo $id . ". " . $row['nombre'] . " - " . $row['email'];
		  echo '<form action="list.php" method="get">
		  			<input type="submit" value="Eliminar">
		  			<input type="hidden" name="action" value="delete">
		  			<input type="hidden" name="id" value="' . $id . '">
		  		</form>';
		  echo "</li>";
		  echo "<br>";
		  }
		  

		echo "</ul>";
	}

	function cargarDatos($filtro)
	{
		return loadUsers($filtro);
	}

?>