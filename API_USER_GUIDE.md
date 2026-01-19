# API User Guide - Complete Reference

This guide explains how to use the HTTP APIs exposed by this project. The API supports **multiple user types** with role-based access control.

## Table of Contents

1. [Default Login Credentials](#default-login-credentials)
2. [Base URL & Headers](#base-url--headers)
3. [Authentication](#authentication)
4. [User Types & Dashboard Routing](#user-types--dashboard-routing)
5. [API Endpoints by Category](#api-endpoints-by-category)
   - [Auth APIs](#auth-apis)
   - [Dashboard APIs](#dashboard-apis)
   - [User Profile APIs](#user-profile-apis)
   - [Dealer APIs](#dealer-apis)
   - [Product APIs](#product-apis)
   - [Order APIs](#order-apis)
   - [Dispatch APIs](#dispatch-apis)
   - [Payment APIs](#payment-apis)
   - [Report APIs](#report-apis)
6. [Error Handling](#error-handling)

---

## Default Login Credentials

These credentials are created by `php artisan db:seed` (see `database/seeders/*`).

**Important**: Do not use these in production. Change passwords immediately in any real environment.

### Organizations (for `organization_id`)

The API login may require `organization_id` depending on user type. After a fresh migrate+seed, these are typically:

| organization_id | Organization | GSTIN |
|---:|---|---|
| 1 | AgriChemTech Maharashtra Pvt Ltd | 27AABCU9601R1ZM |
| 2 | AgriChemTech Gujarat Industries | 24AABCU9601R1ZN |

If your IDs differ, confirm in DB:

```sql
SELECT id, name, gstin FROM organizations ORDER BY id;
```

### Web App Users (for API login with `user_type=user`)

Password for all seeded web users: **`password`**

| Role | Org | Mobile | Password | Access Level |
|---|---:|---|---|---|
| admin | 1 | 9876543210 | password | Full access to all APIs |
| accountant | 1 | 9876543211 | password | Payments, Reports, Orders (view) |
| sales_officer | 2 | 9876543212 | password | Orders, Dealers, Products |
| dispatch_officer | 2 | 9876543213 | password | Dispatches, Orders (view) |

### Dealers (for API login with `user_type=dealer` or default)

Password for all seeded dealers: **`password`**

Use any valid `organization_id` (commonly `1` after seeding).

| Dealer | Mobile | Password |
|---|---|---|
| Mumbai Agro Traders | 9876543210 | password |
| Pune Farm Supplies | 9876543211 | password |
| Ahmedabad Crop Care | 9876543212 | password |
| Surat Agri Solutions | 9876543213 | password |
| Bangalore Green Fields | 9876543214 | password |
| Chennai Harvest Mart | 9876543215 | password |

---

## Base URL & Headers

### Base URL

All endpoints below are under Laravel's default `/api` prefix.

Set your base URL depending on where the app is running:

- **Artisan dev server**: `http://127.0.0.1:8000`
- **XAMPP** (typical): `http://localhost/agrosalescrm/public`

So the full URL looks like: `{BASE_URL}/api/...`

### Request Headers

For JSON APIs, always send:

- **Accept**: `application/json`
- **Content-Type**: `application/json`

For authenticated endpoints, also send:

- **Authorization**: `Bearer {token}`

Example:

```bash
curl "{BASE_URL}/api/dashboard" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer 1|xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"
```

---

## Authentication

### Unified Login API

The login endpoint supports **both Users and Dealers**. It returns `user_type` and `roles` to help your app route users to the correct dashboard.

**Endpoint**: `POST /api/login` (Public)

**Request Body**

For **Dealer** login (default or explicit):

```json
{
  "mobile": "9876543210",
  "password": "password",
  "organization_id": 1,
  "user_type": "dealer"
}
```

For **User** login (admin/accountant/sales/dispatch):

```json
{
  "mobile": "9876543210",
  "password": "password",
  "organization_id": 1,
  "user_type": "user"
}
```

**Note**: `user_type` is optional. If omitted, defaults to `"dealer"` for backward compatibility. `organization_id` is required for dealers, optional for users (but recommended).

**cURL Example (Dealer)**

```bash
curl -X POST "{BASE_URL}/api/login" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{
    "mobile": "9876543210",
    "password": "password",
    "organization_id": 1,
    "user_type": "dealer"
  }'
```

**cURL Example (User)**

```bash
curl -X POST "{BASE_URL}/api/login" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{
    "mobile": "9876543210",
    "password": "password",
    "organization_id": 1,
    "user_type": "user"
  }'
```

**Success Response (200) - Dealer**

```json
{
  "user_type": "dealer",
  "dealer": {
    "id": 1,
    "name": "Mumbai Agro Traders",
    "mobile": "9876543210",
    "email": "mumbai@agrotraders.com",
    "state": { "id": 1, "name": "Maharashtra", "code": "MH" },
    "city": { "id": 1, "name": "Mumbai" },
    "zone": { "id": 1, "name": "Zone 1", "code": "MH-Z1" }
  },
  "organization": {
    "id": 1,
    "name": "AgriChemTech Maharashtra Pvt Ltd"
  },
  "roles": ["dealer"],
  "permissions": ["view_orders", "create_orders", "view_profile", "update_profile"],
  "token": "1|xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"
}
```

**Success Response (200) - User**

```json
{
  "user_type": "user",
  "user": {
    "id": 1,
    "name": "Admin User",
    "email": "admin@aglichemtech-mh.com",
    "mobile": "9876543210",
    "organization_id": 1
  },
  "organization": {
    "id": 1,
    "name": "AgriChemTech Maharashtra Pvt Ltd"
  },
  "roles": ["admin"],
  "permissions": [
    "manage_users", "manage_products", "manage_dealers", "manage_orders",
    "manage_dispatches", "manage_payments", "view_reports", "manage_settings"
  ],
  "token": "1|xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"
}
```

### Get Current User/Dealer Info

**Endpoint**: `GET /api/me` (Bearer token required)

Returns the same structure as login response, useful for checking authentication status and user type.

**cURL**

```bash
curl "{BASE_URL}/api/me" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer 1|xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"
```

### Logout

**Endpoint**: `POST /api/logout` (Bearer token required)

Invalidates the current token.

**cURL**

```bash
curl -X POST "{BASE_URL}/api/logout" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer 1|xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"
```

**Success Response (200)**

```json
{
  "message": "Logged out successfully"
}
```

---

## User Types & Dashboard Routing

After login, check the `user_type` and `roles` fields to route users to the correct dashboard:

### User Type: `"dealer"`

- **Dashboard**: Dealer dashboard (orders, profile)
- **Access**: Own orders, profile management, create orders
- **Redirect to**: `/dealer-dashboard` or similar

### User Type: `"user"` with Roles

#### Role: `"admin"`

- **Dashboard**: Admin dashboard (full access)
- **Access**: All APIs (users, products, dealers, orders, dispatches, payments, reports)
- **Redirect to**: `/admin-dashboard`

#### Role: `"accountant"`

- **Dashboard**: Accountant dashboard (payments, reports)
- **Access**: Payments (view/create), Reports, Orders (view), Dealers (view)
- **Redirect to**: `/accountant-dashboard`

#### Role: `"sales_officer"`

- **Dashboard**: Sales dashboard (orders, dealers, products)
- **Access**: Orders (manage), Dealers (manage), Products (manage), Dispatches (view)
- **Redirect to**: `/sales-dashboard`

#### Role: `"dispatch_officer"`

- **Dashboard**: Dispatch dashboard (dispatches, orders)
- **Access**: Dispatches (manage), Orders (view)
- **Redirect to**: `/dispatch-dashboard`

**Example Routing Logic (JavaScript/Pseudo-code)**

```javascript
const response = await login(credentials);
const { user_type, roles, permissions } = response;

if (user_type === 'dealer') {
  navigateTo('/dealer-dashboard');
} else if (user_type === 'user') {
  if (roles.includes('admin')) {
    navigateTo('/admin-dashboard');
  } else if (roles.includes('accountant')) {
    navigateTo('/accountant-dashboard');
  } else if (roles.includes('sales_officer')) {
    navigateTo('/sales-dashboard');
  } else if (roles.includes('dispatch_officer')) {
    navigateTo('/dispatch-dashboard');
  }
}
```

---

## API Endpoints by Category

### Auth APIs

| Method | Endpoint | Auth Required | User Type | Description |
|--------|----------|----------------|-----------|-------------|
| POST | `/api/login` | No | All | Unified login for Users and Dealers |
| POST | `/api/logout` | Yes | All | Logout and invalidate token |
| GET | `/api/me` | Yes | All | Get current authenticated user/dealer info |

---

### Dashboard APIs

#### Get Dashboard Data

**Endpoint**: `GET /api/dashboard` (Bearer token required)

Returns dashboard statistics and recent data based on user type and role.

**cURL**

```bash
curl "{BASE_URL}/api/dashboard" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer 1|xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"
```

**Success Response (200) - User (Admin)**

```json
{
  "user_type": "user",
  "roles": ["admin"],
  "stats": {
    "orders": {
      "total": 150,
      "pending": 10,
      "confirmed": 5,
      "dispatched": 20,
      "delivered": 115,
      "total_amount": 2500000.00
    },
    "dispatches": {
      "total": 120,
      "pending": 5,
      "dispatched": 15,
      "in_transit": 10,
      "delivered": 90
    },
    "payments": {
      "total": 200,
      "total_amount": 2400000.00
    },
    "dealers": {
      "total": 50,
      "active": 45
    }
  },
  "recent_orders": [ /* 5 most recent orders */ ]
}
```

**Success Response (200) - Dealer**

```json
{
  "user_type": "dealer",
  "stats": {
    "orders": {
      "total": 25,
      "pending": 2,
      "confirmed": 1,
      "dispatched": 5,
      "delivered": 17,
      "total_amount": 125000.00
    }
  },
  "recent_orders": [ /* 5 most recent orders for this dealer */ ]
}
```

**Access Control**:
- **Users**: Returns stats based on role (admin sees all, accountant sees payments/reports, etc.)
- **Dealers**: Returns only their own order stats

---

### User Profile APIs

#### Get User Profile

**Endpoint**: `GET /api/users/profile` (Bearer token required, Users only)

**cURL**

```bash
curl "{BASE_URL}/api/users/profile" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer 1|xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"
```

**Success Response (200)**

```json
{
  "user": {
    "id": 1,
    "name": "Admin User",
    "email": "admin@aglichemtech-mh.com",
    "mobile": "9876543210",
    "organization_id": 1
  },
  "organization": { /* organization details */ },
  "roles": ["admin"]
}
```

#### Update User Profile

**Endpoint**: `PUT /api/users/profile` (Bearer token required, Users only)

**cURL**

```bash
curl -X PUT "{BASE_URL}/api/users/profile" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer 1|xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx" \
  -d '{
    "name": "Updated Name",
    "email": "newemail@example.com",
    "mobile": "9876543219"
  }'
```

**Request Body** (all fields optional)

```json
{
  "name": "Updated Name",
  "email": "newemail@example.com",
  "mobile": "9876543219",
  "address": "New Address",
  "city": "Mumbai",
  "state": "Maharashtra",
  "pincode": "400001"
}
```

**Success Response (200)**

```json
{
  "message": "Profile updated successfully",
  "user": { /* updated user */ }
}
```

---

### Dealer APIs

#### Register Dealer

**Endpoint**: `POST /api/dealers/register` (Public)

**cURL**

```bash
curl -X POST "{BASE_URL}/api/dealers/register" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "New Dealer",
    "mobile": "9999999999",
    "email": "dealer@example.com",
    "gstin": "27ABCDE1234F1Z5",
    "address": "Address line",
    "state_id": 1,
    "city_id": 1,
    "pincode": "400001",
    "password": "secret123",
    "password_confirmation": "secret123"
  }'
```

**Request Body**

```json
{
  "name": "New Dealer",
  "mobile": "9999999999",
  "email": "dealer@example.com",
  "gstin": "27ABCDE1234F1Z5",
  "address": "Address line",
  "state_id": 1,
  "city_id": 1,
  "pincode": "400001",
  "password": "secret123",
  "password_confirmation": "secret123"
}
```

**Notes**:
- `password` must be **min 8 characters** and must include `password_confirmation`
- `state_id` and `city_id` must exist in database
- `mobile` must be unique
- `email` must be unique (if provided)

**Success Response (201)**

```json
{
  "message": "Dealer registered successfully",
  "dealer": { /* dealer + state/city/zone */ }
}
```

#### Get Dealer Profile

**Endpoint**: `GET /api/dealers/profile` (Bearer token required, Dealers only)

**cURL**

```bash
curl "{BASE_URL}/api/dealers/profile" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer 1|xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"
```

**Success Response (200)**

```json
{
  "dealer": {
    "id": 1,
    "name": "Mumbai Agro Traders",
    "mobile": "9876543210",
    "email": "mumbai@agrotraders.com",
    "state": { /* state details */ },
    "city": { /* city details */ },
    "zone": { /* zone details */ }
  }
}
```

#### Update Dealer Profile

**Endpoint**: `PUT /api/dealers/profile` (Bearer token required, Dealers only)

**cURL**

```bash
curl -X PUT "{BASE_URL}/api/dealers/profile" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer 1|xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx" \
  -d '{
    "name": "Updated Name",
    "email": "newemail@example.com",
    "city_id": 2
  }'
```

**Request Body** (all fields optional)

```json
{
  "name": "Updated Name",
  "email": "newemail@example.com",
  "address": "New Address",
  "city_id": 2,
  "pincode": "400002"
}
```

**Notes**:
- If `city_id` is updated, the API auto-updates `zone_id` and `state_id` from that city
- `email` must be unique (excluding current dealer)

**Success Response (200)**

```json
{
  "message": "Profile updated successfully",
  "dealer": { /* updated dealer */ }
}
```

#### List Dealers

**Endpoint**: `GET /api/dealers` (Bearer token required, Users: admin/sales_officer/accountant)

**cURL**

```bash
curl "{BASE_URL}/api/dealers?search=mumbai&zone_id=1&is_active=1&per_page=20" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer 1|xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"
```

**Query Parameters**

- `search` (optional): Search by name, mobile, or email
- `zone_id` (optional): Filter by zone
- `state_id` (optional): Filter by state
- `is_active` (optional): Filter by active status (1 or 0)
- `per_page` (optional): Items per page (default: 15)

**Success Response (200)** - Paginated

```json
{
  "current_page": 1,
  "data": [ /* dealers array */ ],
  "per_page": 15,
  "total": 50
}
```

#### Get Dealer Details

**Endpoint**: `GET /api/dealers/{id}` (Bearer token required, Users: admin/sales_officer/accountant)

**cURL**

```bash
curl "{BASE_URL}/api/dealers/1" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer 1|xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"
```

**Success Response (200)**

```json
{
  "dealer": { /* dealer + state/city/zone */ }
}
```

---

### Cart APIs (Dealers Only)

#### Get Cart Products

**Endpoint**: `GET /api/cart` (Bearer token required, Dealers only)

**cURL**

```bash
curl "{BASE_URL}/api/cart" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer 1|xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"
```

**Success Response (200)**

```json
{
  "items": [
    {
      "id": 1,
      "product_id": 1,
      "quantity": 2,
      "rate": 520.00,
      "subtotal": 1040.00,
      "product": {
        "id": 1,
        "name": "Pesticide A",
        "code": "PEST-A",
        "description": "Product description",
        "base_price": 500.00,
        "gst_rate": 18.00,
        "unit": { "id": 1, "name": "Kg" },
        "is_active": true
      }
    }
  ],
  "summary": {
    "item_count": 1,
    "total_quantity": 2,
    "subtotal": 1040.00
  }
}
```

**Notes**:
- Returns all cart items with calculated rates based on dealer's state
- Includes product details and pricing information
- Summary provides total count and subtotal

#### Add Product to Cart

**Endpoint**: `POST /api/cart` (Bearer token required, Dealers only)

**cURL**

```bash
curl -X POST "{BASE_URL}/api/cart" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer 1|xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx" \
  -d '{
    "product_id": 1,
    "quantity": 2
  }'
```

**Request Body**

```json
{
  "product_id": 1,
  "quantity": 2
}
```

**Notes**:
- If product already exists in cart, quantity is added to existing quantity
- Product must be active
- `quantity` must be at least 1

**Success Response (201)**

```json
{
  "message": "Product added to cart successfully",
  "cart_item": {
    "id": 1,
    "product_id": 1,
    "quantity": 2,
    "product": { /* product details */ }
  }
}
```

#### Update Cart Item Quantity

**Endpoint**: `PUT /api/cart/{id}` (Bearer token required, Dealers only)

**cURL**

```bash
curl -X PUT "{BASE_URL}/api/cart/1" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer 1|xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx" \
  -d '{
    "quantity": 5
  }'
```

**Request Body**

```json
{
  "quantity": 5
}
```

**Success Response (200)**

```json
{
  "message": "Cart item updated successfully",
  "cart_item": {
    "id": 1,
    "product_id": 1,
    "quantity": 5,
    "product": { /* product details */ }
  }
}
```

#### Remove Product from Cart

**Endpoint**: `DELETE /api/cart/{id}` (Bearer token required, Dealers only)

**cURL**

```bash
curl -X DELETE "{BASE_URL}/api/cart/1" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer 1|xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"
```

**Success Response (200)**

```json
{
  "message": "Product removed from cart successfully"
}
```

#### Clear All Cart Items

**Endpoint**: `DELETE /api/cart` (Bearer token required, Dealers only)

**cURL**

```bash
curl -X DELETE "{BASE_URL}/api/cart" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer 1|xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"
```

**Success Response (200)**

```json
{
  "message": "Cart cleared successfully"
}
```

---

### Product APIs

#### Get Product Categories

**Endpoint**: `GET /api/products/categories` (Bearer token required, All authenticated users)

**cURL**

```bash
curl "{BASE_URL}/api/products/categories" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer 1|xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"
```

**Success Response (200)**

```json
{
  "categories": [
    { "id": 1, "name": "Pesticides", "slug": "pesticides" },
    { "id": 2, "name": "Fertilizers", "slug": "fertilizers" },
    { "id": 3, "name": "Seeds", "slug": "seeds" },
    { "id": 4, "name": "Tools & Equipment", "slug": "tools-equipment" },
    { "id": 5, "name": "Other", "slug": "other" }
  ]
}
```

#### List Products

**Endpoint**: `GET /api/products` (Bearer token required, All authenticated users)

**cURL**

```bash
curl "{BASE_URL}/api/products?search=pesticide&per_page=20" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer 1|xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"
```

**Query Parameters**

- `search` (optional): Search by product name
- `per_page` (optional): Items per page (default: 15)

**Success Response (200)** - Paginated

```json
{
  "current_page": 1,
  "data": [
    {
      "id": 1,
      "name": "Pesticide A",
      "code": "PEST-A",
      "base_price": 500.00,
      "unit": { "id": 1, "name": "Kg" },
      "stateRates": [ /* state-specific rates */ ]
    }
  ],
  "per_page": 15,
  "total": 100
}
```

**Access Control**:
- **Users**: See products from their organization
- **Dealers**: See all active products with calculated rates based on their state

#### Get Product Details

**Endpoint**: `GET /api/products/{id}` (Bearer token required, All authenticated users)

**cURL**

```bash
curl "{BASE_URL}/api/products/1" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer 1|xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"
```

**Success Response (200)**

```json
{
  "product": {
    "id": 1,
    "name": "Pesticide A",
    "code": "PEST-A",
    "description": "Product description",
    "base_price": 500.00,
    "gst_rate": 18.00,
    "unit": { /* unit details */ },
    "stateRates": [
      {
        "id": 1,
        "state_id": 1,
        "rate": 520.00,
        "state": { "name": "Maharashtra" }
      }
    ]
  }
}
```

#### Create Product

**Endpoint**: `POST /api/products` (Bearer token required, Users: admin/sales_officer)

**cURL**

```bash
curl -X POST "{BASE_URL}/api/products" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer 1|xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx" \
  -d '{
    "name": "New Product",
    "code": "PROD-001",
    "description": "Product description",
    "base_price": 1000.00,
    "unit_id": 1,
    "gst_rate": 18.00,
    "is_active": true
  }'
```

**Request Body**

```json
{
  "name": "New Product",
  "code": "PROD-001",
  "description": "Product description",
  "base_price": 1000.00,
  "unit_id": 1,
  "gst_rate": 18.00,
  "is_active": true
}
```

**Success Response (201)**

```json
{
  "message": "Product created successfully",
  "product": { /* created product */ }
}
```

#### Update Product

**Endpoint**: `PUT /api/products/{id}` (Bearer token required, Users: admin/sales_officer)

**cURL**

```bash
curl -X PUT "{BASE_URL}/api/products/1" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer 1|xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx" \
  -d '{
    "name": "Updated Product Name",
    "base_price": 1200.00
  }'
```

**Request Body** (all fields optional)

```json
{
  "name": "Updated Product Name",
  "code": "PROD-002",
  "description": "Updated description",
  "base_price": 1200.00,
  "unit_id": 2,
  "gst_rate": 12.00,
  "is_active": false
}
```

**Success Response (200)**

```json
{
  "message": "Product updated successfully",
  "product": { /* updated product */ }
}
```

#### Delete Product

**Endpoint**: `DELETE /api/products/{id}` (Bearer token required, Users: admin only)

**cURL**

```bash
curl -X DELETE "{BASE_URL}/api/products/1" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer 1|xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"
```

**Success Response (200)**

```json
{
  "message": "Product deleted successfully"
}
```

---

### Order APIs

#### Create Order (Place Order)

**Endpoint**: `POST /api/orders` (Bearer token required, Dealers only)

**Option 1: Place Order from Cart**

**cURL**

```bash
curl -X POST "{BASE_URL}/api/orders" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer 1|xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx" \
  -d '{
    "use_cart": true
  }'
```

**Request Body**

```json
{
  "use_cart": true
}
```

**Notes**: 
- Uses all items from dealer's cart
- Cart is automatically cleared after order is created
- Returns 422 if cart is empty

**Option 2: Place Order with Direct Items**

**cURL**

```bash
curl -X POST "{BASE_URL}/api/orders" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer 1|xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx" \
  -d '{
    "cart_items": [
      { "product_id": 1, "quantity": 2 },
      { "product_id": 5, "quantity": 1 }
    ]
  }'
```

**Request Body**

```json
{
  "cart_items": [
    { "product_id": 1, "quantity": 2 },
    { "product_id": 5, "quantity": 1 }
  ]
}
```

**Pricing Logic**:
- For each item, rate is picked from `product_state_rates` for the dealer's `state_id` if available, otherwise `products.base_price`
- `subtotal` = sum of `quantity × rate`
- Order totals/taxes are calculated automatically by observers

**Success Response (201)**

```json
{
  "message": "Order created successfully",
  "order": {
    "id": 123,
    "order_number": "ORD-ABC123",
    "dealer_id": 1,
    "subtotal": 2000.00,
    "discount_amount": 0.00,
    "taxable_amount": 2000.00,
    "cgst_amount": 180.00,
    "sgst_amount": 180.00,
    "igst_amount": 0.00,
    "grand_total": 2360.00,
    "status": "pending",
    "items": [ /* order items with products */ ],
    "dealer": { /* dealer details */ },
    "organization": { /* organization details */ }
  }
}
```

#### Order History (List Orders)

**Endpoint**: `GET /api/orders` (Bearer token required, All authenticated users)

**cURL**

```bash
# For Dealers
curl "{BASE_URL}/api/orders" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer 1|xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"

# For Users (with filters)
curl "{BASE_URL}/api/orders?dealer_id=1&status=pending&per_page=20" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer 1|xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"
```

**Query Parameters** (for Users only)

- `dealer_id` (optional): Filter by dealer
- `status` (optional): Filter by status (pending, confirmed, dispatched, delivered, cancelled)
- `per_page` (optional): Items per page (default: 15)

**Access Control**:
- **Dealers**: See only their own orders
- **Users** (admin/sales_officer/accountant): See orders from their organization

**Success Response (200)** - Paginated

```json
{
  "current_page": 1,
  "data": [ /* orders array */ ],
  "per_page": 15,
  "total": 50
}
```

#### Get Order Details

**Endpoint**: `GET /api/orders/{id}` (Bearer token required, All authenticated users)

**cURL**

```bash
curl "{BASE_URL}/api/orders/123" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer 1|xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"
```

**Success Response (200)**

```json
{
  "order": {
    "id": 123,
    "order_number": "ORD-ABC123",
    "dealer": { /* dealer details */ },
    "items": [
      {
        "id": 1,
        "product_id": 1,
        "quantity": 2,
        "rate": 1000.00,
        "subtotal": 2000.00,
        "product": { /* product details */ }
      }
    ],
    "dispatches": [ /* dispatches array */ ],
    "payments": [ /* payments array */ ],
    "organization": { /* organization details */ }
  }
}
```

#### Update Order

**Endpoint**: `PUT /api/orders/{id}` (Bearer token required, Users: admin/sales_officer)

**cURL**

```bash
curl -X PUT "{BASE_URL}/api/orders/123" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer 1|xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx" \
  -d '{
    "status": "confirmed"
  }'
```

**Request Body**

```json
{
  "status": "confirmed"
}
```

**Allowed Status Values**: `pending`, `confirmed`, `cancelled`

**Notes**: Only pending orders can be updated.

**Success Response (200)**

```json
{
  "message": "Order updated successfully",
  "order": { /* updated order */ }
}
```

#### Delete Order

**Endpoint**: `DELETE /api/orders/{id}` (Bearer token required, Users: admin only)

**cURL**

```bash
curl -X DELETE "{BASE_URL}/api/orders/123" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer 1|xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"
```

**Notes**: Only pending orders can be deleted.

**Success Response (200)**

```json
{
  "message": "Order deleted successfully"
}
```

---

### Dispatch APIs

#### Create Dispatch

**Endpoint**: `POST /api/dispatches` (Bearer token required, Users: admin/dispatch_officer)

**cURL**

```bash
curl -X POST "{BASE_URL}/api/dispatches" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer 1|xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx" \
  -d '{
    "order_id": 123,
    "lr_number": "LR-001",
    "transporter_name": "ABC Logistics",
    "vehicle_number": "MH12AB1234",
    "dispatched_at": "2026-01-17 10:30:00"
  }'
```

**Request Body**

```json
{
  "order_id": 123,
  "lr_number": "LR-001",
  "transporter_name": "ABC Logistics",
  "vehicle_number": "MH12AB1234",
  "dispatched_at": "2026-01-17 10:30:00"
}
```

**Notes**:
- `dispatched_at` is optional (defaults to current time)
- If order status is `cancelled`, returns 422
- On success, dispatch status is set to `dispatched`
- If order is `pending` or `confirmed`, order status is updated to `dispatched`

**Success Response (201)**

```json
{
  "message": "Dispatch created successfully",
  "dispatch": {
    "id": 10,
    "dispatch_number": "DISP-ABC123",
    "order_id": 123,
    "lr_number": "LR-001",
    "transporter_name": "ABC Logistics",
    "vehicle_number": "MH12AB1234",
    "status": "dispatched",
    "order": { /* order with items */ }
  }
}
```

#### List Dispatches for Order

**Endpoint**: `GET /api/orders/{orderId}/dispatches` (Bearer token required, All authenticated users)

**cURL**

```bash
curl "{BASE_URL}/api/orders/123/dispatches" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer 1|xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"
```

**Access Control**:
- **Dealers**: Can see dispatches for their own orders
- **Users** (admin/dispatch_officer/sales_officer): Can see dispatches for orders in their organization

**Success Response (200)**

```json
{
  "dispatches": [
    {
      "id": 10,
      "dispatch_number": "DISP-ABC123",
      "lr_number": "LR-001",
      "status": "dispatched",
      "dispatched_at": "2026-01-17T10:30:00.000000Z"
    }
  ]
}
```

#### Update Dispatch

**Endpoint**: `PUT /api/dispatches/{id}` (Bearer token required, Users: admin/dispatch_officer)

**cURL**

```bash
curl -X PUT "{BASE_URL}/api/dispatches/10" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer 1|xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx" \
  -d '{
    "status": "in_transit",
    "lr_number": "LR-002"
  }'
```

**Request Body** (all fields optional)

```json
{
  "status": "in_transit",
  "lr_number": "LR-002",
  "transporter_name": "XYZ Logistics",
  "vehicle_number": "MH12AB9999"
}
```

**Allowed Status Values**: `pending`, `dispatched`, `in_transit`, `delivered`

**Notes**: If status becomes `delivered`, the related order is updated to `delivered`.

**Success Response (200)**

```json
{
  "message": "Dispatch updated successfully",
  "dispatch": { /* updated dispatch */ }
}
```

---

### Payment APIs

#### List Payments

**Endpoint**: `GET /api/payments` (Bearer token required, Users: admin/accountant)

**cURL**

```bash
curl "{BASE_URL}/api/payments?order_id=123&status=completed&per_page=20" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer 1|xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"
```

**Query Parameters**

- `order_id` (optional): Filter by order
- `status` (optional): Filter by status (pending, completed, failed, refunded)
- `per_page` (optional): Items per page (default: 15)

**Success Response (200)** - Paginated

```json
{
  "current_page": 1,
  "data": [
    {
      "id": 1,
      "order_id": 123,
      "amount": 2360.00,
      "payment_method": "bank_transfer",
      "status": "completed",
      "payment_date": "2026-01-17",
      "order": { /* order with dealer */ }
    }
  ],
  "per_page": 15,
  "total": 50
}
```

#### Get Payment Details

**Endpoint**: `GET /api/payments/{id}` (Bearer token required, Users: admin/accountant)

**cURL**

```bash
curl "{BASE_URL}/api/payments/1" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer 1|xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"
```

**Success Response (200)**

```json
{
  "payment": {
    "id": 1,
    "order_id": 123,
    "amount": 2360.00,
    "payment_method": "bank_transfer",
    "status": "completed",
    "payment_date": "2026-01-17",
    "reference_number": "TXN123456",
    "order": { /* order with items and dealer */ }
  }
}
```

#### Create Payment

**Endpoint**: `POST /api/payments` (Bearer token required, Users: admin/accountant)

**cURL**

```bash
curl -X POST "{BASE_URL}/api/payments" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer 1|xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx" \
  -d '{
    "order_id": 123,
    "amount": 2360.00,
    "payment_method": "bank_transfer",
    "payment_date": "2026-01-17",
    "reference_number": "TXN123456",
    "notes": "Payment received"
  }'
```

**Request Body**

```json
{
  "order_id": 123,
  "amount": 2360.00,
  "payment_method": "bank_transfer",
  "payment_date": "2026-01-17",
  "reference_number": "TXN123456",
  "notes": "Payment received"
}
```

**Payment Methods**: `cash`, `bank_transfer`, `cheque`, `online`

**Success Response (201)**

```json
{
  "message": "Payment created successfully",
  "payment": { /* created payment */ }
}
```

---

### Report APIs

#### Sales Report

**Endpoint**: `GET /api/reports/sales` (Bearer token required, Users: admin/accountant)

**cURL**

```bash
curl "{BASE_URL}/api/reports/sales?start_date=2026-01-01&end_date=2026-01-31" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer 1|xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"
```

**Query Parameters**

- `start_date` (optional): Start date (default: start of current month)
- `end_date` (optional): End date (default: end of current month)

**Success Response (200)**

```json
{
  "period": {
    "start_date": "2026-01-01",
    "end_date": "2026-01-31"
  },
  "summary": {
    "total_orders": 150,
    "total_amount": 2500000.00,
    "total_paid": 2400000.00,
    "by_status": {
      "pending": { "count": 10, "amount": 150000.00 },
      "confirmed": { "count": 5, "amount": 75000.00 },
      "dispatched": { "count": 20, "amount": 300000.00 },
      "delivered": { "count": 115, "amount": 1975000.00 }
    }
  },
  "orders": [ /* all orders in period */ ]
}
```

#### Dealer Performance Report

**Endpoint**: `GET /api/reports/dealer-performance` (Bearer token required, Users: admin/sales_officer)

**cURL**

```bash
curl "{BASE_URL}/api/reports/dealer-performance?start_date=2026-01-01&end_date=2026-01-31" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer 1|xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"
```

**Query Parameters**

- `start_date` (optional): Start date (default: start of current month)
- `end_date` (optional): End date (default: end of current month)

**Success Response (200)**

```json
{
  "period": {
    "start_date": "2026-01-01",
    "end_date": "2026-01-31"
  },
  "dealers": [
    {
      "dealer_id": 1,
      "order_count": 25,
      "total_amount": 125000.00,
      "dealer": { /* dealer details */ }
    }
  ]
}
```

---

## Error Handling

### Common HTTP Status Codes

- **200 OK**: Request successful
- **201 Created**: Resource created successfully
- **401 Unauthorized**: Missing or invalid authentication token
- **403 Forbidden**: Authenticated but not authorized (wrong role/user type)
- **404 Not Found**: Resource not found
- **422 Unprocessable Entity**: Validation errors
- **500 Internal Server Error**: Server error

### Error Response Format

**Validation Error (422)**

```json
{
  "message": "The given data was invalid.",
  "errors": {
    "mobile": ["The mobile field is required."],
    "password": ["The password must be at least 8 characters."]
  }
}
```

**Unauthorized (401)**

```json
{
  "message": "Unauthenticated."
}
```

**Forbidden (403)**

```json
{
  "message": "Forbidden"
}
```

**Not Found (404)**

```json
{
  "message": "No query results for model [App\\Models\\Order] 123"
}
```

---

## Summary of Access Control

### Dealers

- ✅ Own profile (view/update)
- ✅ Own orders (view/create)
- ✅ Products (view)
- ✅ Own dispatches (view)
- ❌ Payments (no access)
- ❌ Reports (no access)
- ❌ Other dealers (no access)
- ❌ User management (no access)

### Users - Admin

- ✅ Full access to all APIs
- ✅ Users, Products, Dealers, Orders, Dispatches, Payments, Reports

### Users - Accountant

- ✅ Payments (view/create)
- ✅ Reports (view)
- ✅ Orders (view)
- ✅ Dealers (view)
- ❌ Orders (create/update/delete)
- ❌ Dispatches (no access)
- ❌ Products (no access)

### Users - Sales Officer

- ✅ Orders (manage)
- ✅ Dealers (manage)
- ✅ Products (manage)
- ✅ Dispatches (view)
- ❌ Payments (no access)
- ❌ Reports (no access)

### Users - Dispatch Officer

- ✅ Dispatches (manage)
- ✅ Orders (view)
- ❌ Payments (no access)
- ❌ Reports (no access)
- ❌ Dealers (no access)
- ❌ Products (no access)

---

## Quick Reference

### Endpoint Summary

| Method | Endpoint | Auth | User Type | Role Required |
|--------|----------|------|-----------|---------------|
| POST | `/api/login` | No | All | - |
| POST | `/api/logout` | Yes | All | - |
| GET | `/api/me` | Yes | All | - |
| GET | `/api/dashboard` | Yes | All | - |
| GET | `/api/users/profile` | Yes | User | - |
| PUT | `/api/users/profile` | Yes | User | - |
| POST | `/api/dealers/register` | No | - | - |
| GET | `/api/dealers/profile` | Yes | Dealer | - |
| PUT | `/api/dealers/profile` | Yes | Dealer | - |
| GET | `/api/dealers` | Yes | User | admin/sales/accountant |
| GET | `/api/dealers/{id}` | Yes | User | admin/sales/accountant |
| GET | `/api/products/categories` | Yes | All | - |
| GET | `/api/products` | Yes | All | - |
| GET | `/api/products/{id}` | Yes | All | - |
| POST | `/api/products` | Yes | User | admin/sales |
| PUT | `/api/products/{id}` | Yes | User | admin/sales |
| DELETE | `/api/products/{id}` | Yes | User | admin |
| POST | `/api/orders` | Yes | Dealer | - |
| GET | `/api/orders` | Yes | All | - |
| GET | `/api/cart` | Yes | Dealer | - |
| POST | `/api/cart` | Yes | Dealer | - |
| PUT | `/api/cart/{id}` | Yes | Dealer | - |
| DELETE | `/api/cart/{id}` | Yes | Dealer | - |
| DELETE | `/api/cart` | Yes | Dealer | - |
| GET | `/api/orders/{id}` | Yes | All | - |
| PUT | `/api/orders/{id}` | Yes | User | admin/sales |
| DELETE | `/api/orders/{id}` | Yes | User | admin |
| POST | `/api/dispatches` | Yes | User | admin/dispatch |
| GET | `/api/orders/{orderId}/dispatches` | Yes | All | - |
| PUT | `/api/dispatches/{id}` | Yes | User | admin/dispatch |
| GET | `/api/payments` | Yes | User | admin/accountant |
| GET | `/api/payments/{id}` | Yes | User | admin/accountant |
| POST | `/api/payments` | Yes | User | admin/accountant |
| GET | `/api/reports/sales` | Yes | User | admin/accountant |
| GET | `/api/reports/dealer-performance` | Yes | User | admin/sales |

---

**Last Updated**: 2026-01-17
