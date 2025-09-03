<?php

include("connect.php");

// Fonction pour vérifier la force du mot de passe
function isStrongPassword($password) {
    // Au moins 8 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial
    return preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $password);
}

$error_message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // 1. Récupérer l'utilisateur en base de données (requête préparée)
    $stmt = $db->prepare("SELECT id, login, name, password, attempts, locked_until FROM users WHERE login = :login");
    $stmt->bindParam(':login', $login);
    $stmt->execute();
    $user = $stmt->fetch();

    if ($user) {
        // 2. Vérifie si compte  bloqué
        if ($user['locked_until'] && new DateTime() < new DateTime($user['locked_until'])) {
            $error_message = "Compte bloqué. Réessayez après " . $user['locked_until'];
        }
        // 3. Vérifie mdp (avec password_verify)
        else if (password_verify($password, $user['password'])) {
            // Réinitialise les tentatives de connexion
            $resetStmt = $db->prepare("UPDATE users SET attempts = 0, locked_until = NULL WHERE id = :id");
            $resetStmt->bindParam(':id', $user['id']);
            $resetStmt->execute();

            // Connexion réussie
            $_SESSION['user'] = $user;
            echo "Bienvenue " . htmlspecialchars($user['name']);
            if (isset($strPage) && $strPage == "csrf") {
                
            }
        }
        // 4. MDP incorrect
        else {
            // Incrémente le nombre de tentatives
            $newAttempts = $user['attempts'] + 1;

            // Bloquer le compte après 5 tentatives
            if ($newAttempts >= 5) {
                $lockedUntil = (new DateTime())->modify('+15 minutes')->format('Y-m-d H:i:s');
                $updateStmt = $db->prepare("UPDATE users SET attempts = :attempts, locked_until = :locked_until WHERE id = :id");
                $updateStmt->bindParam(':attempts', $newAttempts);
                $updateStmt->bindParam(':locked_until', $lockedUntil);
                $updateStmt->bindParam(':id', $user['id']);
                $updateStmt->execute();
                $error_message = "Trop de tentatives de connexion. Votre compte est bloqué pour 15 minutes.";
            }
            // Mettre à jour le nombre de tentatives
            else {
                $updateStmt = $db->prepare("UPDATE users SET attempts = :attempts WHERE id = :id");
                $updateStmt->bindParam(':attempts', $newAttempts);
                $updateStmt->bindParam(':id', $user['id']);
                $updateStmt->execute();
                $error_message = "Identifiant ou mot de passe incorrect. Tentative $newAttempts/5.";
            }
        }
    }
    else {
        $error_message = "Identifiant ou mot de passe incorrect.";
    }
}
?>

<div class="py-4">
    <form method="post" action="#">
        <input type="hidden" name="page" value="<?php echo $strPage ?? 'brut_force'; ?>">
        <?php if ($error_message): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>
        <p>
            <label>Username:</label>
            <input class="form-control" type="text" name="username" required>
        </p>
        <p>
            <label>Password:</label>
            <input class="form-control" type="password" name="password" required>
        </p>
        <p>
            <input class="form-control btn btn-primary" type="submit" value="Se connecter" name="login">
        </p>
    </form>
</div>
