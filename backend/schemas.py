from pydantic import BaseModel
from datetime import datetime
from typing import List, Optional
from enum import Enum


class KategoriBase(BaseModel):
    nama: str


class KategoriCreate(KategoriBase):
    pass


class Kategori(KategoriBase):
    id: int

    class Config:
        from_attributes = True


class ProdukBase(BaseModel):
    nama: str
    harga: float
    stok: int
    kategori_id: int


class ProdukCreate(ProdukBase):
    pass


class Produk(ProdukBase):
    id: int
    kategori: Optional[Kategori] = None

    class Config:
        from_attributes = True


class PelangganBase(BaseModel):
    nama: str
    telepon: Optional[str] = None
    alamat: Optional[str] = None


class PelangganCreate(PelangganBase):
    pass


class Pelanggan(PelangganBase):
    id: int

    class Config:
        from_attributes = True


class MetodePembayaran(str, Enum):
    cash = "cash"
    transfer = "transfer"
    qris = "qris"


class StatusPembayaran(str, Enum):
    lunas = "lunas"
    belum_lunas = "belum_lunas"


class TransaksiDetailBase(BaseModel):
    produk_id: int
    qty: int


class TransaksiDetailCreate(TransaksiDetailBase):
    pass


class TransaksiDetail(TransaksiDetailBase):
    id: int
    harga_satuan: float
    subtotal: float
    produk: Optional[Produk] = None

    class Config:
        from_attributes = True


class TransaksiHeaderBase(BaseModel):
    pelanggan_id: int
    metode_pembayaran: MetodePembayaran
    status_pembayaran: StatusPembayaran = StatusPembayaran.lunas


class TransaksiHeaderCreate(TransaksiHeaderBase):
    items: List[TransaksiDetailCreate]


class TransaksiHeader(TransaksiHeaderBase):
    id: int
    tanggal: datetime
    total: float
    pelanggan: Optional[Pelanggan] = None
    detail: List[TransaksiDetail] = []

    class Config:
        from_attributes = True
