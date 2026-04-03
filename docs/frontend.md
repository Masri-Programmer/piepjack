# Frontend (Vue.js)

The frontend is a Vue 3 application utilizing the Composition API and a modern toolchain.

## Key Technologies

- **Vue 3:** Framework for building the SPA.
- **Vite:** Next-generation frontend tooling for fast builds.
- **Tailwind CSS 4:** Utility-first CSS framework for styling.
- **TanStack Vue Query:** For asynchronous state management and data fetching.
- **Vue i18n:** For multi-language support (EN/DE).
- **Lucide Vue Next:** For consistent iconography.

## Project Structure

- `resources/js/App.vue`: Main entry point component.
- `resources/js/router/index.js`: Route definitions for Shop and Admin areas.
- `resources/js/pages/`: Page-level components.
- `resources/js/components/`:
    - `shop/`: Shop-specific components (Cart, ProductGrid, etc.).
    - `admin/`: Admin-specific components (Dashboards, Tables, etc.).
    - `ui/`: Common reusable UI elements (Buttons, Inputs, Spinners).
- `resources/js/layouts/`: Layout wrappers for different application areas.
- `resources/js/lib/`:
    - `config.js`: i18n and QueryClient setup.
    - `helpers.js`: API request utilities and common functions.
    - `locales/`: JSON translation files.

## Data Fetching

We use a custom wrapper around `useQuery` and `useMutation` from TanStack Vue Query, located in `@lib/helpers` as `apiQuery`.

Example usage:
```javascript
const { data, isLoading } = apiQuery("products").useGet();
```

## Internationalization (i18n)

Translations are managed in `resources/js/lib/locales/`. 
To add a translation in a component:
```html
<p>{{ $t('admin.common.welcome') }}</p>
```

In scripts:
```javascript
import { useI18n } from 'vue-i18n';
const { t } = useI18n();
const title = t('admin.common.welcome');
```

## State Management

While Vue Query handles server state, client-side UI state is managed using custom reactive stores in `resources/js/lib/store/`.
