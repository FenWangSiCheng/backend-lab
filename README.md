# Backend Lab

基于 Laravel 的用户管理 API，使用 Docker Compose 运行 Nginx、PHP-FPM 和 MySQL。

当前环境：PHP 8.5、Laravel 13、Nginx 1.28、MySQL 8.4。

## 启动项目

```bash
docker compose up -d
docker compose exec app php artisan migrate
```

服务地址：

- API：http://localhost:8080
- MySQL：localhost:3306

停止服务：

```bash
docker compose down
```

## API

健康检查：

```bash
curl http://localhost:8080/api/health
```

用户 CRUD 调用链：

```text
Route → FormRequest → Controller → Service → Repository Contract
                                            ↓
                                  Eloquent Repository → User Model → MySQL
```

创建用户：

```bash
curl -X POST http://localhost:8080/api/users \
  -H 'Content-Type: application/json' \
  -H 'Accept: application/json' \
  -d '{"name":"张三","email":"zhangsan@example.com","password":"password123","password_confirmation":"password123"}'
```

其他接口：

```bash
curl http://localhost:8080/api/users
curl http://localhost:8080/api/users/1
curl -X PATCH http://localhost:8080/api/users/1 -H 'Content-Type: application/json' -d '{"name":"新名字"}'
curl -X DELETE http://localhost:8080/api/users/1
```

## 测试与排障

```bash
docker compose exec app php artisan test
docker compose logs -f app
docker compose logs -f nginx
docker compose logs -f mysql
```
