<?php
//-------- bootstrap 
require_once 'bootstrap.php';
// -------- fin bootstrap

if (!empty($_POST)) {
    if (create()) {
        header('Location: index.php');
        exit(); // sinon le script continu jusqu'en bas de la page
    }
}
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
                <p>
                    <label for="name">Nom</label>
                    <input  required class="input-medium" value=""  name="name" id="name" type="text" >
                </p>
                <p>
                    <label for="avatar">Avatar</label>
                    <input  id="avatar" type="file" name="avatar" >
                </p>
                <p>
                    <label class="radio">
                        <input type="radio" name="status" id="status1" value="1" checked>
                        accès à l'admin
                    </label>
                </p>
                <p><label class="radio">
                        <input type="radio" name="status" id="status2" value="0">
                        Bloqué l'accès à l'admin
                    </label>
                </p>
                <p><input class="btn" type="submit" value="ok" name="ok" /></p>
            </form>
        </div>
    </body>
</html>