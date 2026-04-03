# Database & Models

The database follows a standard e-commerce schema with products, categories, orders, and user management.

## Core Models

### Products & Categories
- **Product:** Represents a general product (e.g., "T-Shirt").
- **ProductItem:** Represents a specific SKU or variant of a product.
- **Category:** Products are organized into hierarchical categories.
- **Variation & VariationOption:** Defines product attributes like size or color.
- **ProductConfiguration:** Pivot table linking `ProductItem` to specific `VariationOption`.

### Orders & Payments
- **Order:** Main order record tracking status and total price.
- **OrderProduct:** Pivot table for items included in an order.
- **Payments:** Tracks payment status and transaction IDs.
- **Shipments:** Manages shipping status and tracking information.
- **OrderLogs:** Historical log of order status changes.

### User Management
- **User:** Core user record for customers and admins.
- **Address:** Stores shipping and billing addresses for users.
- **Role:** RBAC system for controlling access levels.

### Returns
- **Returning:** Main record for a customer return request.
- **ReturnItem:** Specific items being returned within a request.

## Key Relationships

- A **Product** has many **ProductItems**.
- A **ProductItem** belongs to a **Product**.
- An **Order** has many **OrderProducts**.
- A **Category** can have many **Subcategories** (parent-child relationship).
- A **User** has many **Addresses** and many **Orders**.

## Data Casting

Standard casts are defined in models (e.g., date formatting, boolean conversions). Since Laravel 12, casts are typically defined via the `casts()` method.
