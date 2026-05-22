<?php
function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function format_number($value, $decimals = 2) {
    return number_format((float)$value, $decimals, ',', '.');
}

function format_usd_m($value) {
    return '$ ' . number_format((float)$value, 2, '.', ',') . ' M';
}

function format_rupiah($value) {
    return 'Rp ' . number_format((float)$value, 0, ',', '.');
}

function status_badge($status) {
    $class = 'bg-slate-100 text-slate-700';
    if ($status === 'Berjalan') $class = 'bg-green-100 text-green-700';
    if ($status === 'Direncanakan') $class = 'bg-blue-100 text-blue-700';
    if ($status === 'Selesai') $class = 'bg-orange-100 text-orange-700';
    return '<span class="px-3 py-1 rounded-full text-xs font-semibold '.$class.'">'.e($status).'</span>';
}

function get_usd_to_idr_rate() {
    // Konsep API kurs: mencoba membaca kurs USD-IDR dari API publik.
    // Jika gagal karena internet/server tidak mengizinkan, sistem memakai nilai fallback.
    $fallback = 15700;
    $cacheFile = __DIR__ . '/../assets/js/kurs-cache.json';
    $cacheMaxAge = 3600; // 1 jam

    if (file_exists($cacheFile) && (time() - filemtime($cacheFile) < $cacheMaxAge)) {
        $cached = json_decode(file_get_contents($cacheFile), true);
        if (!empty($cached['rate'])) return (float)$cached['rate'];
    }

    $url = 'https://open.er-api.com/v6/latest/USD';
    $json = @file_get_contents($url);
    if ($json !== false) {
        $data = json_decode($json, true);
        if (isset($data['rates']['IDR'])) {
            $rate = (float)$data['rates']['IDR'];
            @file_put_contents($cacheFile, json_encode(['rate' => $rate, 'updated_at' => date('c')]));
            return $rate;
        }
    }
    return $fallback;
}
?>
