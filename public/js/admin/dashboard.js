
    // ----- DATA FETCHING -----
    async function loadDashboardData() {
        try {
            const res = await fetch('/api/admin/dashboard', { headers: getHeaders() });
            if(res.status === 401) { logoutBtn.click(); return; }
            const payload = await res.json();
            if(!payload.data) return;
            const stats = payload.data;
            
            const heroDate = document.getElementById('hero-banner-date-sub');
            if (heroDate) {
                const opt = { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' };
                heroDate.textContent = 'Administrator • ' + new Date().toLocaleDateString('en-IN', opt);
            }

            // Legacy elements
            const oldCollection = document.getElementById('stat-collection');
            if (oldCollection) oldCollection.textContent = '₹' + (stats.today_collection || 0);
            const oldMembers = document.getElementById('stat-members');
            if (oldMembers) oldMembers.textContent = stats.total_members || 0;
            const oldPaid = document.getElementById('stat-paid-members');
            if (oldPaid) oldPaid.textContent = stats.paid_members_count || 0;
            const oldDue = document.getElementById('stat-due-amount');
            if (oldDue) oldDue.textContent = '₹' + (stats.total_due_amount || 0);

            // New Figma-matching elements
            const dashDisbursements = document.getElementById('dash-disbursements');
            if (dashDisbursements) dashDisbursements.textContent = '₹' + (stats.total_disbursements_formatted || '4.5Cr');
            const dashActiveMembers = document.getElementById('dash-active-members');
            if (dashActiveMembers) dashActiveMembers.textContent = stats.active_members_count || stats.total_members || 0;
            const dashTotalCollections = document.getElementById('dash-total-collections');
            if (dashTotalCollections) dashTotalCollections.textContent = '₹' + (stats.total_collections_formatted || '1.2Cr');
            const dashKycCompliance = document.getElementById('dash-kyc-compliance');
            if (dashKycCompliance) dashKycCompliance.textContent = (stats.kyc_compliance_rate || 94) + '%';

            // Populate recent transactions on dashboard
            const dashTxTbody = document.getElementById('dashboard-transactions-tbody');
            if (dashTxTbody && stats.recent_transactions && stats.recent_transactions.length > 0) {
                dashTxTbody.innerHTML = '';
                stats.recent_transactions.forEach(tx => {
                    let badgeClass = 'badge-success';
                    if (tx.status.toLowerCase() === 'pending') badgeClass = 'badge-pending';
                    if (tx.status.toLowerCase() === 'failed') badgeClass = 'badge-failed';
                    
                    dashTxTbody.innerHTML += `
                        <tr>
                            <td>
                                <div class="user-avatar-group">
                                    <img src="https://ui-avatars.com/api/?name=${encodeURIComponent(tx.name)}&background=004d40&color=fff" alt="Avatar">
                                    <span class="user-detail-name">${tx.name}</span>
                                </div>
                            </td>
                            <td>${tx.reference_id}</td>
                            <td>${tx.type}</td>
                            <td class="font-semibold">₹${tx.amount}</td>
                            <td><span class="badge ${badgeClass}">${tx.status}</span></td>
                        </tr>
                    `;
                });
            }

            renderCharts(stats.monthly_trends, stats.member_distribution);
        } catch (err) { console.error(err); }
    }

    async function loadPaidMembersData() {
        try {
            const res = await fetch('/api/admin/dashboard/paid-members', { headers: getHeaders() });
            if(res.status === 401) { logoutBtn.click(); return; }
            const payload = await res.json();
            const data = payload.data || [];
            const tbody = document.getElementById('paid-members-tbody');
            tbody.innerHTML = '';
            if(Array.isArray(data)) {
                data.forEach(m => {
                    tbody.innerHTML += `
                        <tr>
                            <td>#${m.id}</td>
                            <td><strong>${m.name}</strong></td>
                            <td>${m.email}</td>
                            <td class="text-success">₹${m.total_paid || 0}</td>
                        </tr>
                    `;
                });
            }
        } catch(err) { console.error(err); }
    }

    async function loadDueMembersData() {
        try {
            const res = await fetch('/api/admin/dashboard/due-members', { headers: getHeaders() });
            if(res.status === 401) { logoutBtn.click(); return; }
            const payload = await res.json();
            const data = payload.data || [];
            const tbody = document.getElementById('due-members-tbody');
            tbody.innerHTML = '';
            if(Array.isArray(data)) {
                data.forEach(m => {
                    tbody.innerHTML += `
                        <tr>
                            <td>#${m.id}</td>
                            <td><strong>${m.name}</strong></td>
                            <td>${m.email}</td>
                            <td class="text-success">₹${m.total_paid || 0}</td>
                            <td class="text-danger">₹${m.overdue_amount || 0}</td>
                        </tr>
                    `;
                });
            }
        } catch(err) { console.error(err); }
    }