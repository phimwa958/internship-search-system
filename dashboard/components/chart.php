<?php
require_once __DIR__ . '/../../includes/functions.php';

try {
    $rows = db_fetch_all("
        SELECT DATE(created_at) AS visit_date, COUNT(*) AS total
        FROM access_logs
        WHERE created_at >= CURDATE() - INTERVAL 6 DAY
        GROUP BY DATE(created_at)
        ORDER BY DATE(created_at) ASC
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

    $js_dates = json_encode($dates);
    $js_values = json_encode($values);
} catch (Exception $e) {
    $js_dates = json_encode([]);
    $js_values = json_encode([]);
}
?>

<!-- Chart Section -->
<section class="flex flex-col items-center gap-4 sm:gap-4 lg:gap-6 w-full">
    <h2 class="text-2xl sm:text-3xl md:text-3xl lg:text-4xl font-semibold text-center">
        สถิติผู้ใช้งาน (คน)
    </h2>

    <div class="relative w-full h-[300px] max-w-[820px]">
        <canvas id="chart" class="w-full h-full"></canvas>
    </div>

    <script src="../public/js/chart.js"></script>
    <script type="module" defer>
        const values = <?= $js_values ?>;
        const dates = <?= $js_dates ?>;

        const barChart = document.getElementById('chart').getContext('2d');

        new Chart(barChart, {
            type: 'bar',
            data: {
                labels: dates,
                datasets: [{
                    label: 'จำนวนเข้าชม (คน)',
                    data: values,
                    backgroundColor: values.map((_, i) =>
                        i === values.length - 1 ?
                            'rgba(251, 191, 36, 0.9)' : // ไฮไลต์วันล่าสุด
                            'rgba(14, 165, 233, 0.6)' // วันอื่น
                    ),
                    borderColor: 'rgba(14, 165, 233, 1)',
                    borderWidth: 0,
                    borderRadius: 3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0,0,0,0.8)',
                        titleFont: {
                            size: 14
                        },
                        bodyFont: {
                            size: 13
                        },
                        padding: 10
                    }
                },
                scales: {
                    x: {
                        ticks: {
                            font: {
                                size: 14
                            },
                            maxRotation: 60,
                            minRotation: 45
                        },
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            font: {
                                size: 13
                            }
                        },
                        grid: {
                            color: 'rgba(0,0,0,0.05)'
                        }
                    }
                }
            }
        });
    </script>
</section>