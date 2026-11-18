<?php
session_start();
require_once 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nasabah_id  = (int)($_POST['nasabah_id'] ?? 0);
    $kategori_id = (int)($_POST['kategori_id'] ?? 0);
    $berat_kg    = (float)($_POST['berat_kg'] ?? 0);
    $catatan     = trim($_POST['catatan'] ?? '');

    if ($nasabah_id > 0 && $kategori_id > 0 && $berat_kg > 0) {
        $stmtKat = mysqli_prepare($conn, "SELECT harga_per_kg FROM kategori_sampah WHERE id = ?");
        mysqli_stmt_bind_param($stmtKat, "i", $kategori_id);
        mysqli_stmt_execute($stmtKat);
        $kat = mysqli_fetch_assoc(mysqli_stmt_get_result($stmtKat));
        mysqli_stmt_close($stmtKat);

        if ($kat) {
            $harga = (int)$kat['harga_per_kg'];
            $total = (int)round($berat_kg * $harga);
            $nextNum = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(MAX(id), 0) + 1001 AS next_num FROM transaksi_setoran"))['next_num'];
            $kodeTrx = 'TRX-' . $nextNum;

            mysqli_begin_transaction($conn);
            try {
                $stmtIns = mysqli_prepare($conn, "INSERT INTO transaksi_setoran (kode_transaksi, nasabah_id, kategori_id, berat_kg, harga_per_kg, total_nilai, catatan) VALUES (?, ?, ?, ?, ?, ?, ?)");
                mysqli_stmt_bind_param($stmtIns, "siidiis", $kodeTrx, $nasabah_id, $kategori_id, $berat_kg, $harga, $total, $catatan);
                mysqli_stmt_execute($stmtIns);
                mysqli_stmt_close($stmtIns);

                $stmtUpd = mysqli_prepare($conn, "UPDATE nasabah SET saldo = saldo + ? WHERE id = ?");
                mysqli_stmt_bind_param($stmtUpd, "ii", $total, $nasabah_id);
                mysqli_stmt_execute($stmtUpd);
                mysqli_stmt_close($stmtUpd);

                mysqli_commit($conn);
                $_SESSION['flash'] = "Setoran $kodeTrx tersimpan! Saldo nasabah bertambah " . formatRupiah($total) . ".";
            } catch (Exception $ex) {
                mysqli_rollback($conn);
                $_SESSION['flash'] = "Gagal menyimpan setoran.";
            }
        }
    }
    header("Location: setoran.php");
    exit;
}

$nasabahList = [];
$qN = mysqli_query($conn, "SELECT * FROM nasabah ORDER BY id ASC");
while ($r = mysqli_fetch_assoc($qN)) $nasabahList[] = $r;

$kategoriList = [];
$qK = mysqli_query($conn, "SELECT * FROM kategori_sampah ORDER BY id ASC");
while ($r = mysqli_fetch_assoc($qK)) $kategoriList[] = $r;

$page = 'setoran';
$pageTitle = 'Data Setoran & Hitung Saldo';
$pageSubtitle = 'Catat setoran sampah untuk menghitung dan menambah saldo nasabah otomatis.';
require_once 'header.php';
?>
<div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
  <div class="lg:col-span-6 bg-white rounded-2xl border border-slate-200 p-5 sm:p-6 shadow-sm">
    <h2 class="text-lg font-bold text-slate-900 mb-1">Catat Setoran Sampah</h2>
    <p class="text-xs sm:text-sm text-slate-500 mb-5">Pilih nasabah, kategori, dan beratnya. Saldo otomatis dihitung dan disimpan ke database.</p>

    <form method="POST" action="setoran.php" class="space-y-4">
      <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">Pilih Nasabah</label>
        <select name="nasabah_id" required class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm">
          <?php foreach ($nasabahList as $n): ?>
            <option value="<?= (int)$n['id'] ?>"><?= e($n['nama']) ?> (<?= e($n['kode_nasabah']) ?>)</option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1.5">Kategori Sampah</label>
          <select id="setor-kategori" name="kategori_id" required class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm">
            <?php foreach ($kategoriList as $k): ?>
              <option value="<?= (int)$k['id'] ?>" data-harga="<?= (int)$k['harga_per_kg'] ?>">
                <?= e($k['nama_kategori']) ?> — <?= formatRupiah($k['harga_per_kg']) ?>/kg
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1.5">Berat Sampah (kg)</label>
          <input id="setor-berat" name="berat_kg" type="number" min="0.1" step="0.1" value="2.0" required class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm" />
        </div>
      </div>

      <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">Catatan (Opsional)</label>
        <input name="catatan" type="text" placeholder="Contoh: Kondisi bersih dan sudah dipilah" class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm" />
      </div>

      <div class="rounded-xl bg-emerald-50 border border-emerald-200 p-4 flex items-center justify-between">
        <div>
          <p class="text-xs font-semibold text-emerald-800">Perhitungan Saldo Otomatis</p>
          <p id="calc-formula" class="text-xs text-emerald-700 mt-0.5">-</p>
        </div>
        <span id="calc-total" class="text-xl font-extrabold text-emerald-700">Rp 0</span>
      </div>

      <button type="submit" class="w-full py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-sm">
        Simpan Setoran &amp; Tambahkan Saldo
      </button>
    </form>
  </div>

  <div class="lg:col-span-6 bg-white rounded-2xl border border-slate-200 p-5 sm:p-6 shadow-sm">
    <h3 class="text-base font-bold text-slate-900 mb-4">Saldo Nasabah Saat Ini</h3>
    <div class="space-y-2.5">
      <?php foreach ($nasabahList as $n): ?>
        <div class="p-3 rounded-xl border border-slate-200 flex items-center justify-between">
          <div>
            <p class="text-sm font-semibold text-slate-900"><?= e($n['nama']) ?></p>
            <p class="text-xs text-slate-500"><?= e($n['kode_nasabah']) ?></p>
          </div>
          <span class="text-sm font-extrabold text-emerald-700"><?= formatRupiah($n['saldo']) ?></span>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>

<script>
  function updateCalc() {
    const sel = document.getElementById('setor-kategori');
    const opt = sel.options[sel.selectedIndex];
    const harga = opt ? parseInt(opt.dataset.harga || '0', 10) : 0;
    const berat = parseFloat(document.getElementById('setor-berat').value || '0');
    const total = Math.round(harga * berat);
    document.getElementById('calc-formula').textContent = berat + ' kg × Rp ' + harga.toLocaleString('id-ID') + '/kg';
    document.getElementById('calc-total').textContent = 'Rp ' + total.toLocaleString('id-ID');
  }
  document.getElementById('setor-kategori').addEventListener('change', updateCalc);
  document.getElementById('setor-berat').addEventListener('input', updateCalc);
  updateCalc();
</script>
</main></div></body></html>