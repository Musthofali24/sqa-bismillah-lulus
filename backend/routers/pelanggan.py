from fastapi import APIRouter, Depends, HTTPException
from sqlalchemy.orm import Session
from database import get_db
from auth import get_current_user
import crud, schemas

router = APIRouter(prefix="/pelanggan", tags=["pelanggan"])


@router.post("/", response_model=schemas.Pelanggan)
def create_pelanggan(
    pelanggan: schemas.PelangganCreate,
    db: Session = Depends(get_db),
    current_user=Depends(get_current_user),
):
    return crud.create_pelanggan(db=db, pelanggan=pelanggan)


@router.get("/", response_model=list[schemas.Pelanggan])
def read_pelanggans(
    skip: int = 0,
    limit: int = 100,
    db: Session = Depends(get_db),
    current_user=Depends(get_current_user),
):
    return crud.get_pelanggans(db, skip=skip, limit=limit)


@router.get("/{pelanggan_id}", response_model=schemas.Pelanggan)
def read_pelanggan(
    pelanggan_id: int,
    db: Session = Depends(get_db),
    current_user=Depends(get_current_user),
):
    db_pelanggan = crud.get_pelanggan(db, pelanggan_id=pelanggan_id)
    if db_pelanggan is None:
        raise HTTPException(status_code=404, detail="Pelanggan tidak ditemukan")
    return db_pelanggan


@router.put("/{pelanggan_id}", response_model=schemas.Pelanggan)
def update_pelanggan(
    pelanggan_id: int,
    pelanggan: schemas.PelangganCreate,
    db: Session = Depends(get_db),
    current_user=Depends(get_current_user),
):
    db_pelanggan = crud.update_pelanggan(
        db, pelanggan_id=pelanggan_id, pelanggan=pelanggan
    )
    if db_pelanggan is None:
        raise HTTPException(status_code=404, detail="Pelanggan tidak ditemukan")
    return db_pelanggan


@router.delete("/{pelanggan_id}")
def delete_pelanggan(
    pelanggan_id: int,
    db: Session = Depends(get_db),
    current_user=Depends(get_current_user),
):
    db_pelanggan = crud.delete_pelanggan(db, pelanggan_id=pelanggan_id)
    if db_pelanggan is None:
        raise HTTPException(status_code=404, detail="Pelanggan tidak ditemukan")
    return {"message": "Pelanggan berhasil dihapus"}
