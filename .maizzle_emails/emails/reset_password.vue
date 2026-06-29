<template>
  <html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Réinitialisation de votre mot de passe - FanRead</title>
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
                max-width: 580px;
                margin: 0 auto;
                background-color: #ffffff;
                border-radius: 20px;
                overflow: hidden;
                box-shadow: 0 20px 60px rgba(225, 29, 72, 0.1), 0 0 0 1px rgba(225, 29, 72, 0.05);
            }

            /* ── Header ── */
            .header {
                background: linear-gradient(135deg, #e11d48 0%, #be123c 50%, #9f1239 100%);
                padding: 52px 40px;
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
                width: 140px; height: 140px;
                background: rgba(255, 140, 102, 0.2);
                border-radius: 50%;
            }

            .logo-container {
                width: 88px; height: 88px;
                background: rgba(255, 255, 255, 0.2);
                border: 3px solid rgba(255, 255, 255, 0.4);
                border-radius: 50%;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                margin-bottom: 20px;
                box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
                position: relative; z-index: 1;
            }
            .logo-container svg { width: 44px; height: 44px; fill: #ffffff; }

            .header h1 {
                color: #ffffff;
                font-family: 'Outfit', sans-serif;
                font-size: 28px; font-weight: 800;
                margin: 0 0 6px;
                letter-spacing: -0.3px;
                position: relative; z-index: 1;
            }
            .header p {
                color: rgba(255, 255, 255, 0.88);
                font-size: 15px; margin: 0;
                position: relative; z-index: 1;
            }

            /* ── Content ── */
            .content { padding: 48px 40px; }

            .intro-text {
                color: #9d7b86; font-size: 16px;
                line-height: 1.8; margin-bottom: 36px;
                text-align: center;
            }
            .intro-text strong { color: #5c3d47; font-weight: 700; }

            /* ── CTA section ── */
            .cta-section { text-align: center; margin: 0 0 36px; }

            .cta-button {
                display: inline-block;
                background: linear-gradient(135deg, #e11d48 0%, #be123c 100%);
                color: #ffffff; text-decoration: none;
                padding: 18px 56px; border-radius: 14px;
                font-family: 'Outfit', sans-serif;
                font-size: 17px; font-weight: 700;
                letter-spacing: 0.3px;
                box-shadow: 0 10px 30px rgba(225, 29, 72, 0.35);
            }

            .cta-fallback {
                margin-top: 20px;
                font-size: 13px; color: #9d7b86;
                text-align: center;
            }
            .cta-fallback a {
                color: #e11d48; word-break: break-all;
                font-size: 12px;
            }

            /* ── Expiry info (lavender) ── */
            .expiry-section {
                background: linear-gradient(135deg, #f5f3ff 0%, #ede9fe 100%);
                border: 1px solid #ddd6fe;
                border-radius: 14px;
                padding: 20px 24px;
                margin-bottom: 28px;
                text-align: center;
            }
            .expiry-section p {
                color: #7c5db0; font-size: 14px;
                line-height: 1.6; margin: 0;
            }
            .expiry-section strong { color: #5c3d47; }

            /* ── Security note ── */
            .security-note {
                background: #fff7f0;
                border: 1px solid #f0dce3;
                border-radius: 12px;
                padding: 18px 24px;
            }
            .security-note p {
                color: #9d7b86; font-size: 13px;
                line-height: 1.6; margin: 0;
            }

            /* ── Footer ── */
            .footer {
                background: linear-gradient(180deg, #fff7f0 0%, #fff1f2 100%);
                padding: 32px 40px; text-align: center;
                border-top: 1px solid #f0dce3;
            }
            .footer-brand {
                font-family: 'Outfit', sans-serif;
                color: #5c3d47; font-size: 17px;
                font-weight: 800; margin-bottom: 6px;
            }
            .footer p { color: #9d7b86; font-size: 13px; margin-bottom: 4px; }
            .footer-note { margin-top: 16px; font-size: 12px; color: #9d7b86; }

            @media only screen and (max-width: 600px) {
                .email-container { border-radius: 0; }
                .header { padding: 40px 24px; }
                .content { padding: 32px 24px; }
                .cta-button { padding: 16px 36px; font-size: 16px; }
                .footer { padding: 28px 24px; }
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
                <p>Réinitialisation du mot de passe 🔐</p>
            </div>

            <!-- Content -->
            <div class="content">

                <p class="intro-text">
                    Vous avez demandé à <strong>réinitialiser votre mot de passe</strong> sur FanRead.<br>
                    Cliquez sur le bouton ci-dessous pour choisir un nouveau mot de passe.
                </p>

                <!-- CTA -->
                <div class="cta-section">
                    <a v-pre href="{{ url('app_reset_password', {token: resetToken.token}) }}" class="cta-button">
                        🔑 Réinitialiser mon mot de passe
                    </a>
                    <p class="cta-fallback">
                        Si le bouton ne fonctionne pas, copiez ce lien dans votre navigateur :<br>
                        <a v-pre href="{{ url('app_reset_password', {token: resetToken.token}) }}"><Raw>{{ url('app_reset_password', {token: resetToken.token}) }}</Raw></a>
                    </p>
                </div>

                <!-- Expiry info -->
                <div class="expiry-section">
                    <p>
                        ⏳ <strong>Ce lien expirera dans :</strong><br>
                        <Raw>{{ resetToken.expirationMessageKey|trans(resetToken.expirationMessageData, 'ResetPasswordBundle') }}</Raw>
                    </p>
                </div>

                <!-- Security note -->
                <div class="security-note">
                    <p>
                        🔒 Si vous n'avez pas demandé de réinitialisation de mot de passe sur <strong>FanRead</strong>, ignorez cet email en toute sécurité. Votre mot de passe ne sera pas modifié.
                    </p>
                </div>

            </div>

            <!-- Footer -->
            <div class="footer">
                <p class="footer-brand">❤️ FanRead</p>
                <p>Votre compagnon de lecture personnel</p>
                <p>papoel.dev@gmail.com</p>
                <p class="footer-note">
                    Cet email a été envoyé automatiquement, merci de ne pas y répondre.
                </p>
            </div>

        </div>
    </body>
  </html>
</template>
