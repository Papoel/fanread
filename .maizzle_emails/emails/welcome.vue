<template>
  <html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Bienvenue sur FanRead 📚</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Quicksand:wght@400;500;600;700&display=swap" rel="stylesheet">
        <style>
            * { margin: 0; padding: 0; box-sizing: border-box; }

            body {
                font-family: 'Quicksand', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
                background-color: #fff7f0;
                padding: 20px;
                line-height: 1.6;
            }

            .email-container {
                max-width: 650px;
                margin: 0 auto;
                background-color: #ffffff;
                border-radius: 20px;
                overflow: hidden;
                box-shadow: 0 20px 60px rgba(225, 29, 72, 0.1), 0 0 0 1px rgba(225, 29, 72, 0.05);
            }

            /* ── Header rose gradient ── */
            .header {
                background: linear-gradient(135deg, #e11d48 0%, #be123c 50%, #9f1239 100%);
                padding: 60px 40px;
                text-align: center;
                position: relative;
                overflow: hidden;
            }
            .header::before {
                content: '';
                position: absolute;
                top: -50%; right: -50%;
                width: 200%; height: 200%;
                background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, transparent 70%);
                opacity: 0.6;
            }
            .header::after {
                content: '';
                position: absolute;
                bottom: -40px; left: -40px;
                width: 150px; height: 150px;
                background: rgba(255, 140, 102, 0.25);
                border-radius: 50%;
            }

            .logo-container {
                width: 100px; height: 100px;
                background: rgba(255, 255, 255, 0.2);
                border: 3px solid rgba(255, 255, 255, 0.4);
                border-radius: 50%;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                margin-bottom: 24px;
                box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
                position: relative; z-index: 1;
            }
            .logo-container svg { width: 50px; height: 50px; fill: #ffffff; }

            .header h1 {
                color: #ffffff;
                font-family: 'Outfit', sans-serif;
                font-size: 40px; font-weight: 800;
                margin: 0 0 8px;
                letter-spacing: -0.5px;
                text-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
                position: relative; z-index: 1;
            }
            .header-tagline {
                color: rgba(255, 255, 255, 0.9);
                font-size: 16px;
                font-style: italic;
                margin: 0 0 8px;
                position: relative; z-index: 1;
            }
            .header p {
                color: rgba(255, 255, 255, 0.95);
                font-size: 18px; margin: 0;
                position: relative; z-index: 1;
                font-weight: 500;
            }

            /* ── Content ── */
            .content { padding: 50px 40px; }

            .greeting {
                font-family: 'Outfit', sans-serif;
                font-size: 24px; color: #5c3d47;
                font-weight: 700; margin-bottom: 16px;
            }

            .intro-text {
                color: #9d7b86; font-size: 17px;
                line-height: 1.8; margin-bottom: 36px;
            }
            .intro-text strong { color: #5c3d47; font-weight: 700; }

            /* ── Welcome card rose ── */
            .welcome-card {
                background: linear-gradient(135deg, #fff1f2 0%, #fecdd3 100%);
                border-left: 5px solid #e11d48;
                padding: 28px 32px; border-radius: 16px;
                margin-bottom: 36px;
                box-shadow: 0 4px 12px rgba(225, 29, 72, 0.12);
            }
            .welcome-card h2 {
                font-family: 'Outfit', sans-serif;
                color: #5c3d47; font-size: 20px;
                font-weight: 700; margin-bottom: 12px;
            }
            .welcome-card p {
                color: #9d7b86; font-size: 15px;
                margin: 0; line-height: 1.7;
            }

            /* ── Login button (outlined) ── */
            .login-section {
                text-align: center;
                margin-bottom: 36px;
            }
            .login-button {
                display: inline-block;
                background: #ffffff;
                color: #e11d48;
                text-decoration: none;
                padding: 14px 48px;
                border-radius: 14px;
                font-family: 'Outfit', sans-serif;
                font-size: 16px; font-weight: 700;
                letter-spacing: 0.3px;
                border: 2px solid #e11d48;
                box-shadow: 0 4px 12px rgba(225, 29, 72, 0.12);
            }

            /* ── Features grid ── */
            .features-section { margin-bottom: 40px; }

            .features-title {
                font-family: 'Outfit', sans-serif;
                font-size: 22px; color: #5c3d47;
                font-weight: 700; margin-bottom: 24px;
                text-align: center;
            }

            .features-grid {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 20px; margin-bottom: 36px;
            }

            .feature-item {
                background: linear-gradient(135deg, #fff7f0 0%, #fff1f2 100%);
                padding: 24px; border-radius: 14px;
                border: 1px solid #f0dce3;
                text-align: center;
                box-shadow: 0 2px 8px rgba(225, 29, 72, 0.06);
            }
            .feature-icon { font-size: 36px; margin-bottom: 12px; }
            .feature-title {
                font-family: 'Outfit', sans-serif;
                color: #5c3d47; font-size: 16px;
                font-weight: 700; margin-bottom: 8px;
            }
            .feature-text {
                color: #9d7b86; font-size: 14px;
                line-height: 1.6; margin: 0;
            }

            /* ── CTA button ── */
            .cta-section { text-align: center; margin: 48px 0; }

            .cta-button {
                display: inline-block;
                background: linear-gradient(135deg, #e11d48 0%, #be123c 100%);
                color: #ffffff; text-decoration: none;
                padding: 20px 60px; border-radius: 14px;
                font-family: 'Outfit', sans-serif;
                font-size: 18px; font-weight: 700;
                letter-spacing: 0.3px;
                box-shadow: 0 10px 30px rgba(225, 29, 72, 0.35), 0 1px 3px rgba(0, 0, 0, 0.1);
            }

            /* ── Stats banner gold ── */
            .stats-banner {
                background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
                border: 2px solid #ffb020;
                padding: 28px 32px; border-radius: 16px;
                text-align: center; margin-bottom: 36px;
                box-shadow: 0 4px 12px rgba(255, 176, 32, 0.2);
            }
            .stats-banner h3 {
                font-family: 'Outfit', sans-serif;
                color: #5c3d47; font-size: 22px;
                font-weight: 800; margin-bottom: 8px;
            }
            .stats-banner p { color: #9d7b86; font-size: 15px; margin: 8px 0 0; }

            /* ── Tip section lavender ── */
            .tip-section {
                background: linear-gradient(135deg, #f5f3ff 0%, #ede9fe 100%);
                padding: 28px 32px; border-radius: 16px;
                border: 1px solid #ddd6fe; margin-bottom: 36px;
            }
            .tip-section h3 {
                font-family: 'Outfit', sans-serif;
                color: #5c3d47; font-size: 18px;
                font-weight: 700; margin-bottom: 12px;
            }
            .tip-section p {
                color: #7c5db0; font-size: 15px;
                margin: 0 0 16px; line-height: 1.7;
            }
            .tip-links { display: flex; gap: 16px; flex-wrap: wrap; }
            .tip-link {
                color: #7c5db0; text-decoration: none;
                font-size: 14px; font-weight: 600;
                padding: 8px 16px; background: #ffffff;
                border-radius: 8px; border: 1px solid #c4b5fd;
            }

            /* ── Footer ── */
            .footer {
                background: linear-gradient(180deg, #fff7f0 0%, #fff1f2 100%);
                padding: 40px; text-align: center;
                border-top: 1px solid #f0dce3;
            }
            .footer-brand {
                font-family: 'Outfit', sans-serif;
                color: #5c3d47; font-size: 20px;
                font-weight: 800; margin-bottom: 8px;
            }
            .footer p { color: #9d7b86; font-size: 14px; margin-bottom: 8px; }

            .footer-links { margin-top: 24px; padding-top: 20px; border-top: 1px solid #f0dce3; }
            .footer-links a {
                color: #e11d48; text-decoration: none;
                margin: 0 12px; font-size: 13px; font-weight: 500;
            }
            .footer-note { margin-top: 20px; font-size: 12px; color: #9d7b86; }

            @media only screen and (max-width: 600px) {
                .email-container { border-radius: 0; }
                .header { padding: 40px 24px; }
                .header h1 { font-size: 30px; }
                .content { padding: 32px 24px; }
                .features-grid { grid-template-columns: 1fr; }
                .cta-button { padding: 16px 40px; font-size: 16px; }
                .tip-links { flex-direction: column; }
            }
        </style>
    </head>
    <body>
        <div class="email-container">

            <!-- Header -->
            <div class="header">
                <div class="logo-container">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                    </svg>
                </div>
                <h1>FanRead</h1>
                <p class="header-tagline">Prenez le temps de savourer vos mots.</p>
                <p>Votre cocon littéraire est prêt ✨</p>
            </div>

            <!-- Content -->
            <div class="content">

                <p v-pre class="greeting">👋 Bonjour {{ firstname|raw }} !</p>

                <p class="intro-text">
                    Nous sommes <strong>ravis de vous accueillir</strong> sur <strong>FanRead</strong> —
                    votre espace personnel pour collectionner vos lectures, noter vos pensées
                    et suivre l'évolution de votre bibliothèque.
                </p>

                <!-- Welcome card -->
                <div class="welcome-card">
                    <h2>🎉 Votre compte est activé !</h2>
                    <p>
                        Vous pouvez dès maintenant ajouter vos premiers livres, écrire vos avis
                        et commencer à construire votre bibliothèque personnelle.
                    </p>
                </div>

                <!-- Login button -->
                <a v-pre href="{{ url('app_login') }}" class="login-button">
                    🔑 Accéder à mon compte
                </a>

                <!-- Features grid -->
                <div class="features-section">
                    <h2 class="features-title">✨ Ce que vous pouvez faire</h2>
                    <div class="features-grid">
                        <div class="feature-item">
                            <div class="feature-icon">📖</div>
                            <div class="feature-title">Suivre vos lectures</div>
                            <p class="feature-text">Ajoutez vos livres et suivez votre progression en temps réel</p>
                        </div>
                        <div class="feature-item">
                            <div class="feature-icon">⭐</div>
                            <div class="feature-title">Écrire des avis</div>
                            <p class="feature-text">Partagez vos ressentis et notez chaque lecture</p>
                        </div>
                        <div class="feature-item">
                            <div class="feature-icon">📊</div>
                            <div class="feature-title">Mes statistiques</div>
                            <p class="feature-text">Visualisez vos habitudes avec des graphiques détaillés</p>
                        </div>
                        <div class="feature-item">
                            <div class="feature-icon">🔖</div>
                            <div class="feature-title">Ma bibliothèque</div>
                            <p class="feature-text">Gérez votre collection et retrouvez vos livres facilement</p>
                        </div>
                    </div>
                </div>

                <!-- CTA -->
                <div class="cta-section">
                  <a v-pre href="{{ url('app_book') }}" class="cta-button">
                      Découvrir mes livres 📚
                  </a>
                </div>

                <!-- Stats banner gold -->
                <div class="stats-banner">
                    <h3>🏆 Votre première lecture vous attend</h3>
                    <p>
                        Ajoutez un livre que vous lisez en ce moment et commencez
                        à construire votre historique de lecture personnel.
                    </p>
                </div>

                <!-- Tip section lavender -->
                <div class="tip-section">
                    <h3>💡 Comment bien démarrer ?</h3>
                    <p>
                        Commencez par ajouter un livre en cours ou terminé récemment.
                        Notre guide vous accompagne pas à pas pour profiter de toutes les fonctionnalités.
                    </p>
                    <div class="tip-links">
                        <a href="#" class="tip-link">📖 Guide de démarrage (à venir)</a>
                        <a href="#" class="tip-link">❓ FAQ (à venir)</a>
                        <a href="mailto:papoel.dev@gmail.com" class="tip-link">💬 Me contacter</a>
                    </div>
                </div>

            </div>

            <!-- Footer -->
            <div class="footer">
                <p class="footer-brand">❤️ FanRead</p>
                <p>Votre compagnon de lecture personnel</p>
                <p>papoel.dev@gmail.com</p>

                <div class="footer-links">
                    <a href="#">Confidentialité</a>
                    <a href="#">Conditions d'utilisation</a>
                    <a href="#">Support</a>
                </div>

                <p class="footer-note">
                    Vous recevez cet email car vous vous êtes inscrit sur FanRead.
                </p>
            </div>

        </div>
    </body>
  </html>
</template>
