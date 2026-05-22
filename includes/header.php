<?php
if (!isset($pageTitle)) $pageTitle = 'Website Perhitungan Investasi Proyek Sumur Migas';
if (!isset($activePage)) $activePage = 'dashboard';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-slate-100 text-slate-800">
<div class="min-h-screen flex">
    <aside class="w-72 bg-slate-950 text-white hidden md:flex md:flex-col">
        <div class="px-6 py-6 border-b border-slate-800">
            <div class="text-xl font-bold leading-tight">Migas Investasi</div>
            <div class="text-xs text-slate-400 mt-1">Perhitungan NCF Sumur Migas</div>
        </div>
        <nav class="flex-1 p-4 space-y-2">
            <a href="index.php" class="block px-4 py-3 rounded-xl <?= $activePage === 'dashboard' ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800' ?>">Dashboard</a>
            <a href="proyek.php" class="block px-4 py-3 rounded-xl <?= $activePage === 'proyek' ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800' ?>">Proyek Sumur</a>
        </nav>
        <div class="p-4 text-xs text-slate-500 border-t border-slate-800">© 2026 Sistem Informasi Migas</div>
    </aside>

    <main class="flex-1 min-w-0">
        <header class="bg-white border-b border-slate-200 px-4 md:px-8 py-4 sticky top-0 z-10">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                <div>
                    <h1 class="text-xl md:text-2xl font-bold text-slate-900"><?= e($pageTitle) ?></h1>
                    <p class="text-sm text-slate-500">Sistem pengelolaan proyek sumur, produksi, pendapatan, pajak, dan net cash flow.</p>
                </div>
                <div class="flex items-center gap-3">
                    <input type="text" placeholder="Cari proyek..." class="hidden lg:block w-64 px-4 py-2 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center">🔔</div>
                    <div class="w-10 h-10 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold">U</div>
                </div>
            </div>
        </header>
        <section class="p-4 md:p-8">
