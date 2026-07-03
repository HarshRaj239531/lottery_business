
    async function loadPnLData() {
        try {
            const res = await fetch('/api/admin/accounting/pnl', { headers: getHeaders() });
            if(res.status === 401) { logoutBtn.click(); return; }
            const payload = await res.json();
            const data = payload.data;
            if(!data) return;

            document.getElementById('pnl-total-revenue').textContent = '₹' + data.total_revenue;
            document.getElementById('pnl-total-expense').textContent = '₹' + data.total_expense;
            document.getElementById('pnl-net-profit').textContent = '₹' + data.net_profit;

            const revTbody = document.getElementById('pnl-revenue-tbody');
            revTbody.innerHTML = '';
            data.revenue.forEach(acc => {
                revTbody.innerHTML += `<tr><td>${acc.name}</td><td class="text-right">₹${acc.balance}</td></tr>`;
            });

            const expTbody = document.getElementById('pnl-expense-tbody');
            expTbody.innerHTML = '';
            data.expenses.forEach(acc => {
                expTbody.innerHTML += `<tr><td>${acc.name}</td><td class="text-right">₹${acc.balance}</td></tr>`;
            });
        } catch(err) { console.error(err); }
    }

    async function loadBalanceSheetData() {
        try {
            const res = await fetch('/api/admin/accounting/balance-sheet', { headers: getHeaders() });
            if(res.status === 401) { logoutBtn.click(); return; }
            const payload = await res.json();
            const data = payload.data;
            if(!data) return;

            document.getElementById('bs-total-assets').textContent = '₹' + data.total_assets;
            document.getElementById('bs-total-liabilities').textContent = '₹' + data.total_equity_and_liabilities;

            const assetsTbody = document.getElementById('bs-assets-tbody');
            assetsTbody.innerHTML = '';
            data.assets.forEach(acc => {
                assetsTbody.innerHTML += `<tr><td>${acc.name}</td><td class="text-right">₹${acc.balance}</td></tr>`;
            });

            const liabTbody = document.getElementById('bs-liabilities-tbody');
            liabTbody.innerHTML = '';
            data.liabilities.forEach(acc => {
                liabTbody.innerHTML += `<tr><td>${acc.name}</td><td class="text-right">₹${acc.balance}</td></tr>`;
            });
            // Add Net Profit to Equity/Liabilities side
            liabTbody.innerHTML += `<tr><td><strong>Net Profit (Equity)</strong></td><td class="text-right"><strong>₹${data.net_profit}</strong></td></tr>`;
            
        } catch(err) { console.error(err); }
    }

    // Load Member Ledger
    document.getElementById('btn-load-member-ledger')?.addEventListener('click', async () => {
        const id = document.getElementById('ledger-member-id').value;
        if (!id) return alert('Enter Member ID');
        try {
            const res = await fetch(`/api/admin/accounting/member-ledger/${id}`, { headers: getHeaders() });
            const payload = await res.json();
            if (!res.ok) { alert(payload.message || 'Error loading ledger'); return; }
            
            document.getElementById('ledger-member-name').textContent = 'Ledger for Member: ' + payload.data.member;
            const tbody = document.getElementById('member-ledger-tbody');
            tbody.innerHTML = '';
            payload.data.entries.forEach(entry => {
                tbody.innerHTML += `
                    <tr>
                        <td>${entry.transaction_date}</td>
                        <td>${entry.description}</td>
                        <td>${entry.account.name}</td>
                        <td class="text-success">${entry.debit > 0 ? '₹'+entry.debit : '-'}</td>
                        <td class="text-danger">${entry.credit > 0 ? '₹'+entry.credit : '-'}</td>
                    </tr>
                `;
            });
        } catch(e) { console.error(e); alert('Error fetching ledger'); }
    });

    // Load Committee Ledger
    document.getElementById('btn-load-committee-ledger')?.addEventListener('click', async () => {
        const id = document.getElementById('ledger-committee-id').value;
        if (!id) return alert('Enter Committee ID');
        try {
            const res = await fetch(`/api/admin/accounting/committee-ledger/${id}`, { headers: getHeaders() });
            const payload = await res.json();
            if (!res.ok) { alert(payload.message || 'Error loading ledger'); return; }
            
            document.getElementById('ledger-committee-name').textContent = 'Ledger for Committee: ' + payload.data.committee;
            const tbody = document.getElementById('committee-ledger-tbody');
            tbody.innerHTML = '';
            payload.data.entries.forEach(entry => {
                tbody.innerHTML += `
                    <tr>
                        <td>${entry.transaction_date}</td>
                        <td>${entry.description}</td>
                        <td>${entry.account.name}</td>
                        <td class="text-success">${entry.debit > 0 ? '₹'+entry.debit : '-'}</td>
                        <td class="text-danger">${entry.credit > 0 ? '₹'+entry.credit : '-'}</td>
                    </tr>
                `;
            });
        } catch(e) { console.error(e); alert('Error fetching ledger'); }
    });