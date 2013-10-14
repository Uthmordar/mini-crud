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
    $sql = "SELECT * FROM `user` WHERE 1=1  ";

    // just where with and
    if (isset($args['where']) && is_array($args['where'])) {

        foreach ($args['where'] as $k => $v) {
            $sql.=" AND $k=?";
            $executes[] = $v;
        }
        
        // if order by
        if(isset($args['order'])){
            $sql.=" ORDER BY {$args['order']}";
        }else{
            $sql.=" ORDER BY date_crea DESC";
        }
        
        $stmt = $pdo->prepare($sql); // requête préparée
        // bind value
        $i = 1;
        foreach ($executes as $e) {
            if (is_string($e))
                $stmt->bindValue($i, $e, PDO::PARAM_STR);
            else
                $stmt->bindValue($i, $e, PDO::PARAM_INT);
            $i++;
        }
        $stmt->execute();
        return $stmt;
    }
}

/**
 * ----- C(R)UD
 */
function create() {
    global $pdo;
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

    $name = trim($_POST['name']);

    //$status = (secur($_POST['status'])) ? $_POST['status'] : '0';

    // gestion de la partie image si 0 pas d'erreur, il faudrait aller plus loin dans la gestion des erreurs...
    if ($_FILES['avatar']['error'] == 0) {
        $avatar = upload($_FILES['avatar']);
        // si cette fonction retourne un tableau il y a des erreurs...
        if (is_array($avatar)) {
            $errors = $avatar;
            return false;
        }
    } else {
        $avatar = 'no';
    }

    /* ---------- On a passé les tests on continue  --- */
    $stmt->bindValue(':name', $name, PDO::PARAM_STR);
    $stmt->bindValue(':avatar', 'no', PDO::PARAM_STR);
    $stmt->bindValue(':status', $status, PDO::PARAM_STR);

    return $stmt->execute(); // true or false
}

function update() {
    global $pdo, $errors;
    $table = "user";

    $sql = "UPDATE $table SET name=:name, avatar=:avatar, status=:status, date_crea=NOW() WHERE user_id=:user_id ;";

    $stmt = $pdo->prepare($sql);

    // secur le nom est obligatoire
    if (!isset($_POST['name'])) {
        $errors[] = "Le nom est obligatoire";
        return false;
    }
    // sécu pas de message
    if (!secur($_POST['name']) || !secur($_POST['user_id'])){
        $errors[] = "Pb dans le nom, ne pas afficher cette erreur sécu";
        return false;
    }
        

    $name = trim($_POST['name']);
    $status = (secur($_POST['status'])) ? $_POST['status'] : '0';
    $userId = (int) $_POST['user_id'];

    // on récupère des info sur user
    $user = selec(array('where' => array('user_id' => $userId)));
    $user = $user->fetch();
    $avatar=$user['avatar'];
    
    // gestion des avatars dans l'upload
    if ($_FILES['avatar']['error'] == 0) {
       
        $avatar = upload($_FILES['avatar']);
        // si cette fonction retourne un tableau il y a des erreurs...
        if (is_array($avatar)) {
            $errors = $avatar;
            return false;
        }
        
         if ($avatar != 'no'){
            if(file_exists($user['avatar']))
                unlink($user['avatar']) ;
        }
    }

    $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
    $stmt->bindValue(':name', $name, PDO::PARAM_STR);
    $stmt->bindValue(':avatar', $avatar, PDO::PARAM_STR);
    $stmt->bindValue(':status', $status, PDO::PARAM_STR);

    return $stmt->execute(); // true or false
}

function delete($userId, $life = 'delete') {
    global $pdo, $errors;
    $table = "user";

    // on récupère des info sur user
    $user = selec(array('where' => array('user_id' => $userId)));
    $user = $user->fetch();
    $avatar=$user['avatar'];
    if(file_exists($user['avatar']))
                unlink($user['avatar']) ;
    
    $sql = "DELETE FROM $table WHERE user_id=:user_id ;";

    $stmt = $pdo->prepare($sql);
    $userId = (int) $userId;

    $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);

    return $stmt->execute(); // true or false
}

/*
 * ----- secu
 */

function secur($post) {
    // on accepte dans notre système que des lettre ou chiffre pas de caractères spéciaux
    $post = trim($post);
    // j'ai mis ! pour que la fonction retourne soit true soit false et pas 0 ou 1 !0 => true et !1 => false
    if (!preg_match('/^[\w àáâãäåçèéêëìíîïðòóôõöùúûüýÿ]+$/', $post)) {
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
        $errors[] = 'image > à 1mo (taille: '.$file['size'].')';
    }
    // vérification du type de l'image...
    if (!in_array($size['mime'], $types)) {
        $errors[] = 'type d\'image inconnu';
    }

    // vérification de l'extension de l'image
    if (!in_array(substr(strrchr($file['name'], '.'), 1), $extensions)) {
        $errors[] = 'extension inconnu';
    } else {
        $ext = substr(strrchr($file['name'], '.'), 1);
    }

    // on dépasse le nombre d'image sur le serveur...
    if (count(scandir(PATH_AVATARS)) > 2000) {
        $errors[] = 'Plus de place dans le dossier';
    }

    // maintenant on est partie pour faire les traitements si tout est ok
    if (!empty($errors))
        return $errors;


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
    chmod($thumbPath, 0777);  // assigner les droits de lecture etc...
    imagedestroy($thumb);
    imagedestroy($img);

    return $thumbPath;
}

?>
