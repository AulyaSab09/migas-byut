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
    <div class="app-card rounded-[28px] p-6 sm:p-8 text-center">
        <div class="icon-box mx-auto mb-4 w-14 h-14 rounded-2xl">
            <svg class="ui-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M5 19h14"/><path d="M7 19V8l5-4 5 4v11"/><path d="M9 19v-5h6v5"/><path d="M10 9h4"/></svg>
        </div>
        <h2 class="text-xl font-bold mb-2" style="color: var(--color-heading);">Belum ada proyek sumur</h2>
        <p class="mb-6" style="color: var(--color-muted);">Tambahkan proyek terlebih dahulu agar dashboard dapat menampilkan grafik dan tabel perhitungan.</p>
        <a href="tambah-proyek.php" class="inline-block app-btn-primary px-5 py-3 rounded-xl font-semibold transition">Tambah Proyek</a>
    </div>
<?php else:
    $hasil = hitung_ncf($project);
    $rows = $hasil['rows'];
    $summary = $hasil['summary'];
    $labels = array_column($rows, 'tahun');
    $produksi = array_map(fn($r) => round($r['produksi'], 2), $rows);
    $income = array_map(fn($r) => round($r['income'], 2), $rows);
    $ncf = array_map(fn($r) => round($r['ncf'], 2), $rows);
    $isLayak = $summary['total_ncf_setelah_investasi'] >= 0;
    $statusBg = $isLayak ? '#EAF8ED' : '#FFF4DA';
    $statusColor = $isLayak ? '#3F8F4D' : '#7A5A1E';
    $statusIconBg = $isLayak ? '#DFF3E4' : '#FFF0C2';
?>
<div class="space-y-5 lg:space-y-6 min-w-0">
    <div class="app-card rounded-[26px] sm:rounded-[28px] p-5 sm:p-6 relative overflow-hidden min-w-0">
        <div class="absolute -right-16 -top-16 w-44 sm:w-48 h-44 sm:h-48 rounded-full opacity-40" style="background: var(--color-primary-pale);"></div>
        <div class="relative flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5 min-w-0">
            <div class="min-w-0">
                <h2 class="text-xl sm:text-2xl xl:text-3xl font-bold tracking-tight leading-tight" style="color: var(--color-heading);">Perhitungan Investasi Proyek Sumur Migas</h2>
                <p class="mt-2 text-sm sm:text-base leading-relaxed" style="color: var(--color-muted);">Pilih proyek untuk melihat produksi, pendapatan, net cash flow, dan tabel perhitungan tahunan.</p>
            </div>
            <form method="GET" class="w-full lg:w-auto shrink-0">
                <label class="block text-sm font-bold mb-2" style="color: var(--color-heading);">Pilih Proyek Sumur</label>
                <select name="proyek_id" onchange="this.form.submit()" class="w-full lg:w-80 max-w-full px-4 py-3 rounded-xl app-input">
                    <?php foreach ($projects as $p): ?>
                        <option value="<?= e($p['id']) ?>" <?= $p['id'] == $selectedId ? 'selected' : '' ?>><?= e($p['nama_proyek']) ?> - <?= e($p['nama_sumur']) ?></option>
                    <?php endforeach; ?>
                </select>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 2xl:grid-cols-5 gap-4 min-w-0">
        <div class="app-card app-card-hover p-5 rounded-[22px] sm:rounded-[24px] min-w-0">
            <div class="flex items-start justify-between gap-3 min-w-0">
                <div class="min-w-0">
                    <div class="text-sm font-semibold" style="color: var(--color-muted);">Total Produksi</div>
                    <div class="kpi-value font-bold mt-1" style="color: var(--color-heading);"><?= format_number($summary['total_produksi']) ?> Mbbl</div>
                </div>
                <div class="icon-box">
                    <svg class="ui-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M8 20V6.5A2.5 2.5 0 0 1 10.5 4h3A2.5 2.5 0 0 1 16 6.5V20"/><path d="M6 20h12"/><path d="M10 8h4"/><path d="M10 12h4"/></svg>
                </div>
            </div>
        </div>
        <div class="app-card app-card-hover p-5 rounded-[22px] sm:rounded-[24px] min-w-0">
            <div class="flex items-start justify-between gap-3 min-w-0">
                <div class="min-w-0">
                    <div class="text-sm font-semibold" style="color: var(--color-muted);">Total Pendapatan USD</div>
                    <div class="kpi-value font-bold mt-1" style="color: var(--color-heading);"><?= format_usd_m($summary['total_income']) ?></div>
                </div>
                <div class="icon-box" style="background: #FFF8E6; color: #D88912;">
                    <svg class="ui-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2v20"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7H14a3.5 3.5 0 0 1 0 7H6"/></svg>
                </div>
            </div>
        </div>
        <div class="app-card app-card-hover p-5 rounded-[22px] sm:rounded-[24px] min-w-0 sm:col-span-2 lg:col-span-1">
            <div class="flex items-start justify-between gap-3 min-w-0">
                <div class="min-w-0">
                    <div class="text-sm font-semibold" style="color: var(--color-muted);">Total Pendapatan Rupiah</div>
                    <div class="kpi-value font-bold mt-1" style="color: var(--color-heading);"><?= format_rupiah($summary['total_income'] * 1000000 * $kurs) ?></div>
                </div>
                <div class="icon-box">
                    <svg class="ui-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M6 7h9a3 3 0 0 1 0 6H6z"/><path d="M6 13h10a3 3 0 0 1 0 6H6z"/><path d="M6 7v12"/></svg>
                </div>
            </div>
        </div>
        <div class="app-card app-card-hover p-5 rounded-[22px] sm:rounded-[24px] min-w-0">
            <div class="flex items-start justify-between gap-3 min-w-0">
                <div class="min-w-0">
                    <div class="text-sm font-semibold" style="color: var(--color-muted);">Akumulasi NCF</div>
                    <div class="kpi-value font-bold mt-1 <?= $isLayak ? 'text-[#63C174]' : 'text-[#E46A61]' ?>"><?= format_usd_m($summary['total_ncf_setelah_investasi']) ?></div>
                </div>
                <div class="icon-box">
                    <svg class="ui-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M3 17l6-6 4 4 7-7"/><path d="M14 8h6v6"/></svg>
                </div>
            </div>
        </div>
        <div class="p-5 rounded-[22px] sm:rounded-[24px] text-white relative overflow-hidden min-w-0" style="background: linear-gradient(135deg, #2F2A24, #7A5A1E); box-shadow: var(--shadow-card);">
            <div class="absolute -right-8 -top-8 w-24 h-24 rounded-full bg-white/10"></div>
            <div class="relative flex items-start justify-between gap-3 min-w-0">
                <div class="min-w-0">
                    <div class="text-sm font-semibold text-white/70">Kurs USD ke IDR</div>
                    <div class="kpi-value font-bold mt-1 text-white">Rp<?= number_format($kurs, 0, ',', '.') ?></div>
                </div>
                <div class="w-10 h-10 rounded-2xl bg-white/10 flex items-center justify-center shrink-0">
                    <svg class="ui-icon text-white" viewBox="0 0 24 24" aria-hidden="true"><path d="M4 6h16"/><path d="M4 12h16"/><path d="M4 18h16"/><path d="M8 3c-2 3-2 15 0 18"/><path d="M16 3c2 3 2 15 0 18"/></svg>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-5 lg:gap-6 min-w-0">
        <div class="app-card rounded-[24px] sm:rounded-[26px] p-4 sm:p-5 min-w-0">
            <div class="flex items-start justify-between gap-3 mb-4 min-w-0">
                <div class="min-w-0">
                    <h3 class="font-bold tracking-tight" style="color: var(--color-heading);">Produksi Tahunan</h3>
                    <p class="text-sm leading-relaxed" style="color: var(--color-muted);">Volume produksi setiap tahun.</p>
                </div>
                <span class="px-3 py-1 rounded-full text-xs font-bold shrink-0" style="background: var(--color-primary-pale); color: var(--color-primary-hover);">Mbbl</span>
            </div>
            <div class="chart-wrap h-[260px] sm:h-[310px]"><canvas id="chartProduksi"></canvas></div>
        </div>

        <div class="app-card rounded-[24px] sm:rounded-[26px] p-4 sm:p-5 min-w-0">
            <div class="flex items-start justify-between gap-3 mb-4 min-w-0">
                <div class="min-w-0">
                    <h3 class="font-bold tracking-tight" style="color: var(--color-heading);">Pendapatan Tahunan</h3>
                    <p class="text-sm leading-relaxed" style="color: var(--color-muted);">Income tahunan dalam USD juta.</p>
                </div>
                <span class="px-3 py-1 rounded-full text-xs font-bold shrink-0" style="background: #FFF8E6; color: #D88912;">$M</span>
            </div>
            <div class="chart-wrap h-[260px] sm:h-[310px]"><canvas id="chartIncome"></canvas></div>
        </div>

        <div class="app-card rounded-[24px] sm:rounded-[26px] p-4 sm:p-5 xl:col-span-2 min-w-0">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-2 mb-4 min-w-0">
                <div class="min-w-0">
                    <h3 class="font-bold tracking-tight" style="color: var(--color-heading);">Net Cash Flow per Tahun</h3>
                    <p class="text-sm leading-relaxed" style="color: var(--color-muted);">Grafik utama untuk melihat arus kas bersih operasional per tahun.</p>
                </div>
                <span class="px-3 py-1 rounded-full text-xs font-bold w-fit shrink-0" style="background: var(--color-primary-pale); color: var(--color-primary-hover);">NCF ($M)</span>
            </div>
            <div class="chart-wrap h-[280px] sm:h-[360px]"><canvas id="chartNcf"></canvas></div>
        </div>
    </div>

    <div class="app-card rounded-[24px] sm:rounded-[26px] overflow-hidden min-w-0">
        <div class="p-4 sm:p-5 border-b flex flex-col md:flex-row md:items-center md:justify-between gap-2" style="border-color: var(--color-divider);">
            <div>
                <h3 class="font-bold text-lg tracking-tight" style="color: var(--color-heading);">Tabel Perhitungan Tahunan</h3>
            </div>
            <span class="text-sm" style="color: var(--color-muted);">Proyek: <?= e($project['nama_proyek']) ?></span>
        </div>
        <div class="table-scroll">
            <table class="calculation-table w-full text-[13px] sm:text-sm">
                <thead style="background: #FFF8EB; color: #7A5A1E;">
                    <tr>
                        <th class="p-3 text-left">Tahun</th>
                        <th class="p-3 text-right">Produksi</th>
                        <th class="p-3 text-right">Income USD</th>
                        <th class="p-3 text-right">Income Rupiah</th>
                        <th class="p-3 text-right">OPEX</th>
                        <th class="p-3 text-right">Depresiasi</th>
                        <th class="p-3 text-right">Taxable Income</th>
                        <th class="p-3 text-right">Tax</th>
                        <th class="p-3 text-right">NCF</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-t" style="background: #FFFDF8; border-color: var(--color-divider);">
                        <td class="p-3 font-bold" style="color: var(--color-heading);">0</td>
                        <td class="p-3 text-right">-</td>
                        <td class="p-3 text-right">-</td>
                        <td class="p-3 text-right">-</td>
                        <td class="p-3 text-right">-</td>
                        <td class="p-3 text-right">-</td>
                        <td class="p-3 text-right">-</td>
                        <td class="p-3 text-right">-</td>
                        <td class="p-3 text-right font-bold text-[#E46A61]">-<?= format_usd_m($summary['total_investasi']) ?></td>
                    </tr>
                    <?php foreach ($rows as $r): ?>
                    <tr class="border-t hover:bg-[#FFF9EE] transition" style="border-color: var(--color-divider);">
                        <td class="p-3 font-semibold" style="color: var(--color-heading);"><?= e($r['tahun']) ?></td>
                        <td class="p-3 text-right"><?= format_number($r['produksi']) ?> Mbbl</td>
                        <td class="p-3 text-right"><?= format_usd_m($r['income']) ?></td>
                        <td class="p-3 text-right"><?= format_rupiah($r['income'] * 1000000 * $kurs) ?></td>
                        <td class="p-3 text-right"><?= format_usd_m($r['opex']) ?></td>
                        <td class="p-3 text-right"><?= format_usd_m($r['depresiasi']) ?></td>
                        <td class="p-3 text-right"><?= format_usd_m($r['taxable_income']) ?></td>
                        <td class="p-3 text-right"><?= format_usd_m($r['tax']) ?></td>
                        <td class="p-3 text-right font-bold <?= $r['ncf'] >= 0 ? 'text-[#3F8F4D]' : 'text-[#E46A61]' ?>"><?= format_usd_m($r['ncf']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <tr style="background: linear-gradient(135deg, #2F2A24, #7A5A1E); color: #FFFDF8;">
                        <td class="p-4 text-lg sm:text-xl font-extrabold tracking-tight" colspan="8" style="color: #FFFDF8;">Total NCF Setelah Investasi</td>
                        <td class="p-4 text-right text-xl sm:text-2xl font-extrabold" style="color: #FFD27A;"><?= format_usd_m($summary['total_ncf_setelah_investasi']) ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="rounded-[24px] sm:rounded-[28px] overflow-hidden relative" style="background: linear-gradient(135deg, #FFFDF8, #FFF3DA); border: 1px solid var(--color-accent-peach); box-shadow: 0 18px 45px rgba(214,170,94,.16);">
        <div class="absolute -right-12 -top-12 w-40 h-40 rounded-full opacity-60" style="background: var(--color-primary-pale);"></div>
        <div class="relative p-5 sm:p-6 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5">
            <div class="min-w-0">
                <h3 class="font-bold text-xl sm:text-2xl tracking-tight" style="color: var(--color-heading);">Status Proyek: <?= e($summary['status_kelayakan']) ?></h3>
                <p class="mt-2 text-sm sm:text-base leading-relaxed" style="color: var(--color-body);">Total NCF setelah investasi: <strong style="color: #7A5A1E;"><?= format_usd_m($summary['total_ncf_setelah_investasi']) ?></strong></p>
            </div>
            <div class="shrink-0 rounded-3xl px-5 py-4 text-center min-w-[220px]" style="background: <?= $statusBg ?>; color: <?= $statusColor ?>; border: 1px solid rgba(122,90,30,.14);">
                <div class="text-xs font-bold uppercase tracking-wider opacity-80">Keputusan</div>
                <div class="text-lg font-extrabold mt-1 leading-tight"><?= e($summary['status_kelayakan']) ?></div>
                <div class="text-2xl font-extrabold mt-3 leading-tight"><?= format_usd_m($summary['total_ncf_setelah_investasi']) ?></div>
            </div>
        </div>
    </div>
</div>

<script>
const labels = <?= json_encode($labels) ?>;
const produksi = <?= json_encode($produksi) ?>;
const income = <?= json_encode($income) ?>;
const ncf = <?= json_encode($ncf) ?>;

const chartColors = {
    primary: '#F5A623',
    primaryDark: '#D88912',
    yellow: '#F7C948',
    pale: 'rgba(245, 166, 35, 0.14)',
    grid: 'rgba(233, 227, 216, 0.72)',
    text: '#8D857A',
    heading: '#2F2A24'
};

function baseOptions(prefix = '') {
    return {
        responsive: true,
        maintainAspectRatio: false,
        interaction: { mode: 'index', intersect: false },
        plugins: {
            legend: {
                labels: {
                    color: chartColors.text,
                    usePointStyle: true,
                    boxWidth: 8,
                    font: { family: 'Plus Jakarta Sans', weight: '700' }
                }
            },
            tooltip: {
                backgroundColor: '#2F2A24',
                titleColor: '#FFFFFF',
                bodyColor: '#FFFFFF',
                padding: 12,
                cornerRadius: 12,
                callbacks: {
                    label: function(context) {
                        return `${context.dataset.label}: ${prefix}${context.parsed.y.toLocaleString('id-ID')}`;
                    }
                }
            }
        },
        scales: {
            x: {
                grid: { display: false },
                ticks: { color: chartColors.text, maxRotation: 0, autoSkip: true }
            },
            y: {
                grid: { color: chartColors.grid },
                ticks: { color: chartColors.text }
            }
        }
    };
}

new Chart(document.getElementById('chartProduksi'), {
    type: 'line',
    data: {
        labels,
        datasets: [{
            label: 'Produksi (Mbbl)',
            data: produksi,
            borderColor: chartColors.primary,
            backgroundColor: chartColors.pale,
            pointBackgroundColor: chartColors.primary,
            pointBorderColor: '#FFFFFF',
            pointBorderWidth: 2,
            pointRadius: 4,
            tension: .35,
            fill: true
        }]
    },
    options: baseOptions('')
});

new Chart(document.getElementById('chartIncome'), {
    type: 'bar',
    data: {
        labels,
        datasets: [{
            label: 'Income ($M)',
            data: income,
            backgroundColor: 'rgba(245, 166, 35, 0.34)',
            borderColor: chartColors.primary,
            borderWidth: 1,
            borderRadius: 10,
            maxBarThickness: 46
        }]
    },
    options: baseOptions('$ ')
});

new Chart(document.getElementById('chartNcf'), {
    type: 'line',
    data: {
        labels,
        datasets: [{
            label: 'NCF ($M)',
            data: ncf,
            borderColor: chartColors.primaryDark,
            backgroundColor: 'rgba(216, 137, 18, 0.13)',
            pointBackgroundColor: chartColors.primaryDark,
            pointBorderColor: '#FFFFFF',
            pointBorderWidth: 2,
            pointRadius: 4,
            tension: .35,
            fill: true
        }]
    },
    options: baseOptions('$ ')
});
</script>
<?php endif; ?>

<?php include __DIR__ . '/includes/footer.php'; ?>
