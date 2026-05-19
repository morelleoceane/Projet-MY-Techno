/**
 * app.js – ModeShopping TI2
 * Toutes les opérations JavaScript/AJAX du site
 * Utilise l'API Fetch (asynchrone)
 * =============================================
 */

/* ============================================================
   UTILITAIRES
   ============================================================ */

/** Affiche un toast de notification */
function showToast(message, type = 'success') {
    let container = document.getElementById('toast-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'toast-container';
        document.body.appendChild(container);
    }
    const toast = document.createElement('div');
    toast.className = `toast-msg ${type}`;
    toast.textContent = message;
    container.appendChild(toast);
    setTimeout(() => toast.remove(), 3200);
}

/** Active/désactive la barre de chargement AJAX */
function setLoader(visible) {
    let loader = document.getElementById('ajax-loader');
    if (!loader) {
        loader = document.createElement('div');
        loader.id = 'ajax-loader';
        document.body.prepend(loader);
    }
    loader.style.display = visible ? 'block' : 'none';
}

/* ============================================================
   1. RECHERCHE EN TEMPS RÉEL (AJAX Fetch)
      Filtre les articles du catalogue sans rechargement de page
   ============================================================ */
function initRechercheAjax() {
    const input = document.getElementById('rechercheAjax');
    const grille = document.getElementById('grille-articles');
    if (!input || !grille) return;

    let timeout;
    input.addEventListener('input', function () {
        clearTimeout(timeout);
        const q = this.value.trim();
        timeout = setTimeout(() => {
            setLoader(true);
            fetch('/admin/src/php/ajax/recherche_articles.php?q=' + encodeURIComponent(q))
                .then(r => r.json())
                .then(data => {
                    grille.innerHTML = '';
                    if (data.length === 0) {
                        grille.innerHTML = '<div class="col-12"><div class="alert alert-info">Aucun article trouvé.</div></div>';
                    } else {
                        data.forEach(a => {
                            grille.innerHTML += `
                            <div class="col-6 col-md-4">
                                <div class="card h-100 shadow-sm article-card">
                                    <img src="/admin/assets/images/${a.photo_principale}"
                                         class="card-img-top" style="height:200px;object-fit:cover;"
                                         onerror="this.src='/admin/assets/images/no_image.jpg'"
                                         alt="${a.libelle}">
                                    <div class="card-body d-flex flex-column">
                                        <h6 class="card-title">${a.libelle}</h6>
                                        <p class="text-muted small">${a.marque || '—'} | T.${a.taille || '—'}</p>
                                        <p class="fw-bold text-success fs-5 mt-auto">${parseFloat(a.prix_unitaire).toFixed(2)} €</p>
                                        <a href="/index_.php?page=article_detail&id=${a.id_article}"
                                           class="btn btn-dark btn-sm mt-2">Voir le produit</a>
                                    </div>
                                </div>
                            </div>`;
                        });
                    }
                    // Mise à jour du compteur
                    const counter = document.querySelector('.article-count');
                    if (counter) counter.textContent = `(${data.length} article(s))`;
                })
                .catch(() => showToast('Erreur lors de la recherche', 'error'))
                .finally(() => setLoader(false));
        }, 400);
    });
}

/* ============================================================
   2. TABLEAU ÉDITABLE (Admin) – AJAX Fetch
      Permet de modifier le stock d'un article en double-cliquant
      sur la cellule dans le tableau admin (exemple CRUD AJAX)
   ============================================================ */
function initTableauEditable() {
    const cellules = document.querySelectorAll('.editable-cell');
    cellules.forEach(cell => {
        cell.setAttribute('contenteditable', 'true');
        cell.setAttribute('title', 'Double-cliquez pour modifier');

        cell.addEventListener('blur', function () {
            const idArticle = this.dataset.id;
            const champ     = this.dataset.champ;
            const valeur    = this.textContent.trim();

            if (!idArticle || !champ || valeur === this.dataset.original) return;

            setLoader(true);
            fetch('/admin/src/php/ajax/update_article.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `id_article=${idArticle}&champ=${champ}&valeur=${encodeURIComponent(valeur)}`
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    this.dataset.original = valeur;
                    showToast('✅ Mis à jour avec succès');
                } else {
                    this.textContent = this.dataset.original;
                    showToast('❌ Erreur : ' + (data.message || 'Mise à jour échouée'), 'error');
                }
            })
            .catch(() => {
                this.textContent = this.dataset.original;
                showToast('Erreur réseau', 'error');
            })
            .finally(() => setLoader(false));
        });

        cell.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') { e.preventDefault(); this.blur(); }
            if (e.key === 'Escape') {
                this.textContent = this.dataset.original;
                this.blur();
            }
        });

        // Sauvegarder valeur originale
        cell.dataset.original = cell.textContent.trim();
    });
}

/* ============================================================
   3. VÉRIFICATION AJAX DU CODE PROMO (Fetch)
      Vérifie le code promo sans recharger la page panier
   ============================================================ */
function initVerifPromo() {
    const btn  = document.getElementById('btn-verif-promo');
    const input = document.getElementById('input-code-promo');
    const result = document.getElementById('promo-result');
    if (!btn || !input || !result) return;

    btn.addEventListener('click', function () {
        const code = input.value.trim();
        if (!code) { showToast('Saisissez un code promo', 'error'); return; }

        setLoader(true);
        fetch('/admin/src/php/ajax/verif_promo.php?code=' + encodeURIComponent(code))
            .then(r => r.json())
            .then(data => {
                if (data.valide) {
                    result.innerHTML = `<span class="badge bg-success">✅ Code valide – remise de ${data.taux}%</span>`;
                    showToast(`Code promo appliqué : -${data.taux}%`);
                } else {
                    result.innerHTML = `<span class="badge bg-danger">❌ Code invalide</span>`;
                    showToast('Code promo invalide', 'error');
                }
            })
            .catch(() => showToast('Erreur de vérification', 'error'))
            .finally(() => setLoader(false));
    });
}

/* ============================================================
   4. CONFIRMATION DE SUPPRESSION STYLISÉE
      Remplace les confirm() natifs par une modale Bootstrap
   ============================================================ */
function initDeleteConfirm() {
    // Crée la modale une seule fois
    if (!document.getElementById('modalConfirmDelete')) {
        document.body.insertAdjacentHTML('beforeend', `
        <div class="modal fade" id="modalConfirmDelete" tabindex="-1">
            <div class="modal-dialog modal-sm modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">⚠️ Confirmer</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body text-center" id="modal-confirm-msg">
                        Êtes-vous sûr(e) ?
                    </div>
                    <div class="modal-footer justify-content-center">
                        <button id="btn-confirm-ok" class="btn btn-danger">Oui, supprimer</button>
                        <button class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    </div>
                </div>
            </div>
        </div>`);
    }

    const modal      = new bootstrap.Modal(document.getElementById('modalConfirmDelete'));
    const btnOk      = document.getElementById('btn-confirm-ok');
    const msgEl      = document.getElementById('modal-confirm-msg');
    let targetHref   = null;

    document.querySelectorAll('[data-confirm]').forEach(link => {
        link.addEventListener('click', function (e) {
            e.preventDefault();
            targetHref = this.href;
            msgEl.textContent = this.dataset.confirm || 'Confirmer la suppression ?';
            modal.show();
        });
    });

    btnOk.addEventListener('click', () => {
        if (targetHref) { window.location.href = targetHref; }
        modal.hide();
    });
}

/* ============================================================
   5. COMPTEUR PANIER EN TEMPS RÉEL (Fetch)
      Met à jour le badge panier dans la navbar sans rechargement
   ============================================================ */
function updateCartBadge() {
    fetch('/admin/src/php/ajax/panier_count.php')
        .then(r => r.json())
        .then(data => {
            const badges = document.querySelectorAll('.cart-badge');
            badges.forEach(b => {
                b.textContent = data.count;
                b.style.display = data.count > 0 ? 'inline-block' : 'none';
            });
        })
        .catch(() => {}); // Silencieux si non connecté
}

/* ============================================================
   INITIALISATION
   ============================================================ */
document.addEventListener('DOMContentLoaded', function () {
    initRechercheAjax();
    initTableauEditable();
    initVerifPromo();
    initDeleteConfirm();

    // Mise à jour badge panier toutes les 30s
    updateCartBadge();
    setInterval(updateCartBadge, 30000);

    // Masquer les alertes après 4 secondes
    document.querySelectorAll('.alert:not(.alert-permanent)').forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity .5s';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        }, 4000);
    });
});
