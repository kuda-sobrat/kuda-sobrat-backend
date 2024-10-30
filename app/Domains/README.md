# Домены

## ▎Описание

Директория предназначена для возможного масштабирования.
Предполагается разбиение по модулям (доменам) в совокупности с определением сервисов,
интерфейсов, репозиториев и прочих стандартных элементов архитектуры DDD.

## ▎Пример структуры

```bash
app/
└── Domains/
    ├── User/
    │   ├── Controllers/
    │   │   └── Api/
    │   │       └── V1/
    │   │           └── UserController.php
    │   ├── Models/
    │   │   └── User.php
    │   ├── Services/
    │   │   └── UserService.php
    │   ├── Repositories/
    │   │   ├── UserRepository.php
    │   │   └── UserRepositoryInterface.php
    │   ├── Requests/
    │   │   └── UserRequest.php
    │   ├── Providers/
    │   │   └── UserServiceProvider.php
    │   └── Tests/
    │       └── Feature/
    └── Product/
        ├── Controllers/
        │   └── Api/
        │       └── V1/
        │           └── ProductController.php
        ├── Models/
        │   └── Product.php
        ├── Services/
        │   └── ProductService.php
        ├── Repositories/
        │   ├── ProductRepository.php
        │   └── ProductRepositoryInterface.php
        ├── Requests/
        │   └── ProductRequest.php
        ├── Providers/
        │   └── ProductServiceProvider.php
        └── Tests/
            └── Feature/
```
Структура может быть доработана в дальнейшем
