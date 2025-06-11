# Laravel Repo Controller Generator

  

**A simple Laravel package to generate CRUD Controllers along with Repository and Request classes.**

  

---

  

## 👋 Introduction

  

Hi, I'm **Sobhan Aali** — thank you for choosing this package!

  

This tool helps you quickly scaffold a full set of files for a CRUD resource in your Laravel project, including:

  

- ✅ **CRUD Controller**

- ✅ **Repository**

- ✅ **Form Requests** for `store` and `update`

  

---

  

Before running the command, make sure to publish the stub files that the generator uses as templates:

  

```

php artisan vendor:publish --tag=repo-controller-stubs

```



  ---

## 🚀 Usage

  

To generate a full CRUD setup for a resource, run the following command:

  

```

php artisan make:repo-controller Api/V1/Admin/CRUD/PostController

```