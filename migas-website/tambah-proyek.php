<?php
require_once __DIR__ . '/includes/functions.php';
$pageTitle = 'Tambah Proyek';
$activePage = 'proyek';
include __DIR__ . '/includes/header.php';
?>
<form action="proses/simpan-proyek.php" method="POST" class="space-y-6 max-w-6xl">
    <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200">
        <h2 class="text-2xl font-bold">Tambah Proyek Sumur</h2>
        <p class="text-slate-500">Isi data proyek, parameter perhitungan, produksi, dan keuangan.</p>
    </div>
    <?php include __DIR__ . '/includes/form-proyek.php'; ?>
</form>
<?php include __DIR__ . '/includes/footer.php'; ?>
