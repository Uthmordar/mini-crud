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
function selec($args) {

    global $pdo;
    $executes = array();

    if (isset($args['table'])) {
        $table = "user";
    } else {
        die("API vous devez définir un nom de table"); // arrêt des script
    }

    if (isset($args['status'])) {
        $status = $args['status'];
    } else {
        $status = '0';
    }

    $sql = "SELECT * FROM `$table` status= :status;";

    $stmt = $pdo->prepare($sql); // requête préparée
    // bind value
    $stmt->bindValue(":status", $status, PDO::PARAM_INT);

    $stmt->execute();
    return $stmt;
}

/**
 * ----- C(R)UD
 */
function create() {
      global $pdo, $errors;
    $table = "user";
    
    
    if (!isset($_POST['name'])) {
        //$errors[] = "Le nom est obligatoire";
        return false;
    }
    if (!secur($_POST['name'])){
        $errors[] = "Pb dans le nom, ne pas afficher cette erreur sécu";
        return false;
    }
    
    // INSERT INTO user (name, avatar) VALUES ('Antoine', 'no')
    $sql = "
        INSERT 
        INTO $table (name, avatar) 
        VALUES (:name, :avatar);";

    $stmt = $pdo->prepare($sql);
    
    $name = trim($_POST['name']); // attention il faut utiliser la fonction secur
    
    $stmt->bindValue(':name', $name, PDO::PARAM_STR);
    $stmt->bindValue(':avatar', 'no', PDO::PARAM_STR);

    
    return $stmt->execute(); // true or false
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

function upload($file) {
    
}

?>
