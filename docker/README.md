# Hướng dẫn chạy dự án bằng Docker

Tài liệu này mô tả cách build và chạy stack **nginx + PHP-FPM + MySQL + Redis** cho ứng dụng Laravel trong thư mục `src/` (thư mục gốc repo là nơi chứa `docker-compose.yml`).

## Yêu cầu

- **Docker Engine** và **Docker Compose** (v2), hoặc **Docker Desktop** trên Windows / macOS.
- Đã clone repo và có thư mục `src` chứa mã Laravel (composer install đã chạy trên máy host hoặc sẽ chạy trong container khi cần).

## Cấu trúc liên quan


| Thành phần                           | Vai trò                                                                 |
| ------------------------------------ | ----------------------------------------------------------------------- |
| `docker/nginx/`                      | Image Nginx, cấu hình site trỏ `root` tới `public` của Laravel          |
| `docker/php/`                        | Image PHP 8.3 FPM (Alpine), extension: `pdo_mysql`, `redis`, OPcache, … |
| `docker-compose.yml` (ở thư mục gốc) | Định nghĩa service `nginx`, `php`, `mysql`, `redis`                     |


Code Laravel được mount vào container tại `**/var/www/html`** từ `./src`.

## 1. Biến môi trường ở thư mục gốc repo

Tạo file `**.env**` cạnh `docker-compose.yml` (có thể copy từ `.env.example`):

```env
APP_PORT=80
APP_ENV=production

MYSQL_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=laravel
DB_PASSWORD=secret
DB_ROOT_PASSWORD=rootsecret
```

- `**APP_PORT**`: cổng HTTP trên máy host (mặc định `80`).
- `**MYSQL_PORT**`: cổng MySQL ra host (mặc định `3306`).
- Các biến `DB_*` và `DB_ROOT_PASSWORD` phải **khớp** với cấu hình database trong `src/.env` (xem bước sau).

Trước khi deploy thật, đổi mật khẩu thành giá trị mạnh.

## 2. Cấu hình Laravel (`src/.env`)

Trong `**src/.env`**, các giá trị tối thiểu khi chạy trong Docker:

```env
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=laravel
DB_PASSWORD=secret

REDIS_HOST=redis
REDIS_PORT=6379
```

`DB_HOST=mysql` và `REDIS_HOST=redis` là **tên service** trong `docker-compose.yml`, không dùng `127.0.0.1` từ trong container PHP.

Đảm bảo `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` trùng với file `.env` ở thư mục gốc (Compose truyền vào container MySQL).

## 3. Build và khởi động

Từ **thư mục gốc** repository (nơi có `docker-compose.yml`):

```bash
docker compose build
docker compose up -d
```

Kiểm tra container:

```bash
docker compose ps
```

## 4. Lần đầu: dependency và migration Laravel

Chạy trong container `**php**` (tên service trong Compose):

```bash
docker compose exec php sh -c "cd /var/www/html && composer install --no-interaction --prefer-dist --optimize-autoloader"
docker compose exec php php artisan migrate --force
```

Tuỳ môi trường, có thể thêm:

```bash
docker compose exec php php artisan config:cache
docker compose exec php php artisan route:cache
docker compose exec php php artisan view:cache
```

Nếu Laravel báo thiếu quyền ghi `storage` / `bootstrap/cache`:

```bash
docker compose exec php sh -c "cd /var/www/html && chmod -R ug+rwx storage bootstrap/cache"
```

(trên Windows bind-mount đôi khi không cần; chỉ khi gặp lỗi permission.)

## 5. Truy cập ứng dụng

- **Web**: `http://localhost` (hoặc `http://localhost:APP_PORT` nếu đổi `APP_PORT`).
- **API** (nếu đã cấu hình route `api`): ví dụ `http://localhost/api/...`.

## 6. Lệnh thường dùng


| Mục đích                                                     | Lệnh (từ thư mục gốc repo)                                       |
| ------------------------------------------------------------ | ---------------------------------------------------------------- |
| Xem log                                                      | `docker compose logs -f` hoặc `docker compose logs -f nginx php` |
| Vào shell PHP                                                | `docker compose exec php sh`                                     |
| Chạy Artisan                                                 | `docker compose exec php php artisan <lệnh>`                     |
| Dừng stack                                                   | `docker compose stop`                                            |
| Gỡ container (giữ volume DB)                                 | `docker compose down`                                            |
| Gỡ container + volume (xóa dữ liệu MySQL/Redis trong volume) | `docker compose down -v`                                         |


## 7. Cổng và dịch vụ


| Service | Port host (mặc định)      | Ghi chú                                    |
| ------- | ------------------------- | ------------------------------------------ |
| nginx   | `80` → 80 trong container | HTTP                                       |
| mysql   | `3306`                    | Kết nối từ máy host: `127.0.0.1:3306`      |
| redis   | không publish             | Chỉ dùng nội bộ mạng Docker (`redis:6379`) |


## 8. Xử lý sự cố nhanh

### Cổng 3306 đã bị chiếm (`bind: Only one usage of each socket address…`)

Trên Windows, cổng **3306** thường đã được dùng bởi MySQL/MariaDB cài sẵn, XAMPP, WAMP hoặc container khác. Docker không map được `0.0.0.0:3306` → lỗi như trên.

**Cách xử lý (khuyến nghị):** trong file **`.env` ở thư mục gốc** (cùng `docker-compose.yml`), đổi biến `MYSQL_PORT` sang cổng trống, ví dụ:

```env
MYSQL_PORT=3307
```

Compose sẽ map **`127.0.0.1:3307` → MySQL trong container vẫn là cổng 3306**. Ứng dụng Laravel **trong container** không cần đổi: `DB_HOST=mysql`, `DB_PORT=3306` trong `src/.env` giữ nguyên.

Chỉ khi bạn kết nối MySQL **từ máy host** (DBeaver, client CLI) mới dùng `localhost:3307`.

Sau khi sửa `.env`: `docker compose up -d` (hoặc `docker compose down` rồi `up -d`).

**Cách khác:** tắt dịch vụ đang dùng 3306 (Services.msc → MySQL, hoặc tắt XAMPP MySQL), rồi giữ `MYSQL_PORT=3306`.

### Các lỗi khác

- **Không vào được trang / 502**: đợi MySQL healthy (`docker compose ps`), xem log `docker compose logs php nginx`.
- **Lỗi kết nối DB**: kiểm tra `DB_HOST=mysql`, user/password khớp file `.env` gốc và `src/.env`.
- **Thay đổi code không thấy (OPcache production)**: trong `docker/php/php.ini` OPcache có thể tắt kiểm tra timestamp; sau khi sửa code, restart PHP: `docker compose restart php` hoặc chỉnh cấu hình phù hợp môi trường dev.

---

Để biết thêm về API, migration và cấu trúc app Laravel, xem `**src/README.md`** và `**docs/HUONG_DAN_MIGRATION.md**` ở thư mục gốc repo.