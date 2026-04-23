from fastapi import APIRouter, Depends, HTTPException
from sqlalchemy.orm import Session
from database import get_db
from auth import get_current_user
import crud, schemas

router = APIRouter(prefix="/kategori", tags=["kategori"])


@router.post("/", response_model=schemas.Kategori)
def create_kategori(
    kategori: schemas.KategoriCreate,
    db: Session = Depends(get_db),
    current_user=Depends(get_current_user),
):
    db_kategori = crud.get_kategori_by_nama(db, nama=kategori.nama)
    if db_kategori:
        raise HTTPException(status_code=400, detail="Nama kategori sudah ada")
    return crud.create_kategori(db=db, kategori=kategori)


@router.get("/", response_model=list[schemas.Kategori])
def read_kategoris(
    skip: int = 0,
    limit: int = 100,
    db: Session = Depends(get_db),
    current_user=Depends(get_current_user),
):
    return crud.get_kategoris(db, skip=skip, limit=limit)


@router.get("/{kategori_id}", response_model=schemas.Kategori)
def read_kategori(
    kategori_id: int,
    db: Session = Depends(get_db),
    current_user=Depends(get_current_user),
):
    db_kategori = crud.get_kategori(db, kategori_id=kategori_id)
    if db_kategori is None:
        raise HTTPException(status_code=404, detail="Kategori tidak ditemukan")
    return db_kategori


@router.put("/{kategori_id}", response_model=schemas.Kategori)
def update_kategori(
    kategori_id: int,
    kategori: schemas.KategoriCreate,
    db: Session = Depends(get_db),
    current_user=Depends(get_current_user),
):
    db_kategori = crud.update_kategori(db, kategori_id=kategori_id, kategori=kategori)
    if db_kategori is None:
        raise HTTPException(status_code=404, detail="Kategori tidak ditemukan")
    return db_kategori


@router.delete("/{kategori_id}")
def delete_kategori(
    kategori_id: int,
    db: Session = Depends(get_db),
    current_user=Depends(get_current_user),
):
    db_kategori = crud.delete_kategori(db, kategori_id=kategori_id)
    if db_kategori is None:
        raise HTTPException(status_code=404, detail="Kategori tidak ditemukan")
    return {"message": "Kategori berhasil dihapus"}
