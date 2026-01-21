<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Drift Nimbus</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap');
        
        body {
            margin: 0;
            padding: 0;
            background-color: #05070a;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial, sans-serif;
            -webkit-font-smoothing: antialiased;
        }

        .wrapper {
            width: 100%;
            table-layout: fixed;
            background-color: #05070a;
            padding-bottom: 40px;
        }

        .main {
            background-color: #0b0e14;
            margin: 0 auto;
            width: 100%;
            max-width: 600px;
            border-spacing: 0;
            color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid #1f2937;
        }

        .content {
            padding: 40px 30px;
        }

        .header {
            text-align: center;
            padding: 40px 0 20px 0;
        }

        .logo {
            width: 220px;
            height: auto;
        }

        h1 {
            font-size: 28px;
            font-weight: 700;
            margin: 0 0 20px 0;
            text-align: center;
            letter-spacing: -0.02em;
        }

        .hero-text {
            font-size: 16px;
            line-height: 1.6;
            color: #9ca3af;
            text-align: center;
            margin-bottom: 40px;
        }

        .info-card {
            background-color: #161b22;
            border: 1px solid #30363d;
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 24px;
        }

        .info-card h2 {
            font-size: 18px;
            font-weight: 600;
            margin: 0 0 12px 0;
            color: #ffffff;
            display: flex;
            align-items: center;
        }

        .info-card p.subtitle {
            font-size: 14px;
            color: #8b949e;
            margin: 0 0 20px 0;
            line-height: 1.4;
        }

        .credential-row {
            margin-bottom: 12px;
            font-size: 14px;
        }

        .label {
            color: #8b949e;
            display: block;
            margin-bottom: 4px;
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: 0.05em;
            font-weight: 600;
        }

        .value {
            color: #ffffff;
            font-family: 'Courier New', Courier, monospace;
            background: rgba(255, 255, 255, 0.05);
            padding: 6px 10px;
            border-radius: 4px;
            display: inline-block;
        }

        .link-value {
            color: #58a6ff;
            text-decoration: none;
            font-weight: 600;
        }

        .button {
            display: block;
            background-color: #ffffff;
            color: #0b0e14 !important;
            text-align: center;
            padding: 14px 20px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 700;
            font-size: 15px;
            margin-top: 20px;
            transition: opacity 0.2s;
        }

        .testimonial {
            border-left: 3px solid #ffffff;
            padding: 10px 0 10px 20px;
            margin: 40px 0;
        }

        .testimonial-text {
            font-style: italic;
            color: #d1d5db;
            font-size: 15px;
            margin-bottom: 8px;
        }

        .testimonial-author {
            color: #6e7681;
            font-size: 13px;
        }

        .footer {
            text-align: center;
            padding: 40px 30px;
            color: #6e7681;
            font-size: 12px;
        }

        .footer a {
            color: #8b949e;
            text-decoration: underline;
        }

        .social-links {
            margin: 20px 0 30px 0;
            text-align: center;
        }

        .social-icon {
            display: inline-block;
            margin: 0 12px;
            text-decoration: none;
        }

        .social-icon img {
            width: 20px;
            height: 20px;
            /* Makes black Lucide icons white */
            filter: invert(100%) brightness(200%);
            opacity: 0.7;
            transition: opacity 0.2s;
        }

        .social-icon:hover img {
            opacity: 1;
        }

        @media screen and (max-width: 600px) {
            .content {
                padding: 30px 20px;
            }
            h1 {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="header">
            <img src="https://hosting.driftnimbus.com/wp-content/uploads/2025/02/nimbus-logo-horizontal-white.svg" alt="Drift Nimbus" class="logo">
        </div>

        <table class="main">
            <tr>
                <td class="content">
                    <h1>Your digital backbone is live.</h1>
                    <p class="hero-text">
                        Welcome to Drift Nimbus. We’re dedicated to providing robust hosting solutions and innovative tools tailored for the doers. Your domain has been successfully provisioned and is ready to leap to life.
                    </p>

                    <!-- Dashboard Box -->
                    <div class="info-card">
                        <h2>Nimbus Dashboard (Reccomended)</h2>
                        <p class="subtitle">Your unified digital command center for managing emails, monitoring disk space, and business operations.</p>
                        
                        <div class="credential-row">
                            <span class="label">Access URL</span>
                            <a href="https://dashboard.driftnimbus.com" class="link-value">dashboard.driftnimbus.com</a>
                        </div>
                        
                        <div class="credential-row">
                            <span class="label">Username</span>
                            <span class="value">{{username}}</span>
                        </div>

                        <div class="credential-row">
                            <span class="label">Password</span>
                            <span class="value">{{password}}</span>
                        </div>

                        <a href="https://dashboard.driftnimbus.com" class="button">Go to Dashboard</a>
                    </div>

                    <!-- cPanel Box -->
                    <div class="info-card">
                        <h2>cPanel Hosting Control (Advanced Users)</h2>
                        <p class="subtitle">Advanced technical control for file transfers, database management, and site configurations.</p>
                        
                        <div class="credential-row">
                            <span class="label">Control Panel Link</span>
                            <a href="https://cpanel.{{domainURI}}" class="link-value">{{domainURI}}</a>
                        </div>
                        
                        <div class="credential-row">
                            <span class="label">Username</span>
                            <span class="value">{{username}}</span>
                        </div>

                        <div class="credential-row">
                            <span class="label">Password</span>
                            <span class="value">{{password}}</span>
                        </div>
                    </div>

                    <!-- Testimonial -->
                    <div class="testimonial">
                        <div class="testimonial-text">"Drift Nimbus has truly transformed how we manage our digital. The Nimbus Dashboard is a game-changer – so intuitive and powerful."</div>
                        <div class="testimonial-author">— Aisha K. Mwangi, Founder, Apex Digital Solutions</div>
                    </div>

                    <div style="text-align: center; margin-top: 40px;">
                        <p style="color: #ffffff; font-weight: 600; margin-bottom: 5px;">Need technical support?</p>
                        <p style="color: #9ca3af; font-size: 14px; margin-top: 0;">Our team is here for you 24/7 to assist with your growth.</p>
                    </div>
                </td>
            </tr>
            <tr>
                <td class="footer">
                    <div class="social-links">
                        <a href="https://facebook.com/nimbusbusiness" class="social-icon">
                            <img src="https://cdn.jsdelivr.net/npm/lucide-static@0.344.0/icons/facebook.svg" alt="Facebook">
                        </a>
                        <a href="https://linkedin.com/company/driftnimbus" class="social-icon">
                            <img src="https://cdn.jsdelivr.net/npm/lucide-static@0.344.0/icons/linkedin.svg" alt="LinkedIn">
                        </a>
                        <a href="https://instagram.com/nimbusbusiness" class="social-icon">
                            <img src="https://cdn.jsdelivr.net/npm/lucide-static@0.344.0/icons/instagram.svg" alt="Instagram">
                        </a>
                   
                    </div>
                    <p>
                        <a href="mailto:support@driftnimbus.com">support@driftnimbus.com</a> | +263 78 511 5993
                    </p>
                    <p style="margin-top: 20px; opacity: 0.5;">
                        Copyright © 2026 Drift Nimbus. All rights reserved.<br>
                        Built in Africa for the World.
                    </p>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>