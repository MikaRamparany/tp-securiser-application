<?php
	$strPreco 	= "	<ul>
						<li>Remplacer les caractères spéciaux</li>
						<li>Traiter la saisie utilisateur en fonction de ce qui est demandé :<br>
						IP donc 4 <u>nombres</u> entre 0 et 255, séparés par des .</li>
					</ul>";
	$strDesc	= "L'injection de commande est une attaque informatique qui consiste à insérer des commandes malveillantes dans des entrées utilisateur ou des données qui seront ensuite exécutées par un système informatique. Cette attaque est souvent utilisée pour prendre le contrôle d'un système informatique à distance.";
	$strTip		= "Utiliser le séparateur & ou && ou ; selon le système d'exploitation";
?>

<div class="col-md-8">
	<h2>Injection de commande </h2>
	<?php
		include("_partial/desc.php");
	?>	
	<div class="py-4">
		<form name="ping" action="#" method="post">
			<p>
				Saisir une adresse IP:
				<p><input type="text" class="form-control"  name="ip" size="30" value="<?php echo $_POST['ip']??""; ?>" ></p>
				<p><input type="submit" class="form-control btn btn-primary" name="Submit" value="Envoyer"> </p>
				
			</p>
		</form>
	</div>
	
<?php
	    if( isset( $_POST[ 'ip' ] ) &&  $_POST[ 'ip' ] != "") {
        // Get input
        $target = $_REQUEST[ 'ip' ];

        // Validation IPv4 
        if (filter_var($target, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            
            $escapedTarget = escapeshellarg($target);

         
            if( stristr( php_uname( 's' ), 'Windows NT' ) ) {
                
                $cmd = shell_exec( 'ping ' . $escapedTarget );
            }
            else {
               
                $cmd = shell_exec( 'ping -c 4 ' . $escapedTarget );
            }

         
            echo "<pre>".$cmd."</pre>";
        } else {
            echo '<div class="alert alert-danger">Adresse IP invalide.</div>';
        }
    }
	
	include ("_partial/soluce.php"); 	
?>