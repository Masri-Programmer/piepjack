# Architecture Overview

Piepjackclothing is built as a modern, decoupled application with a Laravel backend and a Vue.js frontend.

## Technical Stack

- **Backend:** Laravel 12 (PHP 8.5)
- **Frontend:** Vue 3 (Composition API)
- **Build Tool:** Vite
- **Styling:** Tailwind CSS 4
- **Database:** MySQL
- **Payments:** Stripe Checkout
- **Testing:** Pest 3

## Backend Architecture

The backend serves as a RESTful API (V1) and handles:
- Business logic and order processing.
- Authentication via Laravel Fortify and Sanctum.
- Database management and migrations.
- External integrations (Stripe, Mail).

### Directory Structure (Partial)
- `app/Http/Controllers/API/V1`: Contains all API versioned controllers.
- `app/Models`: Eloquent models representing the database schema.
- `app/Http/Requests`: Form Requests for request validation.
- `routes/api.php`: API endpoint definitions.

## Frontend Architecture

The frontend is a Single Page Application (SPA) using Vue 3.
- **Routing:** Vue Router.
- **Data Fetching:** TanStack Vue Query for caching and state management.
- **Internationalization:** Vue i18n (supported languages: English, German).
- **Icons:** Lucide Vue Next.

### Directory Structure (Partial)
- `resources/js/pages`: Route-level components.
- `resources/js/components`: Reusable UI components.
- `resources/js/layouts`: Layout wrappers (Admin, Shop, Guest).
- `resources/js/lib`: Shared utilities, locales, and configurations.

## Communication

The frontend communicates with the backend exclusively via JSON APIs. The default API prefix is `/api/V1`.
Authentication is handled via stateful cookies (Sanctum) for the web/admin interface.
