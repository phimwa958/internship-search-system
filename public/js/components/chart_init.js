document.addEventListener('DOMContentLoaded', () => {
    const chartContainer = document.querySelector('.relative.w-full.h-\\[300px\\]');
    if (!chartContainer) return;

    const values = JSON.parse(chartContainer.getAttribute('data-values') || '[]');
    const dates = JSON.parse(chartContainer.getAttribute('data-dates') || '[]');
    const canvas = document.getElementById('chart');

    if (!canvas || !values.length) return;

    const ctx = canvas.getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: dates,
            datasets: [{
                label: 'จำนวนเข้าชม (คน)',
                data: values,
                backgroundColor: values.map((_, i) =>
                    i === values.length - 1 ?
                        'rgba(251, 191, 36, 0.9)' :
                        'rgba(14, 165, 233, 0.6)'
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
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(0,0,0,0.8)',
                    titleFont: { size: 14 },
                    bodyFont: { size: 13 },
                    padding: 10
                }
            },
            scales: {
                x: {
                    ticks: {
                        font: { size: 14 },
                        maxRotation: 60,
                        minRotation: 45
                    },
                    grid: { display: false }
                },
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1,
                        font: { size: 13 }
                    },
                    grid: { color: 'rgba(0,0,0,0.05)' }
                }
            }
        }
    });
});
