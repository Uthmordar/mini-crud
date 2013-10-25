<?php
//-------- bootstrap 
require_once 'bootstrap.php';
// -------- fin bootstrap

$tokenError=0;

if(!empty($_POST)){
	if(create()){
		header('Location:index.php');
		exit();
	}else{
		$tokenError=1;
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
			<h1> <a href="<?php echo getConfig('url'); ?>" ><?php echo getConfig('name') ?></a></h1>
			<form method='post' enctype='multipart/form-data' action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>">
			
				<label for='nom'>Nom</label>
				<input type='text' name='nom' <?php if(!empty($_POST) && secur($_POST['nom'])){ echo 'value='.$_POST['nom'];}else{ echo 'value="Le piratage c\'est mal."';} ?> required/>
				<p><?php if($tokenError==1) : ?>Erreur, nom invalide.<?php endif;?></p>
			
				<label for='avatar'>Avatar</label>
				<input type='file' size='20' name='avatar' value='Parcourir'/>
				<input type="hidden" name="MAX_FILE_SIZE" value="1048576" />
				<br/>
				<input type='radio' name='radio1' value='1' id='radio1' checked><label for='radio1'>accès à l'admin</label>
				<input type='radio' name='radio1' value='0' id='radio2'><label for='radio2'>bloquer l'accès à l'admin</label>
				
				<input type='submit' name='formVal' id='formVal' value='ok'/>
			
			</form>
        </div>
    </body>
</html>