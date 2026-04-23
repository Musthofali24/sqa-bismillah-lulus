<?php
require_once 'api_config.php';
requireLogin();
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat Transaksi Baru</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">Penjualan App</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="kategori.php">Kategori</a></li>
                    <li class="nav-item"><a class="nav-link" href="produk.php">Produk</a></li>
                    <li class="nav-item"><a class="nav-link" href="pelanggan.php">Pelanggan</a></li>
                    <li class="nav-item"><a class="nav-link" href="transaksi.php">Transaksi</a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container mt-4">
        <h2>Buat Transaksi Baru</h2>
        <div id="message"></div>
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="pelanggan_id" class="form-label">Pelanggan</label>
                    <select class="form-select" id="pelanggan_id"></select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="mb-3">
                    <label for="metode_pembayaran" class="form-label">Metode Pembayaran</label>
                    <select class="form-select" id="metode_pembayaran">
                        <option value="cash">Cash</option>
                        <option value="transfer">Transfer</option>
                        <option value="qris">QRIS</option>
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="mb-3">
                    <label for="status_pembayaran" class="form-label">Status</label>
                    <select class="form-select" id="status_pembayaran">
                        <option value="lunas">Lunas</option>
                        <option value="belum_lunas">Belum Lunas</option>
                    </select>
                </div>
            </div>
        </div>
        <hr>
        <h4>Tambah Item</h4>
        <div class="row g-3 align-items-end">
            <div class="col-md-5">
                <label for="produk_id" class="form-label">Produk</label>
                <select class="form-select" id="produk_id"></select>
            </div>
            <div class="col-md-2">
                <label for="qty" class="form-label">Kuantitas</label>
                <input type="number" class="form-control" id="qty" min="1" value="1">
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary" id="btnTambah">Tambah ke Keranjang</button>
            </div>
        </div>
        <hr>
        <h4>Keranjang Belanja</h4>
        <table class="table table-bordered" id="keranjang">
            <thead>
                <tr>
                    <th>Produk</th>
                    <th>Harga</th>
                    <th>Qty</th>
                    <th>Subtotal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody></tbody>
            <tfoot>
                <tr>
                    <th colspan="3" class="text-end">Total</th>
                    <th id="totalDisplay">Rp 0</th>
                    <th></th>
                </tr>
            </tfoot>
        </table>
        <button class="btn btn-success" id="btnSimpan">Simpan Transaksi</button>
        <a href="transaksi.php" class="btn btn-secondary">Kembali</a>
    </div>

    <script>
        const API_URL = 'http://localhost:8000';
        const token = '<?= $_SESSION['access_token'] ?? '' ?>';
        let cart = [];
        let produkList = [];
        let pelangganList = [];

        async function fetchAPI(endpoint, method = 'GET', body = null) {
            const headers = {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer ' + token
            };
            const options = {
                method,
                headers
            };
            if (body) options.body = JSON.stringify(body);
            const res = await fetch(API_URL + endpoint, options);
            return {
                code: res.status,
                data: await res.json()
            };
        }

        async function loadPelanggan() {
            const res = await fetchAPI('/pelanggan');
            if (res.code === 200) {
                pelangganList = res.data;
                const select = document.getElementById('pelanggan_id');
                select.innerHTML = '<option value="">Pilih Pelanggan</option>';
                pelangganList.forEach(p => {
                    select.innerHTML += `<option value="${p.id}">${p.nama}</option>`;
                });
            }
        }

        async function loadProduk() {
            const res = await fetchAPI('/produk');
            if (res.code === 200) {
                produkList = res.data;
                const select = document.getElementById('produk_id');
                select.innerHTML = '<option value="">Pilih Produk</option>';
                produkList.forEach(p => {
                    select.innerHTML += `<option value="${p.id}" data-harga="${p.harga}" data-stok="${p.stok}">${p.nama} (Stok: ${p.stok})</option>`;
                });
            }
        }

        function renderCart() {
            const tbody = document.querySelector('#keranjang tbody');
            tbody.innerHTML = '';
            let total = 0;
            cart.forEach((item, index) => {
                const subtotal = item.harga * item.qty;
                total += subtotal;
                tbody.innerHTML += `<tr>
                    <td>${item.nama}</td>
                    <td>Rp ${item.harga.toLocaleString()}</td>
                    <td>${item.qty}</td>
                    <td>Rp ${subtotal.toLocaleString()}</td>
                    <td><button class="btn btn-sm btn-danger" onclick="removeItem(${index})">Hapus</button></td>
                </tr>`;
            });
            document.getElementById('totalDisplay').innerText = 'Rp ' + total.toLocaleString();
        }

        window.removeItem = (index) => {
            cart.splice(index, 1);
            renderCart();
        };

        document.getElementById('btnTambah').addEventListener('click', () => {
            const produkSelect = document.getElementById('produk_id');
            const produkId = produkSelect.value;
            const qty = parseInt(document.getElementById('qty').value);
            if (!produkId || qty < 1) {
                alert('Pilih produk dan kuantitas valid');
                return;
            }
            const produk = produkList.find(p => p.id == produkId);
            if (produk.stok < qty) {
                alert('Stok tidak mencukupi! Stok tersedia: ' + produk.stok);
                return;
            }
            const existing = cart.find(item => item.id == produkId);
            if (existing) {
                if (existing.qty + qty > produk.stok) {
                    alert('Total kuantitas melebihi stok!');
                    return;
                }
                existing.qty += qty;
            } else {
                cart.push({
                    id: produk.id,
                    nama: produk.nama,
                    harga: produk.harga,
                    qty
                });
            }
            renderCart();
            document.getElementById('qty').value = 1;
            produkSelect.value = '';
        });

        document.getElementById('btnSimpan').addEventListener('click', async () => {
            const pelanggan_id = document.getElementById('pelanggan_id').value;
            if (!pelanggan_id) {
                alert('Pilih pelanggan');
                return;
            }
            if (cart.length === 0) {
                alert('Keranjang kosong');
                return;
            }
            const transaksi = {
                pelanggan_id: parseInt(pelanggan_id),
                metode_pembayaran: document.getElementById('metode_pembayaran').value,
                status_pembayaran: document.getElementById('status_pembayaran').value,
                items: cart.map(item => ({
                    produk_id: item.id,
                    qty: item.qty
                }))
            };
            const res = await fetchAPI('/transaksi', 'POST', transaksi);
            if (res.code === 200) {
                alert('Transaksi berhasil disimpan');
                window.location.href = 'transaksi.php';
            } else {
                alert('Gagal: ' + (res.data.detail || 'Error'));
            }
        });

        loadPelanggan();
        loadProduk();
    </script>
</body>

</html>