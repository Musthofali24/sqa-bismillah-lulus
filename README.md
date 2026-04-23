# SQA Ujian

Proyek untuk ujian Software Quality Assurance, terdiri dari backend FastAPI, frontend PHP, dan kumpulan test.

## Struktur Proyek

```
sqa-ujian/
├── backend/
├── frontend/
├── tests/
│   └── postman/
├── database.sql
├── sqa-ujian.side
├── Dockerfile
└── docker-compose.yml
```

## File Penting

- **Skema database** : `database.sql`
- **Koleksi Postman** : `tests/postman/Ujian SQA Collection.postman_collection.json`
- **Environment Postman** : `tests/postman/Ujian SQA.postman_environment.json`
- **File test Selenium** : `sqa-ujian.side` (buka dengan ekstensi Selenium IDE di browser)

## Menjalankan Proyek

1. Import database menggunakan `database.sql`
2. Install dependensi backend:

```bash
   cd backend
   pip install -r requirements.txt
```

3. Jalankan backend:
   ```bash
   uvicorn main:app --reload
   ```
4. Jalankan folder `frontend/` menggunakan web server PHP (contoh: XAMPP, Laragon)

## Menjalankan Test

- **Postman** : Import `tests/postman/Ujian SQA Collection.postman_collection.json` beserta file environment ke Postman, lalu jalankan koleksinya
- **Selenium** : Buka `sqa-ujian.side` menggunakan ekstensi Selenium IDE di browser firefox
