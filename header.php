<?php
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
$page = $page ?? 'dashboard';
$pageTitle = $pageTitle ?? 'Dashboard';
$pageSubtitle = $pageSubtitle ?? 'Ringkasan aktivitas bank sampah';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= e($pageTitle) ?> — RecyclePoint</title>
  <script src="https://cdn.tailwindcss.com/3.4.17"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
  <style>
    body { font-family: 'Inter', sans-serif; background-color: #F7FDF8; color: #1F2937; }
  </style>
</head>
<body class="bg-[#F7FDF8] text-[#1F2937] min-h-screen">

  <!-- Sidebar Deep Forest (#064E36) -->
  <aside id="sidebar" class="fixed inset-y-0 left-0 z-40 w-64 bg-[#064E36] text-white transform -translate-x-full lg:translate-x-0 transition-transform duration-200 flex flex-col">
    <div class="h-20 px-4 flex items-center justify-between border-b border-white/10">
      <a href="index.php" class="flex items-center gap-3 bg-white rounded-xl px-3 py-1.5 w-full shadow-xs">
        <img src="logo.png" alt="Logo RecyclePoint" class="h-9 w-auto object-contain mx-auto" />
      </a>
      <button type="button" onclick="toggleSidebar()" class="lg:hidden text-white/80 hover:text-white p-1 ml-2">✕</button>
    </div>

    <nav class="flex-1 px-3 py-5 space-y-1.5 overflow-y-auto">
      <p class="px-3 text-[11px] font-semibold uppercase tracking-wider text-[#DCFCE7]/60 mb-2">Menu Utama</p>
      <?php
      $menus = [
          'dashboard' => ['index.php', 'Dashboard'],
          'nasabah'   => ['nasabah.php', 'Data Nasabah'],
          'kategori'  => ['kategori.php', 'Kategori Sampah'],
          'setoran'   => ['setoran.php', 'Setoran & Hitung Saldo'],
          'riwayat'   => ['riwayat.php', 'Riwayat Transaksi'],
      ];
      foreach ($menus as $key => [$url, $label]):
          $active = ($page === $key);
          $cls = $active
              ? 'bg-[#16A34A] text-white shadow-sm'
              : 'text-[#DCFCE7]/90 hover:bg-white/10 hover:text-white';
      ?>
        <a href="<?= $url ?>" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-colors <?= $cls ?>">
          <span class="w-2 h-2 rounded-full <?= $active ? 'bg-[#84CC16]' : 'bg-white/30' ?>"></span>
          <?= e($label) ?>
        </a>
      <?php endforeach; ?>
    </nav>

    <div class="p-4 border-t border-white/10 text-xs text-[#DCFCE7]/80">
      <span class="inline-block px-2 py-0.5 rounded-full bg-[#DCFCE7] text-[#064E36] font-bold text-[11px] mb-1">Smart Waste</span>
      <p class="font-semibold text-white">Recycle, Refresh, Repeat!!!</p>
      <p class="opacity-75">Agnes Jesica</p>
    </div>
  </aside>

  <!-- Content Wrapper -->
  <div class="lg:pl-64 min-h-screen flex flex-col">
    <!-- Topbar -->
    <header class="bg-white border-b border-[#F3F4F6] h-16 px-4 sm:px-6 flex items-center justify-between sticky top-0 z-30">
      <div class="flex items-center gap-3">
        <button type="button" onclick="toggleSidebar()" class="lg:hidden p-2 rounded-lg border border-slate-200 text-[#1F2937] hover:bg-[#F3F4F6]">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
          </svg>
        </button>
        <div class="text-xs sm:text-sm text-slate-500">
          <a href="index.php" class="hover:text-[#16A34A] font-medium">Beranda</a>
          <span class="mx-1.5">/</span>
          <span class="text-[#1F2937] font-semibold"><?= e($pageTitle) ?></span>
        </div>
      </div>

      <a href="setoran.php" class="px-3.5 py-2 rounded-xl bg-[#16A34A] hover:bg-[#064E36] text-white text-xs sm:text-sm font-semibold shadow-xs transition-colors">
        + Catat Setoran
      </a>
    </header>

    <main class="flex-1 max-w-6xl w-full mx-auto px-4 sm:px-6 py-6 space-y-6">
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
        <div>
          <h1 class="text-xl sm:text-2xl font-extrabold text-[#1F2937]"><?= e($pageTitle) ?></h1>
          <p class="text-xs sm:text-sm text-slate-500 mt-0.5"><?= e($pageSubtitle) ?></p>
        </div>
        <span class="self-start sm:self-auto text-xs font-semibold px-3 py-1 rounded-full bg-[#DCFCE7] text-[#064E36]">
          Admin Bank Sampah
        </span>
      </div>

      <?php if ($flash): ?>
        <div class="bg-[#DCFCE7] border border-[#84CC16]/50 text-[#064E36] px-4 py-3 rounded-xl text-sm font-medium">
          <?= e($flash) ?>
        </div>
      <?php endif; ?>
<script>
  function toggleSidebar() {
    document.getElementById('sidebar').classList.toggle('-translate-x-full');
  }
</script>