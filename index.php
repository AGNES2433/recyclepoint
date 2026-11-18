<?php
session_start();
require_once 'koneksi.php';

$nasabahList = [];
$qN = mysqli_query($conn, "SELECT * FROM nasabah ORDER BY saldo DESC");
while ($r = mysqli_fetch_assoc($qN)) $nasabahList[] = $r;

$totalKategori = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS jml FROM kategori_sampah"))['jml'];
$totalBerat = (float)mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(SUM(berat_kg), 0) AS total_berat FROM transaksi_setoran"))['total_berat'];

$totalSaldo = 0;
foreach ($nasabahList as $n) $totalSaldo += (int)$n['saldo'];

$recentTrx = [];
$qT = mysqli_query($conn, "SELECT t.*, n.nama AS nama_nasabah, k.nama_kategori FROM transaksi_setoran t JOIN nasabah n ON t.nasabah_id = n.id JOIN kategori_sampah k ON t.kategori_id = k.id ORDER BY t.id DESC LIMIT 5");
while ($r = mysqli_fetch_assoc($qT)) $recentTrx[] = $r;

$chartLabels = [];
$chartValues = [];
$qC = mysqli_query($conn, "SELECT k.nama_kategori, COALESCE(SUM(t.berat_kg), 0) AS total_kg FROM kategori_sampah k LEFT JOIN transaksi_setoran t ON k.id = t.kategori_id GROUP BY k.id, k.nama_kategori ORDER BY total_kg DESC");
while ($r = mysqli_fetch_assoc($qC)) {
    $chartLabels[] = $r['nama_kategori'];
    $chartValues[] = (float)$r['total_kg'];
}

$page = 'dashboard';
$pageTitle = 'Dashboard';
$pageSubtitle = 'Ringkasan data nasabah, berat sampah terkumpul, dan total saldo.';
require_once 'header.php';
?>
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
  <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
    <p class="text-xs font-medium text-slate-500">Total Nasabah</p>
    <p class="text-2xl font-extrabold text-slate-900 mt-1"><?= count($nasabahList) ?></p>
  </div>
  <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
    <p class="text-xs font-medium text-slate-500">Kategori Sampah</p>
    <p class="text-2xl font-extrabold text-slate-900 mt-1"><?= (int)$totalKategori ?></p>
  </div>
  <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
    <p class="text-xs font-medium text-slate-500">Total Sampah Terkumpul</p>
    <p class="text-2xl font-extrabold text-emerald-600 mt-1"><?= rtrim(rtrim(number_format($totalBerat, 2, '.', ''), '0'), '.') ?> kg</p>
  </div>
  <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
    <p class="text-xs font-medium text-slate-500">Total Saldo Nasabah</p>
    <p class="text-2xl font-extrabold text-emerald-700 mt-1"><?= formatRupiah($totalSaldo) ?></p>
  </div>
</div>

<!-- Grafik Berat Sampah per Kategori -->
<div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
  <h2 class="text-base font-bold text-slate-900 mb-1">Statistik Sampah Terkumpul per Kategori (kg)</h2>
  <p class="text-xs text-slate-500 mb-4">Perbandingan total berat sampah berdasarkan kategori.</p>
  <div class="relative h-64">
    <canvas id="chartKategori"></canvas>
  </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
  <div class="lg:col-span-7 bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
    <div class="flex items-center justify-between mb-4">
      <h2 class="text-base font-bold text-slate-900">Setoran Terbaru</h2>
      <a href="riwayat.php" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700">Lihat Semua →</a>
    </div>
    <div class="space-y-2.5">
      <?php foreach ($recentTrx as $t): ?>
        <div class="p-3 rounded-xl border border-slate-200 flex items-center justify-between gap-3">
          <div>
            <p class="text-sm font-semibold text-slate-900"><?= e($t['nama_nasabah']) ?> • <?= e($t['nama_kategori']) ?></p>
            <p class="text-xs text-slate-500"><?= (float)$t['berat_kg'] ?> kg • <?= e($t['tanggal_setor']) ?></p>
          </div>
          <span class="text-sm font-bold text-emerald-700">+<?= formatRupiah($t['total_nilai']) ?></span>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <div class="lg:col-span-5 bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
    <div class="flex items-center justify-between mb-4">
      <h2 class="text-base font-bold text-slate-900">Saldo Nasabah Tertinggi</h2>
      <a href="nasabah.php" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700">Kelola →</a>
    </div>
    <div class="space-y-2.5">
      <?php foreach (array_slice($nasabahList, 0, 5) as $n): ?>
        <a href="detail_nasabah.php?id=<?= (int)$n['id'] ?>" class="p-3 rounded-xl border border-slate-200 hover:border-emerald-300 flex items-center justify-between gap-3 transition-colors">
          <div>
            <p class="text-sm font-semibold text-slate-900"><?= e($n['nama']) ?></p>
            <p class="text-xs text-slate-500"><?= e($n['alamat']) ?></p>
          </div>
          <span class="text-sm font-bold text-emerald-700"><?= formatRupiah($n['saldo']) ?></span>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
  new Chart(document.getElementById('chartKategori'), {
    type: 'bar',
    data: {
      labels: <?= json_encode($chartLabels) ?>,
      datasets: [{
        label: 'Berat (kg)',
        data: <?= json_encode($chartValues) ?>,
        backgroundColor: '#10b981',
        borderRadius: 8
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: { legend: { display: false } },
      scales: { y: { beginAtZero: true } }
    }
  });
</script>
</main></div></body></html>