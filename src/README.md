# Tralleo

Ứng dụng Laravel 13 nằm trong thư mục `src` của repo; phía trên có thư mục gốc chứa Docker Compose và cấu hình container (nginx, PHP-FPM, MySQL, Redis).

---

## Cấu trúc repo (thư mục gốc)

```
project-root/
├── docker/
│   ├── nginx/          # Dockerfile + cấu hình virtual host Laravel
│   └── php/            # Dockerfile PHP-FPM + extension (MySQL, Redis, OPcache, …)
├── docker-compose.yml
├── .env                # Biến cho Docker Compose (cổng, DB, …)
├── docs/               # Tài liệu bổ sung (ví dụ migration)
└── src/                # Laravel (app này)
```

Trong thư mục `src` (Laravel):

```
app/
├── Http/
│   ├── Controllers/Api/   # AuthController, WorkspaceController
│   ├── Requests/Api/      # Form Request
│   └── Resources/         # JSON Resource
├── Models/
├── Policies/              # WorkspacePolicy
├── Repositories/          # WorkspaceRepository
├── Services/              # AuthService, WorkspaceService
└── Enums/                 # WorkspaceRole
```

---

## Yêu cầu

- **PHP** ≥ 8.3, **Composer** (chạy local hoặc trong container)
- **Docker Desktop** (nếu chạy stack đầy đủ bằng Compose)
- Hoặc chỉ SQLite/MySQL local để chạy `php artisan` và test

---

## Chạy bằng Docker (khuyến nghị)

1. Tạo file `.env` ở **thư mục gốc** repo (cùng cấp `docker-compose.yml`) nếu chưa có. Ví dụ:

   ```env
   APP_PORT=80
   APP_ENV=production
   MYSQL_PORT=3306
   DB_DATABASE=laravel
   DB_USERNAME=laravel
   DB_PASSWORD=secret
   DB_ROOT_PASSWORD=rootsecret
   ```

2. Cấu hình **`src/.env`** (Laravel) trỏ tới service trong Compose:

   - `DB_HOST=mysql`
   - `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` khớp với biến Docker
   - `REDIS_HOST=redis`
   - `SESSION_DRIVER`, `CACHE_STORE`, `QUEUE_CONNECTION` dùng `redis` nếu đã cấu hình như vậy

3. Build và chạy:

   ```bash
   cd /path/to/Tralleo
   docker compose build
   docker compose up -d
   ```

4. Migration và tối ưu (trong container PHP):

   ```bash
   docker compose exec php php artisan migrate --force
   docker compose exec php php artisan config:cache
   docker compose exec php php artisan route:cache
   docker compose exec php php artisan view:cache
   ```

5. Truy cập ứng dụng: `http://localhost` (hoặc cổng trong `APP_PORT`).

---

## Cài đặt local (không Docker)

```bash
cd src
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
```

Chỉnh `DB_*` trong `.env` cho phù hợp (SQLite hoặc MySQL local).

---

## Database & migration

Các bảng đã thiết kế (migrations trong `database/migrations/`):

| Bảng | Mô tả ngắn |
|------|------------|
| `users` | Bảng mặc định Laravel + migration bổ sung `avatar`, `timezone` |
| `sanctum` | `personal_access_tokens` (Laravel Sanctum) |
| `workspaces` | Không gian làm việc; `owner_id` |
| `workspace_users` | Thành viên + `role` (`owner` / `admin` / `member`) |
| `projects`, `project_users`, `columns`, `tasks`, `task_assignees` | Nghiệp vụ board / task |
| `comments`, `activity_logs`, `attachments` | Bình luận, log, file đính kèm |

Hướng dẫn chi tiết migration và lệnh Artisan liên quan: **`../docs/HUONG_DAN_MIGRATION.md`** (từ thư mục gốc repo).

Lệnh thường dùng:

```bash
cd src
php artisan migrate
php artisan migrate:fresh   # chỉ môi trường dev
```

---

## Gói & cấu hình API

- **Laravel Sanctum** — xác thực API bằng token (Bearer).

Đã cài:

```bash
cd src
composer require laravel/sanctum
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
php artisan migrate
```

Route API được đăng ký trong `bootstrap/app.php` (file `routes/api.php`), prefix **`/api`**.

---

## API REST (Auth + Workspace)

Gửi header:

- `Authorization: Bearer {token}`
- `Accept: application/json`
- `Content-Type: application/json` (khi có body)

### Auth

| Phương thức | Đường dẫn | Auth | Nội dung |
|-------------|-----------|------|----------|
| `POST` | `/api/register` | Không | `name`, `email`, `password`, `password_confirmation` |
| `POST` | `/api/login` | Không | `email`, `password` → trả `token` + `user` |
| `POST` | `/api/logout` | Sanctum | Thu hồi token hiện tại |
| `GET` | `/api/user` | Sanctum | Thông tin user đăng nhập |

### Workspace

| Phương thức | Đường dẫn | Auth | Mô tả |
|-------------|-----------|------|--------|
| `GET` | `/api/workspaces` | Sanctum | Danh sách workspace mà user tham gia |
| `POST` | `/api/workspaces` | Sanctum | Tạo workspace: body `name` |
| `POST` | `/api/workspaces/{workspace}/members` | Sanctum | Thêm thành viên: `email`, `role` (`admin` hoặc `member`) |

**Phân quyền:**

- **`WorkspacePolicy`**: chỉ `owner` hoặc `admin` của workspace mới được gọi thêm thành viên.
- Gán role **`admin`** cho user mới chỉ khi người gọi là **owner** (`owner_id` của workspace).

### Ví dụ nhanh (curl)

```bash
# Đăng ký
curl -s -X POST http://localhost/api/register \
  -H "Content-Type: application/json" -H "Accept: application/json" \
  -d '{"name":"Alice","email":"alice@example.com","password":"password","password_confirmation":"password"}'

# Đăng nhập (lấy token trong JSON)
curl -s -X POST http://localhost/api/login \
  -H "Content-Type: application/json" -H "Accept: application/json" \
  -d '{"email":"alice@example.com","password":"password"}'

# Danh sách workspace (thay YOUR_TOKEN)
curl -s http://localhost/api/workspaces \
  -H "Authorization: Bearer YOUR_TOKEN" -H "Accept: application/json"
```

---

## Kiến trúc xử lý nghiệp vụ

- **Controller** (`Http/Controllers/Api/`) — nhận HTTP, gọi Service, trả Resource hoặc JSON.
- **Service** (`Services/`) — luồng nghiệp vụ (đăng ký, đăng nhập, tạo workspace, thêm member).
- **Repository** (`Repositories/`) — truy vấn/ghi DB (ví dụ `WorkspaceRepository`).
- **Policy** (`Policies/`) — ủy quyền (`$this->authorize(...)` trong controller).
- **Form Request** (`Http/Requests/Api/`) — validate input.
- **Resource** (`Http/Resources/`) — định dạng JSON trả về.

`Gate::policy(Workspace::class, WorkspacePolicy::class)` được đăng ký trong `App\Providers\AppServiceProvider`.

---

## Lệnh Artisan hữu ích

```bash
cd src

php artisan route:list --path=api
php artisan migrate
php artisan migrate:fresh --force
php artisan test
php artisan config:clear
php artisan optimize:clear
```

---

## License

Framework Laravel dùng [MIT license](https://opensource.org/licenses/MIT). Mã nghiệp vụ dự án Tralleo theo quy định của repo.
