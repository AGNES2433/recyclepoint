<?php
session_start();
require_once 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'tambah') {
        $nama = trim($_POST['nama'] ?? '');
        $no_hp = trim($_POST['no_hp'] ?? '');
        $alamat = trim($_POST['alamat'] ?? '');
        if ($nama !== '' && $no_hp !== '' && $alamat !== '') {
            $nextId = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(MAX(id), 0) + 1 AS next_id FROM nasabah"))['next_id'];
            $kode = 'NSB-' . str_pad($nextId, 2, '0', STR_PAD_LEFT);
            $stmt = mysqli_prepare($conn, "INSERT INTO nasabah (kode_nasabah, nama, no_hp, alamat, saldo) VALUES (?, ?, ?, ?, 0)");
            mysqli_stmt_bind_param($stmt, "ssss", $kode, $nama, $no_hp, $alamat);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            $_SESSION['flash'] = "Nasabah $nama berhasil ditambahkan.";
        }
    } elseif ($action === 'hapus') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            $stmt = mysqli_prepare($conn, "DELETE FROM nasabah WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "i", $id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            $_SESSION['flash'] = "Data nasabah berhasil dihapus.";
        }
    }
    header("Location: nasabah.php");
    exit;
}

$nasabahList = [];
$q = mysqli_query($conn, "SELECT * FROM nasabah ORDER BY id ASC");
while ($r = mysqli_fetch_assoc($q)) $nasabahList[] = $r;

$page = 'nasabah';
$pageTitle = 'Data Nasabah';
$pageSubtitle = 'Kelola data masyarakat terdaftar dan lihat profil saldo masing-masing.';
require_once 'header.php';
?>
<div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
  <div class="lg:col-span-4 bg-white rounded-2xl border border-slate-200 p-5 shadow-sm h-fit">
    <h2 class="text-base font-bold text-slate-900 mb-1">Tambah Nasabah</h2>
    <p class="text-xs text-slate-500 mb-4">Daftarkan masyarakat baru ke bank sampah.</p>
    <form method="POST" action="nasabah.php" class="space-y-3.5">
      <input type="hidden" name="action" value="tambah" />
      <div>
        <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Lengkap</label>
        <input name="nama" type="text" required placeholder="Contoh: Siti Aminah" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
      </div>
      <div>
        <label class="block text-xs font-semibold text-slate-700 mb-1">No. HP / WhatsApp</label>
        <input name="no_hp" type="text" required placeholder="Contoh: 081234567890" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
      </div>
      <div>
        <label class="block text-xs font-semibold text-slate-700 mb-1">Alamat / RT-RW</label>
        <input name="alamat" type="text" required placeholder="Contoh: Jl. Melati RT 02/05" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
      </div>
      <button type="submit" class="w-full py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold">Simpan Nasabah</button>
    </form>
  </div>

  <div class="lg:col-span-8 bg-white rounded-2xl border border-slate-200 p-5 shadow-sm overflow-x-auto">
    <h2 class="text-base font-bold text-slate-900 mb-4">Daftar Nasabah Terdaftar</h2>
    <table class="w-full text-left border-collapse text-sm">
      <thead>
        <tr class="border-b border-slate-200 text-xs text-slate-500">
          <th class="py-2.5 pr-3 font-semibold">ID</th>
          <th class="py-2.5 pr-3 font-semibold">Nama &amp; Alamat</th>
          <th class="py-2.5 pr-3 font-semibold">No. HP</th>
          <th class="py-2.5 pr-3 font-semibold text-right">Saldo</th>
          <th class="py-2.5 font-semibold text-right">Aksi</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-100">
        <?php foreach ($nasabahList as $n): ?>
          <tr>
            <td class="py-3 pr-3 text-xs font-bold text-slate-500"><?= e($n['kode_nasabah']) ?></td>
            <td class="py-3 pr-3">
              <p class="font-semibold text-slate-900"><?= e($n['nama']) ?></p>
              <p class="text-xs text-slate-500"><?= e($n['alamat']) ?></p>
            </td>
            <td class="py-3 pr-3 text-xs text-slate-600"><?= e($n['no_hp']) ?></td>
            <td class="py-3 pr-3 text-right font-bold text-emerald-700"><?= formatRupiah($n['saldo']) ?></td>
            <td class="py-3 text-right space-x-1 whitespace-nowrap">
              <a href="detail_nasabah.php?id=<?= (int)$n['id'] ?>" class="text-xs font-semibold text-sky-600 hover:text-sky-700 px-2 py-1 rounded-lg hover:bg-sky-50">Detail</a>
              <form method="POST" action="nasabah.php" class="inline">
                <input type="hidden" name="action" value="hapus" />
                <input type="hidden" name="id" value="<?= (int)$n['id'] ?>" />
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