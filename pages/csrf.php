<?php
$strPreco  = "
    <ul>
        <li>Mise en place d'un jeton CSRF unique par session</li>
        <li>Demander le mot de passe actuel pour vérification</li>
        <li>Utilisation de requêtes préparées</li>
        <li>Utilisation de la méthode POST</li>
    </ul>";
$strDesc   = "La faille CSRF est une attaque dans laquelle un attaquant exploite la confiance entre un utilisateur et un site web pour exécuter des actions non désirées.";
$strTip    = "Un jeton CSRF unique est généré pour chaque formulaire et vérifié à la soumission.";


include("connect.php");

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Vérifie le jeton CSRF
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
        echo '<div class="alert alert-danger">Token CSRF invalide.</div>';
    }
    // Vérifie que tous les champs sont bien remplis
    else if (empty($_POST['current_password']) || empty($_POST['password_new']) || empty($_POST['password_conf'])) {
        echo '<div class="alert alert-danger">Tous les champs sont obligatoires.</div>';
    }
    // Vérifie que les nouveaux mdp correspondent lors du changement
    else if ($_POST['password_new'] !== $_POST['password_conf']) {
        echo '<div class="alert alert-danger">Les nouveaux mots de passe ne correspondent pas.</div>';
    }
 
    else {
        // 1. Récupérer le mdp  actuel de l'utilisateur (ex: id=1)
        $stmt = $db->prepare("SELECT password FROM users WHERE id = 1");
        $stmt->execute();
        $user = $stmt->fetch();

        // 2. Vérifie le mdp actuel
        if ($user && password_verify($_POST['current_password'], $user['password'])) {
            // 3. Hacher le nouveau mdp
            $newPasswordHash = password_hash($_POST['password_new'], PASSWORD_DEFAULT);

            // 4. Mettre à jour le mdp
            $updateStmt = $db->prepare("UPDATE users SET password = :password WHERE id = 1");
            $updateStmt->bindParam(':password', $newPasswordHash);
            $updateStmt->execute();

            echo '<div class="alert alert-success">Mot de passe changé avec succès.</div>';
        } else {
            echo '<div class="alert alert-danger">Mot de passe actuel incorrect.</div>';
        }
    }
}

// Génére un jeton CSRF unique
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
?>

<div class="col-md-8">
    <h2>CSRF</h2>
    <?php include("_partial/desc.php"); ?>

    <form action="#" method="POST">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

        <p>
            <label>Mot de passe actuel :</label>
            <input class="form-control" type="password" name="current_password" required>
        </p>
        <p>
            <label>Nouveau mot de passe :</label>
            <input class="form-control" type="password" name="password_new" required>
        </p>
        <p>
            <label>Confirmer le nouveau mot de passe :</label>
            <input class="form-control" type="password" name="password_conf" required>
        </p>
        <p>
            <input class="form-control btn btn-primary" type="submit" value="Changer">
        </p>
    </form>

    <?php include("_partial/soluce.php"); ?>
</div>
