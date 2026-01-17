# API Report (Laravel `routes/api.php`)

This document lists the HTTP APIs currently exposed by this project.

- **Framework**: Laravel 11
- **Auth**: Laravel Sanctum (Bearer tokens)
- **Route prefix**: `/api` (Laravel default for `routes/api.php`)
- **Default content type**: JSON

## Endpoints

| Method | Path | Auth | Controller@method | Notes |
|---|---|---|---|---|
| POST | `/api/login` | Public | `AuthController@login` | Dealer login, returns Sanctum token |
| POST | `/api/dealers/register` | Public | `DealerController@register` | Dealer self-registration |
| POST | `/api/logout` | Bearer token | `AuthController@logout` | Deletes current token |
| GET | `/api/dealers/profile` | Bearer token | `DealerController@profile` | Returns authenticated dealer |
| PUT | `/api/dealers/profile` | Bearer token | `DealerController@update` | Partial update (name/email/address/city_id/pincode) |
| POST | `/api/orders` | Bearer token | `OrderController@create` | Creates an order from `cart_items[]` |
| GET | `/api/orders` | Bearer token | `OrderController@index` | Paginated list (15/page) for authenticated dealer |
| GET | `/api/orders/{id}` | Bearer token | `OrderController@show` | Order details for authenticated dealer |
| POST | `/api/dispatches` | Bearer token | `DispatchController@create` | Creates a dispatch for an order |
| GET | `/api/orders/{orderId}/dispatches` | Bearer token | `DispatchController@index` | List dispatches for an order |
| PUT | `/api/dispatches/{id}` | Bearer token | `DispatchController@update` | Update dispatch fields/status |

## Important behaviors (as implemented)

- **Login requires `organization_id`**: `/api/login` requires a valid `organizations.id`, and returns that organization in the response.
- **Order creation organization**: `/api/orders` currently uses the **first organization** in the DB (`Organization::first()`), not a request field.
- **Dispatch authorization**: Dispatch endpoints require auth, but do **not** currently verify the authenticated dealer owns the target order.

