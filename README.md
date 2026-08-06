# Backend Lab：第一周实践项目

本项目完成第一周的三个目标：跑通 Laravel + Docker 环境、练习 PHP 基础语法、编写并请求 JSON API。

当前运行版本：PHP 8.5.9、Composer 2.10.2、Nginx 1.28.3 stable、MySQL 8.4.11 LTS。

## 1. 启动与验证环境

```bash
docker compose up -d
docker compose ps
docker compose exec app php artisan migrate
curl http://localhost:8080/api/health
```

预期健康检查返回 `status: ok`。三个服务分别是：

- Nginx：http://localhost:8080
- MySQL：localhost:3306（数据库 `backend_lab`，用户名/密码 `laravel`）
- PHP-FPM：容器内部端口 9000，不直接访问

停止环境：

```bash
docker compose down
```

## 2. API 练习

GET 健康检查：

```bash
curl http://localhost:8080/api/health
```

POST JSON 请求：

```bash
curl -X POST http://localhost:8080/api/greet \
  -H 'Content-Type: application/json' \
  -H 'Accept: application/json' \
  -d '{"name":"小王"}'
```

在 Postman 中创建同样的两个请求，并将 `http://localhost:8080` 保存为 `base_url` 环境变量。
也可以直接导入 `postman/Backend-Lab.postman_collection.json`，两个请求和变量均已配置好。

用户 CRUD 使用标准分层：

```text
Route → FormRequest → Controller → Service → Repository Contract
                                            ↓
                                  Eloquent Repository → User Model → MySQL
```

```bash
# 创建用户
curl -X POST http://localhost:8080/api/users \
  -H 'Content-Type: application/json' \
  -H 'Accept: application/json' \
  -d '{"name":"张三","email":"zhangsan@example.com","password":"password123","password_confirmation":"password123"}'

# 列表、详情、更新、删除
curl http://localhost:8080/api/users
curl http://localhost:8080/api/users/1
curl -X PATCH http://localhost:8080/api/users/1 -H 'Content-Type: application/json' -d '{"name":"新名字"}'
curl -X DELETE http://localhost:8080/api/users/1
```

## 3. PHP 基础练习

练习覆盖变量、数组、函数、类、构造函数、严格类型、文件写入和读取：

```bash
docker compose exec app php exercises/week1.php
```

运行后会生成 `exercises/week1-result.json`。

## 4. 测试和常用排障命令

```bash
docker compose exec app php artisan test
docker compose logs -f app
docker compose logs -f nginx
docker compose logs -f mysql
docker compose exec app bash
```

## 5. 第一周学习清单

- [x] Docker 启动 Nginx、PHP-FPM、MySQL
- [x] Laravel 数据库迁移
- [x] 编写返回 JSON 的测试路由
- [x] 编写带参数校验的 POST 路由
- [x] PHP 数组、函数、类及文件读写练习
- [ ] 亲自在 Postman 中创建并发送两个请求
- [ ] 使用 TablePlus、DBeaver 等工具查看 MySQL 的 `migrations` 表
- [ ] 修改练习：新增一位学生，并把及格线改成 70 分
