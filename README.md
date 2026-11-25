# TechMart – Electronic Store Product Management System

A web-based admin system for managing the products, categories and stock of an electronic store.
Built with **Core PHP, MySQL, HTML, CSS and Bootstrap 5** as an individual final project for
**Web Systems Technologies (PHP and MySQL Development)**.

**Default admin login:** `admin` / `admin123` (change it before showing it publicly).

---

## Purpose of the System

Small electronic stores often track their gadgets, prices and stock in notebooks or spreadsheets.
This makes it easy to lose track of what is available, what is running low, and what is already sold out.

**TechMart** solves this by giving the store administrator one secure place to:
- keep an organized list of all electronic products (phones, laptops, audio, gaming, etc.),
- group products into categories,
- see stock levels at a glance and spot items that need restocking,
- quickly find any product using search and filters.

It also demonstrates the course requirements: a responsive Bootstrap 5 front-end,
full CRUD operations with PHP and MySQL, a properly designed database, and deployment on InfinityFree.

---

## Features

| Module | What the admin can do |
|---|---|
| **Admin Login / Logout** | Secure login using PHP sessions and hashed passwords. All pages are protected. |
| **Dashboard** | See total products, categories, available stocks, low-stock and out-of-stock counts, a "Needs Restocking" list and recently added products. Stat cards link to the filtered product lists. |
| **Product Management** | **Add, view, edit and delete** products with name, category, brand, model number, description, purchase price, selling price, stock quantity, image and date added. |
| **Category Management** | **Add, view, edit and delete** categories. A category that still has products cannot be deleted (protects your data). |
| **Inventory Management** | Monitor quantities with status badges (**In Stock**, **Low Stock** = 10 or fewer, **Out of Stock** = 0) and update quantities quickly. Shows the total stock value. |
| **Search and Filter** | Search by product name, brand, model number or category; filter by category and by stock status. |

**Extra quality features:** prepared statements (SQL injection protection), input validation, CSRF tokens,
image upload checks (JPG/PNG/GIF/WEBP, max 2MB), delete confirmation dialogs,
success/error notifications, and a responsive sidebar that works on phones.

---

## Database Design (simple explanation)

Database name: **`electronic_store_db`** – it has **3 tables**.

| Table | What it stores | Important fields |
|---|---|---|
| `users` | The people who can log in to the admin dashboard. | `id` (primary key), `full_name`, `username` (unique), `password` (hashed), `created_at` |
| `categories` | The groups that products belong to (e.g. Smartphones, Laptops, Audio). | `id` (primary key), `name` (unique), `description`, `created_at` |
| `products` | The electronic items for sale. | `id` (primary key), `name`, `category_id` (foreign key), `brand`, `model_number`, `description`, `purchase_price`, `selling_price`, `stock_quantity`, `image`, `date_added` |

The `products` table has 11 fields (the requirement is at least 5), each with a proper data type:
`VARCHAR` for text, `TEXT` for the long description, `DECIMAL(10,2)` for prices (accurate money values),
`INT` for stock quantity, and `TIMESTAMP` for the date added.

### Relationship between the tables

- **One category has many products**, and **each product belongs to exactly one category** (one-to-many).
- This is created by the foreign key `products.category_id → categories.id`.
- Example: the category *Audio* can contain *Sony WH-CH520 Headphones* and *AirPods Pro*, but each of those products has only one category.
- The foreign key uses `ON DELETE RESTRICT`, which means a category **cannot be deleted while it still has products**. This prevents products from ending up with a category that no longer exists.
- The `users` table stands on its own. It is only used to log in, so it has no relationship with the other tables.

```
    CATEGORIES ||--o{ PRODUCTS : "has many"
    CATEGORIES {
        int id PK
        varchar name
        varchar description
        timestamp created_at
    }
    PRODUCTS {
        int id PK
        varchar name
        int category_id FK
        varchar brand
        varchar model_number
        text description
        decimal purchase_price
        decimal selling_price
        int stock_quantity
        varchar image
        timestamp date_added
    }
    USERS {
        int id PK
        varchar full_name
        varchar username
        varchar password
        timestamp created_at
    }
```

```
categories (1) ───────< (many) products
   id  ◄──────────────── category_id
```

---

## Folder Structure
```
electronic_store/
├── index.php              → redirects to login/dashboard
├── login.php / logout.php
├── dashboard.php
├── inventory.php
├── config/db_connect.php  → database connection (edit InfinityFree credentials here)
├── includes/              → init.php, functions.php, header.php, footer.php
├── products/              → index, add, edit, delete, _form, validate
├── categories/            → index (list + add), edit, delete
├── assets/css/style.css   → dark blue / white / light gray theme
├── assets/js/script.js    → delete dialog, auto-hide alerts, image preview
├── uploads/               → product images
└── database/
    ├── electronic_store_db.sql              → for XAMPP (creates the database)
    └── electronic_store_db_infinityfree.sql → for InfinityFree (tables + data only)
```

---

