<?php
/**
 * connexion.php - Page de connexion client
 */
if (isset($_SESSION['client_id'])) {
    header('Location: ./index_.php?page=mon_compte');
    exit();
}

$erreur = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $mdp   = $_POST['mot_de_passe'] ?? '';

    if ($email && $mdp) {
        $clientDAO = new ClientDAO();
        $client = $clientDAO->verifierConnexion($email, $mdp);
        if ($client) {
            if ($client->isBanni()) {
                $erreur = "Votre compte a été banni. Contactez l'administrateur.";
            } else {
                $_SESSION['client_id']    = $client->getIdClient();
                $_SESSION['client_nom']   = $client->getNomClient();
                $_SESSION['client_prenom']= $client->getPrenomClient();
                $redirect = $_GET['redirect'] ?? 'mon_compte';
                header('Location: ./index_.php?page=' . $redirect);
                exit();
            }
        } else {
            $erreur = "Email ou mot de passe incorrect.";
        }
    } else {
        $erreur = "Veuillez remplir tous les champs.";
    }
}
?>

<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card shadow">
            <div class="card-header bg-dark text-white text-center fw-bold fs-5">
                <i class="bi bi-person-lock"></i> Connexion Client
            </div>
            <div class="card-body p-4">
                <?php if ($erreur): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($erreur) ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-3">
                        <label for="email" class="form-label fw-semibold">Email</label>
                        <input type="email" id="email" name="email" class="form-control"
                               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="mot_de_passe" class="form-label fw-semibold">Mot de passe</label>
                        <input type="password" id="mot_de_passe" name="mot_de_passe" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-dark w-100">Se connecter</button>
                </form>

                <hr>
                <p class="text-center mb-0">
                    Pas encore de compte ?
                    <a href="./index_.php?page=inscription">S'inscrire</a>
                </p>
            </div>
        </div>
    </div>
</div>
