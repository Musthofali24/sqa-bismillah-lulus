from sqlalchemy.orm import Session
from models import Kategori, Produk, Pelanggan, TransaksiHeader, TransaksiDetail
from schemas import KategoriCreate, ProdukCreate, PelangganCreate, TransaksiHeaderCreate
from decimal import Decimal


# Kategori
def get_kategori(db: Session, kategori_id: int):
    return db.query(Kategori).filter(Kategori.id == kategori_id).first()


def get_kategori_by_nama(db: Session, nama: str):
    return db.query(Kategori).filter(Kategori.nama == nama).first()


def get_kategoris(db: Session, skip: int = 0, limit: int = 100):
    return db.query(Kategori).offset(skip).limit(limit).all()


def create_kategori(db: Session, kategori: KategoriCreate):
    db_kategori = Kategori(nama=kategori.nama)
    db.add(db_kategori)
    db.commit()
    db.refresh(db_kategori)
    return db_kategori


def update_kategori(db: Session, kategori_id: int, kategori: KategoriCreate):
    db_kategori = get_kategori(db, kategori_id)
    if db_kategori:
        db_kategori.nama = kategori.nama
        db.commit()
        db.refresh(db_kategori)
    return db_kategori


def delete_kategori(db: Session, kategori_id: int):
    db_kategori = get_kategori(db, kategori_id)
    if db_kategori:
        db.delete(db_kategori)
        db.commit()
    return db_kategori


# Produk
def get_produk(db: Session, produk_id: int):
    return db.query(Produk).filter(Produk.id == produk_id).first()


def get_produks(db: Session, skip: int = 0, limit: int = 100):
    return db.query(Produk).offset(skip).limit(limit).all()


def create_produk(db: Session, produk: ProdukCreate):
    db_produk = Produk(**produk.dict())
    db.add(db_produk)
    db.commit()
    db.refresh(db_produk)
    return db_produk


def update_produk(db: Session, produk_id: int, produk: ProdukCreate):
    db_produk = get_produk(db, produk_id)
    if db_produk:
        for key, value in produk.dict().items():
            setattr(db_produk, key, value)
        db.commit()
        db.refresh(db_produk)
    return db_produk


def delete_produk(db: Session, produk_id: int):
    db_produk = get_produk(db, produk_id)
    if db_produk:
        db.delete(db_produk)
        db.commit()
    return db_produk


# Pelanggan
def get_pelanggan(db: Session, pelanggan_id: int):
    return db.query(Pelanggan).filter(Pelanggan.id == pelanggan_id).first()


def get_pelanggans(db: Session, skip: int = 0, limit: int = 100):
    return db.query(Pelanggan).offset(skip).limit(limit).all()


def create_pelanggan(db: Session, pelanggan: PelangganCreate):
    db_pelanggan = Pelanggan(**pelanggan.dict())
    db.add(db_pelanggan)
    db.commit()
    db.refresh(db_pelanggan)
    return db_pelanggan


def update_pelanggan(db: Session, pelanggan_id: int, pelanggan: PelangganCreate):
    db_pelanggan = get_pelanggan(db, pelanggan_id)
    if db_pelanggan:
        for key, value in pelanggan.dict().items():
            setattr(db_pelanggan, key, value)
        db.commit()
        db.refresh(db_pelanggan)
    return db_pelanggan


def delete_pelanggan(db: Session, pelanggan_id: int):
    db_pelanggan = get_pelanggan(db, pelanggan_id)
    if db_pelanggan:
        db.delete(db_pelanggan)
        db.commit()
    return db_pelanggan


# Transaksi
def create_transaksi(db: Session, transaksi: TransaksiHeaderCreate):
    pelanggan = db.query(Pelanggan).filter(Pelanggan.id == transaksi.pelanggan_id).first

    if not pelanggan:
        raise ValueError(f"Pelanggan id {transaksi.pelanggan_id} tidak ditemukan")

    total = Decimal("0.00")
    items_data = []
    for item in transaksi.items:
        produk = db.query(Produk).filter(Produk.id == item.produk_id).first()
        if not produk:
            raise ValueError(f"Produk id {item.produk_id} tidak ditemukan")
        if produk.stok < item.qty:
            raise ValueError(f"Stok produk {produk.nama} tidak mencukupi")
        harga_satuan = Decimal(str(produk.harga))
        subtotal = harga_satuan * Decimal(item.qty)
        total += subtotal
        items_data.append(
            {
                "produk_id": item.produk_id,
                "qty": item.qty,
                "harga_satuan": harga_satuan,
                "subtotal": subtotal,
            }
        )

    db_header = TransaksiHeader(
        pelanggan_id=transaksi.pelanggan_id,
        total=total,
        metode_pembayaran=transaksi.metode_pembayaran,
        status_pembayaran=transaksi.status_pembayaran,
    )
    db.add(db_header)
    db.flush()

    for item in items_data:
        db_detail = TransaksiDetail(
            transaksi_id=db_header.id,
            produk_id=item["produk_id"],
            qty=item["qty"],
            harga_satuan=item["harga_satuan"],
            subtotal=item["subtotal"],
        )
        db.add(db_detail)
        produk = db.query(Produk).filter(Produk.id == item["produk_id"]).first()
        produk.stok -= item["qty"]

    db.commit()
    db.refresh(db_header)
    return db_header


def get_transaksi(db: Session, transaksi_id: int):
    return db.query(TransaksiHeader).filter(TransaksiHeader.id == transaksi_id).first()


def get_transaksis(db: Session, skip: int = 0, limit: int = 100):
    return db.query(TransaksiHeader).offset(skip).limit(limit).all()
