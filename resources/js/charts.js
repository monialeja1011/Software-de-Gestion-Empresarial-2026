import Chart from 'chart.js/auto';

const palette = [
    '#db2777',
    '#f472b6',
    '#be185d',
    '#fb7185',
    '#f9a8d4',
    '#9d174d',
];

const currency = new Intl.NumberFormat('es-CO', {
    style: 'currency',
    currency: 'COP',
    maximumFractionDigits: 0,
});

export function renderCharts() {
    document.querySelectorAll('[data-chart]').forEach((canvas) => {
        const chart = JSON.parse(canvas.dataset.chart);
        const datasets = chart.datasets.map((dataset, index) => {
            const color = palette[index % palette.length];

            return {
                ...dataset,
                backgroundColor: chart.type === 'doughnut'
                    ? palette
                    : `${color}B8`,
                borderColor: chart.type === 'doughnut' ? '#ffffff' : color,
                borderWidth: chart.type === 'doughnut' ? 2 : 1,
                borderRadius: chart.type === 'bar' ? 8 : 0,
                tension: chart.type === 'line' ? 0.35 : 0,
                fill: chart.type === 'line',
            };
        });

        new Chart(canvas, {
            type: chart.type,
            data: {
                labels: chart.labels,
                datasets,
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: chart.horizontal ? 'y' : 'x',
                plugins: {
                    legend: {
                        display: chart.type !== 'line' || datasets.length > 1,
                        position: 'bottom',
                        labels: { usePointStyle: true, padding: 18 },
                    },
                    tooltip: {
                        callbacks: {
                            label(context) {
                                const value = context.parsed.y ?? context.parsed;
                                return `${context.dataset.label}: ${
                                    chart.money ? currency.format(value) : value
                                }`;
                            },
                        },
                    },
                },
                scales: chart.type === 'doughnut' ? {} : {
                    x: {
                        beginAtZero: true,
                        grid: { color: '#fce7f3' },
                        ticks: {
                            callback: (value) => chart.money
                                ? currency.format(value)
                                : value,
                        },
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: '#fce7f3' },
                        ticks: {
                            callback: (value) => chart.money
                                ? currency.format(value)
                                : value,
                        },
                    },
                },
            },
        });
    });
}
