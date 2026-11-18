<?php
session_start();
require_once 'koneksi.php';

$id = (int)($_GET['id'] ?? 0);
$stmt = mysqli_prepare($conn, "SELECT * FROM nasabah WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$nasabah = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

if (!$nasabah) {
    header("Location: nasabah.php");
    exit;
}

$transaksiList = [];
$totalBerat = 0;
$stmtT = mysqli_prepare($conn, "SELECT t.*, k.nama_kategori FROM transaksi_setoran t JOIN kategori_sampah k ON t.kategori_id = k.id WHERE t.nasabah_id = ? ORDER BY t.id DESC");
mysqli_stmt_bind_param($stmtT, "i", $id);
mysqli_stmt_execute($stmtT);
$resT = mysqli_stmt_get_result($stmtT);
while ($r = mysqli_fetch_assoc($resT)) {
    $transaksiList[] = $r;
    $totalBerat += (float)$r['berat_kg'];
}
mysqli_stmt_close($stmtT);

$page = 'nasabah';
$pageTitle = 'Detail Nasabah: ' . $nasabah['nama'];
$pageSubtitle = $nasabah['kode_nasabah'] . ' • ' . $nasabah['alamat'] . ' • ' . $nasabah['no_hp'];
require_once 'header.php';
?>
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
  <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
    <p class="text-xs font-medium text-slate-500">Saldo Saat Ini</p>
    <p class="text-2xl font-extrabold text-emerald-700 mt-1"><?= formatRupiah($nasabah['saldo']) ?></p>
  </div>
  <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
    <p class="text-xs font-medium text-slate-500">Total Berat Setoran</p>
    <p class="text-2xl font-extrabold text-slate-900 mt-1"><?= rtrim(rtrim(number_format($totalBerat, 2, '.', ''), '0'), '.') ?> kg</p>
  </div>
  <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
    <p class="text-xs font-medium text-slate-500">Jumlah Transaksi</p>
    <p class="text-2xl font-extrabold text-slate-900 mt-1"><?= count($transaksiList) ?>x</p>
  </div>
</div>

<div class="bg-white rounded-2xl border border-slate-200 p-5 sm:p-6 shadow-sm overflow-x-auto">
  <div class="flex items-center justify-between mb-4">
    <h2 class="text-base font-bold text-slate-900">Riwayat Setoran <?= e($nasabah['nama']) ?></h2>
    <a href="nasabah.php" class="text-xs font-semibold text-slate-500 hover:text-slate-800">← Kembali ke Data Nasabah</a>
  </div>
  <table class="w-full text-left border-collapse text-sm">
    <thead>
      <tr class="border-b border-slate-200 text-xs text-slate-500">
        <th class="py-2.5 pr-3 font-semibold">No. Transaksi</th>
        <th class="py-2.5 pr-3 font-semibold">Waktu</th>
        <th class="py-2.5 pr-3 font-semibold">Kategori</th>
        <th class="py-2.5 pr-3 font-semibold text-right">Berat</th>
        <th class="py-2.5 pr-3 font-semibold text-right">Nilai Saldo</th>
        <th class="py-2.5 font-semibold text-right">Struk</th>
      </tr>
    </thead>
    <tbody class="divide-y divide-slate-100">
      <?php if (empty($transaksiList)): ?>
        <tr><td colspan="6" class="py-8 text-center text-slate-400">Belum ada setoran dari nasabah ini.</td></tr>
      <?php else: ?>
        <?php foreach ($transaksiList as $t): ?>
          <tr>
            <td class="py-3 pr-3 text-xs font-bold text-slate-500"><?= e($t['kode_transaksi']) ?></td>
            <td class="py-3 pr-3 text-xs text-slate-500"><?= e($t['tanggal_setor']) ?></td>
            <td class="py-3 pr-3 font-semibold text-slate-900"><?= e($t['nama_kategori']) ?></td>
            <td class="py-3 pr-3 text-right"><?= (float)$t['berat_kg'] ?> kg</td>
            <td class="py-3 pr-3 text-right font-bold text-emerald-700">+<?= formatRupiah($t['total_nilai']) ?></td>
            <td class="py-3 text-right"><a href="struk.php?id=<?= (int)$t['id'] ?>" class="text-xs font-semibold text-sky-600 hover:underline">Cetak</a></td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
</div>
</main></div></body></html>