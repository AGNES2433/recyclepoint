<?php
session_start();
require_once 'koneksi.php';

$nasabahList = [];
$qN = mysqli_query($conn, "SELECT * FROM nasabah ORDER BY id ASC");
while ($r = mysqli_fetch_assoc($qN)) $nasabahList[] = $r;

$filterId = (int)($_GET['nasabah_id'] ?? 0);
$sql = "SELECT t.*, n.nama AS nama_nasabah, k.nama_kategori
        FROM transaksi_setoran t
        JOIN nasabah n ON t.nasabah_id = n.id
        JOIN kategori_sampah k ON t.kategori_id = k.id ";
if ($filterId > 0) $sql .= "WHERE t.nasabah_id = $filterId ";
$sql .= "ORDER BY t.id DESC";

$transaksiList = [];
$qT = mysqli_query($conn, $sql);
while ($r = mysqli_fetch_assoc($qT)) $transaksiList[] = $r;

$page = 'riwayat';
$pageTitle = 'Riwayat Transaksi';
$pageSubtitle = 'Catatan seluruh transaksi setoran sampah beserta filter per nasabah.';
require_once 'header.php';
?>
<div class="bg-white rounded-2xl border border-slate-200 p-5 sm:p-6 shadow-sm">
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-5">
    <div>
      <h2 class="text-lg font-bold text-slate-900">Riwayat Transaksi Setoran</h2>
      <p class="text-xs sm:text-sm text-slate-500">Filter berdasarkan nasabah untuk melihat riwayat setoran masing-masing.</p>
    </div>
    <form method="GET" action="riwayat.php">
      <select name="nasabah_id" onchange="this.form.submit()" class="rounded-xl border border-slate-300 bg-white px-3.5 py-2 text-xs sm:text-sm">
        <option value="0">Semua Nasabah</option>
        <?php foreach ($nasabahList as $n): ?>
          <option value="<?= (int)$n['id'] ?>" <?= $filterId === (int)$n['id'] ? 'selected' : '' ?>><?= e($n['nama']) ?></option>
        <?php endforeach; ?>
      </select>
    </form>
  </div>

  <div class="overflow-x-auto">
    <table class="w-full text-left border-collapse text-sm">
      <thead>
        <tr class="border-b border-slate-200 text-xs text-slate-500">
          <th class="py-2.5 pr-3 font-semibold">No. Transaksi</th>
          <th class="py-2.5 pr-3 font-semibold">Waktu</th>
          <th class="py-2.5 pr-3 font-semibold">Nasabah</th>
          <th class="py-2.5 pr-3 font-semibold">Kategori</th>
          <th class="py-2.5 pr-3 font-semibold text-right">Berat</th>
          <th class="py-2.5 pr-3 font-semibold text-right">Nilai Saldo</th>
          <th class="py-2.5 font-semibold text-right">Struk</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-100">
        <?php if (empty($transaksiList)): ?>
          <tr><td colspan="7" class="py-8 text-center text-slate-400">Belum ada riwayat transaksi.</td></tr>
        <?php else: ?>
          <?php foreach ($transaksiList as $t): ?>
            <tr>
              <td class="py-3 pr-3 text-xs font-bold text-slate-500"><?= e($t['kode_transaksi']) ?></td>
              <td class="py-3 pr-3 text-xs text-slate-500"><?= e($t['tanggal_setor']) ?></td>
              <td class="py-3 pr-3 font-semibold text-slate-900"><?= e($t['nama_nasabah']) ?></td>
              <td class="py-3 pr-3 text-slate-700"><?= e($t['nama_kategori']) ?></td>
              <td class="py-3 pr-3 text-right font-medium"><?= (float)$t['berat_kg'] ?> kg</td>
              <td class="py-3 pr-3 text-right font-bold text-emerald-700">+<?= formatRupiah($t['total_nilai']) ?></td>
              <td class="py-3 text-right"><a href="struk.php?id=<?= (int)$t['id'] ?>" class="text-xs font-semibold text-sky-600 hover:underline">Cetak</a></td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
</main></div></body></html>