
    // ----- NEW FIGMA VIEWS BINDINGS & LOADERS -----
    let collectionsTrendChartInstance = null;
    let collectionsMethodsChartInstance = null;

    // Load KYC page user list dropdown
    async function loadKycData() {
        try {
            const res = await fetch('/api/admin/members', { headers: getHeaders() });
            const data = await res.json();
            const select = document.getElementById('kyc_user_select');
            if (!select) return;

            select.innerHTML = '<option value="">-- Choose Member / Agent --</option>';
            const membersList = Array.isArray(data) ? data : (data.data ? data.data : []);
            if (membersList.length > 0) {
                membersList.forEach(m => {
                    const roleText = m.roles && m.roles.some(r => r.name === 'agent') ? 'Agent' : 'Member';
                    select.innerHTML += `<option value="${m.id}">${m.name} (${roleText} - #${m.id})</option>`;
                });
            } else {
                select.innerHTML = '<option value="">No members/agents found</option>';
            }
        } catch (e) {
            console.error(e);
        }
    }

    // Load Collections Overview data & charts
    async function loadCollectionsOverviewData() {
        try {
            // Fetch stats
            const res = await fetch('/api/admin/dashboard', { headers: getHeaders() });
            const payload = await res.json();
            const stats = payload.data;

            // Load Metrics
            const todayColl = document.getElementById('coll-metric-collected');
            if (todayColl) todayColl.textContent = '₹' + (stats.today_collection_formatted || '1.2Cr');
            const actAgents = document.getElementById('coll-metric-agents');
            if (actAgents) actAgents.textContent = stats.active_agents_count || 62;
            const successRate = document.getElementById('coll-metric-success');
            if (successRate) successRate.textContent = (stats.collection_success_rate || 94.5) + '%';
            
            // Populate recent collections table
            const collRes = await fetch('/api/admin/agents/collections', { headers: getHeaders() });
            const colData = await collRes.json();
            const collTbody = document.getElementById('collections-table-tbody');
            if (collTbody && Array.isArray(colData)) {
                collTbody.innerHTML = '';
                const displayData = colData.slice(0, 10);
                document.getElementById('collections-table-count').textContent = `Showing ${displayData.length} of ${colData.length} collections total`;
                
                if (displayData.length > 0) {
                    displayData.forEach(c => {
                        const agentName = c.agent ? c.agent.name : 'Agent';
                        const memberName = c.member ? c.member.name : 'Member';
                        const method = c.details || 'UPI';
                        const time = new Date(c.collected_at || c.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
                        
                        let actionHtml = `<span class="badge badge-success">Success</span>`;
                        if (c.status === 'pending') {
                            actionHtml = `
                                <div style="display:flex; gap:6px;">
                                    <button class="btn-primary" style="padding:4px 8px; font-size:0.75rem; background-color: var(--accent);" onclick="approveCollectionFromOverview(${c.id})">Approve</button>
                                    <button class="btn-secondary text-danger" style="padding:4px 8px; font-size:0.75rem; background:rgba(239,68,68,0.1); border:1px solid rgba(239,68,68,0.2);" onclick="rejectCollectionFromOverview(${c.id})">Reject</button>
                                </div>
                            `;
                        } else if (c.status === 'rejected') {
                            actionHtml = `<span class="badge badge-failed">Rejected</span>`;
                        }

                        collTbody.innerHTML += `
                            <tr>
                                <td>
                                    <div class="user-avatar-group">
                                        <img src="https://ui-avatars.com/api/?name=${encodeURIComponent(agentName)}&background=004d40&color=fff" alt="Avatar">
                                        <span class="user-detail-name">${agentName}</span>
                                    </div>
                                </td>
                                <td>${memberName}</td>
                                <td class="font-semibold">₹${c.amount_collected}</td>
                                <td><span class="badge badge-neutral">${method}</span></td>
                                <td>${time}</td>
                                <td>${actionHtml}</td>
                            </tr>
                        `;
                    });
                } else {
                    collTbody.innerHTML = '<tr><td colspan="6" class="text-center">No recent collection records found.</td></tr>';
                }
            }

            renderCollectionsCharts(stats.weekly_trends, stats.collection_methods);
        } catch (e) {
            console.error(e);
        }
    }

    // Render collections overview charts
    function renderCollectionsCharts(weeklyTrends = null, methods = null) {
        const ctxTrend = document.getElementById('collectionsTrendChart');
        if (ctxTrend) {
            let labels = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
            let dataVals = [24, 38, 30, 48, 56, 42, 60]; // in Lakhs
            
            if (weeklyTrends && Array.isArray(weeklyTrends) && weeklyTrends.length > 0) {
                labels = weeklyTrends.map(t => t.day);
                dataVals = weeklyTrends.map(t => t.total);
            }

            const isDarkTheme = document.body.classList.contains('dark-theme');
            const gridColor = isDarkTheme ? 'rgba(255, 255, 255, 0.08)' : 'rgba(0, 0, 0, 0.08)';
            const textColor = isDarkTheme ? '#94a3b8' : '#64748b';

            const canvasCtx = ctxTrend.getContext('2d');
            const gradient = canvasCtx.createLinearGradient(0, 0, 0, 250);
            if (isDarkTheme) {
                gradient.addColorStop(0, 'rgba(255, 122, 0, 0.25)'); // Orange gradient
                gradient.addColorStop(1, 'rgba(255, 122, 0, 0.0)');
            } else {
                gradient.addColorStop(0, 'rgba(255, 122, 0, 0.15)'); // Orange gradient
                gradient.addColorStop(1, 'rgba(255, 122, 0, 0.0)');
            }
            
            const strokeColor = '#FF7A00';

            if (collectionsTrendChartInstance) collectionsTrendChartInstance.destroy();
            collectionsTrendChartInstance = new Chart(canvasCtx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Collections Today',
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
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { 
                            beginAtZero: true, 
                            grid: { borderDash: [5,5], color: gridColor },
                            ticks: { color: textColor }
                        },
                        x: { 
                            grid: { display: false },
                            ticks: { color: textColor }
                        }
                    }
                }
            });
        }

        const ctxMethod = document.getElementById('collectionsMethodsChart');
        if (ctxMethod) {
            let doughnutData = [70, 30]; // 70% UPI/Wallet, 30% Cash
            if (methods) {
                doughnutData = [methods.digital || 70, methods.cash || 30];
            }

            if (collectionsMethodsChartInstance) collectionsMethodsChartInstance.destroy();
            collectionsMethodsChartInstance = new Chart(ctxMethod.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: ['UPI & Wallet', 'Cash'],
                    datasets: [{
                        data: doughnutData,
                        backgroundColor: ['#004d40', '#0ea5e9'],
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
    }

    // Load settings page mock
    function loadSettingsData() {
        console.log("Settings view loaded");
    }

    // Load agents database list
    async function loadAgentsList() {
        try {
            const res = await fetch('/api/admin/members', { headers: getHeaders() });
            const data = await res.json();
            const tbody = document.getElementById('agents-table-tbody');
            if (!tbody) return;

            tbody.innerHTML = '';
            const membersList = Array.isArray(data) ? data : (data.data ? data.data : []);
            const agents = membersList.filter(m => m.roles && m.roles.some(r => r.name === 'agent'));

            if (agents.length > 0) {
                agents.forEach(a => {
                    const todayCollection = a.today_collection || '₹0';
                    const targetProgress = a.target_progress || 0;
                    const statusText = a.status || 'Active';
                    let badgeClass = 'badge-success';
                    if (statusText.toLowerCase() === 'offline') badgeClass = 'badge-neutral';
                    if (statusText.toLowerCase() === 'on leave') badgeClass = 'badge-failed';

                    tbody.innerHTML += `
                        <tr>
                            <td>
                                <div class="user-avatar-group">
                                    <img src="https://ui-avatars.com/api/?name=${encodeURIComponent(a.name)}&background=004d40&color=fff" alt="Avatar">
                                    <div class="flex-column">
                                        <span class="user-detail-name">${a.name}</span>
                                        <span class="user-detail-sub">${a.email}</span>
                                    </div>
                                </div>
                            </td>
                            <td>#AGT-${a.id}</td>
                            <td>${a.address || 'All Regions'}</td>
                            <td class="font-semibold">${todayCollection}</td>
                            <td>
                                <div class="progress-container">
                                    <div class="progress-bar-bg">
                                        <div class="progress-bar-fill" style="width: ${targetProgress}%;"></div>
                                    </div>
                                    <span class="text-sm font-semibold">${targetProgress}%</span>
                                </div>
                            </td>
                            <td><span class="badge ${badgeClass}">${statusText}</span></td>
                            <td>
                                <button class="btn-secondary" style="padding: 4px 8px; font-size: 0.8rem;" onclick="openAgentManageModal(${a.id})">Manage</button>
                            </td>
                        </tr>
                    `;
                });
            } else {
                tbody.innerHTML = '<tr><td colspan="7" class="text-center">No agents registered in system.</td></tr>';
            }
        } catch (e) {
            console.error(e);
        }
    }

    // Approve & Reject from overview
    window.approveCollectionFromOverview = async function(id) {
        await window.approveCollection(id);
        loadCollectionsOverviewData();
    };

    window.rejectCollectionFromOverview = async function(id) {
        if(!confirm("Are you sure you want to reject this collection voucher?")) return;
        try {
            const res = await fetch(`/api/admin/agents/collections/${id}/reject`, {
                method: 'POST',
                headers: getHeaders()
            });
            const data = await res.json();
            alert(data.message || 'Collection voucher rejected.');
            loadCollectionsOverviewData();
        } catch (e) {
            alert('Failed: ' + e.message);
        }
    };

    // Override loadPayoutsData to support both layouts
    const originalLoadPayoutsData = loadPayoutsData;
    loadPayoutsData = async function() {
        try {
            await originalLoadPayoutsData();
            // Duplicate to payments-payouts-tbody if exists
            const origBody = document.getElementById('payouts-tbody');
            const destBody = document.getElementById('payments-payouts-tbody');
            if (origBody && destBody) {
                destBody.innerHTML = origBody.innerHTML;
            }
        } catch (e) {
            console.error(e);
        }
    };

