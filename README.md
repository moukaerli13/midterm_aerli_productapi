# Lab 02 – Laravel REST API + Flutter (Products)

```
lab02/
├── product_api/   Laravel 13 REST API (MySQL)
└── product_app/   Flutter app (CRUD products)
```

## 1–6. Laravel (product_api)

| # | Task | File |
|---|------|------|
| 1 | Laravel project | `product_api/` |
| 2 | Migrations | `database/migrations/2026_11_15_000001_create_categories_table.php`, `..._000002_create_products_table.php` |
| 3 | Models | `app/Models/Category.php`, `app/Models/Product.php` |
| 4–5 | Controllers (index, store, show, update, destroy) | `app/Http/Controllers/Api/CategoryController.php`, `ProductController.php` |
| 6 | API routes | `routes/api.php` (registered in `bootstrap/app.php`) |

### API endpoints

| Method | URL | Method in controller |
|--------|-----|----------------------|
| GET | `/api/products` (`?search=`) | index |
| POST | `/api/products` | store |
| GET | `/api/products/{id}` | show |
| PUT | `/api/products/{id}` | update |
| DELETE | `/api/products/{id}` | destroy (sets `isDeleted = 1`) |

Same 5 routes exist for `/api/categories`.

### Run

XAMPP's PHP is missing extensions, so use the portable PHP in `C:\php83`:

```bash
cd product_api
C:\php83\php.exe artisan migrate:fresh --seed
C:\php83\php.exe artisan serve --host=0.0.0.0 --port=8000
```

Database: `lab02_product_db` on XAMPP MySQL (root, no password) – see `.env`.

## 7–8. Flutter (product_app)

| Screen | File | API |
|--------|------|-----|
| Product list (search, edit, delete) | `lib/screens/product_list_screen.dart` | GET, DELETE |
| Product detail | `lib/screens/product_detail_screen.dart` | GET {id}, DELETE |
| Add / Edit form | `lib/screens/product_form_screen.dart` | POST, PUT, GET categories |
| API calls | `lib/services/api_service.dart` | all |
| Models | `lib/models/product.dart`, `category.dart` | |
| API URL | `lib/config/api_config.dart` | |

```bash
cd product_app
flutter run
```

API URL: Android emulator uses `10.0.2.2:8000`, Web/Windows use `127.0.0.1:8000`.
For a real phone, set `_lanHost` in `api_config.dart` to your PC's IP.
