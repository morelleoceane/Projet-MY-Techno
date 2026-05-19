<?php
/**
 * connexion_admin.php - Connexion administrateur
 */
if (isset($_SESSION['admin_id'])) {
    header('Location: /admin/index_.php?page=accueil');
    exit();
}
$erreur = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom_admin'] ?? '');
    $mdp = $_POST['mot_de_passe'] ?? '';
    if ($nom && $mdp) {
        $adminDAO = new AdminDAO();
        $admin = $adminDAO->verifierConnexion($nom, $mdp);
        if ($admin) {
            $_SESSION['admin_id']  = $admin->getIdAdmin();
            $_SESSION['admin_nom'] = $admin->getNomAdmin();
            header('Location: /admin/index_.php?page=accueil');
            exit();
        } else {
            $erreur = "Identifiants incorrects.";
        }
    } else {
        $erreur = "Veuillez remplir tous les champs.";
    }
}
?>
<div class="row justify-content-center">
    <div class="col-md-4">
        <div class="card shadow border-danger">
            <div class="card-header bg-danger text-white text-center fw-bold">
                <i class="bi bi-shield-lock"></i> Accès Administration
            </div>
            <div class="card-body p-4">
                <?php if ($erreur): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($erreur) ?></div>
                <?php endif; ?>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nom d'administrateur</label>
                        <input type="text" name="nom_admin" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Mot de passe</label>
                        <input type="password" name="mot_de_passe" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-danger w-100">Se connecter</button>
                </form>
            </div>
        </div>
    </div>
</div>
