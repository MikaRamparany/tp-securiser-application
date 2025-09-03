<?php
include("connect.php");

$error_message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    $stmt = $db->prepare("SELECT id, login, name, password FROM users WHERE login = :login");
    $stmt->bindParam(':login', $login);
    $stmt->execute();
    $user = $stmt->fetch();

    if ($user) {
        if (password_verify($password, $user['password'])) {
            echo "Bienvenue " . htmlspecialchars($user['name']);
        } else {
            $error_message = "Identifiant ou mot de passe incorrect.";
        }
    } else {
        $error_message = "Identifiant ou mot de passe incorrect.";
    }
}
?>

<div class="py-4">
    <form method="post" action="#">
        <input type="hidden" name="page" value="<?php echo $strPage ?? 'sql_injection'; ?>">
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
