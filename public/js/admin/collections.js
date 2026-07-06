
    async function loadCollectionCommittees() {
        try {
            const res = await fetch('/api/admin/committees', { headers: getHeaders() });
            const data = await res.json();
            const tbody = document.getElementById('collection-committees-tbody');
            tbody.innerHTML = '';
            if(Array.isArray(data)) {
                data.forEach(c => {
                    tbody.innerHTML += `
                        <tr>
                            <td>#${c.id}</td>
                            <td><strong>${c.name}</strong></td>
                            <td>₹${c.amount}</td>
                            <td><span class="text-success"><i class="fa-solid fa-circle text-sm"></i> ${c.status}</span></td>
                            <td><button class="btn-primary" onclick="window.location.hash='#committee-details-${c.id}'" style="padding:4px 8px; font-size:0.8rem;">View Collection</button></td>
                        </tr>
                    `;
                });
            }
        } catch (err) { console.error(err); }
    }

    async function loadCommitteeDetails(id) {
        if(!id) return;
        try {
            const res = await fetch(`/api/admin/committees/${id}/collection-stats`, { headers: getHeaders() });
            const data = await res.json();
            
            document.getElementById('cd-title').textContent = `${data.committee.name} Collection`;
            
            const tbody = document.getElementById('committee-details-tbody');
            tbody.innerHTML = '';
            
            if(data.members && Array.isArray(data.members)) {
                data.members.forEach(m => {
                    tbody.innerHTML += `
                        <tr>
                            <td>#${m.id}</td>
                            <td><strong>${m.name}</strong></td>
                            <td>${m.phone || m.email}</td>
                            <td class="text-success">₹${m.total_deposited}</td>
                            <td class="text-danger">₹${m.total_due}</td>
                            <td>₹${m.total_expected}</td>
                            <td>${m.installments_remaining} left (of ${m.total_installments})</td>
                        </tr>
                    `;
                });
            }
        } catch (err) { console.error(err); }
    }