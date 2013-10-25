<?php
//-------- bootstrap 
require_once 'bootstrap.php';
// -------- fin bootstrap

//   XSS attaque    /%22%3E%3Cscript%3Ealert%28%27xss%27%29%3C/script%3E%3Cfoo%22

if(isset($_GET['user']) && $_GET['delete']==true){
	if(delete($_GET['user'])){
		header('Location:index.php');
		exit();
	}
}
?>
<!DOCTYPE html>
<html>
    <head>
        <title><?php echo getConfig('name'); ?></title>
        <meta charset="<?php echo getConfig('charset') ?>" >
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
        <script src="<?php echo getConfig('url') . 'assets/js/bootstrap-dropdown.js'; ?>"></script>
        <link rel='stylesheet' href="<?php echo getConfig('url') . 'assets/css/bootstrap.css'; ?>" type='text/css' media='all' />

    </head>
    <body>
        <div class="container">
            <h1> <a href="<?php echo getConfig('url'); ?>" ><?php echo getConfig('name') ?></a></h1>
            <p><a class="btn dropdown-toggle" href="<?php echo getConfig('url') . 'create.php'; ?>">Create user</a></p>
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>user_id</th>
                        <th>name</th>
                        <th>Status</th>
                        <th>Avatar</th>
						<!-- ALTER TABLE `table1` ADD `lastUpdated` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ; -->
						<th>Timestamp</th>
						<th>dateTime</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
					<?php //$requete=selec(); while($user= $requete->fetch()) : ?>
					<?php $requete=selecNew(array('table'=>'user'/*, 'where'=> array('status'=>'1')*/, 'order'=>array('type'=>'ASC', 'column'=>array('name', 'dateTime')))); if($requete) : while($user= $requete->fetch()) : ?>
                    <tr>
                        <td><?php echo $user['user_id']; ?></td>
                        <td><?php echo $user['name']; ?></td>

                        <td><?php echo $user['status']; ?></td>
                        <td><?php if($user['avatar']!='no'){ echo '<img src='.$user['avatar'].' alt=""/>'; }else{ echo $user['avatar'];} ?></td>
						<td><?php echo $user['date']; ?></td>
						<td><?php echo $user['dateTime']; ?></td>
                        <td><div class="btn-group">
                                <a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
                                    Action
                                    <span class="caret"></span>
                                </a>
                                <ul class="dropdown-menu">
									<li><a href="<?php echo getConfig('url').'index.php?user='.$user['user_id']."&delete=true"; ?>">Delete</a></li>
									<li><a href="<?php echo getConfig('url').'update.php?user='.$user['user_id']; ?>">Update</a></li>
                                </ul>
                            </div></td>
                    </tr>
					<?php endwhile; else :?>
						<p>Erreur dans les requêtes.</p>
					<?php endif;?>
                </tbody>
            </table>
        </div>

    </body>
</html>