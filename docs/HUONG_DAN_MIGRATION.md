# Hướng dẫn migration database

Các file migration nằm trong `src/database/migrations/`. Bảng `users` gốc do Laravel tạo (`0001_01_01_000000_create_users_table.php`); phần bổ sung và các bảng nghiệp vụ dùng timestamp `2026_04_02_1000xx`.

## Lệnh Artisan đã dùng (tương đương khi tạo bằng CLI)

Chạy trong thư mục `src` (hoặc `cd src` trước):

```bash
cd src

php artisan make:migration add_profile_fields_to_users_table
php artisan make:migration create_workspaces_table
php artisan make:migration create_workspace_users_table
php artisan make:migration create_projects_table
php artisan make:migration create_project_users_table
php artisan make:migration create_columns_table
php artisan make:migration create_tasks_table
php artisan make:migration create_task_assignees_table
php artisan make:migration create_comments_table
php artisan make:migration create_activity_logs_table
php artisan make:migration create_attachments_table
```

Nội dung migration đã được điền sẵn trong repo (có thể tạo thủ công hoặc chỉnh sau khi `make:migration`).

## Chạy migration

**Môi trường Docker (MySQL trong compose):**

```bash
cd src
docker compose exec php php artisan migrate --force
```

Hoặc từ máy local khi `.env` trỏ đúng DB:

```bash
cd src
php artisan migrate
```

**Kiểm tra nhanh bằng SQLite (một lần, không ghi đè `.env` lâu dài):**

PowerShell:

```powershell
cd src
$env:DB_CONNECTION="sqlite"
$env:DB_DATABASE="database/database.sqlite"
php artisan migrate:fresh --force
```

## Rollback (khi cần)

```bash
cd src
php artisan migrate:rollback --step=11
```

(Số bước tùy số migration mới; hoặc `migrate:reset` trên môi trường dev.)

## Tóm tắt bảng

| Bảng | Mô tả ngắn |
|------|------------|
| `users` | Bảng mặc định Laravel + migration bổ sung `avatar`, `timezone`. |
| `workspaces` | Không gian làm việc; `owner_id`, `slug` unique, soft delete. |
| `workspace_users` | Thành viên workspace + `role`; unique `(workspace_id, user_id)`. |
| `projects` | Thuộc `workspace_id`; `slug` unique theo workspace; soft delete. |
| `project_users` | Thành viên project + `role`; unique `(project_id, user_id)`. |
| `columns` | Cột Kanban thuộc project (`position`). |
| `tasks` | Thuộc `project` + `column`; `created_by`, `due_at`, soft delete. |
| `task_assignees` | Gán user cho task; unique `(task_id, user_id)`. |
| `comments` | Comment task; `parent_id` cho thread. |
| `activity_logs` | Log hoạt động (polymorphic `subject_type` / `subject_id`), `properties` JSON. |
| `attachments` | File đính kèm polymorphic (`attachable_type`, `attachable_id`). |

## Ghi chú

- `role` (workspace/project) lưu dạng chuỗi (`member`, `admin`, …) — có thể thay bằng enum trong code.
- `attachments` dùng `morphs('attachable')` để gắn vào `Task`, `Comment`, v.v. qua model.
- `activity_logs` dùng pattern giống audit log; có thể map sang `MorphTo` trong Eloquent.
