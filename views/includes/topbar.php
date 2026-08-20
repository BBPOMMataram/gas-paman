<?php
/**
 * Top bar fixed di area konten (kanan sidebar).
 * Ikon notifikasi di pojok kanan atas untuk semua role.
 */
if (!isset($notifUnread)) {
    $notifUnread = 0;
    if (!empty($_SESSION['user_id'])) {
        if (!function_exists('notifikasi_count_unread')) {
            $notifPath = __DIR__ . '/../../core/notifikasi.php';
            if (is_file($notifPath)) require_once $notifPath;
        }
        if (!isset($pdo) || !($pdo instanceof PDO)) {
            $dbPath = __DIR__ . '/../../config/database.php';
            if (is_file($dbPath)) require_once $dbPath;
        }
        if (isset($pdo) && $pdo instanceof PDO && function_exists('notifikasi_count_unread')) {
            $notifUnread = notifikasi_count_unread($pdo, (int)$_SESSION['user_id']);
        }
    }
}
$topbarNama = $_SESSION['nama'] ?? 'User';
$topbarRole = $_SESSION['role'] ?? '';
$topbarRoleLabel = match ($topbarRole) {
    'admin'   => 'Admin',
    'staff'   => 'Staff',
    'kabalai' => 'Kepala Balai',
    'agen'    => 'Agen',
    default   => ucfirst((string)$topbarRole),
};
?>
<style>
/* Agar konten tidak tertutup top bar fixed */
@media (min-width: 768px) {
    main {
        padding-top: 5.5rem !important; /* tinggi top bar ~4rem + jarak napas */
    }
}
</style>
<header id="appTopbar" class="hidden md:block fixed top-4 right-4 left-80 z-40 bg-white/90 backdrop-blur-xl border border-white/70 shadow-lg shadow-red-900/5 rounded-2xl">
    <div class="flex items-center justify-between gap-3 px-5 md:px-8 h-16">
        <div class="min-w-0">
            <p class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-400 truncate">GAS-PAMAN · BBPOM Mataram</p>
            <p class="text-xs font-bold text-gray-700 truncate"><?= htmlspecialchars($topbarRoleLabel) ?> &middot; <?= htmlspecialchars($topbarNama) ?></p>
        </div>

        <div class="flex items-center gap-2 md:gap-3">
            <a href="notifikasi" id="topbarNotifBtn" class="relative inline-flex items-center justify-center w-11 h-11 rounded-2xl bg-gray-50 hover:bg-orange-50 text-gray-600 hover:text-orange-600 border border-gray-100 hover:border-orange-200 transition-all" title="Notifikasi">
                <i class="fas fa-bell text-lg"></i>
                <?php if (!empty($notifUnread) && (int)$notifUnread > 0): ?>
                <span class="absolute -top-1 -right-1 min-w-[18px] h-[18px] px-1 rounded-full bg-red-600 text-white text-[10px] font-black flex items-center justify-center shadow ring-2 ring-white">
                    <?= (int)$notifUnread > 9 ? '9+' : (int)$notifUnread ?>
                </span>
                <?php endif; ?>
            </a>

            <a href="profil" class="flex items-center gap-2 pl-1.5 pr-3 py-1.5 rounded-2xl bg-gray-50 hover:bg-red-50 border border-gray-100 hover:border-red-100 transition-all max-w-[180px]" title="Profil">
                <div class="w-8 h-8 rounded-xl bg-red-800 text-white flex items-center justify-center text-xs font-black flex-shrink-0">
                    <?= strtoupper(mb_substr($topbarNama, 0, 1)) ?>
                </div>
                <span class="text-xs font-bold text-gray-700 truncate"><?= htmlspecialchars($topbarNama) ?></span>
            </a>
        </div>
    </div>
</header>
