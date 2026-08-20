<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$current_page = basename($_SERVER['PHP_SELF'], ".php");
$role = $_SESSION['role'] ?? 'agen';

// Badge notifikasi
$notifUnread = 0;
if (!empty($_SESSION['user_id']) && is_file(__DIR__ . '/../../core/notifikasi.php')) {
    require_once __DIR__ . '/../../core/notifikasi.php';
    if (isset($pdo) && $pdo instanceof PDO) {
        $notifUnread = notifikasi_count_unread($pdo, (int)$_SESSION['user_id']);
    } else {
        // Coba load database jika belum
        if (is_file(__DIR__ . '/../../config/database.php')) {
            require_once __DIR__ . '/../../config/database.php';
            if (isset($pdo) && $pdo instanceof PDO) {
                $notifUnread = notifikasi_count_unread($pdo, (int)$_SESSION['user_id']);
            }
        }
    }
}
?>

<div class="md:hidden bg-red-700 text-white p-4 flex justify-between items-center shadow-md sticky top-0 z-50">
    <div class="flex items-center space-x-3">
        <div class="w-8 h-8 bg-white rounded-full overflow-hidden border border-white/20">
            <img src="views/gas-paman-round.png" alt="Logo" class="w-full h-full object-cover"> 
        </div>
        <h1 class="text-lg font-black tracking-tighter uppercase">GAS-PAMAN</h1>
    </div>
    <div class="flex items-center gap-1">
        <a href="notifikasi" class="relative p-2 focus:outline-none hover:bg-red-800 rounded-lg transition-colors" title="Notifikasi">
            <i class="fas fa-bell text-xl"></i>
            <?php if (!empty($notifUnread)): ?>
            <span class="absolute top-0.5 right-0.5 min-w-[16px] h-4 px-1 bg-orange-400 text-red-900 text-[9px] font-black rounded-full flex items-center justify-center"><?= $notifUnread > 9 ? '9+' : (int)$notifUnread ?></span>
            <?php endif; ?>
        </a>
        <button id="mobileMenuBtn" class="p-2 focus:outline-none hover:bg-red-800 rounded-lg transition-colors">
            <i class="fas fa-bars text-xl"></i>
        </button>
    </div>
</div>

<div id="sidebarOverlay" class="fixed inset-0 bg-black/50 z-40 hidden md:hidden transition-opacity"></div>

<aside id="mainSidebar" class="fixed top-4 bottom-4 left-4 transform -translate-x-full md:translate-x-0 w-72 bg-red-800 text-white flex flex-col shadow-2xl transition-transform duration-300 ease-in-out z-50 overflow-y-auto rounded-[28px]">

    <div class="p-7 text-center border-b border-white/10 hidden md:block relative">
        <div class="relative inline-block group">
            <div class="fx-logo-halo absolute inset-0 bg-orange-400 rounded-full blur-xl opacity-20 group-hover:opacity-40 transition-opacity"></div>

            <div class="relative bg-white p-1 rounded-full shadow-2xl inline-block overflow-hidden w-24 h-24 border-4 border-white/20">
                <img src="views/gas-paman.png" alt="Logo GAS-PAMAN" class="w-full h-full object-cover rounded-full">
            </div>
        </div>
        <h2 class="text-white font-black text-lg mt-4 tracking-tighter leading-none">GAS-PAMAN</h2>
        <p class="text-[9px] uppercase tracking-[0.3em] text-orange-400 mt-2 font-black">BBPOM DI MATARAM</p>
    </div>

    <div class="flex justify-end p-4 md:hidden">
        <button id="closeSidebar" class="p-2 text-white/60 hover:text-white">
            <i class="fas fa-times text-2xl"></i>
        </button>
    </div>

    <nav class="flex-1 p-4 space-y-1.5">
        <?php
        $dash_url_per_role = ['admin' => 'admin-dashboard', 'kabalai' => 'kabalai-dashboard'];
        $dash_url = $dash_url_per_role[$role] ?? 'dashboard';

        // Menu utama — sama persis dengan menu lama, hanya ditata ulang
        $menu = [];
        $menu[] = ['href' => $dash_url, 'icon' => 'fa-th-large', 'label' => 'Dashboard',
                   'active' => $current_page == $dash_url];
        if ($role === 'agen') {
            $menu[] = ['href' => 'tambah-catatan', 'icon' => 'fa-plus-circle', 'label' => 'Tambah Laporan',
                       'active' => $current_page == 'tambah-catatan'];
        }
        if (in_array($role, ['admin', 'kabalai'])) {
            $menu[] = ['href' => 'daftar-agen', 'icon' => 'fa-users-cog', 'label' => 'Manajemen Agen',
                       'active' => in_array($current_page, ['daftar-agen', 'detail-agen'])];
            $menu[] = ['href' => 'analytics', 'icon' => 'fa-chart-line', 'label' => 'Analitik & Laporan',
                       'active' => $current_page == 'analytics'];
            $menu[] = ['href' => 'rangkuman-data', 'icon' => 'fa-file-alt', 'label' => 'Rangkuman Data',
                       'active' => $current_page == 'rangkuman-data'];
        }
        if (in_array($role, ['admin', 'staff'])) {
            $menu[] = ['href' => 'daftar-soal', 'icon' => 'fa-question-circle', 'label' => 'Kelola Soal',
                       'active' => in_array($current_page, ['daftar-soal', 'tambah-soal', 'edit-soal'])];
        }
        if (in_array($role, ['admin', 'staff', 'kabalai'])) {
            $menu[] = ['href' => 'hasil-test-admin', 'icon' => 'fa-chart-bar', 'label' => 'Hasil Test Agen',
                       'active' => $current_page == 'hasil-test-admin'];
        }
        if ($role === 'agen') {
            $menu[] = ['href' => 'pre-test', 'icon' => 'fa-clipboard-list', 'label' => 'Pre-Test',
                       'active' => $current_page == 'pre-test'];
            $menu[] = ['href' => 'post-test', 'icon' => 'fa-clipboard-check', 'label' => 'Post-Test',
                       'active' => $current_page == 'post-test'];
            $menu[] = ['href' => 'hasil-test', 'icon' => 'fa-star', 'label' => 'Hasil Test Saya',
                       'active' => $current_page == 'hasil-test'];
            $menu[] = ['href' => 'log-laporan-agen', 'icon' => 'fa-book', 'label' => 'Log Laporan',
                       'active' => $current_page == 'log-laporan-agen'];
        }
        if ($role === 'kabalai') {
            $menu[] = ['href' => 'sertifikat-approval', 'icon' => 'fa-file-signature', 'label' => 'Persetujuan Sertifikat',
                       'active' => $current_page == 'sertifikat-approval'];
        }
        $menu[] = ['href' => 'riwayat', 'icon' => 'fa-history',
                   'label' => (in_array($role, ['admin', 'kabalai'])) ? 'Monitoring Laporan' : 'Riwayat Catatan',
                   'active' => $current_page == 'riwayat'];
        if ($role === 'admin') {
            $menu[] = ['href' => 'log-laporan', 'icon' => 'fa-clipboard-list', 'label' => 'Log Laporan',
                       'active' => $current_page == 'log-laporan'];
        }
        $menu[] = ['href' => 'profil', 'icon' => 'fa-user-circle', 'label' => 'Profil',
                   'active' => $current_page == 'profil'];

        foreach ($menu as $m):
        ?>
        <a href="<?= $m['href'] ?>" class="flex items-center gap-3 px-3 py-2.5 rounded-2xl transition-all <?= $m['active'] ? 'fx-nav-active bg-gradient-to-r from-orange-600 to-red-600 font-bold' : 'opacity-75 hover:opacity-100 hover:bg-white/10' ?>">
            <span class="fx-nav-icon w-9 h-9 rounded-xl flex items-center justify-center text-sm shrink-0 <?= $m['active'] ? 'bg-white/20' : 'bg-white/10' ?>">
                <i class="fas <?= $m['icon'] ?>"></i>
            </span>
            <span class="text-[13px] leading-tight"><?= $m['label'] ?></span>
            <?php if ($m['active']): ?>
            <span class="ml-auto relative flex h-2 w-2">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-orange-300 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2 w-2 bg-orange-300"></span>
            </span>
            <?php endif; ?>
        </a>
        <?php endforeach; ?>
    </nav>

    <div class="p-4 mt-auto">
        <a href="logout" class="flex items-center gap-3 px-3 py-2.5 bg-white/5 hover:bg-white text-white hover:text-red-800 rounded-2xl transition-all group border border-white/10">
            <span class="w-9 h-9 rounded-xl bg-white/10 group-hover:bg-red-50 flex items-center justify-center text-sm shrink-0">
                <i class="fas fa-sign-out-alt"></i>
            </span>
            <span class="text-[13px] font-bold">Keluar</span>
        </a>
    </div>
</aside>

<!-- Spacer: menjaga lebar konten di desktop karena sidebar fixed (tidak ikut scroll) -->
<div class="sidebar-spacer hidden md:block w-80 flex-shrink-0" aria-hidden="true"></div>

<?php
// Top bar global (ikon notifikasi di kanan atas)
if (is_file(__DIR__ . '/topbar.php')) {
    include __DIR__ . '/topbar.php';
}
?>

<script>
    // Pastikan DOM sudah dimuat sepenuhnya
    document.addEventListener('DOMContentLoaded', function() {
        const mobileMenuBtn = document.getElementById('mobileMenuBtn');
        const closeSidebar = document.getElementById('closeSidebar');
        const mainSidebar = document.getElementById('mainSidebar');
        const sidebarOverlay = document.getElementById('sidebarOverlay');

        function toggleSidebar() {
            mainSidebar.classList.toggle('-translate-x-full');
            sidebarOverlay.classList.toggle('hidden');
            document.body.classList.toggle('overflow-hidden');
        }

        if(mobileMenuBtn) {
            mobileMenuBtn.addEventListener('click', function(e) {
                e.preventDefault();
                toggleSidebar();
            });
        }

        if(closeSidebar) {
            closeSidebar.addEventListener('click', function(e) {
                e.preventDefault();
                toggleSidebar();
            });
        }

        if(sidebarOverlay) {
            sidebarOverlay.addEventListener('click', function() {
                toggleSidebar();
            });
        }
    });
</script>