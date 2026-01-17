# AgriChemTech ERP

A production-ready Laravel 11 ERP system for agricultural chemical management with multi-tenant support, role-based access control, and comprehensive order management.

## Features

- **Multi-tenant Architecture**: Support for multiple organizations
- **Role-Based Access Control (RBAC)**: Using Spatie Permission package
- **API Authentication**: Laravel Sanctum for mobile/API access
- **Order Management**: Complete order lifecycle with GST calculation
- **State-wise Pricing**: Product rates vary by state
- **Discount Slabs**: Configurable discount based on order value
- **Dispatch Management**: Track shipments with LR numbers
- **Livewire Components**: Dynamic UI without heavy JavaScript frameworks
- **Bootstrap 5 UI**: Modern, responsive design with AgriChemTech theme

## Tech Stack

- **Laravel 11**: PHP framework
- **MySQL**: Database
- **Redis**: Cache and queue driver
- **Laravel Sanctum**: API authentication
- **Spatie Permission**: RBAC
- **Livewire 3**: Dynamic components
- **Laravel Horizon**: Queue dashboard
- **Bootstrap 5**: UI framework
- **FontAwesome**: Icons

## Requirements

- PHP >= 8.2
- Composer
- Node.js >= 20
- MySQL >= 8.0
- Redis >= 7.0

## Installation

### 1. Clone the repository

```bash
git clone <repository-url>
cd aglichem-erp
```

### 2. Install PHP dependencies

```bash
composer install
```

### 3. Install Node dependencies

```bash
npm install
```

### 4. Environment Configuration

Copy `.env.example` to `.env`:

```bash
cp .env.example .env
```

Update the following environment variables in `.env`:

```env
APP_NAME="AgriChemTech ERP"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=aglichem_erp
DB_USERNAME=root
DB_PASSWORD=

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis

SANCTUM_STATEFUL_DOMAINS=localhost,127.0.0.1
```

### 5. Generate Application Key

```bash
php artisan key:generate
```

### 6. Publish Package Configurations

```bash
# Publish Sanctum
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"

# Publish Spatie Permission
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"

# Publish Horizon
php artisan horizon:install
php artisan horizon:publish
```

### 7. Run Migrations

```bash
php artisan migrate
```

### 8. Seed Database

```bash
php artisan db:seed
```

This will create:
- 2 organizations (Maharashtra and Gujarat)
- 4 users with roles (admin, accountant, sales_officer, dispatch_officer)
- 6 products with state-specific rates
- Zones, states, cities
- 6 dealers across different zones
- Discount slabs (0-11999 => 0%, 12000-49999 => 5%, 50000+ => 8%)

### 9. Build Assets

```bash
npm run build
```

For development:

```bash
npm run dev
```

### 10. Start Development Server

```bash
php artisan serve
```

Access the application at `http://localhost:8000`

## Default Login Credentials

### Web Users

- **Admin (Org 1)**: admin@aglichemtech-mh.com / password
- **Accountant (Org 1)**: accountant@aglichemtech-mh.com / password
- **Sales Officer (Org 2)**: sales@aglichemtech-gj.com / password
- **Dispatch Officer (Org 2)**: dispatch@aglichemtech-gj.com / password

### Dealers (API)

- **Mobile**: 9876543210 / password
- **Mobile**: 9876543211 / password
- (See seeders for more dealers)

## Queue & Horizon

Start the queue worker:

```bash
php artisan queue:work
```

Or use Horizon dashboard:

```bash
php artisan horizon
```

Access Horizon at: `http://localhost:8000/horizon`

## API Documentation

### Authentication

#### Login
```
POST /api/login
Content-Type: application/json

{
    "mobile": "9876543210",
    "password": "password",
    "organization_id": 1
}
```

#### Register Dealer
```
POST /api/dealers/register
Content-Type: application/json

{
    "name": "Dealer Name",
    "mobile": "9876543210",
    "email": "dealer@example.com",
    "state_id": 1,
    "city_id": 1,
    "password": "password123",
    "password_confirmation": "password123"
}
```

### Protected Endpoints

All protected endpoints require the `Authorization: Bearer {token}` header.

#### Get Profile
```
GET /api/dealers/profile
Authorization: Bearer {token}
```

#### Create Order
```
POST /api/orders
Authorization: Bearer {token}
Content-Type: application/json

{
    "cart_items": [
        {
            "product_id": 1,
            "quantity": 10
        }
    ]
}
```

#### Create Dispatch
```
POST /api/dispatches
Authorization: Bearer {token}
Content-Type: application/json

{
    "order_id": 1,
    "lr_number": "LR123456",
    "transporter_name": "ABC Transport",
    "vehicle_number": "MH-01-AB-1234"
}
```

## Order Calculation Logic

1. **Subtotal**: Sum of (quantity × rate) for all items
2. **Discount**: Applied based on discount slabs
   - 0-11999: 0%
   - 12000-49999: 5%
   - 50000+: 8%
3. **Taxable Amount**: Subtotal - Discount
4. **GST Calculation**:
   - Same state: CGST 9% + SGST 9% = 18%
   - Different state: IGST 18%
5. **Grand Total**: Taxable Amount + CGST + SGST + IGST

## Testing

Run PHPUnit tests:

```bash
php artisan test
```

Run specific test suites:

```bash
# Unit tests
php artisan test --testsuite=Unit

# Feature tests
php artisan test --testsuite=Feature
```

## Code Style

This project follows PSR-12 coding standards. Format code using Laravel Pint:

```bash
php artisan pint
```

## Project Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   └── Api/          # API controllers
│   └── Requests/         # Form requests
├── Livewire/             # Livewire components
├── Models/               # Eloquent models
├── Observers/            # Model observers
└── Policies/             # Authorization policies

database/
├── migrations/           # Database migrations
└── seeders/              # Database seeders

resources/
├── scss/                 # SCSS stylesheets
├── js/                   # JavaScript
└── views/                # Blade templates
    ├── layouts/          # Layout files
    └── livewire/         # Livewire views
```

## Future Enhancements

- Payment gateway integration
- Multilingual support
- Advanced inventory management
- Reports and analytics
- Email notifications
- SMS integration

## Contributing

1. Create a feature branch from `dev`
2. Make your changes
3. Write/update tests
4. Ensure all tests pass
5. Submit a pull request

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Support

For issues and questions, please open an issue on the repository.
