<?php
require_once 'api_config.php';
requireLogin();

$transaksis = callAPI('GET', '/transaksi')['response'] ?? [];

function formatRupiah($amount)
{
    return 'Rp ' . number_format((float)$amount, 0, ',', '.');
}

function formatTanggal($tanggal)
{
    if (!$tanggal) return '-';
    $timestamp = strtotime($tanggal);
    if ($timestamp === false) return htmlspecialchars($tanggal);
    return date('d-m-Y H:i', $timestamp);
}

function getPelangganNama($transaksi)
{
    return htmlspecialchars($transaksi['pelanggan']['nama'] ?? '-');
}

function formatStatus($status)
{
    if ($status === 'lunas') return '<span class="badge bg-success">Lunas</span>';
    if ($status === 'belum_lunas') return '<span class="badge bg-warning text-dark">Belum Lunas</span>';
    return '<span class="badge bg-secondary">' . htmlspecialchars($status ?: '-') . '</span>';
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Transaksi</title>
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
                    <li class="nav-item"><a class="nav-link active" href="transaksi.php">Transaksi</a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="mb-0">Daftar Transaksi</h2>
            <a href="transaksi_create.php" class="btn btn-primary">Buat Transaksi Baru</a>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Pelanggan</th>
                        <th>Tanggal</th>
                        <th>Total</th>
                        <th>Metode</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($transaksis) === 0): ?>
                        <tr>
                            <td colspan="7" class="text-center">Belum ada transaksi</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($transaksis as $t): ?>
                            <tr>
                                <td><?= $t['id'] ?></td>
                                <td><?= getPelangganNama($t) ?></td>
                                <td><?= formatTanggal($t['tanggal'] ?? null) ?></td>
                                <td><?= formatRupiah($t['total'] ?? 0) ?></td>
                                <td><?= htmlspecialchars(strtoupper($t['metode_pembayaran'] ?? '-')) ?></td>
                                <td><?= formatStatus($t['status_pembayaran'] ?? '') ?></td>
                                <td>
                                    <button class="btn btn-sm btn-info" onclick='showDetail(<?= htmlspecialchars(json_encode($t), ENT_QUOTES, "UTF-8") ?>)' data-bs-toggle="modal" data-bs-target="#modalDetail">Detail</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="modal fade" id="modalDetail" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Transaksi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="detailHeader" class="mb-3"></div>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead>
                                <tr>
                                    <th>Produk</th>
                                    <th>Harga</th>
                                    <th>Qty</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody id="detailItems">
                                <tr>
                                    <td colspan="4" class="text-center">Tidak ada detail item</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function rupiah(amount) {
            return 'Rp ' + Number(amount || 0).toLocaleString('id-ID');
        }

        function tanggal(value) {
            if (!value) return '-';
            const date = new Date(value);
            if (isNaN(date.getTime())) return value;
            return date.toLocaleString('id-ID');
        }

        function statusLabel(status) {
            if (status === 'lunas') return '<span class="badge bg-success">Lunas</span>';
            if (status === 'belum_lunas') return '<span class="badge bg-warning text-dark">Belum Lunas</span>';
            return '<span class="badge bg-secondary">-</span>';
        }

        function showDetail(transaksi) {
            const pelanggan = transaksi.pelanggan && transaksi.pelanggan.nama ? transaksi.pelanggan.nama : '-';
            const detailHeader = document.getElementById('detailHeader');
            const detailItems = document.getElementById('detailItems');

            detailHeader.innerHTML = `
				<div class="row g-2">
					<div class="col-md-4"><strong>ID:</strong> ${transaksi.id}</div>
					<div class="col-md-4"><strong>Pelanggan:</strong> ${pelanggan}</div>
					<div class="col-md-4"><strong>Tanggal:</strong> ${tanggal(transaksi.tanggal)}</div>
					<div class="col-md-4"><strong>Metode:</strong> ${(transaksi.metode_pembayaran || '-').toUpperCase()}</div>
					<div class="col-md-4"><strong>Status:</strong> ${statusLabel(transaksi.status_pembayaran)}</div>
					<div class="col-md-4"><strong>Total:</strong> ${rupiah(transaksi.total)}</div>
				</div>
			`;

            const items = Array.isArray(transaksi.detail) ? transaksi.detail : [];
            if (items.length === 0) {
                detailItems.innerHTML = '<tr><td colspan="4" class="text-center">Tidak ada detail item</td></tr>';
                return;
            }

            detailItems.innerHTML = items.map(item => {
                const namaProduk = item.produk && item.produk.nama ? item.produk.nama : ('Produk #' + item.produk_id);
                return `
					<tr>
						<td>${namaProduk}</td>
						<td>${rupiah(item.harga_satuan)}</td>
						<td>${item.qty}</td>
						<td>${rupiah(item.subtotal)}</td>
					</tr>
				`;
            }).join('');
        }
    </script>
</body>

</html>