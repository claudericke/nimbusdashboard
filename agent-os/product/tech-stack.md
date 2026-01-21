# Tech Stack

This document defines the technical stack for the Drift Nimbus Dashboard.

## Framework & Runtime
- **Application Framework:** Custom PHP MVC
- **Language/Runtime:** PHP 8.x
- **Package Manager:** Composer

## Frontend
- **CSS Framework:** Bootstrap 5
- **Icons:** Lucide Icons (via lucide-static)
- **Typography:** Google Fonts (Inter)
- **Animations:** Framer Motion (Note: Typically for React; for this PHP MVC project, we'll use CSS animations or a JS library like GSAP for similar premium effects, or integrate React if necessary).

## Database & Storage
- **Database:** MySQL
- **ORM/Query Builder:** Custom Model-based implementation

## Testing & Quality
- **Linting/Formatting:** PSR-12 (standard for PHP)

## Third-Party Services
- **Hosting API:** cPanel API
- **Billing API:** Zoho API
- **Support API:** Trello API
- **Email:** PHPMailer (with custom templates like `onboardingEmail.md`)

## Environment Configuration
- **Configuration:** `.env` via `vlucas/phpdotenv`
