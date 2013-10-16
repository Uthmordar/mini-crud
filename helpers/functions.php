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
    }else{
        return '';
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
    // order by
    if (isset($args['order_desc'])) {
        $sql .= " ORDER BY {$args['order_desc']} DESC"; // place holder 
    }
    // order by
    if (isset($args['order_asc'])) {
        $sql .= " ORDER BY {$args['order_asc']} ASC"; // place holder 
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
    $avatar = 'no';
    
    $name = trim($_POST['name']); // plus d'espace avant et après
    if (empty($name)) {
        $errors[] = "Le nom est obligatoire";
        return false;
    }

    if (!secur($name)) {
        $errors[] = "Pb dans le nom, ne pas afficher cette erreur sécu";
        return false;
    }

    if (is_uploaded_file($_FILES['avatar']['tmp_name'])) {
        $avatar = upload($_FILES['avatar']);

        if (!$avatar) {
            return false;
        }
    }

    //INSERT INTO user (name, avatar) VALUES ('Antoine', 'no')
    $sql = "
        INSERT 
        INTO `user` (name, avatar, status, date_crea) 
        VALUES (:name, :avatar, :status, NOW());";

    $stmt = $pdo->prepare($sql);
    $status = (secur($_POST['status'])) ? $_POST['status'] : '0';


    /* ---------- On a passé les tests on continue  --- */

    $stmt->bindValue(':name', $name, PDO::PARAM_STR);
    $stmt->bindValue(':avatar', $avatar, PDO::PARAM_STR);
    $stmt->bindValue(':status', $status, PDO::PARAM_STR);
    return $stmt->execute(); // true or false
}

// function update
function update() {
    global $pdo, $errors;
    $name = trim($_POST['name']); // plus d'espace avant et après
    if (empty($name)) {
        $errors[] = "Le nom est obligatoire";
        return false;  // sort de la fonction
    }

    if (!secur($name)) {
        $errors[] = "Pb dans le nom, ne pas afficher cette erreur sécu";
        return false;
    }

    $avatar = 'no';
    $userId = (int) $_POST['user_id'];
    $status = (secur($_POST['status'])) ? $_POST['status'] : '0';

    // upload d'image
    $userId = (int) $_POST['user_id'];
    $user = selec(array('table' => 'user', 'user_id' => $userId));
    $user = $user->fetch();
    
    if (is_uploaded_file($_FILES['avatar']['tmp_name'])) {
        // upload image
        $avatar = upload($_FILES['avatar']);
        if (!$avatar) {
            return false;
        }
        
        if ($user['avatar'] != 'no') {
            // supprime le fichier si il existe
            if (file_exists($user['avatar']))
                unlink($user['avatar']);
        }
    }else{
        $avatar=$user['avatar'];
    }

    $sql = "UPDATE `user` SET name=:name, avatar=:avatar, status=:status, date_crea=NOW() WHERE user_id=:user_id ;"; // moule de la requête
    $stmt = $pdo->prepare($sql); // préparée 

    $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
    $stmt->bindValue(':name', $name, PDO::PARAM_STR);
    $stmt->bindValue(':avatar', $avatar, PDO::PARAM_STR);
    $stmt->bindValue(':status', $status, PDO::PARAM_STR);

    return $stmt->execute(); // true or false
}

function delete($userId) {
    global $pdo;
    $sql = "DELETE FROM `user` WHERE user_id=:user_id ;";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
    return $stmt->execute(); // true or false
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

function upload($file) {

    global $errors;

    $extensions = array('jpg', 'jpeg', 'gif', 'png');
    $types = array('image/png', 'image/jpg', 'image/gif', 'image/jpeg');

    // on calcule les dimensions de l'image
    $size = getimagesize($file['tmp_name']);
    list ($x, $y) = $size;

    // supérieur à 1 Mo 1024*1024
    if ($file['size'] > 1048576) {
        $errors[] = 'image > à 1mo (taille: ' . $file['size'] . ')';
        return false;
    }
    // vérification du type de l'image...
    if (!in_array($size['mime'], $types)) {
        $errors[] = 'type d\'image inconnu';
        return false;
    }


    // on dépasse le nombre d'image sur le serveur...
    if (count(scandir(PATH_AVATARS)) > 2000) {
        $errors[] = 'Plus de place dans le dossier';
        return false;
    }

    // Ici tout est ok on continue

    $ext = substr(strrchr($file['name'], '.'), 1);

    // uniqid génère un identifiant unique true ajoute de l'entropie et rand() est un prefix aléatoire, md5 crypte le tout en hexa sur 32 caractères.
    $name = md5(uniqid(rand(), true));

    // onc crée l'avatar
    // si largeur > hauteur  landscape
    // landscape reduction en pourcentage

    $reduc = ((150 * 100) / $x); // pourcentage X100
    $thumbX = 150;
    $thumbY = ($y * $reduc) / 100; // attention pourcentage X100
    // création de l'image vide
    $thumb = imagecreatetruecolor($thumbX, $thumbY);

    $thumbPath = PATH_AVATARS . $name . '.' . $ext;
    $pathImage = $file['tmp_name'];

    // png ?
    if ($size['mime'] == 'image/png') {
        $img = imagecreatefrompng($pathImage);
        imagecopyresampled($thumb, $img, 0, 0, 0, 0, $thumbX, $thumbY, $x, $y);
        imagepng($thumb, $thumbPath); // save tumbnail
    }

    // jpg ?
    if ($size['mime'] == 'image/jpg' || $size['mime'] == 'image/jpeg') {
        $img = imagecreatefromjpeg($pathImage);
        imagecopyresampled($thumb, $img, 0, 0, 0, 0, $thumbX, $thumbY, $x, $y);
        imagejpeg($thumb, $thumbPath); // save tumbnail
    }

    // gif ?
    if ($size['mime'] == 'image/gif') {
        $img = imagecreatefromgif($pathImage);
        imagecopyresampled($thumb, $img, 0, 0, 0, 0, $thumbX, $thumbY, $x, $y);
        imagegif($thumb, $thumbPath); // save tumbnail
    }

    // libère la mémoire associée à l'image
    //chmod($thumbPath, 0777);  // assigner les droits de lecture etc...Pour ubuntu 
    imagedestroy($thumb);
    imagedestroy($img);

    return $thumbPath;
}

/*
 *  --- getUrl($file), attention 
 * 
 */

function getUrl($file=''){
    return getConfig('url').$file;
}

?>
