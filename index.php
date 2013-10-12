<?php
//-------- bootstrap 
require_once 'bootstrap.php';
// -------- fin bootstrap
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
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>

                    <tr>
                        <td></td>
                        <td></td>

                        <td></td>
                        <td></td>
                        <td><div class="btn-group">
                                <a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
                                    Action
                                    <span class="caret"></span>
                                </a>
                                <ul class="dropdown-menu">


                                </ul>
                            </div></td>
                    </tr>

                </tbody>
            </table>
        </div>

    </body>
</html>