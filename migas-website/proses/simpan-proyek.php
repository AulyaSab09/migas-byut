<?php
require_once __DIR__ . '/../config/database.php';
$fields = ['nama_proyek','nama_sumur','lokasi_lapangan','status_proyek','jenis_produksi','cadangan_mbbl','tahun_awal','jangka_waktu','harga_minyak_usd','persentase_pajak','metode_depresiasi','decline_produksi','keterangan','produksi_tahun1','produksi_tahun2','produksi_tahun3','produksi_tahun4','opex_tahun','kenaikan_opex','capital','non_capital','nilai_depresiasi','pajak_penghasilan'];
$data = [];
foreach ($fields as $f) $data[$f] = $_POST[$f] ?? null;
$sql = 'INSERT INTO proyek_sumur (' . implode(',', $fields) . ') VALUES (' . implode(',', array_fill(0, count($fields), '?')) . ')';
$stmt = $pdo->prepare($sql);
$stmt->execute(array_values($data));
header('Location: ../proyek.php?success=1');
exit;
?>
