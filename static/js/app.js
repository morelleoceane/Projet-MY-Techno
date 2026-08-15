/**
 * app.js – ModeShopping Flask TI2
 * JavaScript côté client pour l'interface Flask
 */

document.addEventListener('DOMContentLoaded', function () {

    /* ============================================================
       1. MASQUAGE AUTOMATIQUE DES ALERTES (après 4s)
       ============================================================ */
    document.querySelectorAll('.alert:not(.alert-dark)').forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity .5s ease';
            alert.style.opacity    = '0';
            setTimeout(() => alert.remove(), 520);
        }, 4000);
    });

    /* ============================================================
       2. CHARGEMENT PARESSEUX DES IMAGES (Intersection Observer)
          Améliore les performances de la grille articles
       ============================================================ */
    const images = document.querySelectorAll('img[data-src]');
    if ('IntersectionObserver' in window && images.length > 0) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.removeAttribute('data-src');
                    observer.unobserve(img);
                }
            });
        }, { rootMargin: '100px' });
        images.forEach(img => observer.observe(img));
    }

    /* ============================================================
       3. BARRE DE PROGRESSION : scroll de lecture
       ============================================================ */
    const progressBar = document.createElement('div');
    progressBar.id = 'scroll-progress';
    progressBar.style.cssText = `
        position: fixed; top: 0; left: 0; height: 3px; width: 0%;
        background: linear-gradient(90deg, #f9c74f, #e63946);
        z-index: 9999; transition: width .1s linear;
        pointer-events: none;
    `;
    document.body.prepend(progressBar);

    window.addEventListener('scroll', () => {
        const scrollTop  = document.documentElement.scrollTop;
        const docHeight  = document.documentElement.scrollHeight - window.innerHeight;
        const pct        = docHeight > 0 ? (scrollTop / docHeight) * 100 : 0;
        progressBar.style.width = pct + '%';
    });

    /* ============================================================
       4. BOUTON "RETOUR EN HAUT"
       ============================================================ */
    const backToTop = document.createElement('button');
    backToTop.id        = 'back-to-top';
    backToTop.innerHTML = '<i class="bi bi-arrow-up"></i>';
    backToTop.title     = 'Revenir en haut';
    backToTop.style.cssText = `
        position: fixed; bottom: 24px; right: 24px;
        background: #1a1a2e; color: #f9c74f;
        border: 2px solid #f9c74f; border-radius: 50%;
        width: 44px; height: 44px; font-size: 1.1rem;
        cursor: pointer; display: none; z-index: 999;
        box-shadow: 0 4px 12px rgba(0,0,0,.3);
        transition: opacity .3s, transform .3s;
    `;
    document.body.appendChild(backToTop);

    window.addEventListener('scroll', () => {
        backToTop.style.display = window.scrollY > 400 ? 'flex' : 'none';
        backToTop.style.alignItems    = 'center';
        backToTop.style.justifyContent = 'center';
    });

    backToTop.addEventListener('click', () => {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });

    /* ============================================================
       5. FILTRE CÔTÉ CLIENT : filtrage instantané des cartes
          (en complément du filtre serveur Flask)
       ============================================================ */
    const filtreRapide = document.getElementById('filtreRapide');
    if (filtreRapide) {
        filtreRapide.addEventListener('input', function () {
            const terme = this.value.toLowerCase().trim();
            document.querySelectorAll('.article-card').forEach(card => {
                const texte = card.textContent.toLowerCase();
                card.closest('[class^="col"]').style.display =
                    terme === '' || texte.includes(terme) ? '' : 'none';
            });
        });
    }

    /* ============================================================
       6. ANIMATION D'ENTRÉE DES CARTES (fade-in au scroll)
       ============================================================ */
    const cardsToAnimate = document.querySelectorAll(
        '.article-card, .categorie-card, .avantage-card'
    );
    if ('IntersectionObserver' in window) {
        const fadeObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity   = '1';
                    entry.target.style.transform = 'translateY(0)';
                    fadeObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.12 });

        cardsToAnimate.forEach(card => {
            card.style.opacity   = '0';
            card.style.transform = 'translateY(20px)';
            card.style.transition = 'opacity .45s ease, transform .45s ease';
            fadeObserver.observe(card);
        });
    }

    /* ============================================================
       7. TOOLTIP BOOTSTRAP : initialisation globale
       ============================================================ */
    const tooltipEls = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    tooltipEls.forEach(el => new bootstrap.Tooltip(el));

});
