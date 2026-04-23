<?php
require_once 'api_config.php';
requireLogin();

$message = '';
$pelanggans = callAPI('GET', '/pelanggan')['response'] ?? [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $data = [
            'nama' => trim($_POST['nama'] ?? ''),
            'telepon' => trim($_POST['telepon'] ?? ''),
            'alamat' => trim($_POST['alamat'] ?? '')
        ];

        if ($_POST['action'] === 'create') {
            $result = callAPI('POST', '/pelanggan', $data);
            if ($result['code'] == 200) $message = '<div class="alert alert-success">Pelanggan berhasil ditambahkan</div>';
            else $message = '<div class="alert alert-danger">Gagal: ' . ($result['response']['detail'] ?? 'Error') . '</div>';
        } elseif ($_POST['action'] === 'update') {
            $id = (int)($_POST['id'] ?? 0);
            $result = callAPI('PUT', "/pelanggan/$id", $data);
            if ($result['code'] == 200) $message = '<div class="alert alert-success">Pelanggan berhasil diupdate</div>';
            else $message = '<div class="alert alert-danger">Gagal: ' . ($result['response']['detail'] ?? 'Error') . '</div>';
        } elseif ($_POST['action'] === 'delete') {
            $id = (int)($_POST['id'] ?? 0);
            $result = callAPI('DELETE', "/pelanggan/$id");
            if ($result['code'] == 200) $message = '<div class="alert alert-success">Pelanggan berhasil dihapus</div>';
            else $message = '<div class="alert alert-danger">Gagal: ' . ($result['response']['detail'] ?? 'Error') . '</div>';
        }
    }
    $pelanggans = callAPI('GET', '/pelanggan')['response'] ?? [];
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Pelanggan</title>
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
                    <li class="nav-item"><a class="nav-link active" href="pelanggan.php">Pelanggan</a></li>
                    <li class="nav-item"><a class="nav-link" href="transaksi.php">Transaksi</a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h2>Manajemen Pelanggan</h2>
        <?= $message ?>

        <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#modalPelanggan" onclick="resetForm()">Tambah Pelanggan</button>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama</th>
                    <th>Telepon</th>
                    <th>Alamat</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pelanggans as $p): ?>
                    <tr>
                        <td><?= $p['id'] ?></td>
                        <td><?= htmlspecialchars($p['nama']) ?></td>
                        <td><?= htmlspecialchars($p['telepon'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($p['alamat'] ?? '-') ?></td>
                        <td>
                            <button
                                class="btn btn-sm btn-warning"
                                onclick='editPelanggan(<?= htmlspecialchars(json_encode($p), ENT_QUOTES, "UTF-8") ?>)'
                                data-bs-toggle="modal"
                                data-bs-target="#modalPelanggan">Edit</button>
                            <form method="POST" style="display:inline;" onsubmit="return confirm('Hapus pelanggan ini?')">
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

    <div class="modal fade" id="modalPelanggan" tabindex="-1">
        <div class="modal-dialog">
            <form method="POST" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Tambah Pelanggan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" id="formAction" value="create">
                    <input type="hidden" name="id" id="pelangganId">

                    <div class="mb-3">
                        <label for="nama" class="form-label">Nama</label>
                        <input type="text" class="form-control" id="nama" name="nama" required>
                    </div>
                    <div class="mb-3">
                        <label for="telepon" class="form-label">Telepon</label>
                        <input type="text" class="form-control" id="telepon" name="telepon">
                    </div>
                    <div class="mb-3">
                        <label for="alamat" class="form-label">Alamat</label>
                        <textarea class="form-control" id="alamat" name="alamat" rows="3"></textarea>
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
            document.getElementById('pelangganId').value = '';
            document.getElementById('nama').value = '';
            document.getElementById('telepon').value = '';
            document.getElementById('alamat').value = '';
            document.getElementById('modalTitle').innerText = 'Tambah Pelanggan';
        }

        function editPelanggan(p) {
            document.getElementById('formAction').value = 'update';
            document.getElementById('pelangganId').value = p.id;
            document.getElementById('nama').value = p.nama || '';
            document.getElementById('telepon').value = p.telepon || '';
            document.getElementById('alamat').value = p.alamat || '';
            document.getElementById('modalTitle').innerText = 'Edit Pelanggan';
        }
    </script>
</body>

</html>