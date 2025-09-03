<?php
$strPreco  = "
    <ul>
        <li>Autoriser uniquement les types de fichier nécessaires (ex: images jpg, png)</li>
        <li>Vérifier les informations du fichier (taille, type MIME, extension)</li>
        <li>Renommer le fichier pour éviter les conflits et les attaques</li>
    </ul>";
$strDesc   = "Les attaquants peuvent utiliser des fichiers malveillants pour contourner les contrôles de sécurité. Il est crucial de vérifier le type, la taille et de renommer les fichiers téléchargés.";
$strTip    = "Essayez de télécharger un fichier avec une extension modifiée ou un script PHP.";

// Configuration des types de fichiers autorisés et de la taille maximale
$allowedTypes = ['image/jpeg', 'image/png'];
$maxFileSize = 2 * 1024 * 1024; // 2 Mo
$uploadDir = './uploads/';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['myFile'])) {
	$file = $_FILES['myFile'];

	// Vérif des erreurs de téléchargement
	if ($file['error'] !== UPLOAD_ERR_OK) {
		echo "<div class='alert alert-danger'>Erreur lors du téléchargement du fichier.</div>";
	}
	// Vérif du type MIME
	else if (!in_array($file['type'], $allowedTypes)) {
		echo "<div class='alert alert-danger'>Type de fichier non autorisé. Seuls les fichiers JPEG et PNG sont acceptés.</div>";
	}
	// Vérif de la taille du fichier
	else if ($file['size'] > $maxFileSize) {
		echo "<div class='alert alert-danger'>Le fichier est trop volumineux. La taille maximale autorisée est de 2 Mo.</div>";
	}
	// Vérif de l'extension
	else {
		$fileInfo = pathinfo($file['name']);
		$extension = strtolower($fileInfo['extension']);

		// Vérif supplémentaire de l'extension
		if (!in_array($extension, ['jpg', 'jpeg', 'png'])) {
			echo "<div class='alert alert-danger'>Extension de fichier non autorisée.</div>";
		}
		// si tout est valide : renomme et déplace le fichier
		else {
			// Générer un nom de fichier unique
			$newFileName = uniqid('img_', true) . '.' . $extension;
			$destination = $uploadDir . $newFileName;

			// Déplacer le fichier
			if (move_uploaded_file($file['tmp_name'], $destination)) {
				echo "<div class='alert alert-success'>Le fichier a bien été téléchargé : <strong>$newFileName</strong></div>";
			} else {
				echo "<div class='alert alert-danger'>Erreur lors de l'enregistrement du fichier.</div>";
			}
		}
	}
}
?>
<div class="col-md-8 position-relative">
    <h2>Traitement des fichiers</h2>
    <?php include("_partial/desc.php"); ?>

    <form enctype="multipart/form-data" action="index.php?page=file_upload" method="POST">
        <p>
            <input class="form-control" type="file" name="myFile" accept=".jpg,.jpeg,.png">
        </p>
        <p>
            <input class="form-control btn btn-primary" type="submit" name="upload" value="Envoyer">
        </p>
    </form>

    <?php
    // Afficher la liste des fichiers téléchargés
    $scandir = scandir($uploadDir);
    echo "<ul>";
    foreach ($scandir as $fichier) {
        if ($fichier != "." && $fichier != "..") {
            // échappe le nom du fichier pour éviter les attaques XSS
            $safeFileName = htmlspecialchars($fichier);
            echo "<li><a href='uploads/$safeFileName'>$safeFileName</a></li>";
        }
    }
    echo "</ul>";

    include("_partial/soluce.php");
    ?>
</div>
