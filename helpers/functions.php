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
    global $pdo, $errors;
    
     $name = trim($_POST['name']); // plus d'espace avant et après
     if (empty($name)) {
        $errors[] = "Le nom est obligatoire";
        return false;
    }
    
    if (!secur($name)) {
        $errors[] = "Pb dans le nom, ne pas afficher cette erreur sécu";
        return false;
    }
    
    //INSERT INTO user (name, avatar) VALUES ('Antoine', 'no')
    $sql = "
        INSERT 
        INTO `user` (name, avatar, status) 
        VALUES (:name, :avatar, :status);";
    
    $stmt = $pdo->prepare($sql);
    $status = (secur($_POST['status'])) ? $_POST['status'] : '0';
    $avatar='no';
    
     /* ---------- On a passé les tests on continue  --- */

    $stmt->bindValue(':name', $name, PDO::PARAM_STR);
    $stmt->bindValue(':avatar', 'no', PDO::PARAM_STR);
    $stmt->bindValue(':status', $status, PDO::PARAM_STR);
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
    // j'ai mis ! pour que la fonction retourne soit true soit false et pas 0 ou 1 !0 => true et !1 => false
    // $$$$ @ <scrip>alert('xss')</script>
    if (!preg_match('/^[\w àáâãäåçèéêëìíîïðòóôõöùúûüýÿ-]+$/', $post)) {
        return false;
    } else {
        return true;
    }
}

/*
 * ----- upload
 */
function uplaod(){
    
}

?>
