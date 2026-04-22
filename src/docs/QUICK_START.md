# Quick Start Guide

## Prerequisites

- Docker & Docker Compose (recommended)
- Node.js & npm (if running locally)
- PHP 8.4+ (if running Laravel locally)

## Setup & Running

### Option 1: Using Docker (Recommended)

1. **Start containers:**
   ```bash
   cd d:\hienluong\projects\Tralleo
   docker-compose up -d
   ```

2. **Install npm dependencies:**
   ```bash
   docker-compose exec php npm install --legacy-peer-deps
   ```

3. **Start development server:**
   ```bash
   # In first terminal - Laravel server
   docker-compose exec php php artisan serve --host=0.0.0.0
   
   # In second terminal - Vite dev server
   docker-compose exec php npm run dev
   ```

4. **Access the app:**
   - Web: http://localhost:80
   - API: http://localhost/api

### Option 2: Local Setup

1. **Install dependencies:**
   ```bash
   cd src
   npm install --legacy-peer-deps
   ```

2. **Start servers:**
   ```bash
   # Terminal 1: Laravel
   php artisan serve
   
   # Terminal 2: Vite
   npm run dev
   ```

3. **Run migrations:**
   ```bash
   docker-compose exec php php artisan migrate
   ```

## Testing the Login Flow

### 1. Create a Test User

```bash
docker-compose exec php php artisan tinker
```

```php
use App\Models\User;
User::create([
    'name' => 'Test User',
    'email' => 'test@example.com',
    'password' => bcrypt('password123'),
]);
exit;
```

### 2. Test Login Page

1. Navigate to: http://localhost/login
2. You should see the login form
3. Enter:
   - Email: test@example.com
   - Password: password123
4. Click "Sign In"
5. You should be redirected to the dashboard

### 3. Verify Authentication

After login, you can:
- Check localStorage: `localStorage.getItem('auth_token')`
- Navigate to protected routes
- Logout and be redirected to login

## File Locations

```
Key Frontend Files:
src/resources/js/
├── app.js              # Vue app entry point
├── App.vue             # Root component
├── pages/Login.vue     # Login page
├── router/index.js     # Routing config
├── store/auth.js       # Auth state management
├── api/auth.js         # API calls
└── utils/axios.js      # HTTP client

Key Backend Files:
src/
├── routes/
│   ├── web.php         # SPA routes
│   └── api.php         # API endpoints
├── app/Http/Controllers/Api/
│   └── AuthController.php
└── resources/views/
    └── app.blade.php   # Vue app template
```

## Development Workflow

### 1. Add a New API Endpoint

**Laravel side (routes/api.php):**
```php
Route::get('/api/my-data', [MyController::class, 'getData']);
```

**Vue side (resources/js/api/myService.js):**
```javascript
export const getMyData = async () => {
    const response = await axios.get('/my-data');
    return response.data;
};
```

**Use in component:**
```vue
<script setup>
import { getMyData } from '../api/myService';

const data = await getMyData();
</script>
```

### 2. Add a New Page

1. Create component: `resources/js/pages/MyPage.vue`
2. Add route in `resources/js/router/index.js`:
   ```javascript
   {
       path: '/my-page',
       component: () => import('../pages/MyPage.vue'),
       meta: { requiresAuth: true }
   }
   ```

### 3. Store Data with Pinia

```javascript
// In resources/js/store/my.js
import { defineStore } from 'pinia';
import { ref } from 'vue';

export const useMyStore = defineStore('my', () => {
    const data = ref(null);
    
    const fetchData = async () => {
        data.value = await getMyData();
    };
    
    return { data, fetchData };
});
```

## Troubleshooting

### Issue: 404 on login page
- Check that routes/web.php has the catch-all route
- Verify app.blade.php exists in resources/views/

### Issue: API calls failing
- Check browser Network tab for request/response
- Verify token is in localStorage
- Check Laravel logs in storage/logs/

### Issue: Login button not working
- Open browser console for JavaScript errors
- Check Network tab for API response status
- Verify AuthController.login() is working

### Issue: npm install errors
- Use `--legacy-peer-deps` flag
- Clear npm cache: `npm cache clean --force`
- Delete node_modules: `rm -rf node_modules package-lock.json`

## Production Build

```bash
# Build frontend
npm run build

# Output goes to public/build/

# Serve with Laravel
php artisan serve

# Or deploy with Docker
docker-compose -f docker-compose.prod.yml up
```

## API Documentation

### Login
```
POST /api/auth/login
Content-Type: application/json

{
    "email": "user@example.com",
    "password": "password123",
    "remember": false
}

Response:
{
    "token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
    "user": {
        "id": 1,
        "name": "Test User",
        "email": "user@example.com"
    }
}
```

### Logout
```
POST /api/auth/logout
Authorization: Bearer {token}

Response:
{
    "message": "Logged out."
}
```

### Get Profile
```
GET /api/auth/profile
Authorization: Bearer {token}

Response:
{
    "id": 1,
    "name": "Test User",
    "email": "user@example.com",
    "created_at": "2026-04-21T10:00:00Z"
}
```

## Next Steps

1. [Read the Routing Flow documentation](./ROUTING_FLOW.md)
2. [Check the Frontend README](../resources/js/README.md)
3. Create additional pages and features
4. Set up API endpoints for your business logic
5. Deploy to production
