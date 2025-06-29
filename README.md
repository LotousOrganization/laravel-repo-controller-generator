# 🚀 Laravel Repo Controller Generator

**A lightweight Laravel package to generate full-featured CRUD controllers, repositories, form requests, and resources — all in one shot.**

---

## 👋 Introduction

Hi, I'm **Sobhan Aali** — thanks for checking out this package!

`laravel-repo-controller-generator` is designed to **speed up your Laravel development workflow** by generating a full CRUD setup with just one Artisan command. It promotes clean architecture by separating concerns using repositories, request validation, and resource transformation.

---

## 📦 Features

- ✅ Generate **CRUD Controllers**
    
- ✅ Automatically create **Repositories**
    
- ✅ Generate **Form Requests** for `store` and `update`
    
- ✅ Create **API Resources** (both admin and public versions)
    
- ✅ Clean file structure based on namespace conventions
    

---

## ⚙️ Installation

Install the package via Composer:

```bash
composer require sobhan-aali/laravel-repo-controller-generator:dev-master
```

---

## 🧱 Before You Start

### 1. Publish Stub Files

The package uses stub templates to generate your classes. Run this command to publish them for customization:

```bash
php artisan vendor:publish --tag=repo-controller-stubs
```

### 2. Use the `GetFillables` Trait

Make sure your base model uses the `GetFillables` trait for automatic field generation:

```php
use SobhanAali\LaravelRepoControllerGenerator\Traits\GetFillables;

class BaseModel extends Model
{
    use GetFillables;
}
```

> You can extend your Eloquent models from this base class for consistency.

---

## 🚀 Usage

Run the following command to generate a complete CRUD setup:

```bash
php artisan make:repo-controller Api/V1/Admin/CRUD/PostController
```

> ⚠️ **Note**: The namespace must begin with `Api/V` followed by your desired version (e.g., `V1`).

---

## 🗂️ What Gets Generated?

Running the command above will generate the following files for the `Post` model:

### 🧾 Form Requests

- `app/Http/Requests/Admin/CRUD/Post/StorePostRequest.php`
    
- `app/Http/Requests/Admin/CRUD/Post/UpdatePostRequest.php`
    

### 🎯 Resources

- `app/Http/Resources/Post/PostResource.php`
    
- `app/Http/Resources/Post/AdminPostResource.php`
    

### 🧠 Repository

- `app/Repositories/Eloquent/PostRepository.php`
    

### 🎮 Controller

- `app/Http/Controllers/Api/V1/Admin/CRUD/PostController.php`
    

All files are tightly connected and follow Laravel's best practices. With minimal adjustments, you can plug them directly into your application and get started fast.
