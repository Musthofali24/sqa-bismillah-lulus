from sqlalchemy import (
    Column,
    Integer,
    String,
    DECIMAL,
    DateTime,
    Text,
    ForeignKey,
    Enum,
)
from sqlalchemy.sql import func
from sqlalchemy.orm import relationship
from database import Base
import enum


class MetodePembayaranEnum(str, enum.Enum):
    cash = "cash"
    transfer = "transfer"
    qris = "qris"


class StatusPembayaranEnum(str, enum.Enum):
    lunas = "lunas"
    belum_lunas = "belum_lunas"


class Kategori(Base):
    __tablename__ = "kategori"
    id = Column(Integer, primary_key=True, index=True)
    nama = Column(String(100), unique=True, nullable=False)
    produk = relationship("Produk", back_populates="kategori")


class Produk(Base):
    __tablename__ = "produk"
    id = Column(Integer, primary_key=True, index=True)
    nama = Column(String(200), nullable=False)
    harga = Column(DECIMAL(10, 2), nullable=False)
    stok = Column(Integer, nullable=False, default=0)
    kategori_id = Column(Integer, ForeignKey("kategori.id"), nullable=False)
    kategori = relationship("Kategori", back_populates="produk")
    transaksi_detail = relationship("TransaksiDetail", back_populates="produk")


class Pelanggan(Base):
    __tablename__ = "pelanggan"
    id = Column(Integer, primary_key=True, index=True)
    nama = Column(String(200), nullable=False)
    telepon = Column(String(20))
    alamat = Column(Text)
    transaksi = relationship("TransaksiHeader", back_populates="pelanggan")


class TransaksiHeader(Base):
    __tablename__ = "transaksi_header"
    id = Column(Integer, primary_key=True, index=True)
    pelanggan_id = Column(Integer, ForeignKey("pelanggan.id"), nullable=False)
    tanggal = Column(DateTime, server_default=func.now())
    total = Column(DECIMAL(10, 2), nullable=False)
    metode_pembayaran = Column(Enum(MetodePembayaranEnum), nullable=False)
    status_pembayaran = Column(
        Enum(StatusPembayaranEnum), default=StatusPembayaranEnum.lunas
    )
    pelanggan = relationship("Pelanggan", back_populates="transaksi")
    detail = relationship(
        "TransaksiDetail", back_populates="transaksi", cascade="all, delete-orphan"
    )


class TransaksiDetail(Base):
    __tablename__ = "transaksi_detail"
    id = Column(Integer, primary_key=True, index=True)
    transaksi_id = Column(Integer, ForeignKey("transaksi_header.id"), nullable=False)
    produk_id = Column(Integer, ForeignKey("produk.id"), nullable=False)
    qty = Column(Integer, nullable=False)
    harga_satuan = Column(DECIMAL(10, 2), nullable=False)
    subtotal = Column(DECIMAL(10, 2), nullable=False)
    transaksi = relationship("TransaksiHeader", back_populates="detail")
    produk = relationship("Produk", back_populates="transaksi_detail")
