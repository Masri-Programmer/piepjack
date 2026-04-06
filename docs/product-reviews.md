# Product Reviews

The Product Review system allows verified customers to leave feedback and ratings for products they have purchased.

## Features

- **Verified Reviews**: Only users with a delivered order containing the product can leave a review.
- **Rating System**: 1-5 star rating system.
- **Moderation**: Reviews are held for moderation (default `is_approved = false`) before being visible to other customers.
- **Unique Constraint**: Each user can leave only one review per product.

## Implementation Details

### Backend

- **Migration**: `database/migrations/2025_03_10_100000_create_product_reviews_table.php`
- **Model**: `app/Models/ProductReview.php`
- **Controller**: `app/Http/Controllers/API/V1/ProductReviewController.php`
- **Request**: `app/Http/Requests/StoreProductReviewRequest.php`

#### Endpoints

- `GET /api/shop/products-reviews/{productId}`: List all approved reviews for a specific product.
- `POST /api/shop/products-reviews`: Submit a new review (requires authentication/verification).

### Frontend

- **Component**: `resources/js/components/shop/product/ProductReview.vue`
- **Form**: `resources/js/components/shop/product/ProductReviewForm.vue`
- **Rating Display**: `resources/js/components/shop/product/StarRating.vue`

## Verification Logic

The `store` method in `ProductReviewController` performs the following checks:

1.  **User Identity**: Verifies that the email provided matches an existing user.
2.  **Purchase Verification**: Checks if the user has a `delivered` order that includes at least one variant of the product being reviewed.
3.  **Duplicate Check**: Ensures the user hasn't already reviewed the product.

## Moderation

By default, new reviews are created with `is_approved = false`. An administrator must manually approve them via the database or an admin panel (if available) for them to appear on the site.
