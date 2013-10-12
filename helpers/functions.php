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
    
}

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

function upload($file) {
    
}

?>
