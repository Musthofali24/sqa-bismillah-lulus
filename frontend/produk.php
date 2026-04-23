<?php
require_once 'api_config.php';
requireLogin();

$message = '';
$produks = callAPI('GET', '/produk')['response'] ?? [];
$kategoris = callAPI('GET', '/kategori')['response'] ?? [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $data = [
            'nama' => $_POST['nama'],
            'harga' => (float)$_POST['harga'],
            'stok' => (int)$_POST['stok'],
            'kategori_id' => (int)$_POST['kategori_id']
        ];
        if ($_POST['action'] === 'create') {
            $result = callAPI('POST', '/produk', $data);
            $message = $result['code'] == 200 ? '<div class="alert alert-success">Produk ditambahkan</div>' : '<div class="alert alert-danger">Gagal: ' . ($result['response']['detail'] ?? 'Error') . '</div>';
        } elseif ($_POST['action'] === 'update') {
            $id = $_POST['id'];
            $result = callAPI('PUT', "/produk/$id", $data);
            $message = $result['code'] == 200 ? '<div class="alert alert-success">Produk diupdate</div>' : '<div class="alert alert-danger">Gagal: ' . ($result['response']['detail'] ?? 'Error') . '</div>';
        } elseif ($_POST['action'] === 'delete') {
            $id = $_POST['id'];
            $result = callAPI('DELETE', "/produk/$id");
            $message = $result['code'] == 200 ? '<div class="alert alert-success">Produk dihapus</div>' : '<div class="alert alert-danger">Gagal: ' . ($result['response']['detail'] ?? 'Error') . '</div>';
        }
    }
    $produks = callAPI('GET', '/produk')['response'] ?? [];
}

function getKategoriName($kategoris, $id)
{
    foreach ($kategoris as $kat) if ($kat['id'] == $id) return $kat['nama'];
    return '';
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Produk</title>
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
        <h2>Manajemen Produk</h2>
        <?= $message ?>
        <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#modalProduk" onclick="resetForm()">Tambah Produk</button>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama</th>
                    <th>Harga</th>
                    <th>Stok</th>
                    <th>Kategori</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($produks as $p): ?>
                    <tr>
                        <td><?= $p['id'] ?></td>
                        <td><?= htmlspecialchars($p['nama']) ?></td>
                        <td>Rp <?= number_format($p['harga'], 0, ',', '.') ?></td>
                        <td><?= $p['stok'] ?></td>
                        <td><?= getKategoriName($kategoris, $p['kategori_id']) ?></td>
                        <td>
                            <button class="btn btn-sm btn-warning" onclick="editProduk(<?= htmlspecialchars(json_encode($p)) ?>)" data-bs-toggle="modal" data-bs-target="#modalProduk">Edit</button>
                            <form method="POST" style="display:inline;" onsubmit="return confirm('Hapus produk ini?')">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= $p['id'] ?>">
                                <button class="btn btn-sm btn-danger">Hapus</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Modal Produk -->
    <div class="modal fade" id="modalProduk" tabindex="-1">
        <div class="modal-dialog">
            <form method="POST" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Tambah Produk</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" id="formAction" value="create">
                    <input type="hidden" name="id" id="produkId">
                    <div class="mb-3">
                        <label for="nama" class="form-label">Nama Produk</label>
                        <input type="text" class="form-control" id="nama" name="nama" required>
                    </div>
                    <div class="mb-3">
                        <label for="harga" class="form-label">Harga</label>
                        <input type="number" class="form-control" id="harga" name="harga" step="0.01" required>
                    </div>
                    <div class="mb-3">
                        <label for="stok" class="form-label">Stok</label>
                        <input type="number" class="form-control" id="stok" name="stok" required>
                    </div>
                    <div class="mb-3">
                        <label for="kategori_id" class="form-label">Kategori</label>
                        <select class="form-select" id="kategori_id" name="kategori_id" required>
                            <option value="">Pilih Kategori</option>
                            <?php foreach ($kategoris as $kat): ?>
                                <option value="<?= $kat['id'] ?>"><?= htmlspecialchars($kat['nama']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function resetForm() {
            document.getElementById('formAction').value = 'create';
            document.getElementById('produkId').value = '';
            document.getElementById('nama').value = '';
            document.getElementById('harga').value = '';
            document.getElementById('stok').value = '';
            document.getElementById('kategori_id').value = '';
            document.getElementById('modalTitle').innerText = 'Tambah Produk';
        }

        function editProduk(p) {
            document.getElementById('formAction').value = 'update';
            document.getElementById('produkId').value = p.id;
            document.getElementById('nama').value = p.nama;
            document.getElementById('harga').value = p.harga;
            document.getElementById('stok').value = p.stok;
            document.getElementById('kategori_id').value = p.kategori_id;
            document.getElementById('modalTitle').innerText = 'Edit Produk';
        }
    </script>
</body>

</html>