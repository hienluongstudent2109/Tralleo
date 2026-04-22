# Frontend Setup Guide

## Project Structure

The frontend is built with Vue 3, Vue Router, and Pinia for state management:

```
resources/js/
├── api/
│   └── auth.js              # Authentication API calls
├── pages/
│   ├── Login.vue            # Login page
│   ├── Register.vue         # Registration page
│   └── Home.vue             # Dashboard (protected route)
├── router/
│   └── index.js             # Vue Router configuration with navigation guards
├── store/
│   └── auth.js              # Pinia store for authentication state
├── utils/
│   └── axios.js             # Axios instance with interceptors
├── App.vue                  # Root component
├── app.js                   # Vue app initialization
└── bootstrap.js             # Bootstrap configuration
```

## Installation

1. Install dependencies:
```bash
npm install
```

2. Add required environment variables to `.env`:
```
VITE_API_BASE_URL=/api
```

3. Start development server:
```bash
npm run dev
```

## Features

### Authentication Store (Pinia)
- User state management
- Token handling
- Login, logout, register functionality
- Profile loading on app initialization

### API Client
- Axios instance with default configuration
- Automatic token injection in request headers
- Automatic logout on 401 responses
- Centralized error handling

### Router
- Protected routes with authentication guards
- Automatic redirect to login for protected pages
- Automatic redirect to home for authenticated users on login page
- Support for redirect query parameter after login

### Pages
- **Login**: Email/password authentication with remember me option
- **Register**: Account creation with password confirmation
- **Home**: Protected dashboard with logout button

## API Endpoints

The application expects these endpoints to be available at your Laravel API:

- `POST /api/auth/login` - User login
- `POST /api/auth/logout` - User logout
- `POST /api/auth/register` - User registration
- `GET /api/auth/profile` - Get current user profile
- `POST /api/auth/refresh` - Refresh authentication token

## Authentication Flow

1. User enters credentials on login page
2. Axios sends request to `/api/auth/login`
3. API returns user data and token
4. Token is stored in localStorage and Pinia store
5. Token is automatically added to all subsequent requests
6. Protected pages check authentication before rendering
7. On 401 response, user is logged out and redirected to login

## Tailwind CSS

The application uses Tailwind CSS for styling. All components are styled with responsive, utility-first classes.
