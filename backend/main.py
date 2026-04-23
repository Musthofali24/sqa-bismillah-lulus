from fastapi import FastAPI
from fastapi.middleware.cors import CORSMiddleware
from database import engine, Base
from routers import kategori, produk, pelanggan, transaksi, auth

Base.metadata.create_all(bind=engine)

app = FastAPI(title="API Sistem Penjualan", version="2.0.0")

app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

app.include_router(auth.router)
app.include_router(kategori.router)
app.include_router(produk.router)
app.include_router(pelanggan.router)
app.include_router(transaksi.router)


@app.get("/")
def root():
    return {"message": "API Sistem Penjualan Sederhana - Protected by JWT"}
