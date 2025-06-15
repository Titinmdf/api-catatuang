// ============================================
// 📖 API Documentation & Usage Guide
// ============================================

/*
🚀 CARA SETUP & INSTALASI:

1. Clone/Setup Laravel Project:
   composer create-project laravel/laravel catatuang-api
   cd catatuang-api

2. Install Sanctum:
   composer require laravel/sanctum
   php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"

3. Copy semua file di atas ke lokasi yang sesuai

4. Setup Database:
   - Buat database MySQL bernama 'catatuang'
   - Update .env dengan kredensial database Anda
   
5. Run Migration & Seeder:
   php artisan migrate
   php artisan db:seed

6. Start Server:
   php artisan serve

📡 API ENDPOINTS:

🔐 AUTHENTICATION:
POST /api/register
POST /api/login
POST /api/logout (requires auth)
GET  /api/profile (requires auth)

👥 USER CATEGORIES:
GET    /api/categories
POST   /api/categories
GET    /api/categories/{id}
PUT    /api/categories/{id}
DELETE /api/categories/{id}

💼 WALLET TYPES:
GET    /api/wallet-types
POST   /api/wallet-types
GET    /api/wallet-types/{id}
PUT    /api/wallet-types/{id}
DELETE /api/wallet-types/{id}

💰 WALLETS:
GET    /api/wallets
POST   /api/wallets
GET    /api/wallets/{id}
PUT    /api/wallets/{id}
DELETE /api/wallets/{id}

📊 TRANSACTIONS:
GET    /api/transactions
POST   /api/transactions
GET    /api/transactions/{id}
PUT    /api/transactions/{id}
DELETE /api/transactions/{id}
GET    /api/transactions-summary

📝 CONTOH PENGGUNAAN:

1. Register User:
POST /api/register
{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123"
}

2. Login:
POST /api/login
{
    "email": "john@example.com",
    "password": "password123"
}

3. Create Category:
POST /api/categories
Headers: Authorization: Bearer {token}
{
    "name": "Gaji",
    "type": "income",
    "icon": "salary"
}

4. Create Wallet:
POST /api/wallets
Headers: Authorization: Bearer {token}
{
    "name": "BCA Tabungan",
    "user_wallet_type_id": 1,
    "balance": 1000000
}

5. Create Transaction:
POST /api/transactions
Headers: Authorization: Bearer {token}
transactions

6. Get Summary:
GET /api/transactions-summary?start_date=2024-01-01&end_date=2024-01-31
Headers: Authorization: Bearer {token}

🔒 KEAMANAN:
- Semua endpoint (kecuali register/login) dilindungi dengan Sanctum authentication
- Setiap user hanya bisa mengakses data miliknya sendiri
- Data terisolasi per user (multi-tenant)
- Password di-hash menggunakan bcrypt

✨ FITUR UTAMA:
- User registration & authentication
- CRUD categories, wallet types, wallets, transactions
- Automatic wallet balance calculation
- Transaction filtering by date, type, wallet
- Financial summary with period filter
- Pagination on transaction list
- Database transactions for data consistency
- Comprehensive validation on all inputs


Semua response menggunakan format JSON yang konsisten dengan struktur:
{
    "success": true/false,
    "message": "...",
    "data": {...}
}
*/