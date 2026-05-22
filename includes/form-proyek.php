<?php
$p = $project ?? [];
function old($p, $key, $default = '') { return $p[$key] ?? $default; }
?>
<div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200">
    <h3 class="font-bold text-lg mb-4">Informasi Awal Proyek</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div><label class="text-sm font-semibold">Nama Proyek</label><input required name="nama_proyek" value="<?= e(old($p,'nama_proyek')) ?>" class="mt-1 w-full px-4 py-3 rounded-xl border border-slate-200"></div>
        <div><label class="text-sm font-semibold">Nama Sumur</label><input required name="nama_sumur" value="<?= e(old($p,'nama_sumur')) ?>" class="mt-1 w-full px-4 py-3 rounded-xl border border-slate-200"></div>
        <div><label class="text-sm font-semibold">Lokasi Lapangan</label><input required name="lokasi_lapangan" value="<?= e(old($p,'lokasi_lapangan')) ?>" class="mt-1 w-full px-4 py-3 rounded-xl border border-slate-200"></div>
        <div><label class="text-sm font-semibold">Status Proyek</label><select name="status_proyek" class="mt-1 w-full px-4 py-3 rounded-xl border border-slate-200"><?php foreach(['Direncanakan','Berjalan','Selesai'] as $s): ?><option <?= old($p,'status_proyek','Direncanakan')===$s?'selected':'' ?>><?= $s ?></option><?php endforeach; ?></select></div>
        <div><label class="text-sm font-semibold">Jenis Produksi</label><select name="jenis_produksi" class="mt-1 w-full px-4 py-3 rounded-xl border border-slate-200"><?php foreach(['Minyak','Gas','Minyak dan Gas'] as $s): ?><option <?= old($p,'jenis_produksi','Minyak')===$s?'selected':'' ?>><?= $s ?></option><?php endforeach; ?></select></div>
        <div><label class="text-sm font-semibold">Cadangan Minyak (Mbbl)</label><input type="number" step="0.01" name="cadangan_mbbl" value="<?= e(old($p,'cadangan_mbbl',4320)) ?>" class="mt-1 w-full px-4 py-3 rounded-xl border border-slate-200"></div>
    </div>
</div>

<div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200">
    <h3 class="font-bold text-lg mb-4">Parameter Perhitungan</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div><label class="text-sm font-semibold">Tahun Awal Proyek</label><input type="number" required name="tahun_awal" value="<?= e(old($p,'tahun_awal',2026)) ?>" class="mt-1 w-full px-4 py-3 rounded-xl border border-slate-200"></div>
        <div><label class="text-sm font-semibold">Jangka Waktu Proyek</label><input type="number" required name="jangka_waktu" value="<?= e(old($p,'jangka_waktu',10)) ?>" class="mt-1 w-full px-4 py-3 rounded-xl border border-slate-200"></div>
        <div><label class="text-sm font-semibold">Harga Minyak per Barrel USD</label><input type="number" step="0.01" required name="harga_minyak_usd" value="<?= e(old($p,'harga_minyak_usd',32)) ?>" class="mt-1 w-full px-4 py-3 rounded-xl border border-slate-200"></div>
        <div><label class="text-sm font-semibold">Persentase Pajak (%)</label><input type="number" step="0.01" name="persentase_pajak" value="<?= e(old($p,'persentase_pajak',51)) ?>" class="mt-1 w-full px-4 py-3 rounded-xl border border-slate-200"></div>
        <div><label class="text-sm font-semibold">Metode Depresiasi</label><select name="metode_depresiasi" class="mt-1 w-full px-4 py-3 rounded-xl border border-slate-200"><option>Garis Lurus / Straight Line</option></select></div>
        <div><label class="text-sm font-semibold">Decline Produksi Mulai Tahun ke-5 (%)</label><input type="number" step="0.01" name="decline_produksi" value="<?= e(old($p,'decline_produksi',3)) ?>" class="mt-1 w-full px-4 py-3 rounded-xl border border-slate-200"></div>
        <div class="md:col-span-2"><label class="text-sm font-semibold">Keterangan Proyek</label><textarea name="keterangan" rows="3" class="mt-1 w-full px-4 py-3 rounded-xl border border-slate-200"><?= e(old($p,'keterangan')) ?></textarea></div>
    </div>
</div>

<div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200">
    <h3 class="font-bold text-lg mb-4">Data Produksi dan OPEX</h3>
    <p class="text-sm text-slate-500 mb-4">Bagian ini diperlukan agar grafik produksi dan perhitungan pendapatan dapat berjalan.</p>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div><label class="text-sm font-semibold">Produksi Tahun 1 (Mbbl)</label><input type="number" step="0.01" name="produksi_tahun1" value="<?= e(old($p,'produksi_tahun1',175)) ?>" class="mt-1 w-full px-4 py-3 rounded-xl border border-slate-200"></div>
        <div><label class="text-sm font-semibold">Produksi Tahun 2 (Mbbl)</label><input type="number" step="0.01" name="produksi_tahun2" value="<?= e(old($p,'produksi_tahun2',201)) ?>" class="mt-1 w-full px-4 py-3 rounded-xl border border-slate-200"></div>
        <div><label class="text-sm font-semibold">Produksi Tahun 3 (Mbbl)</label><input type="number" step="0.01" name="produksi_tahun3" value="<?= e(old($p,'produksi_tahun3',217)) ?>" class="mt-1 w-full px-4 py-3 rounded-xl border border-slate-200"></div>
        <div><label class="text-sm font-semibold">Produksi Tahun 4 (Mbbl)</label><input type="number" step="0.01" name="produksi_tahun4" value="<?= e(old($p,'produksi_tahun4',198)) ?>" class="mt-1 w-full px-4 py-3 rounded-xl border border-slate-200"></div>
        <div><label class="text-sm font-semibold">OPEX per Tahun ($M)</label><input type="number" step="0.01" name="opex_tahun" value="<?= e(old($p,'opex_tahun',180)) ?>" class="mt-1 w-full px-4 py-3 rounded-xl border border-slate-200"></div>
        <div><label class="text-sm font-semibold">Kenaikan OPEX Mulai Tahun ke-4 (%)</label><input type="number" step="0.01" name="kenaikan_opex" value="<?= e(old($p,'kenaikan_opex',2.5)) ?>" class="mt-1 w-full px-4 py-3 rounded-xl border border-slate-200"></div>
    </div>
</div>

<div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200">
    <h3 class="font-bold text-lg mb-4">Data Keuangan</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div><label class="text-sm font-semibold">Capital ($M)</label><input type="number" step="0.01" name="capital" value="<?= e(old($p,'capital',13000)) ?>" class="mt-1 w-full px-4 py-3 rounded-xl border border-slate-200"></div>
        <div><label class="text-sm font-semibold">Non-Capital ($M)</label><input type="number" step="0.01" name="non_capital" value="<?= e(old($p,'non_capital',8000)) ?>" class="mt-1 w-full px-4 py-3 rounded-xl border border-slate-200"></div>
        <div><label class="text-sm font-semibold">Nilai Depresiasi ($M)</label><input type="number" step="0.01" name="nilai_depresiasi" value="<?= e(old($p,'nilai_depresiasi',0)) ?>" class="mt-1 w-full px-4 py-3 rounded-xl border border-slate-200"><p class="text-xs text-slate-500 mt-1">Kosongkan atau isi 0 agar dihitung otomatis dengan metode garis lurus.</p></div>
        <div><label class="text-sm font-semibold">Tarif Pajak Penghasilan (%)</label><input type="number" step="0.01" name="pajak_penghasilan" value="<?= e(old($p,'pajak_penghasilan',51)) ?>" class="mt-1 w-full px-4 py-3 rounded-xl border border-slate-200"></div>
    </div>
</div>

<div class="flex flex-wrap gap-3">
    <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded-xl font-semibold"><?= isset($project) ? 'Simpan Perubahan' : 'Simpan Proyek' ?></button>
    <button type="reset" class="bg-slate-200 text-slate-700 px-6 py-3 rounded-xl font-semibold">Reset</button>
    <a href="proyek.php" class="bg-white border border-slate-200 px-6 py-3 rounded-xl font-semibold">Batal</a>
</div>
