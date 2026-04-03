# Testing

Piepjackclothing uses the **Pest 3** PHP testing framework for both feature and unit testing.

## Running Tests

### All Tests
```bash
php artisan test
```

### With Compact Output
```bash
php artisan test --compact
```

### Specific Test File
```bash
php artisan test tests/Feature/CheckoutDiscountTest.php
```

## Writing Tests

Tests are located in the `tests/` directory.

### Feature Tests
Used for testing API endpoints and end-to-end workflows.
- Location: `tests/Feature/`
- Example: `CheckoutDiscountTest.php`

### Unit Tests
Used for testing isolated pieces of business logic.
- Location: `tests/Unit/`

### Factories
Use Eloquent Factories to generate test data. Always use the `fake()` helper for PHP 8.5 compatibility.
```javascript
$product = Product::factory()->create();
```

## Mocking External Services

We use **Mockery** to mock external integrations like Stripe.
Ensure you use the `overload:` prefix or `alias:` when mocking static classes in separate processes.

Example from `CheckoutDiscountTest.php`:
```php
beforeEach(function () {
    $sessionMock = Mockery::mock('alias:Stripe\Checkout\Session');
    $sessionMock->shouldReceive('create')->andReturn($sessionMock);
});
```

*Note: For tests using Mockery aliases, add `@runTestsInSeparateProcesses` to the test file.*
