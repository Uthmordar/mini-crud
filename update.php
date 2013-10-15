<?php
//-------- bootstrap 
require_once 'bootstrap.php';
// -------- fin bootstrap


if(!empty($_POST)){
    if(update()){
        header('Location: index.php');
        exit();
    }
}

if(isset($_GET['user_id']))
    $userId = (int) $_GET['user_id']; // 0 si pourri typé int


if(isset($_POST['user_id']))
    $userId = (int) $_POST['user_id']; // 0 si pourri typé int

$user=selec(array('table'=>'user', 'user_id'=>$userId));

$u=$user->fetch(); // affiche une ligne

?>


<!DOCTYPE html>
<html>
    <head>
        <title><?php echo getConfig('name') ?></title>
        <meta charset="<?php echo getConfig('charset') ?>" >
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
        <script src="<?php echo getConfig('url') . 'assets/js/bootstrap-dropdown.js'; ?>"></script>
        <link rel='stylesheet'   href="<?php echo getConfig('url') . 'assets/css/bootstrap.css'; ?>" type='text/css' media='all' />

    </head>
    <body>
        <div class="container">
            <?php if (!empty($errors)): ?>
                <h1>Erreurs</h1>
                <?php var_dump($errors); ?>
            <?php endif; ?> 
            <h1><a href="<?php echo getConfig('url'); ?>" ><?php echo getConfig('name') ?></a></h1>
            <form  action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post" enctype="multipart/form-data" >
                <input type="hidden" name="user_id" value="<?php echo $u['user_id']; ?>" >
                <p>
                    <label for="name">Nom</label>
                    <input required class="input-medium" value="<?php echo $u['name']; ?>"  name="name" id="name" type="text" >
                </p>
                <?php if ($u['avatar'] != 'no'): ?>
                    <p>
                        <img src="<?php echo $u['avatar']; ?>" />
                    </p>
                <?php endif; ?>
                <p>
                    <label for="avatar">Avatar (modifier/ajouter)</label>
                    <input  id="avatar" type="file" name="avatar" >
                </p>
                <p>
                    <label class="radio">
                        <input type="radio" name="status" id="status1" value="1" <?php echo ($u['status']) ? 'checked' : ''; ?>>
                        accès à l'admin
                    </label>
                </p>
                <p><label class="radio">
                        <input type="radio" name="status" id="status2" value="0" <?php echo ($u['status']) ? '' : 'checked'; ?> >
                        Bloqué l'accès à l'admin
                    </label>
                </p>
                <p><input class="btn" type="submit" value="ok" name="ok" /></p>
            </form>
        </div>
    </body>
</html>