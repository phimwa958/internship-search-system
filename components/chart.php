<?php
require_once __DIR__ . '/../includes/functions.php';

$rows = db_fetch_all("
    SELECT DATE(created_at) AS visit_date, COUNT(*) AS total
    FROM access_logs
    WHERE created_at >= CURDATE() - INTERVAL 6 DAY
    GROUP BY DATE(created_at)
    ORDER BY DATE(visit_date) ASC
");

$dates = [];
$values = [];
$thai_months = ['ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'];

for ($i = 6; $i >= 0; $i--) {
    $day = date('Y-m-d', strtotime("-$i days"));
    $ts = strtotime($day);
    $dates[] = date('j', $ts) . ' ' . $thai_months[date('n', $ts) - 1] . ' ' . date('y', $ts);

    $found = false;
    foreach ($rows as $row) {
        if ($row['visit_date'] == $day) {
            $values[] = (int) $row['total'];
            $found = true;
            break;
        }
    }
    if (!$found)
        $values[] = 0;
}
?>

<!-- Chart Section -->
<section class="flex flex-col items-center gap-4 sm:gap-4 lg:gap-6 w-full">
    <h2 class="text-2xl sm:text-3xl md:text-3xl lg:text-4xl font-semibold text-center">
        สถิติผู้ใช้งาน (คน)
    </h2>

    <div class="relative w-full h-[300px] max-w-[820px]" data-values="<?= htmlspecialchars(json_encode($values)) ?>"
        data-dates="<?= htmlspecialchars(json_encode($dates)) ?>">
        <canvas id="chart" class="w-full h-full"></canvas>
    </div>

    <script src="./public/js/chart.js"></script>
    <script src="./public/js/components/chart_init.js" defer></script>
</section>