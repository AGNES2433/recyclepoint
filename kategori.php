<?php
session_start();
require_once 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'tambah') {
        $nama = trim($_POST['nama_kategori'] ?? '');
        $harga = (int)($_POST['harga_per_kg'] ?? 0);
        if ($nama !== '' && $harga > 0) {
            $nextId = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(MAX(id), 0) + 1 AS next_id FROM kategori_sampah"))['next_id'];
            $kode = 'KAT-' . str_pad($nextId, 2, '0', STR_PAD_LEFT);
            $stmt = mysqli_prepare($conn, "INSERT INTO kategori_sampah (kode_kategori, nama_kategori, harga_per_kg) VALUES (?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "ssi", $kode, $nama, $harga);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            $_SESSION['flash'] = "Kategori $nama berhasil ditambahkan.";
        }
    } elseif ($action === 'hapus') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            $stmt = mysqli_prepare($conn, "DELETE FROM kategori_sampah WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "i", $id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            $_SESSION['flash'] = "Kategori sampah berhasil dihapus.";
        }
    }
    header("Location: kategori.php");
    exit;
}

$kategoriList = [];
$q = mysqli_query($conn, "SELECT * FROM kategori_sampah ORDER BY id ASC");
while ($r = mysqli_fetch_assoc($q)) $kategoriList[] = $r;

$page = 'kategori';
$pageTitle = 'Kategori Sampah';
$pageSubtitle = 'Atur jenis sampah dan harga beli per kilogram.';
require_once 'header.php';
?>
<div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
  <div class="lg:col-span-4 bg-white rounded-2xl border border-slate-200 p-5 shadow-sm h-fit">
    <h2 class="text-base font-bold text-slate-900 mb-1">Tambah Kategori Sampah</h2>
    <p class="text-xs text-slate-500 mb-4">Atur jenis sampah dan harga beli per kilogram.</p>
    <form method="POST" action="kategori.php" class="space-y-3.5">
      <input type="hidden" name="action" value="tambah" />
      <div>
        <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Kategori</label>
        <input name="nama_kategori" type="text" required placeholder="Contoh: Botol Plastik PET" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
      </div>
      <div>
        <label class="block text-xs font-semibold text-slate-700 mb-1">Harga per Kilogram (Rp/kg)</label>
        <input name="harga_per_kg" type="number" min="100" step="100" required placeholder="Contoh: 3500" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
      </div>
      <button type="submit" class="w-full py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold">Simpan Kategori</button>
    </form>
  </div>

  <div class="lg:col-span-8 bg-white rounded-2xl border border-slate-200 p-5 shadow-sm overflow-x-auto">
    <h2 class="text-base font-bold text-slate-900 mb-4">Daftar Kategori &amp; Harga Sampah</h2>
    <table class="w-full text-left border-collapse text-sm">
      <thead>
        <tr class="border-b border-slate-200 text-xs text-slate-500">
          <th class="py-2.5 pr-3 font-semibold">Kode</th>
          <th class="py-2.5 pr-3 font-semibold">Jenis Sampah</th>
          <th class="py-2.5 pr-3 font-semibold text-right">Harga / kg</th>
          <th class="py-2.5 font-semibold text-right">Aksi</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-100">
        <?php foreach ($kategoriList as $k): ?>
          <tr>
            <td class="py-3 pr-3 text-xs font-bold text-slate-500"><?= e($k['kode_kategori']) ?></td>
            <td class="py-3 pr-3 font-semibold text-slate-900"><?= e($k['nama_kategori']) ?></td>
            <td class="py-3 pr-3 text-right font-bold text-emerald-700"><?= formatRupiah($k['harga_per_kg']) ?>/kg</td>
            <td class="py-3 text-right">
              <form method="POST" action="kategori.php" class="inline">
                <input type="hidden" name="action" value="hapus" />
                <input type="hidden" name="id" value="<?= (int)$k['id'] ?>" />
                <button type="submit" class="text-xs font-semibold text-rose-600 hover:text-rose-700 px-2 py-1 rounded-lg hover:bg-rose-50">Hapus</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
</main></div></body></html>