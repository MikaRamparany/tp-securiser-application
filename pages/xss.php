<?php
$strPreco  = "
    <ul>
        <li>Nettoyage des données saisies avec <code>htmlspecialchars()</code></li>
        <li>Utilisation de requêtes préparées pour éviter les injections SQL</li>
    </ul>";
$strDesc   = "La faille XSS est une vulnérabilité de sécurité qui se produit lorsqu'un site web affiche des données utilisateur non échappées, permettant l'exécution de scripts malveillants.";
$strTip    = "Exemple d'attaque : <code>&lt;script&gt;alert(\"coucou\");&lt;/script&gt;</code>";

include("connect.php");

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name'], $_POST['message'])) {
    $name = htmlspecialchars($_POST['name'], ENT_QUOTES, 'UTF-8');
    $message = htmlspecialchars($_POST['message'], ENT_QUOTES, 'UTF-8');

    // Requête préparée pour éviter les injections SQL
    $stmt = $db->prepare("INSERT INTO comments (name, comment, publish) VALUES (:name, :comment, 1)");
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':comment', $message);
    $stmt->execute();
}

// Récupération des commentaires
$stmt = $db->prepare("SELECT * FROM comments WHERE publish = 1");
$stmt->execute();
$arrComments = $stmt->fetchAll();
?>

<div class="col-md-8">
    <h2>Cross Site Scripting (XSS)</h2>
    <?php include("_partial/desc.php"); ?>

    <div class="py-4">
        <form name="guestform" method="post" action="">
            <input type="hidden" name="page" value="xss">
            <p>
                <label>Nom *</label>
                <input required class="form-control" name="name" type="text" size="30" maxlength="50">
            </p>
            <p>
                <label>Commentaire *</label>
                <textarea required class="form-control" name="message" cols="50" rows="3" maxlength="500"></textarea>
            </p>
            <p>
                <input class="form-control btn btn-primary" name="btnSign" type="submit" value="Envoyer">
            </p>
        </form>
    </div>

    <div id="comments">
        <?php foreach ($arrComments as $arrDet) { ?>
            <div class="card mb-4">
                <div class="card-body">
                    <p><?php echo htmlspecialchars($arrDet['comment'], ENT_QUOTES, 'UTF-8'); ?></p>
                    <div class="d-flex justify-content-between">
                        <div class="d-flex flex-row align-items-center">
                            <p class="small mb-0 ms-2"><?php echo htmlspecialchars($arrDet['name'], ENT_QUOTES, 'UTF-8'); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>

    <?php include("_partial/soluce.php"); ?>
</div>
