import Chart from 'chart.js/auto';

const fallback = {
    100: '#fce7f3',
    300: '#f9a8d4',
    400: '#f472b6',
    500: '#ec4899',
    600: '#db2777',
    700: '#be185d',
    800: '#9d174d',
};

function brand(shade) {
    const raw = getComputedStyle(document.documentElement)
        .getPropertyValue(`--brand-${shade}`)
        .trim();
    const channels = raw.split(/\s+/).map(Number);

    if (channels.length !== 3 || channels.some(Number.isNaN)) {
        return fallback[shade];
    }

    return '#' + channels.map((value) => value.toString(16).padStart(2, '0')).join('');
}

const currency = new Intl.NumberFormat('es-CO', {
    style: 'currency',
    currency: 'COP',
    maximumFractionDigits: 0,
});

export function renderCharts() {
    const palette = [600, 300, 800, 400, 700, 500].map(brand);
    const gridColor = brand(100);

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
                        grid: { color: gridColor },
                        ticks: {
                            callback: (value) => chart.money
                                ? currency.format(value)
                                : value,
                        },
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: gridColor },
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