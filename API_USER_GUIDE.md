# API User Guide - Complete Reference

A comprehensive guide to using the AgriSales CRM API. This API supports multiple user types with role-based access control.

## 📋 Table of Contents

1. [Quick Start](#quick-start)
2. [Authentication](#authentication)
3. [Base URL & Headers](#base-url--headers)
4. [User Types & Permissions](#user-types--permissions)
5. [API Endpoints](#api-endpoints)
   - [Authentication](#auth-apis)
   - [Dashboard](#dashboard-apis)
   - [Banners](#banner-apis)
   - [Crops](#crop-apis)
   - [Products](#product-apis)
   - [Orders](#order-apis)
   - [Cart](#cart-apis)
   - [Dealers](#dealer-apis)
   - [Dispatches](#dispatch-apis)
   - [Payments](#payment-apis)
   - [Reports](#report-apis)
   - [User Profile](#user-profile-apis)
6. [Error Handling](#error-handling)
7. [Quick Reference](#quick-reference)

---

## 🚀 Quick Start

### 1. Login (Simplified)

Just send mobile number and password. The system automatically detects if you're a User or Dealer.

```bash
curl -X POST "http://localhost/agrosalescrm/public/api/login" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{
      "mobile": "9876543210",
      "password": "password"
  }'
```

**Response:**
```json
{
  "user_type": "dealer",
  "dealer": { /* dealer info */ },
  "organization": { /* organization info */ },
  "roles": ["dealer"],
  "permissions": ["view_orders", "create_orders"],
  "token": "1|xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"
}
```

### 2. Use the Token

Add the token to all subsequent requests:

```bash
curl "http://localhost/agrosalescrm/public/api/dashboard" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer 1|xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"
```

### 3. Check User Type

After login, check `user_type` to route to the correct dashboard:
- `"dealer"` → Dealer dashboard
- `"user"` → Check `roles` array for specific role (admin, accountant, sales_officer, dispatch_officer)

---

## 🔐 Authentication

### Login

**Endpoint:** `POST /api/login` (Public - No authentication required)

**Request Body:**
```json
{
  "mobile": "9876543210",
  "password": "password"
}
```

**What happens:**
- System automatically detects if mobile belongs to a User or Dealer
- For Dealers: Organization is automatically determined from their order history
- For Users: Organization comes from their user record

**Success Response (200):**

*For Dealers:*
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
    "zone": { "id": 1, "name": "Zone 1" }
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

*For Users:*
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
    "manage_users", "manage_products", "manage_dealers", 
    "manage_orders", "manage_dispatches", "manage_payments", 
    "view_reports", "manage_settings"
  ],
  "token": "1|xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"
}
```

**Error Response (422):**
```json
{
  "message": "The provided credentials are incorrect.",
  "errors": {
    "mobile": ["The provided credentials are incorrect."]
  }
}
```

### Get Current User Info

**Endpoint:** `GET /api/me` (Requires authentication)

Returns the same structure as login response. Useful for checking authentication status.

```bash
curl "http://localhost/agrosalescrm/public/api/me" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer {token}"
```

### Logout

**Endpoint:** `POST /api/logout` (Requires authentication)

Invalidates the current token.

```bash
curl -X POST "http://localhost/agrosalescrm/public/api/logout" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer {token}"
```

**Response:**
```json
{
  "message": "Logged out successfully"
}
```

---

## 🌐 Base URL & Headers

### Base URL

Set your base URL based on your environment:

- **Local Development (Artisan):** `http://127.0.0.1:8000`
- **XAMPP:** `http://localhost/agrosalescrm/public`
- **Production:** Your domain URL

All endpoints are under `/api` prefix: `{BASE_URL}/api/...`

### Required Headers

**For all requests:**
```
Accept: application/json
Content-Type: application/json
```

**For authenticated requests:**
```
Authorization: Bearer {token}
```

---

## 👥 User Types & Permissions

### User Type: `"dealer"`

- **Access:** Own orders, profile management, create orders, view products
- **Dashboard:** Dealer dashboard
- **Permissions:** `view_orders`, `create_orders`, `view_profile`, `update_profile`

### User Type: `"user"` with Roles

#### Role: `"admin"`
- **Access:** Full access to all APIs
- **Dashboard:** Admin dashboard
- **Permissions:** All permissions

#### Role: `"accountant"`
- **Access:** Payments, Reports, Orders (view), Dealers (view)
- **Dashboard:** Accountant dashboard
- **Permissions:** `view_orders`, `view_dealers`, `manage_payments`, `view_reports`

#### Role: `"sales_officer"`
- **Access:** Orders, Dealers, Products, Dispatches (view)
- **Dashboard:** Sales dashboard
- **Permissions:** `manage_orders`, `manage_dealers`, `view_products`, `view_dispatches`

#### Role: `"dispatch_officer"`
- **Access:** Dispatches, Orders (view)
- **Dashboard:** Dispatch dashboard
- **Permissions:** `manage_dispatches`, `view_orders`

---

## 📡 API Endpoints

### Auth APIs

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| POST | `/api/login` | No | Login (auto-detects user type) |
| POST | `/api/logout` | Yes | Logout |
| GET | `/api/me` | Yes | Get current user/dealer info |

---

### Dashboard APIs

#### Get Dashboard Data

**Endpoint:** `GET /api/dashboard` (Requires authentication)

Returns statistics and recent data based on user type and role.

**Response for Admin:**
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
    "dispatches": { "total": 120, "pending": 5, "dispatched": 15 },
    "payments": { "total": 200, "total_amount": 2400000.00 },
    "dealers": { "total": 50, "active": 45 }
  },
  "recent_orders": [ /* 5 most recent orders */ ]
}
```

**Response for Dealer:**
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

---

### Crop APIs

#### Get All Crops with Products

**Endpoint:** `GET /api/crops` (Requires authentication)

Returns all active crops with their assigned products.

**Query Parameters:**
- `search` (optional): Search crops by name

**Response:**
```json
{
  "crops": [
    {
      "id": 1,
      "unique_id": "CROP-ABC12345",
      "name": "Wheat",
      "image": "crops/wheat.jpg",
      "description": "Wheat crop products",
      "is_active": true,
      "products": [
        {
          "id": 1,
          "name": "Wheat Fertilizer",
          "code": "FERT-W001",
          "description": "Fertilizer for wheat crops",
          "contains_description": "NPK 19:19:19",
          "base_price": 500.00,
          "unit_per_case": 12.00,
          "calculated_rate": 520.00,
          "category": {
            "id": 2,
            "name": "Fertilizers"
          },
          "unit": { "id": 1, "name": "Kg" }
        }
      ]
    }
  ]
}
```

#### Get Specific Crop

**Endpoint:** `GET /api/crops/{id}` (Requires authentication)

Returns a single crop with all its products.

#### Get Products by Crop

**Endpoint:** `GET /api/crops/{id}/products` (Requires authentication)

Returns paginated list of products for a specific crop.

**Query Parameters:**
- `search` (optional): Search products by name
- `per_page` (optional): Items per page (default: 15)

**Response:**
```json
{
  "current_page": 1,
  "data": [ /* products */ ],
  "per_page": 15,
  "total": 50
}
```

---

### Banner APIs

#### Get Active Banners

**Endpoint:** `GET /api/banners` (Public - No authentication required)

Returns all active banners that are currently within their date range (if set).

**Query Parameters:**
- `include_inactive` (optional): Include inactive banners (admin only, requires authentication)

**Response:**
```json
{
  "banners": [
    {
      "id": 1,
      "title": "Summer Sale",
      "description": "Special offers this summer",
      "image": "banners/summer-sale.jpg",
      "image_url": "http://localhost/storage/banners/summer-sale.jpg",
      "link": "https://example.com/sale",
      "sort_order": 1,
      "is_active": true,
      "start_date": "2026-01-01",
      "end_date": "2026-03-31"
    }
  ]
}
```

**Note:** Banners are automatically filtered to show only:
- Active banners (`is_active = true`)
- Banners within their date range (if start_date/end_date are set)
- Ordered by `sort_order`

#### Get Specific Banner

**Endpoint:** `GET /api/banners/{id}` (Public - No authentication required)

Returns a single banner with full image URL.

---

### Product APIs

#### Get Product Categories

**Endpoint:** `GET /api/products/categories` (Requires authentication)

**Response:**
```json
{
  "categories": [
    { "id": 1, "name": "Pesticides", "slug": "pesticides" },
    { "id": 2, "name": "Fertilizers", "slug": "fertilizers" },
    { "id": 3, "name": "Seeds", "slug": "seeds" }
  ]
}
```

#### List Products

**Endpoint:** `GET /api/products` (Requires authentication)

**Query Parameters:**
- `search` (optional): Search by product name
- `category_id` (optional): Filter products by category ID
- `crop_id` (optional): Filter products by crop ID
- `per_page` (optional): Items per page (default: 15)

**Response:**
```json
{
  "current_page": 1,
  "data": [
    {
      "id": 1,
      "name": "Pesticide A",
      "code": "PEST-A",
      "description": "Product description",
      "contains_description": "Contains active ingredients...",
      "base_price": 500.00,
      "unit_per_case": 12.00,
      "calculated_rate": 520.00,
      "category": {
        "id": 1,
        "name": "Pesticides",
        "image": "categories/pesticides.jpg"
      },
      "unit": { "id": 1, "name": "Kg" }
    }
  ],
  "per_page": 15,
  "total": 100
}
```

**Access Control:**
- **Users:** See products from their organization
- **Dealers:** See all active products with state-specific rates

**Product Fields:**
- `id`: Product ID
- `name`: Product name
- `code`: Product code
- `description`: Product description
- `contains_description`: What the product contains (ingredients, components, etc.)
- `category`: Category object (if assigned)
- `base_price`: Base price per unit
- `unit_per_case`: Number of units in one case/pack
- `unit`: Unit object (Kg, Liter, etc.)
- `calculated_rate`: State-specific rate (for dealers)
- `stateRates`: Array of state-specific rates

#### Get Product Details

**Endpoint:** `GET /api/products/{id}` (Requires authentication)

**Response:**
```json
{
  "product": {
    "id": 1,
    "name": "Pesticide A",
    "code": "PEST-A",
    "description": "Product description",
    "contains_description": "Contains active ingredients...",
    "base_price": 500.00,
    "unit_per_case": 12.00,
    "gst_rate": 18.00,
    "category": {
      "id": 1,
      "name": "Pesticides",
      "image": "categories/pesticides.jpg"
    },
    "unit": { "id": 1, "name": "Kg" },
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

**Endpoint:** `POST /api/products` (Requires authentication, Roles: admin/sales_officer)

**Request:**
```json
{
  "name": "New Product",
  "code": "PROD-001",
  "description": "Product description",
  "contains_description": "Contains active ingredients...",
  "category_id": 1,
  "base_price": 1000.00,
  "unit_per_case": 12.00,
  "unit_id": 1,
  "gst_rate": 18.00,
  "is_active": true
}
```

**Request Fields:**
- `name` (required): Product name
- `code` (optional): Product code (must be unique)
- `description` (optional): Product description
- `contains_description` (optional): What the product contains
- `category_id` (optional): Category ID
- `base_price` (required): Base price
- `unit_per_case` (optional): Number of units per case/pack
- `unit_id` (required): Unit ID
- `gst_rate` (required): GST rate (0-100)
- `is_active` (optional): Active status (default: true)

#### Update Product

**Endpoint:** `PUT /api/products/{id}` (Requires authentication, Roles: admin/sales_officer)

#### Delete Product

**Endpoint:** `DELETE /api/products/{id}` (Requires authentication, Role: admin)

---

### Order APIs

#### Create Order

**Endpoint:** `POST /api/orders` (Requires authentication, Dealers only)

**Request:**
```json
{
  "items": [
    {
      "product_id": 1,
      "product_size_id": null,
      "quantity": 10,
      "rate": 500.00
    }
  ],
  "notes": "Please deliver by Friday"
}
```

**Response:**
```json
{
  "message": "Order created successfully",
  "order": {
    "id": 1,
    "order_number": "ORD-2026-001",
    "subtotal": 5000.00,
    "grand_total": 5900.00,
    "status": "pending"
  }
}
```

#### List Orders

**Endpoint:** `GET /api/orders` (Requires authentication)

**Query Parameters:**
- `status` (optional): Filter by status (pending, confirmed, dispatched, delivered, cancelled)
- `per_page` (optional): Items per page (default: 15)

**Access Control:**
- **Dealers:** See only their own orders
- **Users:** See orders based on role and organization

#### Get Order Details

**Endpoint:** `GET /api/orders/{id}` (Requires authentication)

#### Update Order

**Endpoint:** `PUT /api/orders/{id}` (Requires authentication, Roles: admin/sales_officer)

#### Delete Order

**Endpoint:** `DELETE /api/orders/{id}` (Requires authentication, Role: admin)

---

### Cart APIs

#### Get Cart Items

**Endpoint:** `GET /api/cart` (Requires authentication, Dealers only)

**Response:**
```json
{
  "cart": [
    {
      "id": 1,
      "product": { "id": 1, "name": "Product A" },
      "quantity": 5,
      "rate": 500.00,
      "subtotal": 2500.00
    }
  ],
  "total": 2500.00
}
```

#### Add to Cart

**Endpoint:** `POST /api/cart` (Requires authentication, Dealers only)

**Request:**
```json
{
  "product_id": 1,
  "product_size_id": null,
  "quantity": 5
}
```

#### Update Cart Item

**Endpoint:** `PUT /api/cart/{id}` (Requires authentication, Dealers only)

**Request:**
```json
{
  "quantity": 10
}
```

#### Remove from Cart

**Endpoint:** `DELETE /api/cart/{id}` (Requires authentication, Dealers only)

#### Clear Cart

**Endpoint:** `DELETE /api/cart` (Requires authentication, Dealers only)

---

### Dealer APIs

#### Register Dealer

**Endpoint:** `POST /api/dealers/register` (Public)

**Request:**
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

#### Get Dealer Profile

**Endpoint:** `GET /api/dealers/profile` (Requires authentication, Dealers only)

#### Update Dealer Profile

**Endpoint:** `PUT /api/dealers/profile` (Requires authentication, Dealers only)

#### List Dealers

**Endpoint:** `GET /api/dealers` (Requires authentication, Roles: admin/sales_officer/accountant)

#### Get Dealer Details

**Endpoint:** `GET /api/dealers/{id}` (Requires authentication, Roles: admin/sales_officer/accountant)

---

### Dispatch APIs

#### Create Dispatch

**Endpoint:** `POST /api/dispatches` (Requires authentication, Roles: admin/dispatch_officer)

**Request:**
```json
{
  "order_id": 1,
  "dispatched_items": [
    {
      "order_item_id": 1,
      "quantity": 10
    }
  ],
  "dispatch_date": "2026-01-17",
  "tracking_number": "TRACK123",
  "carrier": "Fast Delivery",
  "notes": "Handle with care"
}
```

#### Get Order Dispatches

**Endpoint:** `GET /api/orders/{orderId}/dispatches` (Requires authentication)

#### Update Dispatch

**Endpoint:** `PUT /api/dispatches/{id}` (Requires authentication, Roles: admin/dispatch_officer)

---

### Payment APIs

#### List Payments

**Endpoint:** `GET /api/payments` (Requires authentication, Roles: admin/accountant)

**Query Parameters:**
- `order_id` (optional): Filter by order
- `status` (optional): Filter by status
- `per_page` (optional): Items per page

#### Get Payment Details

**Endpoint:** `GET /api/payments/{id}` (Requires authentication, Roles: admin/accountant)

#### Create Payment

**Endpoint:** `POST /api/payments` (Requires authentication, Roles: admin/accountant)

**Request:**
```json
{
  "order_id": 1,
  "amount": 5000.00,
  "payment_method": "bank_transfer",
  "payment_date": "2026-01-17",
  "reference_number": "TXN123456",
  "notes": "Payment received"
}
```

---

### Report APIs

#### Sales Report

**Endpoint:** `GET /api/reports/sales` (Requires authentication, Roles: admin/accountant)

**Query Parameters:**
- `start_date` (optional): Start date (YYYY-MM-DD)
- `end_date` (optional): End date (YYYY-MM-DD)
- `group_by` (optional): day, week, month, year

#### Dealer Performance Report

**Endpoint:** `GET /api/reports/dealer-performance` (Requires authentication, Roles: admin/sales_officer)

**Query Parameters:**
- `start_date` (optional): Start date
- `end_date` (optional): End date
- `dealer_id` (optional): Filter by dealer

---

### User Profile APIs

#### Get User Profile

**Endpoint:** `GET /api/users/profile` (Requires authentication, Users only)

#### Update User Profile

**Endpoint:** `PUT /api/users/profile` (Requires authentication, Users only)

**Request:**
```json
{
  "name": "Updated Name",
  "email": "newemail@example.com",
  "mobile": "9876543219"
}
```

---

## ⚠️ Error Handling

### Common HTTP Status Codes

- **200 OK:** Request successful
- **201 Created:** Resource created successfully
- **400 Bad Request:** Invalid request data
- **401 Unauthorized:** Missing or invalid token
- **403 Forbidden:** Insufficient permissions
- **404 Not Found:** Resource not found
- **422 Unprocessable Entity:** Validation errors
- **500 Internal Server Error:** Server error

### Error Response Format

```json
{
  "message": "The given data was invalid.",
  "errors": {
    "mobile": ["The mobile field is required."],
    "password": ["The password must be at least 8 characters."]
  }
}
```

### Authentication Errors

**401 Unauthorized:**
```json
{
  "message": "Unauthenticated."
}
```

**403 Forbidden:**
```json
{
  "message": "Forbidden"
}
```

---

## 📚 Quick Reference

### All Endpoints Summary

| Method | Endpoint | Auth | User Type | Role Required |
|--------|----------|------|-----------|---------------|
| **Authentication** |
| POST | `/api/login` | No | All | - |
| POST | `/api/logout` | Yes | All | - |
| GET | `/api/me` | Yes | All | - |
| **Dashboard** |
| GET | `/api/dashboard` | Yes | All | - |
| **Banners** |
| GET | `/api/banners` | No | - | - |
| GET | `/api/banners/{id}` | No | - | - |
| **Banners** |
| GET | `/api/banners` | No | - | - |
| GET | `/api/banners/{id}` | No | - | - |
| **Crops** |
| GET | `/api/crops` | Yes | All | - |
| GET | `/api/crops/{id}` | Yes | All | - |
| GET | `/api/crops/{id}/products` | Yes | All | - |
| **Products** |
| GET | `/api/products/categories` | Yes | All | - |
| GET | `/api/products` | Yes | All | - |
| GET | `/api/products/{id}` | Yes | All | - |
| POST | `/api/products` | Yes | User | admin/sales |
| PUT | `/api/products/{id}` | Yes | User | admin/sales |
| DELETE | `/api/products/{id}` | Yes | User | admin |
| **Orders** |
| POST | `/api/orders` | Yes | Dealer | - |
| GET | `/api/orders` | Yes | All | - |
| GET | `/api/orders/{id}` | Yes | All | - |
| PUT | `/api/orders/{id}` | Yes | User | admin/sales |
| DELETE | `/api/orders/{id}` | Yes | User | admin |
| **Cart** |
| GET | `/api/cart` | Yes | Dealer | - |
| POST | `/api/cart` | Yes | Dealer | - |
| PUT | `/api/cart/{id}` | Yes | Dealer | - |
| DELETE | `/api/cart/{id}` | Yes | Dealer | - |
| DELETE | `/api/cart` | Yes | Dealer | - |
| **Dealers** |
| POST | `/api/dealers/register` | No | - | - |
| GET | `/api/dealers/profile` | Yes | Dealer | - |
| PUT | `/api/dealers/profile` | Yes | Dealer | - |
| GET | `/api/dealers` | Yes | User | admin/sales/accountant |
| GET | `/api/dealers/{id}` | Yes | User | admin/sales/accountant |
| **Dispatches** |
| POST | `/api/dispatches` | Yes | User | admin/dispatch |
| GET | `/api/orders/{orderId}/dispatches` | Yes | All | - |
| PUT | `/api/dispatches/{id}` | Yes | User | admin/dispatch |
| **Payments** |
| GET | `/api/payments` | Yes | User | admin/accountant |
| GET | `/api/payments/{id}` | Yes | User | admin/accountant |
| POST | `/api/payments` | Yes | User | admin/accountant |
| **Reports** |
| GET | `/api/reports/sales` | Yes | User | admin/accountant |
| GET | `/api/reports/dealer-performance` | Yes | User | admin/sales |
| **User Profile** |
| GET | `/api/users/profile` | Yes | User | - |
| PUT | `/api/users/profile` | Yes | User | - |

---

## 🔑 Default Test Credentials

**Note:** These are for development only. Change passwords in production.

### Users (Password: `password`)
- **Admin:** Mobile `9876543210`
- **Accountant:** Mobile `9876543211`
- **Sales Officer:** Mobile `9876543212`
- **Dispatch Officer:** Mobile `9876543213`

### Dealers (Password: `password`)
- **Mumbai Agro Traders:** Mobile `9876543210`
- **Pune Farm Supplies:** Mobile `9876543211`
- **Ahmedabad Crop Care:** Mobile `9876543212`

---

**Last Updated:** 2026-01-18

---

## 📝 Recent Updates

### 2026-01-18
- ✅ **Banner Master**: Added banner management with public APIs for mobile app
- ✅ **Category Master**: Added category master with image support
- ✅ **Crop Master**: Added crop master with auto-generated unique IDs and product assignment
- ✅ **Product Enhancements**:
  - Added category assignment to products
  - Added "contains_description" field
  - Added "unit_per_case" field
- ✅ **Simplified Login**: Only mobile and password required (auto-detects user type)
- ✅ **Crop-Product Relationship**: Products can be assigned to crops for crop-wise product listing

### 2026-01-17
- ✅ Initial API documentation
