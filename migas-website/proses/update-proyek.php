<?php
require_once __DIR__ . '/../config/database.php';
$id = (int)($_POST['id'] ?? 0);
$fields = ['nama_proyek','nama_sumur','lokasi_lapangan','status_proyek','jenis_produksi','cadangan_mbbl','tahun_awal','jangka_waktu','harga_minyak_usd','persentase_pajak','metode_depresiasi','decline_produksi','keterangan','produksi_tahun1','produksi_tahun2','produksi_tahun3','produksi_tahun4','opex_tahun','kenaikan_opex','capital','non_capital','nilai_depresiasi','pajak_penghasilan'];
$sets = array_map(fn($f) => "$f = ?", $fields);
$data = [];
foreach ($fields as $f) $data[] = $_POST[$f] ?? null;
$data[] = $id;
$sql = 'UPDATE proyek_sumur SET ' . implode(',', $sets) . ', updated_at = CURRENT_TIMESTAMP WHERE id = ?';
$stmt = $pdo->prepare($sql);
$stmt->execute($data);
header('Location: ../proyek.php?success=1');
exit;
?>
