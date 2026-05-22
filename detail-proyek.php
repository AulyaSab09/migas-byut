<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/perhitungan.php';
$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare('SELECT * FROM proyek_sumur WHERE id = ?');
$stmt->execute([$id]);
$project = $stmt->fetch();
if (!$project) die('Data proyek tidak ditemukan.');
$hasil = hitung_ncf($project);
$rows = $hasil['rows'];
$summary = $hasil['summary'];
$kurs = get_usd_to_idr_rate();
$pageTitle = 'Detail Proyek';
$activePage = 'proyek';
include __DIR__ . '/includes/header.php';
?>
<div class="space-y-6">
    <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold"><?= e($project['nama_proyek']) ?></h2>
            <p class="text-slate-500">Sumur <?= e($project['nama_sumur']) ?> - <?= e($project['lokasi_lapangan']) ?></p>
        </div>
        <div class="flex gap-3">
            <a href="edit-proyek.php?id=<?= e($project['id']) ?>" class="bg-amber-500 text-white px-5 py-3 rounded-xl font-semibold">Edit Proyek</a>
            <a href="proyek.php" class="bg-white border border-slate-200 px-5 py-3 rounded-xl font-semibold">Kembali</a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 xl:grid-cols-6 gap-4">
        <div class="bg-white p-5 rounded-2xl border shadow-sm"><div class="text-sm text-slate-500">Status</div><div class="mt-2"><?= status_badge($project['status_proyek']) ?></div></div>
        <div class="bg-white p-5 rounded-2xl border shadow-sm"><div class="text-sm text-slate-500">Capital</div><div class="text-xl font-bold"><?= format_usd_m($project['capital']) ?></div></div>
        <div class="bg-white p-5 rounded-2xl border shadow-sm"><div class="text-sm text-slate-500">Non-Capital</div><div class="text-xl font-bold"><?= format_usd_m($project['non_capital']) ?></div></div>
        <div class="bg-white p-5 rounded-2xl border shadow-sm"><div class="text-sm text-slate-500">OPEX/Tahun</div><div class="text-xl font-bold"><?= format_usd_m($project['opex_tahun']) ?></div></div>
        <div class="bg-white p-5 rounded-2xl border shadow-sm"><div class="text-sm text-slate-500">Pajak</div><div class="text-xl font-bold"><?= format_number($project['pajak_penghasilan']) ?>%</div></div>
        <div class="bg-white p-5 rounded-2xl border shadow-sm"><div class="text-sm text-slate-500">Metode</div><div class="text-sm font-bold"><?= e($project['metode_depresiasi']) ?></div></div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-5 border-b border-slate-200">
            <h3 class="font-bold text-lg">Tabel Perhitungan Tahunan</h3>
            <p class="text-sm text-slate-500">Rumus mengikuti contoh spreadsheet: Income - OPEX - Depresiasi = Taxable Income, lalu NCF = Taxable Income - Tax.</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-slate-600 sticky top-0">
                    <tr>
                        <th class="p-3 text-left">Tahun</th><th class="p-3 text-right">Produksi</th><th class="p-3 text-right">Income USD</th><th class="p-3 text-right">Income Rupiah</th><th class="p-3 text-right">OPEX</th><th class="p-3 text-right">Depresiasi</th><th class="p-3 text-right">Taxable Income</th><th class="p-3 text-right">Tax</th><th class="p-3 text-right">NCF</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="bg-slate-50 border-t border-slate-100">
                        <td class="p-3 font-semibold">0</td><td class="p-3 text-right">-</td><td class="p-3 text-right">-</td><td class="p-3 text-right">-</td><td class="p-3 text-right">-</td><td class="p-3 text-right">-</td><td class="p-3 text-right">-</td><td class="p-3 text-right">-</td><td class="p-3 text-right font-bold text-red-600">-<?= format_usd_m($summary['total_investasi']) ?></td>
                    </tr>
                    <?php foreach ($rows as $r): ?>
                    <tr class="border-t border-slate-100 hover:bg-slate-50">
                        <td class="p-3"><?= e($r['tahun']) ?></td>
                        <td class="p-3 text-right"><?= format_number($r['produksi']) ?></td>
                        <td class="p-3 text-right"><?= format_usd_m($r['income']) ?></td>
                        <td class="p-3 text-right"><?= format_rupiah($r['income'] * 1000000 * $kurs) ?></td>
                        <td class="p-3 text-right"><?= format_usd_m($r['opex']) ?></td>
                        <td class="p-3 text-right"><?= format_usd_m($r['depresiasi']) ?></td>
                        <td class="p-3 text-right"><?= format_usd_m($r['taxable_income']) ?></td>
                        <td class="p-3 text-right"><?= format_usd_m($r['tax']) ?></td>
                        <td class="p-3 text-right font-semibold"><?= format_usd_m($r['ncf']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <tr class="bg-slate-950 text-white">
                        <td class="p-3 font-bold" colspan="8">Total NCF Setelah Investasi</td>
                        <td class="p-3 text-right font-bold"><?= format_usd_m($summary['total_ncf_setelah_investasi']) ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
