# Unified Search Controller - README

This controller provides a **single API endpoint** to perform a website-wide search across multiple content types, including **Products, Blog Posts, Pages, and FAQs**. It also includes endpoints for typeahead suggestions, search logs, and analytics for admin users.

---

## ⚙️ Setup Instructions

This project supports **local** and **Docker** environments, with **MeiliSearch** used for full-text indexing.

### 1. Clone & Install Dependencies

```bash
git clone <repo-url>
cd project
composer install
cp .env.example .env
php artisan key:generate
```

### 2. Docker Setup (Recommended)

```bash
docker-compose build app
docker-compose up -d
```

This starts the following services:

* `app` (Laravel)
* `meilisearch`
* `mysql`

### 3. Configure Scout + MeiliSearch

In `.env`:

```env
SCOUT_DRIVER=meilisearch
MEILISEARCH_HOST=http://meilisearch:7700
```

### 4. Run Migrations & Seed Data

```bash
docker-compose exec app php artisan migrate --seed
```

### 5. Index Data

```bash
docker-compose exec app php artisan scout:import "App\Models\Product"
docker-compose exec app php artisan scout:import "App\Models\Blog"
docker-compose exec app php artisan scout:import "App\Models\Page"
docker-compose exec app php artisan scout:import "App\Models\Faq"
```

---

## 🔍 API Endpoints

### 1. `search()` – Main Unified Search Endpoint

**Purpose:** Perform a full website-wide search across products, blogs, pages, and FAQs.

**Flow:**

1. Accepts user input via the `q` query parameter.
2. Stores search activity in the `search_logs` table.
3. Fetches results individually from `Product`, `BlogPost`, `Page`, and `Faq` models.
4. Merges all results into a single collection.
5. Prioritizes exact matches first.
6. Returns paginated results.

**Key Benefit:** Centralized search in one API call.

---

### 2. `suggestions()` – Typeahead Endpoint

**Purpose:** Provide realtime search suggestions as users type (2+ characters).

**Flow:**

1. Reads partial query string from `q`.
2. Queries each model using Laravel Scout.
3. Collects and merges only `title` or `question` fields.
4. Removes duplicate suggestions.
5. Limits the number of suggestions returned.

**Key Benefit:** Improves UX with faster, predictive search results.

---

### 3. `logs()` – Search Logs (Admin)
 `/admin/login`

**Purpose:** Display user search activity.

**Modes:**

* `recent` – latest searches
* `popular` – most searched keywords (grouped and counted)

**Note:** Admin users are stored in the `users` table. Seeders can prepopulate admin credentials and sample search logs.

---

### 4. `analytics()` – Search Statistics (Admin)

**Purpose:** Provide insights into overall search trends.

**Returns:**

* Total searches
* Unique queries
* Searches today and this week
* Top 10 most searched queries
* 10 most recent searches

**Benefit:** Helps admins understand user interests and content engagement.

---

## ✅ Feature Summary

| Feature     | Function        | Purpose                                              |
| ----------- | --------------- | ---------------------------------------------------- |
| Search      | `search()`      | Unified full search                                  |
| Suggestions | `suggestions()` | Typeahead / autocomplete                             |
| Logs        | `logs()`        | Admin – view recent & popular queries (latest first) |
| Analytics   | `analytics()`   | Admin – statistics & insights                        |

---

## 🧵 Queues & Scheduler (Optional)

Search logging and indexing are automatically handled via **model events**. Whenever a model is **created, updated, or deleted**, the corresponding search index is updated. Additionally, a daily scheduler ensures indexing is refreshed every day.



## 🔎 Indexing & Search Logic

The application uses **Laravel Scout** with **MeiliSearch** as the backend.

**Indexing Flow:**

1. Data is stored in MySQL when products/blogs/faqs are created.
2. Scout observes the model (`Searchable` trait).
3. On save/update/delete, a JSON document is pushed to MeiliSearch automatically.
4. Daily scheduled indexing ensures consistency.
5. Queries hit MeiliSearch, not MySQL, for **millisecond-level responses**.

**Searchable Fields by Model:**

| Model   | Indexed Fields              |
| ------- | --------------------------- |
| Product | name, description, category |
| Blog    | title, content              |
| Page    | title, content              |
| FAQ     | question, answer            |

**Example `toSearchableArray()` in Product model:**

```php
public function toSearchableArray()
{
    return [
        'name' => $this->name,
        'description' => $this->description,
    ];
}
```

**Relevance Ranking by MeiliSearch:**

1. Typo tolerance
2. Exact match score
3. Keyword proximity
4. Attribute importance

---

### 🔍 Sample API Queries

* Unified search: `/api/search?q=iphone`
* Suggestions: `/api/search/suggestions?q=iph`
* Logs (popular): `/api/search/logs?type=popular`

**Sample Search Response:**

```json
{
    "query": "aut",
    "current_page": 1,
    "per_page": 10,
    "total_results": 139,
    "data": [
        {
            "type": "product",
            "title": "aut",
            "snippet": "Eaque ut itaque dolores inventore animi assumenda non.",
            "link": 2,
            "created_at": "2025-10-21T18:39:18.000000Z"
        },
        {
            "type": "product",
            "title": "aut",
            "snippet": "Minus ex consequatur eos aperiam sed sed id atque voluptatem est veritatis.",
            "link": 69,
            "created_at": "2025-10-21T19:17:01.000000Z"
        }
    ]
}
```

---

### ✅ Key Advantages

* Centralized search across multiple content types.
* Real-time suggestions for improved UX.
* Admin analytics to track trends and user behavior (admins stored in `users` table, with latest search logs first).
* Automatic indexing on create, update, delete, and daily refresh.
* Optimized full-text search using MeiliSearch.
* Optional asynchronous processing via queues.
