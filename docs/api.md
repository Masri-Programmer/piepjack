# API Reference

The application follows a RESTful API structure with a `V1` prefix. All endpoints return and accept JSON.

## Base URL
`https://piepjack.test/api/V1`

## Shop (Public) API

### Products
- `GET /shop/products`: List all active products.
- `GET /shop/products/{id}`: Get product details including variations and items.

### Categories
- `GET /shop/categories`: Get all active categories.

### Checkout
- `POST /shop/checkout`: Initiate checkout process. Returns a Stripe URL.
- `POST /shop/webhook/stripe`: Stripe payment confirmation webhook.

### Returns
- `POST /shop/returns`: Submit a return request.
- `GET /shop/returns/{id}`: Check return status.

## Admin API
Requires `auth:sanctum` middleware and admin privileges.

### Management
- `GET /admin/dashboard`: Statistical overview for the dashboard.
- `GET /admin/users`: List all users.
- `POST /admin/ban/{user}`: Ban a user.

### Catalog
- `apiResource /admin/products`: CRUD for products.
- `apiResource /admin/product-items`: CRUD for specific SKU items.
- `apiResource /admin/categories`: CRUD for categories.

### Orders
- `apiResource /admin/orders`: Manage customer orders.
- `apiResource /admin/returns`: Manage return requests.

## Authentication
Admin login is handled via Laravel Sanctum.
- `POST /admin/login`: Guest login endpoint.
- `POST /admin/logout`: Auth logout endpoint.
- `GET /admin/user`: Get current authenticated admin user.
