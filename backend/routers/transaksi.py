from fastapi import APIRouter, Depends, HTTPException
from sqlalchemy.orm import Session
from database import get_db
from auth import get_current_user
import crud, schemas

router = APIRouter(prefix="/transaksi", tags=["transaksi"])


@router.post("/", response_model=schemas.TransaksiHeader)
def create_transaksi(
    transaksi: schemas.TransaksiHeaderCreate,
    db: Session = Depends(get_db),
    current_user=Depends(get_current_user),
):
    try:
        return crud.create_transaksi(db=db, transaksi=transaksi)
    except ValueError as e:
        raise HTTPException(status_code=400, detail=str(e))


@router.get("/", response_model=list[schemas.TransaksiHeader])
def read_transaksis(
    skip: int = 0,
    limit: int = 100,
    db: Session = Depends(get_db),
    current_user=Depends(get_current_user),
):
    return crud.get_transaksis(db, skip=skip, limit=limit)


@router.get("/{transaksi_id}", response_model=schemas.TransaksiHeader)
def read_transaksi(
    transaksi_id: int,
    db: Session = Depends(get_db),
    current_user=Depends(get_current_user),
):
    db_transaksi = crud.get_transaksi(db, transaksi_id=transaksi_id)
    if db_transaksi is None:
        raise HTTPException(status_code=404, detail="Transaksi tidak ditemukan")
    return db_transaksi
