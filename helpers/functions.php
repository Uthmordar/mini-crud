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
 *  @connexion à la base de données PDO php.net
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
 */
function selec($args) {
    global $pdo;
    
    // table required
    if (isset($args['table'])) {
        $table = $args['table'];
    } else {
        die('API erreur définir un nom de table');
    }

    $sql = "SELECT * FROM `$table` WHERE 1=1 ";
    
    // status ?
    if (isset($args['status'])) {
        $status = $args['status'];
        $sql .= " AND status= :status "; // place holder 
    }
    
     // user_id ?
    if (isset($args['user_id'])) {
        $userId = (int) $args['user_id'];
        $sql .= " AND user_id= :user_id";
    }
    
    $sql.=";";
    
    // SELECT * FROM FROM user WHERE 1=1 AND status= :status AND user_id= :status;
    $stmt = $pdo->prepare($sql); // requête préparée moule retourne un objet de type PDOStatement
    
    if (isset($args['user_id'])) {
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
    }

    if (isset($args['status'])) {
        $stmt->bindValue(':status', $status, PDO::PARAM_STR);
    }
    
    $stmt->execute();  // on execute
    return $stmt;
    
    
}

$errors = array();

/**
 * ----- C(R)UD
 */
function create() {
    
}

function update() {
   
}

function delete($userId, $life = 'delete') {
  
}

/*
 * ----- secu
 */

function secur($post) {
   
}

/*
 * ----- upload
 */
function uplaod(){
    
}

?>
