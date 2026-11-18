<?php
require_once 'koneksi.php';

$id = (int)($_GET['id'] ?? 0);
$stmt = mysqli_prepare($conn, "SELECT t.*, n.nama AS nama_nasabah, n.kode_nasabah, n.alamat, k.nama_kategori FROM transaksi_setoran t JOIN nasabah n ON t.nasabah_id = n.id JOIN kategori_sampah k ON t.kategori_id = k.id WHERE t.id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$trx = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

if (!$trx) {
    header("Location: riwayat.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Struk <?= e($trx['kode_transaksi']) ?> — RecyclePoint</title>
  <script src="https://cdn.tailwindcss.com/3.4.17"></script>
  <style>@media print { .no-print { display: none; } }</style>
</head>
<body class="bg-slate-100 min-h-screen flex items-center justify-center p-4">
  <div class="bg-white max-w-md w-full rounded-2xl border border-slate-200 p-6 shadow-sm">
    <div class="text-center border-b border-dashed border-slate-300 pb-4">
      <h1 class="text-lg font-extrabold text-slate-900">Recycle<span class="text-emerald-600">Point</span></h1>
      <p class="text-xs text-slate-500">Bukti Setoran Bank Sampah Digital</p>
    </div>

    <div class="py-4 space-y-2 text-sm border-b border-dashed border-slate-300">
      <div class="flex justify-between"><span class="text-slate-500">No. Transaksi</span><span class="font-bold"><?= e($trx['kode_transaksi']) ?></span></div>
      <div class="flex justify-between"><span class="text-slate-500">Tanggal</span><span><?= e($trx['tanggal_setor']) ?></span></div>
      <div class="flex justify-between"><span class="text-slate-500">Nasabah</span><span class="font-semibold"><?= e($trx['nama_nasabah']) ?> (<?= e($trx['kode_nasabah']) ?>)</span></div>
      <div class="flex justify-between"><span class="text-slate-500">Kategori</span><span><?= e($trx['nama_kategori']) ?></span></div>
      <div class="flex justify-between"><span class="text-slate-500">Berat</span><span><?= (float)$trx['berat_kg'] ?> kg × <?= formatRupiah($trx['harga_per_kg']) ?></span></div>
    </div>

    <div class="py-4 flex justify-between items-center">
      <span class="text-sm font-bold text-slate-700">Total Masuk Saldo</span>
      <span class="text-xl font-extrabold text-emerald-700"><?= formatRupiah($trx['total_nilai']) ?></span>
    </div>

    <div class="no-print mt-4 flex gap-2">
      <button type="button" onclick="window.print()" class="flex-1 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold">Cetak / Simpan PDF</button>
      <a href="riwayat.php" class="px-4 py-2.5 rounded-xl border border-slate-300 text-slate-700 text-sm font-semibold hover:bg-slate-50">Kembali</a>
    </div>
  </div>
</body>
</html>