---
description: finish the backend setup and migrations
---
1. Generate APP_KEY
// turbo
2. php artisan key:generate
3. Run Migrations
// turbo
4. php artisan migrate --force
5. Seed Roles
// turbo
6. php artisan db:seed --class=RoleAndPermissionSeeder
7. Serve Backend
// turbo
8. php artisan serve
