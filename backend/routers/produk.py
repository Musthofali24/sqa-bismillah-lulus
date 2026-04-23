from fastapi import APIRouter, Depends, HTTPException
from sqlalchemy.orm import Session
from database import get_db
from auth import get_current_user
import crud, schemas

router = APIRouter(prefix="/produk", tags=["produk"])


@router.post("/", response_model=schemas.Produk)
def create_produk(
    produk: schemas.ProdukCreate,
    db: Session = Depends(get_db),
    current_user=Depends(get_current_user),
):
    kategori = crud.get_kategori(db, produk.kategori_id)
    if not kategori:
        raise HTTPException(status_code=400, detail="Kategori tidak ditemukan")
    return crud.create_produk(db=db, produk=produk)


@router.get("/", response_model=list[schemas.Produk])
def read_produks(
    skip: int = 0,
    limit: int = 100,
    db: Session = Depends(get_db),
    current_user=Depends(get_current_user),
):
    return crud.get_produks(db, skip=skip, limit=limit)


@router.get("/{produk_id}", response_model=schemas.Produk)
def read_produk(
    produk_id: int,
    db: Session = Depends(get_db),
    current_user=Depends(get_current_user),
):
    db_produk = crud.get_produk(db, produk_id=produk_id)
    if db_produk is None:
        raise HTTPException(status_code=404, detail="Produk tidak ditemukan")
    return db_produk


@router.put("/{produk_id}", response_model=schemas.Produk)
def update_produk(
    produk_id: int,
    produk: schemas.ProdukCreate,
    db: Session = Depends(get_db),
    current_user=Depends(get_current_user),
):
    db_produk = crud.update_produk(db, produk_id=produk_id, produk=produk)
    if db_produk is None:
        raise HTTPException(status_code=404, detail="Produk tidak ditemukan")
    return db_produk


@router.delete("/{produk_id}")
def delete_produk(
    produk_id: int,
    db: Session = Depends(get_db),
    current_user=Depends(get_current_user),
):
    db_produk = crud.delete_produk(db, produk_id=produk_id)
    if db_produk is None:
        raise HTTPException(status_code=404, detail="Produk tidak ditemukan")
    return {"message": "Produk berhasil dihapus"}
