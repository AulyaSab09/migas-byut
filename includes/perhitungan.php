<?php
function hitung_ncf(array $project): array {
    $jangkaWaktu = max((int)($project['jangka_waktu'] ?? 10), 1);
    $harga = (float)($project['harga_minyak_usd'] ?? 0);
    $capital = (float)($project['capital'] ?? 0);
    $nonCapital = (float)($project['non_capital'] ?? 0);
    $totalInvestasi = $capital + $nonCapital;

    $taxRate = (float)($project['pajak_penghasilan'] ?? 0);
    if ($taxRate <= 0) $taxRate = (float)($project['persentase_pajak'] ?? 0);

    $opexAwal = (float)($project['opex_tahun'] ?? 0);
    $kenaikanOpex = (float)($project['kenaikan_opex'] ?? 0);
    $decline = (float)($project['decline_produksi'] ?? 0);

    // Jika nilai depresiasi tidak diisi, hitung otomatis dengan metode garis lurus.
    // Mengikuti contoh Excel: Depresiasi = total investasi / jangka waktu.
    $depresiasi = (float)($project['nilai_depresiasi'] ?? 0);
    if ($depresiasi <= 0) $depresiasi = $totalInvestasi / $jangkaWaktu;

    $produksiAwal = [
        1 => (float)($project['produksi_tahun1'] ?? 0),
        2 => (float)($project['produksi_tahun2'] ?? 0),
        3 => (float)($project['produksi_tahun3'] ?? 0),
        4 => (float)($project['produksi_tahun4'] ?? 0),
    ];

    $rows = [];
    $totalProduksi = 0;
    $totalIncome = 0;
    $totalOpex = 0;
    $totalTax = 0;
    $totalNcfOperasi = 0;
    $produksiSebelumnya = 0;
    $opexSebelumnya = $opexAwal;

    for ($tahunKe = 1; $tahunKe <= $jangkaWaktu; $tahunKe++) {
        if ($tahunKe <= 4) {
            $produksi = $produksiAwal[$tahunKe] ?? 0;
        } else {
            $produksi = $produksiSebelumnya * (1 - ($decline / 100));
        }
        $produksiSebelumnya = $produksi;

        if ($tahunKe <= 3) {
            $opex = $opexAwal;
        } else {
            $opex = $opexSebelumnya * (1 + ($kenaikanOpex / 100));
        }
        $opexSebelumnya = $opex;

        $income = $produksi * $harga;
        $taxableIncome = $income - $opex - $depresiasi;
        $tax = max($taxableIncome, 0) * ($taxRate / 100);

        // Rumus ini mengikuti spreadsheet contoh:
        // NCF Undiscounted = Taxable Income - Tax.
        $ncf = $taxableIncome - $tax;

        $rows[] = [
            'tahun_ke' => $tahunKe,
            'tahun' => ((int)$project['tahun_awal'] + $tahunKe - 1),
            'produksi' => $produksi,
            'income' => $income,
            'capital' => 0,
            'non_capital' => 0,
            'opex' => $opex,
            'depresiasi' => $depresiasi,
            'taxable_income' => $taxableIncome,
            'tax' => $tax,
            'ncf' => $ncf,
        ];

        $totalProduksi += $produksi;
        $totalIncome += $income;
        $totalOpex += $opex;
        $totalTax += $tax;
        $totalNcfOperasi += $ncf;
    }

    $totalNcfSetelahInvestasi = $totalNcfOperasi - $totalInvestasi;
    $sisaCadangan = max(((float)($project['cadangan_mbbl'] ?? 0)) - $totalProduksi, 0);

    return [
        'rows' => $rows,
        'summary' => [
            'total_produksi' => $totalProduksi,
            'total_income' => $totalIncome,
            'total_opex' => $totalOpex,
            'total_tax' => $totalTax,
            'total_investasi' => $totalInvestasi,
            'total_ncf_operasi' => $totalNcfOperasi,
            'total_ncf_setelah_investasi' => $totalNcfSetelahInvestasi,
            'sisa_cadangan' => $sisaCadangan,
            'depresiasi' => $depresiasi,
            'status_kelayakan' => $totalNcfSetelahInvestasi >= 0 ? 'Berpotensi Menguntungkan' : 'Perlu Dikaji Kembali',
        ]
    ];
}
?>
