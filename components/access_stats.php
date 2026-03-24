<?php
require_once __DIR__ . '/../includes/functions.php';

$total_today = db_count("SELECT COUNT(ip_address) FROM access_logs WHERE DATE(created_at) = CURDATE()");
$total_7days = db_count("SELECT COUNT(ip_address) FROM access_logs WHERE created_at >= NOW() - INTERVAL 7 DAY");
$total_all = db_count("SELECT COUNT(ip_address) FROM access_logs");

function formatNumber($number)
{
    if ($number >= 1000000000)
        return round($number / 1000000000, 1) . 'B';
    if ($number >= 1000000)
        return round($number / 1000000, 1) . 'M';
    if ($number >= 1000)
        return round($number / 1000, 1) . 'K';
    return $number;
}
?>

<!-- Website access statistics -->
<aside class="flex flex-col items-center justify-center mt-4">
    <div class="w-full grid grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 items-stretch">
        <!-- Today  -->
        <div
            class="col-span-2 lg:col-span-1 h-full flex flex-col justify-center content-center w-full bg-sky-400 text-white rounded-[20px] shadow-md px-6 py-6 text-center">
            <div id="today" class="text-3xl sm:text-5xl md:text-5xl font-bold mb-2">
                <?= formatNumber($total_today) ?>
            </div>
            <div class="text-xl sm:text-2xl md:text-2xl">จำนวนการใช้งานวันนี้</div>
        </div>

        <!-- Last 7 days -->
        <div
            class="h-full flex flex-col justify-center content-center w-full bg-cyan-50 rounded-[20px] shadow-md px-6 py-6 text-center">
            <div id="last-seven-day" class="text-3xl sm:text-5xl md:text-5xl font-bold mb-2">
                <?= formatNumber($total_7days) ?>
            </div>
            <div class="text-xl sm:text-2xl md:text-2xl">จำนวนการใช้งานย้อนหลัง 7 วัน</div>
        </div>

        <!-- Accumulated -->
        <div
            class="h-full flex flex-col justify-center content-center w-full bg-cyan-50 rounded-[20px] shadow-md px-6 py-6 text-center">
            <div id="totalAll" class="text-3xl sm:text-5xl md:text-5xl font-bold mb-2">
                <?= formatNumber($total_all) ?>
            </div>
            <div class="text-xl sm:text-2xl md:text-2xl">จำนวนการใช้งานสะสม</div>
        </div>
    </div>
</aside>