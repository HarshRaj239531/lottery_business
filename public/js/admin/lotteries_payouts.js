
    async function loadLotteriesData() {
        try {
            const res = await fetch('/api/admin/lotteries', { headers: getHeaders() });
            const payload = await res.json();
            const data = payload.data || [];
            const container = document.getElementById('lotteries-container');
            container.innerHTML = '';
            
            if(Array.isArray(data) && data.length > 0) {
                data.forEach(l => {
                    const commName = l.committee ? l.committee.name : 'N/A';
                    const winnerName = l.winner ? l.winner.name : 'Unknown';
                    container.innerHTML += `
                        <div class="card" style="border-left: 4px solid #8b5cf6; padding: 20px;">
                            <div class="flex-between">
                                <span style="font-size: 0.8rem; color:#888;">ID: #${l.id}</span>
                                <span style="font-size: 0.8rem; background: rgba(139, 92, 246, 0.1); color: #8b5cf6; padding: 2px 8px; border-radius: 12px;"><i class="fa-solid fa-calendar-day"></i> ${l.draw_date}</span>
                            </div>
                            <h3 style="margin: 0 0 5px 0; font-size: 1.1rem; color:#333;">${winnerName} <i class="fa-solid fa-trophy" class="text-warning"></i></h3>
                            <p class="text-muted">Committee: <strong>${commName}</strong></p>
                        </div>
                    `;
                });
            } else {
                container.innerHTML = `<div style="grid-column: 1/-1; text-align: center; padding: 40px; color: #888; background: white; border-radius: 12px;">No lottery winners yet.</div>`;
            }
        } catch (err) { console.error(err); }
    }

    async function loadPaymentSettingsData() {
        try {
            const res = await fetch('/api/admin/payment-setting', { headers: getHeaders() });
            const payload = await res.json();
            if (payload.status && payload.data) {
                const data = payload.data;
                const phoneInput = document.getElementById('pay-setting-phone');
                if (phoneInput) {
                    phoneInput.value = data.admin_phone || '';
                }
                const qrPreview = document.getElementById('pay-setting-qr-preview');
                if (qrPreview) {
                    if (data.qr_code) {
                        qrPreview.innerHTML = `<img src="${data.qr_code}" style="max-width:100%; max-height:100%; border-radius:8px;">`;
                    } else {
                        qrPreview.innerHTML = `<span style="color: #94a3b8; font-size: 0.85rem;">No QR Code Set</span>`;
                    }
                }
            }
        } catch (err) { console.error(err); }
    }

    // Bind form submit for payment settings
    document.addEventListener('submit', async function(e) {
        if (e.target && e.target.id === 'payment-settings-form') {
            e.preventDefault();
            const formData = new FormData(e.target);
            try {
                const res = await fetch('/api/admin/payment-setting', {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${localStorage.getItem('admin_token')}`
                    },
                    body: formData
                });
                const payload = await res.json();
                if (payload.status) {
                    alert('Payment settings saved successfully!');
                    loadPaymentSettingsData();
                } else {
                    alert('Error saving payment settings: ' + (payload.message || 'Unknown error'));
                }
            } catch (err) {
                console.error(err);
                alert('Failed to save settings.');
            }
        }
    });

    async function loadPayoutsData() {
        try {
            loadPaymentSettingsData();
            const res = await fetch('/api/admin/payouts', { headers: getHeaders() });
            const data = await res.json();
            const tbody = document.getElementById('payouts-tbody');
            tbody.innerHTML = '';
            if(Array.isArray(data)) {
                data.forEach(p => {
                    const userName = p.user ? p.user.name : p.user_id;
                    const commName = p.committee ? p.committee.name : p.committee_id;
                    const actionBtn = p.status === 'pending' ? `<button class="btn-primary" onclick="payPayout(${p.id})"><i class="fa-solid fa-building-columns"></i> Pay to Bank</button>` : `<span class="badge" class="bg-success"><i class="fa-solid fa-check"></i> Paid on ${p.paid_date}</span>`;
                    const statusColor = p.status === 'pending' ? '#f59e0b' : '#10b981';

                    tbody.innerHTML += `
                        <tr>
                            <td>#${p.id}</td>
                            <td><strong>${userName}</strong></td>
                            <td>${commName}</td>
                            <td>₹${p.total_deposits}</td>
                            <td>₹${p.return_amount}</td>
                            <td><strong>₹${p.total_payout}</strong></td>
                            <td><span style="color:${statusColor}"><i class="fa-solid fa-circle text-sm"></i> ${p.status}</span></td>
                            <td>${actionBtn}</td>
                        </tr>
                    `;
                });
            }
        } catch (err) { console.error(err); }
    }

    window.payPayout = async function(id) {
        if (!confirm("Are you sure you want to mark this as Paid and transfer to the user's bank?")) return;
        try {
            const res = await fetch(`/api/admin/payouts/${id}/pay`, {
                method: 'POST',
                headers: getHeaders()
            });
            const data = await res.json();
            if (res.ok) {
                alert('Success: ' + data.message);
                loadPayoutsData();
            } else {
                alert('Error: ' + data.message);
            }
        } catch (err) { console.error(err); alert('Failed to process payout.'); }
    };