<?php
/**
 * inscription.php - Inscription d'un nouveau client
 */

if (isset($_SESSION['client_id'])) {
    header('Location: ./index_.php?page=mon_compte');
    exit();
}


$erreur  = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom     = trim($_POST['nom'] ?? '');
    $prenom  = trim($_POST['prenom'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $mdp     = $_POST['mot_de_passe'] ?? '';
    $mdp2    = $_POST['mot_de_passe2'] ?? '';
    $cgv     = isset($_POST['cgv']);

    if (!$nom || !$prenom || !$email || !$mdp) {
        $erreur = "Veuillez remplir tous les champs obligatoires.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erreur = "Adresse email invalide.";
    } elseif ($mdp !== $mdp2) {
        $erreur = "Les mots de passe ne correspondent pas.";
    } elseif (strlen($mdp) < 6) {
        $erreur = "Le mot de passe doit contenir au moins 6 caractères.";
    } elseif (!$cgv) {
        $erreur = "Vous devez accepter les Conditions Générales de Vente pour commander.";
    } else {
        $clientDAO = new ClientDAO();
        // Vérifier si email déjà utilisé
        if ($clientDAO->findByEmail($email)) {
            $erreur = "Cette adresse email est déjà utilisée.";
        } else {
            $client = new Client(0, $nom, $prenom, $email, $mdp);
            $client->setCgvAcceptees(true);
            try {
                $clientDAO->insert($client);
                $success = "Compte créé avec succès ! Vous pouvez vous connecter.";
            } catch (Exception $e) {
                $erreur = "Erreur : " . $e->getMessage(); // ← affiche le vrai message
            }
        }
    }
}
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow">
            <div class="card-header bg-dark text-white text-center fw-bold fs-5">
                <i class="bi bi-person-plus"></i> Créer un compte
            </div>
            <div class="card-body p-4">
                <?php if ($erreur): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($erreur) ?></div>
                <?php elseif ($success): ?>
                    <div class="alert alert-success">
                        <?= htmlspecialchars($success) ?>
                        <a href="./index_.php?page=connexion">Se connecter</a>
                    </div>
                <?php endif; ?>

                <?php if (!$success): ?>
                <form method="POST">
                    <div class="row mb-3">
                        <div class="col">
                            <label class="form-label fw-semibold">Nom *</label>
                            <input type="text" name="nom" class="form-control"
                                   value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>" required>
                        </div>
                        <div class="col">
                            <label class="form-label fw-semibold">Prénom *</label>
                            <input type="text" name="prenom" class="form-control"
                                   value="<?= htmlspecialchars($_POST['prenom'] ?? '') ?>" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email *</label>
                        <input type="email" name="email" class="form-control"
                               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Mot de passe * (min. 6 caractères)</label>
                        <input type="password" name="mot_de_passe" class="form-control" required minlength="6">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Confirmer le mot de passe *</label>
                        <input type="password" name="mot_de_passe2" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="cgv" id="cgv" required>
                            <label class="form-check-label" for="cgv">
                                J'accepte les <a href="./index_.php?page=cgv" target="_blank">Conditions Générales de Vente</a> *
                            </label>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-dark w-100">Créer mon compte</button>
                </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
