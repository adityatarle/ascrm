# API Login & Registration Guide

This guide covers only the **Login** and **Registration** APIs for the AgroSales CRM system.

---

## Base URL

```
http://localhost/agrosalescrm/public/api/v1
```

**Note:** If your server is configured differently, adjust the base URL accordingly.

---

## 1. Login API

### Endpoint
```
POST /api/v1/login
```

### Full URL
```
http://localhost/agrosalescrm/public/api/v1/login
```

### Description
Login API automatically detects whether you are a **User** (admin/sales/dispatch/accountant) or a **Dealer** based on your mobile number and password. No need to specify user type or organization ID.

### Request Method
`POST`

### Headers
```
Content-Type: application/json
Accept: application/json
```

### Request Body
```json
{
    "mobile": "9876543210",
    "password": "your_password"
}
```

### Required Fields
- `mobile` (string) - Mobile number
- `password` (string) - Password

### Response Format

#### Success Response (200 OK)
```json
{
    "result": {
        "user_type": "user",
        "user": {
            "id": 1,
            "name": "Admin User",
            "email": "admin@example.com",
            "mobile": "9876543210",
            "created_at": "2025-01-18T10:00:00.000000Z",
            "updated_at": "2025-01-18T10:00:00.000000Z"
        },
        "organization": {
            "id": 1,
            "name": "AgriChemTech Maharashtra Pvt Ltd",
            "gstin": "27AABCU9601R1ZM",
            "address": "123 Industrial Area, Andheri East",
            "state_id": 1,
            "city_id": 1,
            "pincode": "400069",
            "phone": "+91-22-12345678",
            "email": "info@aglichemtech-mh.com",
            "created_at": "2025-11-16T13:36:43.000000Z",
            "updated_at": "2025-11-16T13:36:43.000000Z",
            "deleted_at": null
        },
        "roles": ["admin"],
        "token": "1|abcdefghijklmnopqrstuvwxyz1234567890"
    },
    "status": true,
    "message": "LOGIN SUCCESSFUL"
}
```

#### For Dealer Login
```json
{
    "result": {
        "user_type": "dealer",
        "dealer": {
            "id": 1,
            "name": "Dealer Name",
            "mobile": "9876543210",
            "email": "dealer@example.com",
            "gstin": "27AABCU9601R1ZM",
            "address": "Dealer Address",
            "zone_id": 1,
            "state_id": 1,
            "city_id": 1,
            "pincode": "400069",
            "is_active": true,
            "state": {
                "id": 1,
                "name": "Maharashtra"
            },
            "city": {
                "id": 1,
                "name": "Mumbai"
            },
            "zone": {
                "id": 1,
                "name": "Zone 1"
            }
        },
        "organization": {
            "id": 1,
            "name": "AgriChemTech Maharashtra Pvt Ltd",
            "gstin": "27AABCU9601R1ZM",
            "address": "123 Industrial Area, Andheri East",
            "state_id": 1,
            "city_id": 1,
            "pincode": "400069",
            "phone": "+91-22-12345678",
            "email": "info@aglichemtech-mh.com"
        },
        "roles": ["dealer"],
        "token": "1|abcdefghijklmnopqrstuvwxyz1234567890"
    },
    "status": true,
    "message": "LOGIN SUCCESSFUL"
}
```

#### Error Response (401 Unauthorized)
```json
{
    "result": null,
    "status": false,
    "message": "INVALID CREDENTIALS"
}
```

#### Error Response (422 Validation Error)
```json
{
    "result": null,
    "status": false,
    "message": "The given data was invalid.",
    "errors": {
        "mobile": ["The mobile field is required."],
        "password": ["The password field is required."]
    }
}
```

### Important Notes
- The system automatically detects if you are a **User** or **Dealer** based on your credentials
- For dealers, the organization is automatically determined from their order history or defaults to the first organization
- Save the `token` from the response - you'll need it for authenticated API calls
- Include the token in the `Authorization` header as: `Bearer {token}`

---

## 2. Dealer Registration API

### Endpoint
```
POST /api/v1/dealers/register
```

### Full URL
```
http://localhost/agrosalescrm/public/api/v1/dealers/register
```

### Description
Register a new dealer account. This is a public endpoint (no authentication required).

### Request Method
`POST`

### Headers

**For JSON requests (without images):**
```
Content-Type: application/json
Accept: application/json
```

**For form-data requests (with images):**
```
Content-Type: multipart/form-data
Accept: application/json
```

**Note:** When uploading images, you must use `multipart/form-data`. For text-only requests, you can use `application/json`.

### Request Body

**Note:** This endpoint accepts `multipart/form-data` for image uploads, or `application/json` for text fields only.

#### Using multipart/form-data (Recommended for images):
```
name: Dealer Name
mobile: 9876543210
email: dealer@example.com
gstin: 27AABCU9601R1ZM
address: Dealer Address
state_id: 1
district_id: 1
taluka_id: 1
city_id: 1
pincode: 400069
password: password123
password_confirmation: password123
image_1: [file]
image_2: [file]
image_3: [file]
image_4: [file]
```

#### Using application/json (without images):
```json
{
    "name": "Dealer Name",
    "mobile": "9876543210",
    "email": "dealer@example.com",
    "gstin": "27AABCU9601R1ZM",
    "address": "Dealer Address",
    "state_id": 1,
    "district_id": 1,
    "taluka_id": 1,
    "city_id": 1,
    "pincode": "400069",
    "password": "password123",
    "password_confirmation": "password123"
}
```

### Required Fields
- `name` (string, max 255) - Dealer's full name
- `mobile` (string, unique) - Mobile number (must be unique)
- `state_id` (integer) - Valid state ID (must exist in tbl_state_master table)
- `district_id` (integer) - Valid district ID (must exist in tbl_dist_master table)
- `taluka_id` (integer) - Valid taluka ID (must exist in tbl_taluka_master table)
- `city_id` (integer) - Valid city ID (must exist in cities table)
- `password` (string, min 8) - Password (minimum 8 characters)
- `password_confirmation` (string) - Must match password exactly

### Optional Fields
- `email` (string, email, unique) - Email address (must be unique if provided)
- `gstin` (string, max 15) - GSTIN number
- `address` (string) - Dealer's address
- `pincode` (string, max 10) - Pincode
- `image_1` (file) - First image (max 2MB, formats: jpeg, png, jpg, gif)
- `image_2` (file) - Second image (max 2MB, formats: jpeg, png, jpg, gif)
- `image_3` (file) - Third image (max 2MB, formats: jpeg, png, jpg, gif)
- `image_4` (file) - Fourth image (max 2MB, formats: jpeg, png, jpg, gif)

### Response Format

#### Success Response (201 Created)
```json
{
    "result": {
        "id": 1,
        "name": "Dealer Name",
        "mobile": "9876543210",
        "email": "dealer@example.com",
        "gstin": "27AABCU9601R1ZM",
        "address": "Dealer Address",
        "zone_id": 1,
        "state_id": 1,
        "district_id": 1,
        "taluka_id": 1,
        "city_id": 1,
        "pincode": "400069",
        "is_active": true,
        "image_1": "dealers/abc123_image1.jpg",
        "image_2": "dealers/abc123_image2.jpg",
        "image_3": "dealers/abc123_image3.jpg",
        "image_4": "dealers/abc123_image4.jpg",
        "created_at": "2025-01-19T10:00:00.000000Z",
        "updated_at": "2025-01-19T10:00:00.000000Z",
        "state": {
            "fld_state_id": 1,
            "fld_name": "Maharashtra"
        },
        "district": {
            "fld_dist_id": 1,
            "fld_dist_name": "Mumbai"
        },
        "taluka": {
            "fld_taluka_id": 1,
            "fld_name": "Andheri"
        },
        "city": {
            "id": 1,
            "name": "Mumbai",
            "state_id": 1,
            "zone_id": 1
        },
        "zone": {
            "id": 1,
            "name": "Zone 1",
            "code": "Z1"
        }
    },
    "status": true,
    "message": "DEALER REGISTERED SUCCESSFULLY"
}
```

**Note:** Image paths are relative to the storage. To get full URLs, prepend your base URL with `/storage/`:
- Full URL: `http://yourdomain.com/storage/dealers/abc123_image1.jpg`

#### Error Response (422 Validation Error)
```json
{
    "result": null,
    "status": false,
    "message": "The given data was invalid.",
    "errors": {
        "mobile": ["The mobile has already been taken."],
        "email": ["The email has already been taken."],
        "password": ["The password confirmation does not match."],
        "state_id": ["The selected state id is invalid."],
        "district_id": ["The selected district id is invalid."],
        "taluka_id": ["The selected taluka id is invalid."],
        "city_id": ["The selected city id is invalid."],
        "image_1": ["The image 1 must be an image.", "The image 1 must not be greater than 2048 kilobytes."]
    }
}
```

### Important Notes
- **Zone is automatically assigned** based on the selected city
- Mobile number must be **unique** (cannot be registered twice)
- Email must be **unique** if provided
- Password must be at least **8 characters** long
- `password_confirmation` must **exactly match** the password
- `state_id`, `district_id`, `taluka_id`, and `city_id` must be **valid IDs** that exist in the database
- **Images are optional** but if provided:
  - Maximum file size: 2MB per image
  - Supported formats: JPEG, PNG, JPG, GIF
  - Use `multipart/form-data` content type when uploading images
  - Images are stored in `storage/app/public/dealers/` directory
- After successful registration, you can use the login API to get an authentication token

### Image Upload Tips
- **For mobile apps:** Use `multipart/form-data` to send images
- **For web/Postman:** Select "form-data" in Body tab, set key type to "File" for image fields
- **Image URLs:** Access uploaded images via `/storage/dealers/{filename}`
- **Full image URL example:** `http://yourdomain.com/storage/dealers/abc123_image1.jpg`

---

## Testing Examples

### Using cURL (Command Line)

#### Login Example
```bash
curl -X POST http://localhost/agrosalescrm/public/api/v1/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "mobile": "9876543210",
    "password": "password123"
  }'
```

#### Registration Example (JSON - without images)
```bash
curl -X POST http://localhost/agrosalescrm/public/api/v1/dealers/register \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "Test Dealer",
    "mobile": "9999999999",
    "email": "test@example.com",
    "state_id": 1,
    "district_id": 1,
    "taluka_id": 1,
    "city_id": 1,
    "password": "password123",
    "password_confirmation": "password123"
  }'
```

#### Registration Example (Form-Data - with images)
```bash
curl -X POST http://localhost/agrosalescrm/public/api/v1/dealers/register \
  -H "Accept: application/json" \
  -F "name=Test Dealer" \
  -F "mobile=9999999999" \
  -F "email=test@example.com" \
  -F "state_id=1" \
  -F "district_id=1" \
  -F "taluka_id=1" \
  -F "city_id=1" \
  -F "password=password123" \
  -F "password_confirmation=password123" \
  -F "image_1=@/path/to/image1.jpg" \
  -F "image_2=@/path/to/image2.jpg" \
  -F "image_3=@/path/to/image3.jpg" \
  -F "image_4=@/path/to/image4.jpg"
```

### Using Postman/Thunder Client

#### For Login API:
1. **Set Method:** Select `POST` from the dropdown
2. **Enter URL:** `http://localhost/agrosalescrm/public/api/v1/login`
3. **Set Headers:**
   - `Content-Type: application/json`
   - `Accept: application/json`
4. **Set Body:** Select `Body` tab → Select `JSON` → Paste the JSON request body
5. **Click Send**

#### For Registration API (without images):
1. **Set Method:** Select `POST` from the dropdown
2. **Enter URL:** `http://localhost/agrosalescrm/public/api/v1/dealers/register`
3. **Set Headers:**
   - `Content-Type: application/json`
   - `Accept: application/json`
4. **Set Body:** Select `Body` tab → Select `JSON` → Paste the JSON request body
5. **Click Send**

#### For Registration API (with images):
1. **Set Method:** Select `POST` from the dropdown
2. **Enter URL:** `http://localhost/agrosalescrm/public/api/v1/dealers/register`
3. **Set Headers:**
   - `Accept: application/json`
   - **Note:** Don't set `Content-Type` manually - it will be set automatically for form-data
4. **Set Body:** 
   - Select `Body` tab → Select `form-data`
   - Add text fields (name, mobile, email, etc.) as **Text** type
   - Add `state_id`, `district_id`, `taluka_id`, `city_id` as **Text** type with numeric values
   - Add `image_1`, `image_2`, `image_3`, `image_4` as **File** type
   - Select image files for each image field
5. **Click Send**

---

## Common Issues & Troubleshooting

### Issue 1: 405 Method Not Allowed
**Problem:** Getting 405 error when trying to register

**Solutions:**
1. ✅ Verify the HTTP method is set to **POST** (not GET)
2. ✅ **Try different URL formats:**
   - `http://localhost/agrosalescrm/public/api/v1/dealers/register` (with /public/)
   - `http://localhost/api/v1/dealers/register` (without /public/ - if document root is set correctly)
   - `http://localhost/agrosalescrm/api/v1/dealers/register` (without /public/)
3. ✅ Ensure headers include:
   - `Content-Type: application/json`
   - `Accept: application/json`
4. ✅ **Remove Bearer Token** from Auth tab (registration is public)
5. ✅ Clear route cache: `php artisan route:clear`
6. ✅ **Check if login works** - if login works with `/public/`, registration should too

### Issue 2: 422 Validation Error
**Problem:** Getting validation errors

**Solutions:**
1. ✅ Check all **required fields** are included:
   - `name`, `mobile`, `state_id`, `district_id`, `taluka_id`, `city_id`, `password`, `password_confirmation`
2. ✅ Ensure `password` and `password_confirmation` **match exactly**
3. ✅ Verify `state_id`, `district_id`, `taluka_id`, and `city_id` are **valid IDs** in the database:
   - `state_id` must exist in `tbl_state_master` table
   - `district_id` must exist in `tbl_dist_master` table
   - `taluka_id` must exist in `tbl_taluka_master` table
   - `city_id` must exist in `cities` table
4. ✅ Check `mobile` and `email` are **unique** (not already registered)
5. ✅ If uploading images, ensure:
   - Using `multipart/form-data` content type
   - Image files are valid (jpeg, png, jpg, gif)
   - Each image is under 2MB

### Issue 3: 401 Unauthorized (Login)
**Problem:** Login fails with invalid credentials

**Solutions:**
1. ✅ Verify mobile number is correct
2. ✅ Verify password is correct
3. ✅ Check if the user/dealer account exists
4. ✅ Check if the account is active (`is_active = true`)

### Issue 4: Token Not Working
**Problem:** After login, using the token gives unauthorized error

**Solutions:**
1. ✅ Include token in header: `Authorization: Bearer {token}`
2. ✅ Make sure there are no extra spaces in the token
3. ✅ Verify the token hasn't expired
4. ✅ Check if you're using the correct endpoint (some require authentication)

---

## Quick Reference

### Login API
- **URL:** `POST /api/v1/login`
- **Auth Required:** No
- **Required Fields:** `mobile`, `password`
- **Returns:** User/Dealer info, organization, roles, token

### Registration API
- **URL:** `POST /api/v1/dealers/register`
- **Auth Required:** No
- **Content-Type:** `multipart/form-data` (for images) or `application/json` (text only)
- **Required Fields:** `name`, `mobile`, `state_id`, `district_id`, `taluka_id`, `city_id`, `password`, `password_confirmation`
- **Optional Fields:** `email`, `gstin`, `address`, `pincode`, `image_1`, `image_2`, `image_3`, `image_4`
- **Returns:** Created dealer info with state, district, taluka, city, zone, and image paths

---

## Next Steps

After successful login or registration:

1. **Save the token** from the login/registration response
2. **Use the token** in subsequent API calls by adding this header:
   ```
   Authorization: Bearer {your_token_here}
   ```
3. **Test authenticated endpoints** like:
   - `GET /api/v1/me` - Get your profile
   - `GET /api/v1/products` - Get products list
   - `GET /api/v1/crops` - Get crops list

---

## Support

If you continue to experience issues:

1. Check the Laravel logs: `storage/logs/laravel.log`
2. Verify your database has valid data:
   - States in `tbl_state_master` table
   - Districts in `tbl_dist_master` table
   - Talukas in `tbl_taluka_master` table
   - Cities in `cities` table
3. Ensure your server configuration is correct
4. For image uploads, verify:
   - `storage/app/public/dealers/` directory exists and is writable
   - Storage link is created: `php artisan storage:link`
5. Test with a simple tool like Postman or Thunder Client

---

## 3. State, District & Taluka Master APIs

These APIs provide cascading location data (State → District → Taluka) for use in forms and dropdowns.

### 3.1. Get All States

#### Endpoint
```
GET /api/v1/states
```

#### Full URL
```
http://localhost/agrosalescrm/public/api/v1/states
```

#### Request Method
`GET`

#### Headers
```
Accept: application/json
```

#### Query Parameters (Optional)
- `country_id` (integer) - Filter states by country ID
- `search` (string) - Search states by name

#### Response Format

##### Success Response (200 OK)
```json
{
    "result": [
        {
            "fld_state_id": 1,
            "fld_name": "Maharashtra",
            "fld_country_id": 1,
            "fld_created_by": null,
            "fld_created_date": null,
            "fld_updated_by": null,
            "fld_updated_date": null,
            "fld_isdeleted": 0,
            "fld_system_date": null
        },
        {
            "fld_state_id": 2,
            "fld_name": "Gujarat",
            "fld_country_id": 1,
            "fld_created_by": null,
            "fld_created_date": null,
            "fld_updated_by": null,
            "fld_updated_date": null,
            "fld_isdeleted": 0,
            "fld_system_date": null
        }
    ],
    "status": true,
    "message": "STATES RETRIEVED SUCCESSFULLY"
}
```

#### Example Requests
```
GET /api/v1/states
GET /api/v1/states?country_id=1
GET /api/v1/states?search=Maharashtra
```

---

### 3.2. Get Specific State

#### Endpoint
```
GET /api/v1/states/{id}
```

#### Full URL
```
http://localhost/agrosalescrm/public/api/v1/states/1
```

#### Response Format
```json
{
    "result": {
        "fld_state_id": 1,
        "fld_name": "Maharashtra",
        "fld_country_id": 1,
        "districts": [
            {
                "fld_dist_id": 1,
                "fld_dist_name": "Mumbai",
                "fld_state_id": 1
            }
        ]
    },
    "status": true,
    "message": "STATE RETRIEVED SUCCESSFULLY"
}
```

---

### 3.3. Get Districts by State

#### Endpoint
```
GET /api/v1/states/{id}/districts
```

#### Full URL
```
http://localhost/agrosalescrm/public/api/v1/states/1/districts
```

#### Query Parameters (Optional)
- `search` (string) - Search districts by name

#### Response Format
```json
{
    "result": [
        {
            "fld_dist_id": 1,
            "fld_dist_name": "Mumbai",
            "fld_state_id": 1,
            "fld_country_id": 1,
            "state": {
                "fld_state_id": 1,
                "fld_name": "Maharashtra"
            }
        }
    ],
    "status": true,
    "message": "DISTRICTS RETRIEVED SUCCESSFULLY"
}
```

---

### 3.4. Get All Districts

#### Endpoint
```
GET /api/v1/districts
```

#### Full URL
```
http://localhost/agrosalescrm/public/api/v1/districts
```

#### Query Parameters (Optional)
- `state_id` (integer) - Filter districts by state ID
- `country_id` (integer) - Filter districts by country ID
- `search` (string) - Search districts by name

#### Response Format
```json
{
    "result": [
        {
            "fld_dist_id": 1,
            "fld_dist_name": "Mumbai",
            "fld_state_id": 1,
            "fld_country_id": 1,
            "state": {
                "fld_state_id": 1,
                "fld_name": "Maharashtra"
            }
        }
    ],
    "status": true,
    "message": "DISTRICTS RETRIEVED SUCCESSFULLY"
}
```

#### Example Requests
```
GET /api/v1/districts
GET /api/v1/districts?state_id=1
GET /api/v1/districts?state_id=1&search=Mumbai
```

---

### 3.5. Get Specific District

#### Endpoint
```
GET /api/v1/districts/{id}
```

#### Full URL
```
http://localhost/agrosalescrm/public/api/v1/districts/1
```

#### Response Format
```json
{
    "result": {
        "fld_dist_id": 1,
        "fld_dist_name": "Mumbai",
        "fld_state_id": 1,
        "fld_country_id": 1,
        "state": {
            "fld_state_id": 1,
            "fld_name": "Maharashtra"
        },
        "talukas": [
            {
                "fld_taluka_id": 1,
                "fld_name": "Andheri",
                "fld_disc_id": 1
            }
        ]
    },
    "status": true,
    "message": "DISTRICT RETRIEVED SUCCESSFULLY"
}
```

---

### 3.6. Get Talukas by District

#### Endpoint
```
GET /api/v1/districts/{id}/talukas
```

#### Full URL
```
http://localhost/agrosalescrm/public/api/v1/districts/1/talukas
```

#### Query Parameters (Optional)
- `search` (string) - Search talukas by name

#### Response Format
```json
{
    "result": [
        {
            "fld_taluka_id": 1,
            "fld_name": "Andheri",
            "fld_code": "AND",
            "fld_state_id": 1,
            "fld_disc_id": 1,
            "fld_country_id": 1,
            "fld_sequence": 1,
            "state": {
                "fld_state_id": 1,
                "fld_name": "Maharashtra"
            },
            "district": {
                "fld_dist_id": 1,
                "fld_dist_name": "Mumbai"
            }
        }
    ],
    "status": true,
    "message": "TALUKAS RETRIEVED SUCCESSFULLY"
}
```

---

### 3.7. Get All Talukas

#### Endpoint
```
GET /api/v1/talukas
```

#### Full URL
```
http://localhost/agrosalescrm/public/api/v1/talukas
```

#### Query Parameters (Optional)
- `district_id` (integer) - Filter talukas by district ID (highest priority)
- `state_id` (integer) - Filter talukas by state ID (if district_id not provided)
- `country_id` (integer) - Filter talukas by country ID
- `search` (string) - Search talukas by name

#### Response Format
```json
{
    "result": [
        {
            "fld_taluka_id": 1,
            "fld_name": "Andheri",
            "fld_code": "AND",
            "fld_state_id": 1,
            "fld_disc_id": 1,
            "fld_country_id": 1,
            "fld_sequence": 1,
            "state": {
                "fld_state_id": 1,
                "fld_name": "Maharashtra"
            },
            "district": {
                "fld_dist_id": 1,
                "fld_dist_name": "Mumbai"
            }
        }
    ],
    "status": true,
    "message": "TALUKAS RETRIEVED SUCCESSFULLY"
}
```

#### Example Requests
```
GET /api/v1/talukas
GET /api/v1/talukas?district_id=1
GET /api/v1/talukas?state_id=1
GET /api/v1/talukas?state_id=1&search=Andheri
```

---

### 3.8. Get Specific Taluka

#### Endpoint
```
GET /api/v1/talukas/{id}
```

#### Full URL
```
http://localhost/agrosalescrm/public/api/v1/talukas/1
```

#### Response Format
```json
{
    "result": {
        "fld_taluka_id": 1,
        "fld_name": "Andheri",
        "fld_code": "AND",
        "fld_state_id": 1,
        "fld_disc_id": 1,
        "fld_country_id": 1,
        "fld_sequence": 1,
        "state": {
            "fld_state_id": 1,
            "fld_name": "Maharashtra"
        },
        "district": {
            "fld_dist_id": 1,
            "fld_dist_name": "Mumbai"
        }
    },
    "status": true,
    "message": "TALUKA RETRIEVED SUCCESSFULLY"
}
```

---

### Important Notes for Location APIs

- **All location APIs are public** (no authentication required)
- **Cascading filters**: Use `state_id` to get districts, then use `district_id` to get talukas
- **Sorting**: All results are sorted by ID (ascending)
- **Active records only**: Only records with `fld_isdeleted = 0` are returned
- **Relationships**: State includes districts, District includes talukas, Taluka includes state and district

### Usage Example for Cascading Dropdowns

1. **Get all states:**
   ```
   GET /api/v1/states
   ```

2. **When user selects a state, get districts:**
   ```
   GET /api/v1/districts?state_id=1
   ```

3. **When user selects a district, get talukas:**
   ```
   GET /api/v1/talukas?district_id=1
   ```

Or use the nested endpoints:
```
GET /api/v1/states/1/districts
GET /api/v1/districts/1/talukas
```

---

**Last Updated:** January 19, 2025

