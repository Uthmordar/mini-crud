<?php
//-------- bootstrap 
require_once 'bootstrap.php';
// -------- fin bootstrap

$tokenErrorUpdate = 0;

if(!empty($_POST)){
	if(update2()){
		header('Location:index.php');
		exit();
	}else{
		$tokenErrorUpdate = 1;
	}
}else{
	if(!isset($_GET['user'])){
		echo '<p>Not allowed.</p>';
		die();
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
            <h1><a href="<?php echo getConfig('url'); ?>" ><?php echo getConfig('name') ?></a></h1>
			
			<?php $requete=selecTarget('user', 'user_id', $_GET['user']); while($user= $requete->fetch()) : ?>
			
			<form method='post' enctype='multipart/form-data' action="<?php echo htmlentities($_SERVER['PHP_SELF']) ?>">
				<?php //if($tokenErrorUpdate==1){	echo '<p>Données fournies incorrectes.</p>'; }; ?>
				<!-- pour récupérer le user_id on le place dans un champ caché plutôt que d'utiliser un GET pour sécuriser -->
				<input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>" />
				<label for='nom'>Nom</label>
				<input type='text' name='nom' value='<?php echo $user['name']; ?>' required/>
			
				<label for='avatar'>Avatar</label>
				<p><?php if($user['avatar']!='no'){ echo '<img src='.$user['avatar'].' alt=""/>'; }else{ echo $user['avatar'];} ?></p>
				<?php $_POST['avatar']=$user['avatar']; ?>
				<input type='file' name='avatar' size='20' value='change'/>
				<input type="hidden" name="MAX_FILE_SIZE" value="1048576" />
				<br/>
				
				<?php if($user['status']==1) : ?>
				<input type='radio' name='radio1' value='1' id='radio1' checked><label for='radio1'>accès à l'admin</label>
				<input type='radio' name='radio1' value='0' id='radio2'><label for='radio2'>bloquer l'accès à l'admin</label>
				<?php else : ?>
				<input type='radio' name='radio1' value='1' id='radio1'><label for='radio1'>accès à l'admin</label>
				<input type='radio' name='radio1' value='0' id='radio2' checked><label for='radio2'>bloquer l'accès à l'admin</label>
				<?php endif; ?>
				
				<input type='submit' name='formVal' id='formVal' value='ok'/>
			
			</form>
			<?php endwhile; ?>
        </div>
    </body>
</html>