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
	
	// selec(array('table'=>'user', 'where'=> array('status'=>'1', user_id='2'), 'order'=>array('type'=>'ASC', 'column'=>array('dateTime', 'name'))); 
	if(isset($args['where'])){
		foreach($args['where'] as $k=>$v){
			$sql .= " AND $k=?";
			
			$execute[]=$v;
		}
	}
	//... WHERE 1=1 AND status=? AND user_id=?
	
	// implode(', ', $array)
	
	if(isset($args['order'])){
		$sql.=" ORDER BY ";
		
		if(isset($args['order']['column'])){
			$column=implode(', ', $args['order']['column']);
			$sql .= $column;
		}
		
		if($args['order']['type']=='ASC' || !isset($args['order']['type'])){
			$sql .= " ASC";
		}else if($args['order']['type']=='DESC'){
			$sql .= " DESC";
		}else{
			return false;
		}
	}
	
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
	
	$sql="INSERT INTO `user` (name, status, avatar, dateTime) VALUES (:name, :status, :avatar, NOW());";
	
	$stmt= $pdo->prepare($sql);
	if(secur($_POST['nom']) && secur($_POST['radio1'])){
		$name=$_POST['nom'];
		$status=$_POST['radio1'];
		if(!empty($_FILES['avatar']) && $_FILES['avatar']['size']<=1048576 && $_FILES['avatar']['error']==0){
			$avatar=upload($_FILES['avatar']);
		}else{
			$avatar='no';
		}
	
		$stmt->bindValue(':name', trim($name), PDO::PARAM_STR);
		$stmt->bindValue(':status', $status, PDO::PARAM_STR);
		$stmt->bindValue(':avatar', $avatar, PDO::PARAM_STR);
		
		return $stmt->execute();
    }else{
		return false;
	}
}

/*function update() {

	global $pdo;
	
	if(!empty($_FILES['avatar']['tmp_name']) && $_FILES['avatar']['error']==0 && $_FILES['avatar']['size']<=1048576){
	
		$sql="UPDATE `user` SET `name`=:name, `avatar`=:avatar, `status`= :status WHERE `user_id`=:user ;";
		$avatar=upload($_FILES['avatar']);
		$stmt= $pdo->prepare($sql);
		$stmt->bindValue(':avatar', $avatar, PDO::PARAM_STR);
		
	}else{
		$sql="UPDATE `user` SET `name`=:name, `status`= :status WHERE `user_id`=:user ;";
		$stmt= $pdo->prepare($sql);		
	}
		
	if(secur($_POST['nom']) && secur($_POST['radio1'])){
		$user=$_GET['user'];
		$name=$_POST['nom'];
		$status=$_POST['radio1'];
	

		$stmt->bindValue(':name', $name, PDO::PARAM_STR);
		$stmt->bindValue(':status', $status, PDO::PARAM_STR);
		$stmt->bindValue(':user', $user, PDO::PARAM_STR);
		
		return $stmt->execute();
	}
}*/

function update2(){

	global $pdo;
	
	$sql="UPDATE `user` SET `name`=:name, `avatar`=:avatar, `status`= :status, `dateTime`=NOW() WHERE `user_id`=:user ;";
		
	$stmt= $pdo->prepare($sql);	
		
	if(secur($_POST['nom']) && secur($_POST['radio1'])){
		$user=$_POST['user_id'];
		$name=$_POST['nom'];
		$status=$_POST['radio1'];
		
		$requete=selecNew(array('table'=>'user', 'where'=> array('user_id'=>$user)));
		
		while($user2= $requete->fetch()){
			$avatar=$user2['avatar'];
		}
		
		if(!empty($_FILES['avatar']['tmp_name']) && $_FILES['avatar']['error']==0 && $_FILES['avatar']['size']<=1048576){
			unlink($avatar);
			$avatar=upload($_FILES['avatar']);
		}
	

		$stmt->bindValue(':name', $name, PDO::PARAM_STR);
		$stmt->bindValue(':status', $status, PDO::PARAM_STR);
		$stmt->bindValue(':avatar', $avatar, PDO::PARAM_STR);
		$stmt->bindValue(':user', $user, PDO::PARAM_STR);
		
		return $stmt->execute();
	}else{
		return false;
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
	
	$file=$file['tmp_name'];

	//$ext= array('.jpg', '.jpeg', '.png', '.gif');
	$contentType=array('image/jpg', 'image/jpeg', 'image/png', 'image/gif');
	
	// On parse $fil['name'], on repére '.' et on retourne ce qui se trouve après si 1 ou aprés avec '.' inclus si 0.
	// substr(strrchr($file['name'], '.'), 1);
	
	$percent=20/100;
	$size=getimagesize($file);
	$x=$size[0];
	$y=$size[1];
	$thumbX=80;
	$thumbY=80;
	
	$MIME=$size['mime'];
	
	if(!in_array($MIME, $contentType)){
		return false;
	}
	
	if(count(scandir(PATH_IMAGE))>200){
		return false;
	}
	
	$name=md5(uniqid(rand(), true));
	//$thumbX=$x*$percent;
	//$thumbY=$y*$percent;
	
	
	$thumb = imagecreatetruecolor($thumbX, $thumbY);
	
	/*$img=imagecreatefrompng($file);
	
	$copy=imagecopyresampled($thumb, $img, 0, 0, 0, 0, $thumbX, $thumbY, $x, $y);*/
	
	
	if($MIME==$contentType[0]){
	
		$img=imagecreatefromjpg($file);
		$copy=imagecopyresampled($thumb, $img, 0, 0, 0, 0, $thumbX, $thumbY, $x, $y);
	
		header('Content-Type : '.$contentType[0]);
		
		$ext=substr(strrchr($contentType[0], '/'), 1);
		
		imagejpg($thumb);
		imagejpg($thumb, PATH_IMAGE.$name.'.'.$ext);
		
		imagedestroy($img);
		imagedestroy($thumb);
	
		return PATH_IMAGE.$name.'.'.$ext;
		
	}else if($MIME==$contentType[1]){
	
		$img=imagecreatefromjpeg($file);
		$copy=imagecopyresampled($thumb, $img, 0, 0, 0, 0, $thumbX, $thumbY, $x, $y);
	
		header('Content-Type : '.$contentType[1]);
		
		$ext=substr(strrchr($contentType[1], '/'), 1);
		
		imagejpeg($thumb);
		imagejpeg($thumb, PATH_IMAGE.$name.'.'.$ext);
		
		imagedestroy($img);
		imagedestroy($thumb);
	
		return PATH_IMAGE.$name.'.'.$ext;
		
	}else if($MIME==$contentType[2]){
	
		$img=imagecreatefrompng($file);
		$copy=imagecopyresampled($thumb, $img, 0, 0, 0, 0, $thumbX, $thumbY, $x, $y);
	
		header('Content-Type : '.$contentType[2]);
		
		$ext=substr(strrchr($contentType[2], '/'), 1);
		
		imagepng($thumb);
		imagepng($thumb, PATH_IMAGE.$name.'.'.$ext);
		
		imagedestroy($img);
		imagedestroy($thumb);
	
		return PATH_IMAGE.$name.'.'.$ext;
		
	}else if($MIME==$contentType[3]){
	
		$img=imagecreatefromgif($file);
		$copy=imagecopyresampled($thumb, $img, 0, 0, 0, 0, $thumbX, $thumbY, $x, $y);
	
		header('Content-Type : '.$contentType[3]);
		
		$ext=substr(strrchr($contentType[3], '/'), 1);
		
		imagegif($thumb);
		imagegif($thumb, PATH_IMAGE.$name.'.'.$ext);
		
		imagedestroy($img);
		imagedestroy($thumb);
	
		return PATH_IMAGE.$name.'.'.$ext;
	}
	
	/*header('Content-Type : image/png');
	
	imagepng($thumb);
	imagepng($thumb, PATH_IMAGE.$name.'.png');
	
	imagedestroy($img);
	imagedestroy($thumb);
	
	return PATH_IMAGE.$name.'.png';*/
}

?>
