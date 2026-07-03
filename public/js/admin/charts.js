
    // ----- CHARTS (Chart.js) -----
    function renderCharts(trends = null, distribution = null) {
        const ctxLine = document.getElementById('lineChart');
        if (!ctxLine) return;
        
        let labels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'];
        let dataVals = [2.1, 3.4, 2.8, 5.2, 4.5, 5.5]; // in Crores or standard values
        
        if (trends && Array.isArray(trends) && trends.length > 0) {
            labels = trends.map(t => t.month);
            dataVals = trends.map(t => t.total);
        }

        const isDarkTheme = document.body.classList.contains('dark-theme');
        const gridColor = isDarkTheme ? 'rgba(255, 255, 255, 0.08)' : 'rgba(0, 0, 0, 0.08)';
        const textColor = isDarkTheme ? '#94a3b8' : '#64748b';
        
        const canvasCtx = ctxLine.getContext('2d');
        const gradient = canvasCtx.createLinearGradient(0, 0, 0, 250);
        if (isDarkTheme) {
            gradient.addColorStop(0, 'rgba(255, 122, 0, 0.25)'); // Orange gradient
            gradient.addColorStop(1, 'rgba(255, 122, 0, 0.0)');
        } else {
            gradient.addColorStop(0, 'rgba(255, 122, 0, 0.15)'); // Orange gradient
            gradient.addColorStop(1, 'rgba(255, 122, 0, 0.0)');
        }
        
        const strokeColor = '#FF7A00';

        if (lineChartInstance) lineChartInstance.destroy();
        lineChartInstance = new Chart(canvasCtx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    { 
                        label: 'Collections (in Lakhs)', 
                        data: dataVals, 
                        borderColor: strokeColor,
                        backgroundColor: gradient,
                        borderWidth: 2,
                        tension: 0.4,
                        fill: true,
                        pointRadius: 0,
                        pointHoverRadius: 6,
                        pointBackgroundColor: strokeColor,
                        pointHoverBackgroundColor: strokeColor,
                        pointHoverBorderColor: '#ffffff',
                        pointHoverBorderWidth: 2
                    }
                ]
            },
            options: { 
                responsive: true, 
                maintainAspectRatio: false, 
                plugins: { legend: { display: false } }, 
                scales: { 
                    y: { 
                        beginAtZero: true, 
                        grid: { borderDash: [5, 5], color: gridColor },
                        ticks: { color: textColor }
                    }, 
                    x: { 
                        grid: { display: false },
                        ticks: { color: textColor }
                    } 
                } 
            }
        });

        const ctxDoughnut = document.getElementById('doughnutChart');
        if (!ctxDoughnut) return;

        let doughnutData = [70, 20, 10];
        if (distribution) {
            doughnutData = [distribution.urban || 70, distribution.rural || 20, distribution.unmapped || 10];
        }

        if (doughnutChartInstance) doughnutChartInstance.destroy();
        doughnutChartInstance = new Chart(ctxDoughnut.getContext('2d'), {
            type: 'doughnut',
            data: { 
                labels: ['Urban Centres', 'Rural Clusters', 'Unmapped'], 
                datasets: [{ 
                    data: doughnutData, 
                    backgroundColor: ['#004d40', '#10b981', '#cbd5e1'], 
                    borderWidth: 0, 
                    hoverOffset: 4 
                }] 
            },
            options: { 
                responsive: true, 
                maintainAspectRatio: false, 
                cutout: '75%', 
                plugins: { legend: { display: false } } 
            }
        });
    }