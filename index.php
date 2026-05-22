<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/perhitungan.php';

$pageTitle = 'Dashboard';
$activePage = 'dashboard';

$projects = $pdo->query('SELECT * FROM proyek_sumur ORDER BY created_at DESC')->fetchAll();
$selectedId = isset($_GET['proyek_id']) ? (int)$_GET['proyek_id'] : ($projects[0]['id'] ?? 0);
$stmt = $pdo->prepare('SELECT * FROM proyek_sumur WHERE id = ?');
$stmt->execute([$selectedId]);
$project = $stmt->fetch();
$kurs = get_usd_to_idr_rate();
include __DIR__ . '/includes/header.php';
?>

<?php if (!$project): ?>
    <div class="bg-white rounded-2xl p-8 shadow-sm border border-slate-200 text-center">
        <h2 class="text-xl font-bold mb-2">Belum ada proyek sumur</h2>
        <p class="text-slate-500 mb-6">Tambahkan proyek terlebih dahulu agar dashboard dapat menampilkan grafik dan tabel perhitungan.</p>
        <a href="tambah-proyek.php" class="inline-block bg-blue-600 text-white px-5 py-3 rounded-xl font-semibold">Tambah Proyek</a>
    </div>
<?php else:
    $hasil = hitung_ncf($project);
    $rows = $hasil['rows'];
    $summary = $hasil['summary'];
    $labels = array_column($rows, 'tahun');
    $produksi = array_map(fn($r) => round($r['produksi'], 2), $rows);
    $income = array_map(fn($r) => round($r['income'], 2), $rows);
    $opex = array_map(fn($r) => round($r['opex'], 2), $rows);
    $ncf = array_map(fn($r) => round($r['ncf'], 2), $rows);
?>
<div class="space-y-6">
    <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-slate-900">Dashboard Perhitungan Investasi Proyek Sumur Migas</h2>
                <p class="text-slate-500">Pilih proyek untuk melihat grafik produksi, pendapatan, dan NCF.</p>
            </div>
            <form method="GET">
                <label class="block text-sm font-semibold mb-1">Pilih Proyek Sumur</label>
                <select name="proyek_id" onchange="this.form.submit()" class="w-full lg:w-80 px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-500">
                    <?php foreach ($projects as $p): ?>
                        <option value="<?= e($p['id']) ?>" <?= $p['id'] == $selectedId ? 'selected' : '' ?>><?= e($p['nama_proyek']) ?> - <?= e($p['nama_sumur']) ?></option>
                    <?php endforeach; ?>
                </select>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-4">
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200"><div class="text-sm text-slate-500">Total Produksi</div><div class="text-2xl font-bold"><?= format_number($summary['total_produksi']) ?> Mbbl</div></div>
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200"><div class="text-sm text-slate-500">Total Pendapatan USD</div><div class="text-2xl font-bold"><?= format_usd_m($summary['total_income']) ?></div></div>
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200"><div class="text-sm text-slate-500">Total Pendapatan Rupiah</div><div class="text-2xl font-bold"><?= format_rupiah($summary['total_income'] * 1000000 * $kurs) ?></div></div>
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200"><div class="text-sm text-slate-500">Akumulasi NCF</div><div class="text-2xl font-bold <?= $summary['total_ncf_setelah_investasi'] >= 0 ? 'text-green-600' : 'text-red-600' ?>"><?= format_usd_m($summary['total_ncf_setelah_investasi']) ?></div></div>
        <div class="bg-slate-950 text-white p-5 rounded-2xl shadow-sm"><div class="text-sm text-slate-300">Kurs USD ke IDR</div><div class="text-2xl font-bold">Rp<?= number_format($kurs, 0, ',', '.') ?></div><div class="text-xs text-slate-400 mt-1">Data kurs melalui API, fallback jika gagal.</div></div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200"><h3 class="font-bold mb-4">Produksi Tahunan</h3><canvas id="chartProduksi"></canvas></div>
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200"><h3 class="font-bold mb-4">Pendapatan Tahunan</h3><canvas id="chartIncome"></canvas></div>
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200"><h3 class="font-bold mb-4">Net Cash Flow per Tahun</h3><canvas id="chartNcf"></canvas></div>
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200"><h3 class="font-bold mb-4">Income vs OPEX</h3><canvas id="chartIncomeOpex"></canvas></div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-5 border-b border-slate-200 flex items-center justify-between">
            <h3 class="font-bold">Pendapatan Tahunan dalam Rupiah</h3>
            <span class="text-sm text-slate-500">Proyek: <?= e($project['nama_proyek']) ?></span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-slate-600">
                    <tr><th class="p-3 text-left">Tahun</th><th class="p-3 text-right">Produksi</th><th class="p-3 text-right">Harga USD/Barrel</th><th class="p-3 text-right">Income USD</th><th class="p-3 text-right">Kurs</th><th class="p-3 text-right">Income Rupiah</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($rows as $r): ?>
                    <tr class="border-t border-slate-100 hover:bg-slate-50">
                        <td class="p-3"><?= e($r['tahun']) ?></td>
                        <td class="p-3 text-right"><?= format_number($r['produksi']) ?> Mbbl</td>
                        <td class="p-3 text-right">$<?= format_number($project['harga_minyak_usd']) ?></td>
                        <td class="p-3 text-right"><?= format_usd_m($r['income']) ?></td>
                        <td class="p-3 text-right">Rp<?= number_format($kurs, 0, ',', '.') ?></td>
                        <td class="p-3 text-right font-semibold"><?= format_rupiah($r['income'] * 1000000 * $kurs) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200">
        <h3 class="font-bold text-lg">Ringkasan Kelayakan Proyek</h3>
        <p class="mt-2 text-slate-600">Berdasarkan hasil perhitungan, status proyek adalah <strong><?= e($summary['status_kelayakan']) ?></strong>. Total NCF setelah investasi sebesar <strong><?= format_usd_m($summary['total_ncf_setelah_investasi']) ?></strong>.</p>
    </div>
</div>

<script>
const labels = <?= json_encode($labels) ?>;
const produksi = <?= json_encode($produksi) ?>;
const income = <?= json_encode($income) ?>;
const opex = <?= json_encode($opex) ?>;
const ncf = <?= json_encode($ncf) ?>;

new Chart(document.getElementById('chartProduksi'), {type:'line', data:{labels, datasets:[{label:'Produksi (Mbbl)', data:produksi, tension:.35}]}});
new Chart(document.getElementById('chartIncome'), {type:'bar', data:{labels, datasets:[{label:'Income ($M)', data:income}]}});
new Chart(document.getElementById('chartNcf'), {type:'line', data:{labels, datasets:[{label:'NCF ($M)', data:ncf, tension:.35}]}});
new Chart(document.getElementById('chartIncomeOpex'), {type:'bar', data:{labels, datasets:[{label:'Income ($M)', data:income},{label:'OPEX ($M)', data:opex}]}});
</script>
<?php endif; ?>

<?php include __DIR__ . '/includes/footer.php'; ?>
