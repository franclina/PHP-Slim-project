<?php

define('_TABLE_', 'clientes');

$validatePOST = function ($request, $response, $next) {
    // $response->getBody()->write('BEFORE');
    $cliente = json_decode($request->getBody());

    /********** VALIDATE REQUEST ********/
	list($valid, $errors)=validateCliente($cliente);
	if(!$valid){return $response->withJson(['error' => $errors], 400);}

    $response = $next($request, $response);

    // $response->getBody()->write('AFTER');

    return $response;
};


$validatePUT = function ($request, $response, $next) {
    // $response->getBody()->write('BEFORE');
    $cliente=json_decode($request->getBody());
    $id=basename(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

    /********** VALIDATE REQUEST ********/
	list($valid, $errors)=validateCliente($cliente);
	if(!$valid){return $response->withJson(['error' => $errors], 400);}
	/********** VALIDATE ID EXISTS********/
	if(is_numeric($id) && verify(_TABLE_, $id)){
	    $response = $next($request, $response);
	}else{
		 $response = $response->withJson(['error' => 'No existe el id del cliente especificado'], 400);		
	}
    
    // $response->getBody()->write('AFTER');
    return $response;
};

$validateDELETE = function ($request, $response, $next) {
    // $response->getBody()->write('BEFORE');
    $id=basename(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

	/********** VALIDATE ID EXISTS********/
	if(is_numeric($id) && verify(_TABLE_, $id)){
	    $response = $next($request, $response);
	}else{
		 $response = $response->withJson(['error' => 'No existe el id del cliente especificado'], 400);		
	}
    
    // $response->getBody()->write('AFTER');
    return $response;
};

function validateCliente($cliente){
	$email = filter_var($cliente->email, FILTER_VALIDATE_EMAIL);
	$error = [];
	if ( $email === false ) {
		$error[] = 'Error formato email';
	 }

	return array(empty($error), $error);	
}