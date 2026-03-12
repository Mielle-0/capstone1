# 📥 Feedback Management System

An attempt at a feedback classification system inspired by the **fms.umin.edu.ph** submission flow. This project uses a **Laravel 10** web interface to manage submissions and a **FastAPI (Python 3.12)** to predict departments and categories using Machine Learning models.

## 🛠 Tech Stack

### Web / Management (Laravel)

* **PHP:** ^8.1
* **Framework:** Laravel 10.10
* **Charts:** Larapex Charts (for feedback analytics)
* **Database:** MySQL 8 and Doctrine DBAL (for schema management)

### Machine Learning API (Python)

* **Language:** Python 3.12
* **Framework:** FastAPI
* **Storage:** Local `.joblib` models (referenced from Hugging Face)

---

## Getting Started

### 1. Web Application (Laravel)

First, clone the repository and enter the project folder.

**Installation:**

```bash
composer install

```

**Environment Setup:**

1. Copy the example env file: `cp .env.example .env`
2. Generate an app key: `php artisan key:generate`
3. Configure your database settings in `.env`.

**Database & Seeding:**
This project includes migrations for the feedback structure and seeders for institutional department data.

```bash
php artisan migrate --seed

```

**Run Server:**

```bash
php artisan serve

```

---

### 2. Machine Learning API (Python)

The API handles the logic for predicting which department ID, based from an institution (not suggested for other uses).

**Navigate and Setup Virtual Environment:**

```bash
cd python_api
python -m venv venv

```

**Activate Environment:**

* **Windows:** `venv\Scripts\activate`
* **Mac/Linux:** `source venv/bin/activate`

**Install Dependencies:**

```bash
pip install -r requirements.txt

```

**Model Setup:**
Ensure your `.joblib` files are placed in the `python_api/models/department` directory. These models are trained based on public institutional data and hosted on [Hugging Face](https://huggingface.co/Mielle-0/department-prediction-v4/tree/main).

**Run API:**

```bash
uvicorn main:app --reload --port 5000

```
---

**Test the API**
Open two terminal and change directory to project folder.

*Terminal 1*
```bash
php artisan queue:work

```
*Terminal 2*
```bash
php artisan db:seed --class=FeedbackSeeder

```


---

## 📂 Project Structure

```text
├── app/                
├── python_api/         
│   ├── models/         # .joblib files (Ignored by Git)
│       └── department/         # place models here
│   └── main.py         # API Entry point
│   └── local.py        # Suggested to use for local testing
├── database/           
├── resources/          
└── .gitignore          

```