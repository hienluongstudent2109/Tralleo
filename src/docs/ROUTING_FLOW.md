# Routing Flow Documentation

## Architecture Overview

```
┌─────────────────────────────────────────────────────────────────┐
│                          Browser Request                         │
└────────────────────────────┬────────────────────────────────────┘
                             │
                    ┌────────▼────────┐
                    │   Laravel Web   │
                    │   Routes        │
                    └────────┬────────┘
                             │
        ┌────────────────────┼────────────────────┐
        │                    │                    │
        │          ┌─────────▼──────────┐         │
        │          │ /health            │         │
        │          │ (JSON Response)    │         │
        │          └────────────────────┘         │
        │                                          │
        │          ┌─────────────────────────┐    │
        │          │ /{any}                  │    │
        │          │ (Catch-all Route)       │    │
        │          │ Returns: app.blade.php  │    │
        │          └────────┬────────────────┘    │
        │                   │                      │
        │          ┌────────▼──────────┐          │
        │          │  app.blade.php    │          │
        │          │  (Vue App Shell)  │          │
        │          │  - @vite() Loads  │          │
        │          │  - #app div       │          │
        │          └────────┬──────────┘          │
        │                   │                      │
        │          ┌────────▼──────────────────┐   │
        │          │ JavaScript Bundles       │   │
        │          │ - app.js (Vue setup)     │   │
        │          │ - app.css (Tailwind)     │   │
        │          └────────┬─────────────────┘   │
        │                   │                      │
        └───────────────────┼──────────────────────┘
                            │
                   ┌────────▼───────┐
                   │  Vue Router    │
                   │  Handles:      │
                   │  - /           │
                   │  - /login      │
                   │  - /register   │
                   │  - All SPA     │
                   │    Routes      │
                   └────────┬───────┘
                            │
              ┌─────────────┼─────────────┐
              │             │             │
        ┌─────▼─────┐ ┌────▼─────┐ ┌────▼──────┐
        │  /login   │ │ /register │ │   Home    │
        │  Page     │ │  Page     │ │  Page     │
        │(Public)   │ │(Public)   │ │(Protected)│
        └─────┬─────┘ └────┬─────┘ └────┬──────┘
              │             │            │
              └─────────────┼────────────┘
                            │
              ┌─────────────▼──────────┐
              │   API Calls            │
              │   (Axios Instance)     │
              │   Base URL: /api       │
              └─────────────┬──────────┘
                            │
                   ┌────────▼────────┐
                   │ Laravel API     │
                   │ Routes (api.php)│
                   └────────┬────────┘
                            │
         ┌──────────────────┼──────────────────┐
         │                  │                  │
    ┌────▼────────┐  ┌──────▼──────┐  ┌──────▼──────┐
    │ POST         │  │ GET          │  │ Protected   │
    │ /auth/login  │  │ /auth/profile│  │ Routes      │
    │ /auth/...    │  │ /workspaces/ │  │ (Sanctum)   │
    └────┬────────┘  └──────┬──────┘  └──────┬──────┘
         │                  │                │
    ┌────▼──────────────────▼────────────────▼────┐
    │     AuthController / Other Controllers      │
    │     Process Requests & Return JSON          │
    └───────────────────────────────────────────────┘
```

## Detailed Flow Steps

### 1. Initial Page Load
```
User visits http://localhost/
         ↓
Laravel matches route /{any}
         ↓
Returns app.blade.php (HTML template)
         ↓
Browser downloads Vue + dependencies via @vite()
         ↓
Vue App initializes in #app div
```

### 2. Vue Router Initialization
```
App.vue loads
         ↓
Router initializes (createWebHistory)
         ↓
Navigation guard checks auth state
         ↓
If no token: redirect to /login
If token exists: load profile from API
```

### 3. Login Flow
```
User visits /login
         ↓
Vue Router shows Login.vue component
         ↓
User enters credentials
         ↓
Form submission calls authStore.login()
         ↓
Axios POST to /api/auth/login
         ↓
Laravel AuthController.login()
         ↓
Returns: { token, user }
         ↓
Token stored in localStorage
         ↓
User stored in Pinia store
         ↓
Redirect to / (home page)
```

### 4. Protected Routes
```
User navigates to protected route
         ↓
Navigation guard checks: authStore.isAuthenticated
         ↓
If authenticated: show page
If not: redirect to /login with redirect query param
```

### 5. API Request Flow
```
Component calls API (e.g., get workspaces)
         ↓
Axios interceptor adds token to headers
Authorization: Bearer <token>
         ↓
Request sent to /api/workspaces
         ↓
Laravel checks auth:sanctum middleware
         ↓
If valid: Process request & return JSON
If invalid (401): Return unauthorized
         ↓
Axios response interceptor catches 401
         ↓
Clear localStorage & redirect to /login
```

## File Structure & Flow

```
routes/
├── web.php          # SPA routes (all go to app.blade.php)
└── api.php          # API endpoints (/api/*)

resources/
├── views/
│   ├── app.blade.php    # Main Vue app template
│   └── welcome.blade.php # (Legacy)
└── js/
    ├── app.js           # Vue initialization
    ├── App.vue          # Root component
    ├── api/
    │   └── auth.js      # API service layer
    ├── pages/
    │   ├── Login.vue    # Login page
    │   ├── Register.vue # Register page
    │   └── Home.vue     # Dashboard
    ├── router/
    │   └── index.js     # Vue Router config
    ├── store/
    │   └── auth.js      # Pinia store
    └── utils/
        └── axios.js     # Axios config

app/Http/Controllers/Api/
└── AuthController.php   # Handles login, register, logout, profile
```

## Authentication Flow

### Token Management
```
1. Login successful → token received from API
2. Token stored: localStorage.setItem('auth_token', token)
3. Every API request: Axios adds Authorization header
4. If token expires: 401 response → auto logout & redirect to /login
5. Logout: Clear token & user from localStorage
```

### Protected Routes Check
```
Navigation guard on every route change:
  ├─ If route requires auth && not authenticated
  │  └─ Redirect to /login
  ├─ If on /login && authenticated
  │  └─ Redirect to /
  └─ Otherwise continue
```

## Environment Configuration

Set in `.env`:
```
VITE_API_BASE_URL=/api
```

Used in axios.js:
```javascript
const instance = axios.create({
    baseURL: import.meta.env.VITE_API_BASE_URL || '/api'
});
```

## API Endpoints

### Public Routes (No Authentication)
```
POST /api/auth/register  → AuthController@register
POST /api/auth/login     → AuthController@login
```

### Protected Routes (Requires auth:sanctum)
```
POST   /api/auth/logout      → AuthController@logout
GET    /api/auth/profile     → AuthController@user
GET    /api/workspaces       → WorkspaceController@index
POST   /api/workspaces       → WorkspaceController@store
POST   /api/workspaces/{id}/members → WorkspaceController@addMember
```

## Error Handling

### Login Errors
```
Invalid credentials → 422 response
Error displayed in UI via authStore.error
```

### API Errors
```
401 Unauthorized → Auto logout & redirect to /login
Other errors → Caught by component try/catch
Error message: error.response?.data?.message
```

## Development Commands

```bash
# Install dependencies
npm install --legacy-peer-deps

# Start dev server
npm run dev

# Build for production
npm run build

# Run Laravel server (if not using Docker)
php artisan serve

# With Docker
docker-compose exec php php artisan serve --host=0.0.0.0
```

## Vite + Laravel Integration

The `@vite()` directive in app.blade.php:
```blade
@vite(['resources/css/app.css', 'resources/js/app.js'])
```

This loads:
1. Hot module replacement in development
2. Optimized bundles in production
3. Handles CSS and JS compilation
