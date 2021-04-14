<?php

/************ MESSAGES ERRORS **********/

// $message = array(
// 	"DATABASE_EMPTY" => "There aren't clients in the database",
// 	"CLIENT_NOT_FOUND" => "Client specified doesn't exist",
// 	"INTERNAL_ERROR" => "Internal error, please contact with the adminstrator",

// 	"CLIENT_CREATED" => "Client created",
// 	"ERROR_CREATE_CLIENT" => "Error trying to create the client",


// );




/************ FUNCTIONS **************/ 
function getConnectionDB(){
	$db = new db();
	return $db->connectionDB();
}

function verify($table, $id){
	$resultado = null;
	$db = null;
	$sql = 'SELECT 1 FROM '.$table.' WHERE id='.$id.' LIMIT 1';
	try{
		$db = getConnectionDB();
		$resultado = $db->query($sql);
		if($resultado->rowCount() > 0){
			return true;
		}
		return false;
	}catch(PDOException $e){
		echo '{"error":"text":'.$e->getMessage().'}';
	}finally {
		$resultado = null;
		$db = null;
	}
}

//function clean($data){
//	return htmlspecialchars(stripslashes(trim($data)));
//}