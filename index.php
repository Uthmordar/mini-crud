<?php
//-------- bootstrap 
require_once 'bootstrap.php';
// -------- fin bootstrap

$users=selec(array('table'=>'user', 'status'=>'1'));  // objet PDOStatement  ->fetch() une ligne de la table

if(isset($_GET['user_id']) && isset($_GET['action'])){
    $userId= (int) $_GET['user_id'];
    delete($userId);
    header('Location: index.php');
    exit();
}

?>
<!DOCTYPE html>
<html>
    <head>
        <title><?php echo getConfig('name'); ?></title>
        <meta charset="<?php echo getConfig('charset') ?>" >
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
        <script src="<?php echo getConfig('url') . 'assets/js/bootstrap-dropdown.js'; ?>"></script>
        <link rel='stylesheet'   href="<?php echo getConfig('url') . 'assets/css/bootstrap.css'; ?>" type='text/css' media='all' />

    </head>
    <body>
        <div class="container">
            <h1> <a href="<?php echo getConfig('url'); ?>" ><?php echo getConfig('name') ?></a></h1>
            <p><a class="btn dropdown-toggle" href="<?php echo getConfig('url') . 'create.php'; ?>" >Create user</a></p>
            <table class="table table-hover" >
                <thead>
                    <tr>
                        <th>user_id</th>
                        <th>name</th>
                        <th>Status</th>
                        <th>Avatar</th>
                        <th>Date de création/modification</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
<?php while ($user = $users->fetch()) : ?>
                        <tr>
                            <td><?php echo $user['user_id']; ?></td>
                            <td><?php echo $user['name']; ?></td>

                            <td><?php echo ($user['status'] == '1') ? 'ok' : 'supprimer'; ?></td>
                            <td><img src="<?php echo $user['avatar']; ?>" /></td>
                            <td><?php // echo $user['date_crea']; ?></td>
                            <td><div class="btn-group">
                                    <a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
                                        Action
                                        <span class="caret"></span>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li><a href="<?php echo getConfig('url') . 'update.php?user_id=' . $user['user_id']; ?>">update</a></li>
                                        <li><a href="<?php echo getConfig('url') . 'index.php?action=delete&user_id=' . $user['user_id']; ?>&action=delete">delete</a></li>
                                    </ul>
                                </div></td>
                        </tr>
<?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </body>
</html>