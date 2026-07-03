
    async function loadBalanceSheetData() {
        try {
            const res = await fetch('/api/admin/accounting/balance-sheet', { headers: getHeaders() });
            const payload = await res.json();
            const data = payload.data;
            
            // Committees
            document.getElementById('bs-comm-assets').textContent = '₹' + parseFloat(data.committee.total_assets).toFixed(2);
            document.getElementById('bs-comm-liabilities').textContent = '₹' + parseFloat(data.committee.total_liabilities).toFixed(2);
            
            const commAssetsBody = document.getElementById('bs-comm-assets-tbody');
            commAssetsBody.innerHTML = '';
            data.committee.assets.forEach(a => {
                commAssetsBody.innerHTML += `<tr><td>${a.name}</td><td class="text-right">₹${parseFloat(a.balance).toFixed(2)}</td></tr>`;
            });
            
            const commLiabBody = document.getElementById('bs-comm-liabilities-tbody');
            commLiabBody.innerHTML = '';
            data.committee.liabilities.forEach(l => {
                commLiabBody.innerHTML += `<tr><td>${l.name}</td><td class="text-right">₹${parseFloat(l.balance).toFixed(2)}</td></tr>`;
            });
            data.committee.equity.forEach(e => {
                commLiabBody.innerHTML += `<tr><td>${e.name}</td><td style="text-align:right; color:#8b5cf6;">₹${parseFloat(e.balance).toFixed(2)}</td></tr>`;
            });

            // Loans
            document.getElementById('bs-loan-assets').textContent = '₹' + parseFloat(data.loan.total_assets).toFixed(2);
            document.getElementById('bs-loan-liabilities').textContent = '₹' + parseFloat(data.loan.total_liabilities).toFixed(2);
            
            const loanAssetsBody = document.getElementById('bs-loan-assets-tbody');
            loanAssetsBody.innerHTML = '';
            data.loan.assets.forEach(a => {
                loanAssetsBody.innerHTML += `<tr><td>${a.name}</td><td class="text-right">₹${parseFloat(a.balance).toFixed(2)}</td></tr>`;
            });
            
            const loanLiabBody = document.getElementById('bs-loan-liabilities-tbody');
            loanLiabBody.innerHTML = '';
            data.loan.liabilities.forEach(l => {
                loanLiabBody.innerHTML += `<tr><td>${l.name}</td><td class="text-right">₹${parseFloat(l.balance).toFixed(2)}</td></tr>`;
            });
            data.loan.equity.forEach(e => {
                loanLiabBody.innerHTML += `<tr><td>${e.name}</td><td style="text-align:right; color:#8b5cf6;">₹${parseFloat(e.balance).toFixed(2)}</td></tr>`;
            });

            // Loan Breakdown
            const breakdownBody = document.getElementById('bs-loan-breakdown-tbody');
            if (breakdownBody) {
                breakdownBody.innerHTML = '';
                if (data.loan.breakdown && data.loan.breakdown.length > 0) {
                    data.loan.breakdown.forEach(b => {
                        const statusColor = b.status === 'paid' ? '#10b981' : '#f59e0b';
                        breakdownBody.innerHTML += `
                            <tr>
                                <td><strong>${b.user_name}</strong><br><small class="text-muted">ID: #${b.id} | ${b.interest_rate}</small></td>
                                <td>₹${parseFloat(b.principal).toFixed(2)}</td>
                                <td class="text-success">₹${parseFloat(b.total_expected_interest).toFixed(2)}</td>
                                <td style="color:#8b5cf6;">₹${parseFloat(b.recovered_interest).toFixed(2)}</td>
                                <td>₹${parseFloat(b.total_recovered).toFixed(2)} / <span class="text-muted">₹${parseFloat(b.total_expected_return).toFixed(2)}</span></td>
                                <td><span style="background: ${statusColor}22; color: ${statusColor}; padding: 3px 8px; border-radius: 12px; font-size: 0.8rem; text-transform: capitalize;">${b.status}</span></td>
                            </tr>
                        `;
                    });
                } else {
                    breakdownBody.innerHTML = `<tr><td colspan="6" style="text-align:center; color:#888;">No loans given yet.</td></tr>`;
                }
            }

        } catch (err) { console.error(err); }
    }

    // =====================================
    // AGENT MANAGEMENT
    // =====================================
