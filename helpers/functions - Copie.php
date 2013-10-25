<?php

/*
 *  @config
 */

$config = require_once PATH_CONFIG . 'config.php';
$database = require_once PATH_CONFIG . 'database.php';

function getConfig($name) {
    global $config;
    if (isset($config[$name])) {
        return $config[$name];
    }
}

/*
 *  @connexion à la base de données PDO 
 */

try {
    $options = array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8", PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION); // exécution de la requête en UTF-8
    $pdo = new PDO("mysql:host={$database['host']};dbname={$database['dbname']}", $database['user'], $database['pass'], $options);

    // $pdo = null; // ferme la connexion ...
} catch (PDOException $e) {
    echo "Erreur: à la ligne:" . $e->getLine() . "on a l'erreur:" . $e->getMessage();
}

/*
 *  @errors
 */

$errors = array();

/**
 *  version ***
 * 
 * @global type $pdo
 * @param type $args 
 */
 
function selec(){
	global $pdo;
	$stmt=$pdo->prepare("
			SELECT *
			FROM `user`
			"
	);
	
	$stmt->execute();
	return $stmt;

}

function selecNew($args){
    global $pdo;
	
	$execute=array();
	$table='`user`';
	
	if(isset($args['table'])){
		$table=$args['table'];
	}
	
	$sql="SELECT * FROM $table WHERE 1=1 ";
	
	// selec(array('table'=>'user', 'where'=> array('status'=>'1', user_id='2'))); 
	if(isset($args['where'])){
		foreach($args['where'] as $k=>$v){
			$sql .= " AND $k=?";
			
			$execute[]=$v;
		}
	}
	//... WHERE 1=1 AND status=? AND user_id=?
	
	$stmt = $pdo->prepare($sql);
	
	// bindValues
	$i=0;
	foreach($execute as $v){
		$i++;
		$stmt->bindValue($i, $v, PDO::PARAM_INT);
	}
	
	
	$stmt->execute();
	
	return $stmt;
}

function selecTarget($table, $argsWhere, $valueWhere){
	global $pdo;
	
	$stmt = $pdo->prepare("
				SELECT *
				FROM `".$table."`
				WHERE ".$argsWhere."=:valueWhere
			"
		);
		
	$stmt->bindValue(':valueWhere', $valueWhere, PDO::PARAM_STR);
	
	$stmt->execute();
	
	return $stmt;

}

/**
 * ----- C(R)UD
 */
function create() {
	global $pdo;
	
	$sql="INSERT INTO `user` (name, status, avatar) VALUES (:name, :status, :avatar);";
	
	$stmt= $pdo->prepare($sql);
	if(secur($_POST['nom']) && secur($_POST['radio1'])){
		$name=$_POST['nom'];
		$status=$_POST['radio1'];
		if(!empty($_FILES['avatar']['tmp_name'])){
			$avatar=upload($_FILES['avatar']['tmp_name']);
		}else{
			$avatar='no';
		}
	
		$stmt->bindValue(':name', trim($name), PDO::PARAM_STR);
		$stmt->bindValue(':status', $status, PDO::PARAM_STR);
		$stmt->bindValue(':avatar', $avatar, PDO::PARAM_STR);
		
		$stmt->execute();
		
		return $stmt;
    }
}

function update() {

	global $pdo;
	
	$sql="UPDATE `user` SET `name`=:name, `avatar`=:avatar, `status`= :status WHERE `user_id`=:user ;";
	
	$stmt= $pdo->prepare($sql);
	
	if(secur($_POST['nom']) && secur($_POST['radio1'])){
		$user=$_GET['user'];
		$name=$_POST['nom'];
		$status=$_POST['radio1'];
		$avatar='no';
	

		$stmt->bindValue(':name', $name, PDO::PARAM_STR);
		$stmt->bindValue(':status', $status, PDO::PARAM_STR);
		$stmt->bindValue(':avatar', $avatar, PDO::PARAM_STR);
		$stmt->bindValue(':user', $user, PDO::PARAM_STR);
		
		$stmt->execute();
		
		return $stmt;
    }
    
}

function delete($userId) {
    global $pdo;
	
	$sql="DELETE FROM `user` WHERE user_id=".$userId.";";
	
	$stmt= $pdo->prepare($sql);

	$stmt->execute();
		
	return $stmt;
}

/*
 * ----- securité
 */

function secur($post) {
    $pattern="/^[\w àáâãäåçèéêëìíîïðòóôõöùúûüýÿ-]+$/";
	
	$check=preg_match($pattern, $post);
	
	if($check==0 || $check==FALSE){
		return FALSE;
	}else{
		return TRUE;
	}
}

/*
 * ----- upload
 */

function upload($file) {
	
	$img=imagecreatefrompng($file);
	
	$percent=20/100;
	$size=getimagesize($file);
	$x=$size[0];
	$y=$size[1];
	
	$thumbX=$x*$percent;
	$thumbY=$y*$percent;
	
	$thumb = imagecreatetruecolor($thumbX, $thumbY);
	
	$copy=imagecopyresampled($thumb, $img, 0, 0, 0, 0, $thumbX, $thumbY, $x, $y);
	
	header('Content-Type : image/png');
	$name=md5(uniqid(rand(), true));
	
	imagepng($thumb);
	imagepng($thumb, 'assets/images/'.$name.'.png');
	
	imagedestroy($img);
	imagedestroy($thumb);
	
	return 'assets/images/'.$name.'.png';
}

?>
