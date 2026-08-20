<?php
require_once 'config/database.php';
require_once 'core/auth.php';
require_once 'core/notifikasi.php';
cek_login();

$userId = (int)$_SESSION['user_id'];
$role   = $_SESSION['role'] ?? 'agen';

// Aksi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['mark_all'])) {
        notifikasi_mark_all_read($pdo, $userId);
        header('Location: notifikasi');
        exit;
    }
    if (isset($_POST['mark_read'], $_POST['id'])) {
        notifikasi_mark_read($pdo, (int)$_POST['id'], $userId);
        header('Location: notifikasi?id=' . (int)$_POST['id']);
        exit;
    }
}

$detailId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$detail = null;
if ($detailId > 0) {
    $detail = notifikasi_get($pdo, $detailId, $userId);
    if ($detail && !(int)$detail['is_read']) {
        notifikasi_mark_read($pdo, $detailId, $userId);
        $detail['is_read'] = 1;
    }
}

$filter = $_GET['filter'] ?? 'all';
$unreadOnly = ($filter === 'unread');
$list = notifikasi_list($pdo, $userId, 100, $unreadOnly);
$unreadCount = notifikasi_count_unread($pdo, $userId);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifikasi | GAS-PAMAN</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="assets/css/futuristik.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; }</style>
</head>
<body class="bg-gray-50 flex flex-col md:flex-row min-h-screen">
<?php include 'views/includes/sidebar.php'; ?>

<main class="flex-1 p-4 md:p-10 overflow-y-auto">
    <div class="max-w-5xl mx-auto">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl md:text-3xl font-black text-gray-900 tracking-tight">Notifikasi</h1>
                <p class="text-sm text-gray-400 font-medium mt-1">
                    <?= $unreadCount > 0 ? $unreadCount . ' belum dibaca' : 'Semua sudah dibaca' ?>
                </p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="notifikasi" class="px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest <?= $filter==='all'?'bg-red-800 text-white':'bg-white border text-gray-600' ?>">Semua</a>
                <a href="notifikasi?filter=unread" class="px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest <?= $filter==='unread'?'bg-red-800 text-white':'bg-white border text-gray-600' ?>">Belum dibaca</a>
                <?php if ($unreadCount > 0): ?>
                <form method="POST" class="inline">
                    <button type="submit" name="mark_all" value="1" class="px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest bg-orange-100 text-orange-700 hover:bg-orange-200">
                        Tandai semua dibaca
                    </button>
                </form>
                <?php endif; ?>
            </div>
        </div>

        <div class="grid grid-cols-1 <?= $detail ? 'lg:grid-cols-5' : '' ?> gap-6">
            <!-- Daftar -->
            <div class="<?= $detail ? 'lg:col-span-2' : '' ?> bg-white rounded-[28px] border border-gray-100 shadow-sm overflow-hidden">
                <?php if (empty($list)): ?>
                <div class="p-12 text-center text-gray-400 italic text-sm">Belum ada notifikasi.</div>
                <?php else: ?>
                <ul class="divide-y divide-gray-50 max-h-[70vh] overflow-y-auto">
                    <?php foreach ($list as $n):
                        $active = ($detail && (int)$detail['id'] === (int)$n['id']);
                        $icon = notifikasi_icon($n['tipe']);
                    ?>
                    <li>
                        <a href="notifikasi?id=<?= (int)$n['id'] ?><?= $filter==='unread'?'&filter=unread':'' ?>"
                           class="block px-5 py-4 hover:bg-orange-50/40 transition-all <?= $active ? 'bg-orange-50 border-l-4 border-orange-500' : '' ?> <?= !(int)$n['is_read'] ? 'bg-red-50/30' : '' ?>">
                            <div class="flex gap-3 items-start">
                                <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 <?= !(int)$n['is_read'] ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-400' ?>">
                                    <i class="fas <?= $icon ?>"></i>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-2">
                                        <p class="text-sm font-bold text-gray-800 truncate"><?= htmlspecialchars($n['judul']) ?></p>
                                        <?php if (!(int)$n['is_read']): ?>
                                        <span class="w-2 h-2 rounded-full bg-red-600 flex-shrink-0"></span>
                                        <?php endif; ?>
                                    </div>
                                    <p class="text-xs text-gray-500 line-clamp-2 mt-0.5"><?= htmlspecialchars(mb_substr($n['pesan'] ?? '', 0, 100)) ?></p>
                                    <p class="text-[10px] text-gray-400 font-semibold mt-1"><?= date('d M Y, H:i', strtotime($n['created_at'])) ?></p>
                                </div>
                            </div>
                        </a>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <?php endif; ?>
            </div>

            <!-- Detail -->
            <?php if ($detail): ?>
            <div class="lg:col-span-3 bg-white rounded-[28px] border border-gray-100 shadow-sm p-6 md:p-8">
                <div class="flex items-start gap-4 mb-6">
                    <div class="w-14 h-14 rounded-2xl bg-orange-100 text-orange-700 flex items-center justify-center text-xl">
                        <i class="fas <?= notifikasi_icon($detail['tipe']) ?>"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-widest text-orange-600 mb-1"><?= htmlspecialchars(str_replace('_', ' ', $detail['tipe'])) ?></p>
                        <h2 class="text-xl font-black text-gray-900"><?= htmlspecialchars($detail['judul']) ?></h2>
                        <p class="text-xs text-gray-400 font-semibold mt-1"><?= date('d M Y, H:i', strtotime($detail['created_at'])) ?></p>
                    </div>
                </div>
                <div class="prose prose-sm max-w-none text-gray-700 font-medium leading-relaxed whitespace-pre-line mb-8">
                    <?= htmlspecialchars($detail['pesan'] ?? '—') ?>
                </div>
                <?php if (!empty($detail['link'])): ?>
                <a href="<?= htmlspecialchars($detail['link']) ?>"
                   class="inline-flex items-center gap-2 px-6 py-4 bg-red-800 hover:bg-black text-white text-xs font-black uppercase tracking-widest rounded-2xl transition-all shadow-lg">
                    <i class="fas fa-external-link-alt"></i> Lihat lebih detail
                </a>
                <?php endif; ?>
                <a href="notifikasi" class="ml-3 inline-flex items-center gap-2 px-6 py-4 bg-gray-100 text-gray-600 text-xs font-black uppercase tracking-widest rounded-2xl">
                    Kembali
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>
</main>
    <script src="assets/js/futuristik.js?v=2"></script>
</body>
</html>
