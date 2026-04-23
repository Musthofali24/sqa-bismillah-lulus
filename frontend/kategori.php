<?php
require_once 'api_config.php';
requireLogin();

$message = '';
$kategoris = callAPI('GET', '/kategori')['response'] ?? [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'create') {
            $result = callAPI('POST', '/kategori', ['nama' => $_POST['nama']]);
            if ($result['code'] == 200) $message = '<div class="alert alert-success">Kategori berhasil ditambahkan</div>';
            else $message = '<div class="alert alert-danger">Gagal: ' . ($result['response']['detail'] ?? 'Error') . '</div>';
        } elseif ($_POST['action'] === 'update') {
            $id = $_POST['id'];
            $result = callAPI('PUT', "/kategori/$id", ['nama' => $_POST['nama']]);
            if ($result['code'] == 200) $message = '<div class="alert alert-success">Kategori berhasil diupdate</div>';
            else $message = '<div class="alert alert-danger">Gagal: ' . ($result['response']['detail'] ?? 'Error') . '</div>';
        } elseif ($_POST['action'] === 'delete') {
            $id = $_POST['id'];
            $result = callAPI('DELETE', "/kategori/$id");
            if ($result['code'] == 200) $message = '<div class="alert alert-success">Kategori berhasil dihapus</div>';
            else $message = '<div class="alert alert-danger">Gagal: ' . ($result['response']['detail'] ?? 'Error') . '</div>';
        }
    }
    // Refresh data
    $kategoris = callAPI('GET', '/kategori')['response'] ?? [];
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Kategori</title>
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
        <h2>Manajemen Kategori</h2>
        <?= $message ?>
        <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#modalKategori" onclick="resetForm()">Tambah Kategori</button>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama Kategori</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($kategoris as $kat): ?>
                    <tr>
                        <td><?= $kat['id'] ?></td>
                        <td><?= htmlspecialchars($kat['nama']) ?></td>
                        <td>
                            <button class="btn btn-sm btn-warning" onclick="editKategori(<?= $kat['id'] ?>, '<?= htmlspecialchars($kat['nama']) ?>')" data-bs-toggle="modal" data-bs-target="#modalKategori">Edit</button>
                            <form method="POST" style="display:inline;" onsubmit="return confirm('Hapus kategori ini?')">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= $kat['id'] ?>">
                                <button class="btn btn-sm btn-danger">Hapus</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="modalKategori" tabindex="-1">
        <div class="modal-dialog">
            <form method="POST" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Tambah Kategori</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" id="formAction" value="create">
                    <input type="hidden" name="id" id="kategoriId">
                    <div class="mb-3">
                        <label for="nama" class="form-label">Nama Kategori</label>
                        <input type="text" class="form-control" id="nama" name="nama" required>
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
            document.getElementById('kategoriId').value = '';
            document.getElementById('nama').value = '';
            document.getElementById('modalTitle').innerText = 'Tambah Kategori';
        }

        function editKategori(id, nama) {
            document.getElementById('formAction').value = 'update';
            document.getElementById('kategoriId').value = id;
            document.getElementById('nama').value = nama;
            document.getElementById('modalTitle').innerText = 'Edit Kategori';
        }
    </script>
</body>

</html>