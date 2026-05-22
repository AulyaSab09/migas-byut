<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/perhitungan.php';

$pageTitle = 'Proyek Sumur';
$activePage = 'proyek';

$search = trim($_GET['search'] ?? '');
$status = trim($_GET['status'] ?? '');
$where = [];
$params = [];
if ($search !== '') {
    $where[] = '(nama_proyek LIKE ? OR nama_sumur LIKE ?)';
    $params[] = "%$search%";
    $params[] = "%$search%";
}
if ($status !== '') {
    $where[] = 'status_proyek = ?';
    $params[] = $status;
}
$sql = 'SELECT * FROM proyek_sumur';
if ($where) $sql .= ' WHERE ' . implode(' AND ', $where);
$sql .= ' ORDER BY created_at DESC';
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$projects = $stmt->fetchAll();
include __DIR__ . '/includes/header.php';
?>

<div class="space-y-6">
    <?php if (!empty($_GET['success'])): ?>
        <div class="bg-green-50 text-green-700 border border-green-200 rounded-xl px-4 py-3">Data proyek berhasil diproses.</div>
    <?php endif; ?>
    <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold">Daftar Proyek Sumur</h2>
            <p class="text-slate-500">Kelola proyek sumur migas yang sudah dibangun maupun yang masih dalam perencanaan.</p>
        </div>
        <a href="tambah-proyek.php" class="bg-blue-600 text-white px-5 py-3 rounded-xl font-semibold text-center">Tambah Proyek</a>
    </div>

    <form method="GET" class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200 grid grid-cols-1 md:grid-cols-3 gap-4">
        <input type="text" name="search" value="<?= e($search) ?>" placeholder="Cari nama proyek atau sumur" class="px-4 py-3 rounded-xl border border-slate-200">
        <select name="status" class="px-4 py-3 rounded-xl border border-slate-200">
            <option value="">Semua Status</option>
            <?php foreach (['Direncanakan','Berjalan','Selesai'] as $s): ?>
                <option value="<?= e($s) ?>" <?= $status === $s ? 'selected' : '' ?>><?= e($s) ?></option>
            <?php endforeach; ?>
        </select>
        <button class="bg-slate-950 text-white rounded-xl font-semibold">Filter</button>
    </form>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-slate-600">
                    <tr>
                        <th class="p-3 text-left">Nama Proyek</th>
                        <th class="p-3 text-left">Nama Sumur</th>
                        <th class="p-3 text-left">Lokasi</th>
                        <th class="p-3 text-left">Jenis</th>
                        <th class="p-3 text-center">Tahun Awal</th>
                        <th class="p-3 text-center">Jangka Waktu</th>
                        <th class="p-3 text-center">Status</th>
                        <th class="p-3 text-right">Total NCF</th>
                        <th class="p-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (!$projects): ?>
                    <tr><td colspan="9" class="p-6 text-center text-slate-500">Belum ada data proyek.</td></tr>
                <?php endif; ?>
                <?php foreach ($projects as $p): $h = hitung_ncf($p); ?>
                    <tr class="border-t border-slate-100 hover:bg-slate-50">
                        <td class="p-3 font-semibold"><?= e($p['nama_proyek']) ?></td>
                        <td class="p-3"><?= e($p['nama_sumur']) ?></td>
                        <td class="p-3"><?= e($p['lokasi_lapangan']) ?></td>
                        <td class="p-3"><?= e($p['jenis_produksi']) ?></td>
                        <td class="p-3 text-center"><?= e($p['tahun_awal']) ?></td>
                        <td class="p-3 text-center"><?= e($p['jangka_waktu']) ?> tahun</td>
                        <td class="p-3 text-center"><?= status_badge($p['status_proyek']) ?></td>
                        <td class="p-3 text-right font-semibold"><?= format_usd_m($h['summary']['total_ncf_setelah_investasi']) ?></td>
                        <td class="p-3">
                            <div class="flex justify-center gap-2">
                                <a href="detail-proyek.php?id=<?= e($p['id']) ?>" class="px-3 py-2 rounded-lg bg-blue-50 text-blue-700">Detail</a>
                                <a href="edit-proyek.php?id=<?= e($p['id']) ?>" class="px-3 py-2 rounded-lg bg-amber-50 text-amber-700">Edit</a>
                                <a onclick="return confirm('Hapus proyek ini?')" href="hapus-proyek.php?id=<?= e($p['id']) ?>" class="px-3 py-2 rounded-lg bg-red-50 text-red-700">Hapus</a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
