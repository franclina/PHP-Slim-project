<?php
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

require_once 'commons.php';
require_once 'mwclientes.php';

//define('_TABLE_', 'clientes');

/******************************************
200 Ok 			-> Successful requests other than creations and deletions.
201 Created 	-> Successful creation of a queue, topic, temporary queue, temporary topic, session, producer, consumer, listener, queue browser, or message.
204 No content  -> Successful deletion of a queue, topic, session, producer, or listener.
400 Bad Request -> The path info doesn't have the right format, or a parameter or request body value doesn't have the right format, or a required parameter is
				   missing, or values have the right format but are invalid in some way (for example, destination parameter does not exist, content is too big, or client ID is in use).
403 Forbidden 	-> The invoker is not authorized to invoke the operation.
404 Not Found   -> The object referenced by the path does not exist.
405 Method Not Allowed -> The method is not one of those allowed for the path.
409 Conflict    -> An attempt was made to create an object that already exists.
500 Internal Server Error -> The execution of the service failed in some way.
*********************************************/

//GET all clients
$app->get('/api/clientes', function (Request $request, Response $response){
	$resultado = null;
	$db = null;
	$sql = 'SELECT * FROM clientes';
	try {
		$db = getConnectionDB();
		$resultado = $db->query($sql);
		if($resultado->rowCount() > 0){
			$clientes = $resultado->fetchAll(PDO::FETCH_OBJ);
			return $response->withJson($clientes, 200);
		}
		return $response->withJson(['error' => 'No existen clientes en la base de datos'], 404);
		
	// } catch(PDOException $e){
	} catch(Exception $e){
		$this->logger->addError($request->getMethod(), array('uri' => $request->getUri(), 'error' => $e->getMessage()));
		return $response->withJson(['error' => 'Error interno de la aplicación'], 400);
	} finally {
		$resultado = null;
		$db = null;
	}
});

//POST new cliente
$app->post('/api/clientes', function (Request $request, Response $response){
	$resultado = null;
	$db = null;
	$cliente = json_decode($request->getBody());

	$sql = 'INSERT INTO clientes (nombre, apellidos, telefono, email, direccion, ciudad) VALUES (:nombre, :apellidos, :telefono, :email, :direccion, :ciudad)';
	try{
		$db = getConnectionDB();
		$resultado = $db->prepare($sql);

		$resultado->bindParam(':nombre', $cliente->nombre);
		$resultado->bindParam(':apellidos', $cliente->apellidos);
		$resultado->bindParam(':telefono', $cliente->telefono);
		$resultado->bindParam(':email', $cliente->email);
		$resultado->bindParam(':direccion', $cliente->direccion);
		$resultado->bindParam(':ciudad', $cliente->ciudad);

		if($resultado->execute()){
			return $response->withJson("Cliente añadido", 202);
		}
		
		return $response->withJson("Error añadir Cliente", 500);
		
	// } catch(PDOException $e){
	} catch(Exception $e){
		$this->logger->addError($request->getMethod(), array('uri' => $request->getUri(), 'request'=> $cliente, 'error' => $e->getMessage()));
		return $response->withJson(['error' => 'Error interno de la aplicación'], 400);
	} finally{
		$resultado = null;
		$db = null;
	}
})->add($validatePOST);

//PUT update cliente
$app->put('/api/clientes/{id}', function (Request $request, Response $response){
	
	$resultado = null;
	$db = null;
	$cliente = json_decode($request->getBody(), true);
	$id = $request->getAttribute('id');

	$sql = 'UPDATE clientes SET nombre=:nombre, apellidos=:apellidos, telefono=:telefono, email=:email,	direccion=:direccion, ciudad=:ciudad WHERE id=:id';
	try
	{

		$db = getConnectionDB();
		$resultado = $db->prepare($sql);

		$resultado->bindParam(':id', $id);//, PDO::PARAM_INT
		$resultado->bindParam(':nombre', filter_var($cliente->nombre, FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW));
		$resultado->bindParam(':apellidos', filter_var($cliente->apellidos, FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW));
		$resultado->bindParam(':telefono', filter_var($cliente->telefono, FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW));
		$resultado->bindParam(':email', filter_var($cliente->email, FILTER_VALIDATE_EMAIL));
		$resultado->bindParam(':direccion', filter_var($cliente->direccion, FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW));
		$resultado->bindParam(':ciudad', filter_var($cliente->ciudad, FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW));

		if($resultado->execute()){
			return $response->withJson(['result' => 'Cliente actualizado'], 200);
		}

		return $response->withJson(['error' => 'Error al actualizar el cliente'], 500);
		
	// } catch(PDOException $e){
	} catch(Exception $e){
		$this->logger->addError($request->getMethod(), array('uri' => $request->getUri(), 'request'=> $cliente, 'error' => $e->getMessage()));
		return $response->withJson(['error' => 'Error interno de la aplicación'], 400);
	}finally{
		$resultado = null;
		$db = null;
	}
})->add($validatePUT);

//DELETE cliente
$app->delete('/api/clientes/{id}', function (Request $request, Response $response){
	$resultado = null;
	$db = null;
	$id = $request->getAttribute('id');

	$sql = 'DELETE FROM clientes WHERE id=:id';
	try
	{
		$resultado = $db->prepare($sql);
		$resultado->bindParam(':id', $id);//, PDO::PARAM_INT

		if($resultado->execute()){
			return $response->withJson(['result' => 'Cliente eliminado'], 200);
		}

		return $response->withJson(['error' => 'Error al eliminar el cliente'], 500);
		
	// } catch(PDOException $e){
	} catch(Exception $e){
		return $response->withJson(['error' => 'Error interno de la aplicación'], 400);
	}finally{
		$resultado = null;
		$db = null;
	}
})->add($validateDELETE);
