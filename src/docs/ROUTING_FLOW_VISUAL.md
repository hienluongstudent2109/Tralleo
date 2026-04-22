# Tralleo - Request to Login Page Flow

## Complete Flow Diagram

### User Request → Login Page

```
1. USER VISIT BROWSER
   └─ http://localhost/login
      or any other URL

2. LARAVEL ROUTING (routes/web.php)
   ├─ Check if request is /health
   │  └─ Return JSON: { status: "ok" }
   └─ Otherwise match catch-all route /{any}
      └─ Return view('app')  ← app.blade.php

3. RENDER APP TEMPLATE (resources/views/app.blade.php)
   ├─ Load HTML structure
   ├─ Add CSRF token
   └─ Execute @vite() directive
      ├─ Load resources/css/app.css
      └─ Load resources/js/app.js (entry point)

4. JAVASCRIPT INITIALIZATION (resources/js/app.js)
   ├─ Import Vue from 'vue'
   ├─ Import Pinia (state management)
   ├─ Import Vue Router
   ├─ Create Vue app instance
   ├─ Mount Pinia store
   ├─ Mount Vue Router
   └─ Mount to #app DOM element

5. ROOT COMPONENT MOUNT (App.vue)
   ├─ Initialize auth store
   ├─ Load user profile if token exists
   └─ Render <router-view/>

6. ROUTER INITIALIZATION (resources/js/router/index.js)
   ├─ Create router with createWebHistory()
   ├─ Define routes:
   │  ├─ / (home - requires auth)
   │  ├─ /login (public)
   │  └─ /register (public)
   └─ Setup navigation guards
      ├─ Check auth:sanctum token
      ├─ Check if route requires auth
      └─ Redirect if needed

7. NAVIGATION GUARD LOGIC
   ├─ If token exists && user not loaded
   │  └─ Call /api/auth/profile
   ├─ If route requires auth && not authenticated
   │  └─ Redirect to /login
   ├─ If on /login && authenticated
   │  └─ Redirect to /
   └─ Otherwise allow navigation

8. RENDER LOGIN COMPONENT (resources/js/pages/Login.vue)
   ├─ Display form
   ├─ Bind to credentials state
   └─ Listen for submit event

9. USER ENTERS CREDENTIALS & CLICKS "SIGN IN"
   └─ Form submit event triggered

10. SUBMIT HANDLER (Login.vue)
    └─ Call authStore.login(credentials)

11. PINIA STORE ACTION (resources/js/store/auth.js)
    ├─ Set isLoading = true
    └─ Call authAPI.login(credentials)

12. API CALL (resources/js/api/auth.js)
    ├─ Use axios instance with /api base URL
    ├─ POST /api/auth/login
    └─ Axios interceptor adds Authorization header if needed

13. AXIOS INTERCEPTOR (resources/js/utils/axios.js)
    ├─ Add headers:
    │  ├─ X-Requested-With: XMLHttpRequest
    │  ├─ Content-Type: application/json
    │  └─ Authorization: Bearer {token} (if exists)
    └─ Send request

14. LARAVEL API ROUTE (routes/api.php)
    └─ POST /api/auth/login → AuthController@login

15. LARAVEL CONTROLLER (app/Http/Controllers/Api/AuthController.php)
    ├─ Validate request (LoginUserRequest)
    ├─ Call AuthService::login()
    ├─ Check credentials against database
    ├─ Generate API token (via Sanctum)
    ├─ Return JSON response:
    │  ├─ token: "sanctum_token"
    │  └─ user: { id, name, email }
    └─ Status: 200 OK

16. RESPONSE INTERCEPTOR (axios.js)
    ├─ Check status
    ├─ If 401: clear token & redirect to /login
    ├─ If success: return response data
    └─ Otherwise: pass through error

17. STORE UPDATES (auth.js store)
    ├─ setToken(response.token)
    │  └─ Save to localStorage
    ├─ setUser(response.user)
    │  └─ Save to Pinia store
    ├─ Set isLoading = false
    └─ Return response

18. COMPONENT UPDATE (Login.vue)
    ├─ Catch then() of login promise
    ├─ Clear form errors
    └─ Redirect to home page

19. ROUTER REDIRECTS
    └─ Navigate to '/'
       └─ Home component loads (protected route)

20. USER SEES DASHBOARD
    └─ Successfully authenticated!
```

## State Flow Diagram

### Before Login
```
Browser localStorage: { }
Pinia store: { 
    user: null, 
    token: null, 
    isAuthenticated: false 
}
Navigation guard: redirects to /login
```

### During Login
```
1. User submits form
2. authStore.isLoading = true
3. API call in flight
4. UI shows "Signing In..." button
```

### After Successful Login
```
Browser localStorage: { 
    auth_token: "eyJ0eXAiOiJKV1QiLCJhbGc...",
    user: "{ id: 1, name: '...', email: '...' }"
}
Pinia store: { 
    user: { id: 1, name: '...', email: '...' }, 
    token: "eyJ0eXAiOiJKV1QiLCJhbGc...",
    isAuthenticated: true
}
Navigation guard: allows protected routes
Browser redirects: to /
```

## File Dependency Chain

```
Browser
  ↓
routes/web.php (Laravel routing)
  ↓
resources/views/app.blade.php (HTML template)
  ↓
@vite() directive loads
  ├─ resources/css/app.css (Tailwind)
  └─ resources/js/app.js (Vue entry point)
      ├─ imports App.vue
      │  └─ uses <router-view/>
      │     └─ routes/index.js creates router
      │        └─ defines routes to pages/
      │           └─ Login.vue
      │              ├─ uses store/auth.js
      │              └─ calls api/auth.js
      │                 └─ uses utils/axios.js
      │                    └─ sends request to routes/api.php
      │
      └─ createPinia() creates store
         └─ store/auth.js
            ├─ manages auth state
            ├─ calls authAPI.login()
            ├─ saves token to localStorage
            └─ updates isAuthenticated flag
```

## HTTP Request Flow

### Login Request
```
CLIENT REQUEST:
POST /api/auth/login
Host: localhost
Content-Type: application/json
X-Requested-With: XMLHttpRequest
Authorization: (none for login)

{
    "email": "test@example.com",
    "password": "password123",
    "remember": false
}

↓

LARAVEL PROCESSING:
1. Route matches: /api/auth/login
2. Controller: AuthController@login
3. Middleware: api (CORS, JSON, etc.)
4. Validation: LoginUserRequest validates email/password
5. Service: AuthService::login() checks credentials
6. Database: Query users table for matching email
7. Hash: Check password against hashed DB value
8. Token: Generate Sanctum API token
9. Response: Format as JSON

↓

SERVER RESPONSE:
HTTP/1.1 200 OK
Content-Type: application/json

{
    "token": "3|abcdef123456...",
    "user": {
        "id": 1,
        "name": "Test User",
        "email": "test@example.com",
        "created_at": "2026-04-21T10:00:00Z",
        "updated_at": "2026-04-21T10:00:00Z"
    }
}

↓

CLIENT PROCESSING:
1. Response interceptor checks status (200)
2. Login action in store receives data
3. Token saved to localStorage
4. User saved to Pinia store
5. Component redirects to /
6. Navigation guard allows access
7. Home component renders
```

## Error Flow

### Invalid Credentials
```
User enters wrong password
  ↓
Submit to /api/auth/login
  ↓
AuthService checks password hash
  ↓
Password doesn't match
  ↓
Throws exception (invalid credentials)
  ↓
Laravel returns JSON response
HTTP/1.1 422 Unprocessable Entity
{
    "message": "Invalid credentials",
    "errors": { ... }
}
  ↓
Axios response interceptor sees 422
  ↓
Promise rejects
  ↓
Component catch block handles error
  ↓
Store.error = "Invalid credentials"
  ↓
UI displays error message
```

### Session Expired / 401 Unauthorized
```
User tries to access /workspaces
  ↓
API call sends stored token
  ↓
GET /api/workspaces (with Authorization header)
  ↓
Sanctum middleware checks token
  ↓
Token is expired or invalid
  ↓
Returns HTTP 401 Unauthorized
  ↓
Axios response interceptor catches 401
  ↓
Clear localStorage (auth_token, user)
  ↓
window.location.href = '/login'
  ↓
Full page redirect to login
  ↓
Navigation guard verifies: not authenticated
  ↓
User sees login form
```

## Component Lifecycle

### App.vue
```
mounted()
  ├─ Load authStore
  └─ If token exists:
     └─ Call authStore.loadProfile()
        └─ GET /api/auth/profile

render()
  └─ <router-view/> 
     └─ Renders matching route component
```

### Login.vue
```
mounted()
  └─ Clear any previous errors

rendered()
  └─ Focus on email input (optional)

submit()
  ├─ Validate form
  ├─ Call authStore.login()
  ├─ Wait for API response
  └─ Redirect on success

watch(authStore.error)
  └─ Display error if it changes
```

## Configuration Checklist

✓ routes/web.php - catch-all route to app.blade.php
✓ resources/views/app.blade.php - @vite() directive
✓ package.json - Vue, Vue Router, Pinia dependencies
✓ vite.config.js - @vitejs/plugin-vue plugin
✓ resources/js/app.js - Vue app initialization
✓ resources/js/App.vue - root component with router-view
✓ resources/js/router/index.js - route definitions
✓ resources/js/store/auth.js - Pinia auth store
✓ resources/js/api/auth.js - API service functions
✓ resources/js/utils/axios.js - HTTP client setup
✓ resources/js/pages/Login.vue - login form component
✓ routes/api.php - /api/auth/* endpoints
✓ app/Http/Controllers/Api/AuthController.php - API logic
✓ .env - VITE_API_BASE_URL=/api

All connected! ✓
