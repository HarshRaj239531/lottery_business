    // Load Agents View
    window.loadAgentsView = async function() {
        try {
            // Load Pending Collections
            const colRes = await fetch('/api/admin/agents/collections?status=pending', { headers: getHeaders() });
            if(colRes.status === 401) { document.getElementById('logout-btn').click(); return; }
            const colData = await colRes.json();
            if (!Array.isArray(colData)) throw new Error("API did not return an array: " + JSON.stringify(colData).substring(0, 50));
            const commBody = document.getElementById('agent-pending-committee-collections-tbody');
            const loanBody = document.getElementById('agent-pending-loan-collections-tbody');
            commBody.innerHTML = '';
            loanBody.innerHTML = '';
            
            let hasComm = false, hasLoan = false;
            let commIndex = 1, loanIndex = 1;

            if(colData.length > 0) {
                colData.forEach(c => {
                    const agentName = c.agent ? c.agent.name : 'N/A';
                    const memberName = c.member ? c.member.name : 'N/A';
                    if (c.collection_type === 'committee') {
                        hasComm = true;
                        const details = c.installment && c.installment.committee ? c.installment.committee.name : 'Unknown Committee';
                        commBody.innerHTML += `
                            <tr>
                                <td>#${commIndex++}</td>
                                <td>${agentName}</td>
                                <td>${memberName}</td>
                                <td>${details}</td>
                                <td class="text-success">₹${parseFloat(c.amount_collected).toFixed(2)}</td>
                                <td>${new Date(c.collected_at).toLocaleDateString()}</td>
                                <td>
                                    <button class="btn-primary" style="padding: 5px 10px; font-size: 0.8rem;" onclick="approveCollection(${c.id})">Approve</button>
                                </td>
                            </tr>
                        `;
                    } else if (c.collection_type === 'loan') {
                        hasLoan = true;
                        const details = c.loan_installment && c.loan_installment.loan ? `Loan #${c.loan_installment.loan.id}` : 'Unknown Loan';
                        loanBody.innerHTML += `
                            <tr>
                                <td>#${loanIndex++}</td>
                                <td>${agentName}</td>
                                <td>${memberName}</td>
                                <td>${details}</td>
                                <td class="text-success">₹${parseFloat(c.amount_collected).toFixed(2)}</td>
                                <td>${new Date(c.collected_at).toLocaleDateString()}</td>
                                <td>
                                    <button class="btn-primary" style="padding: 5px 10px; font-size: 0.8rem;" onclick="approveCollection(${c.id})">Approve</button>
                                </td>
                            </tr>
                        `;
                    }
                });
            }

            if(!hasComm) commBody.innerHTML = '<tr><td colspan="7" class="text-center">No pending committee collections.</td></tr>';
            if(!hasLoan) loanBody.innerHTML = '<tr><td colspan="7" class="text-center">No pending loan collections.</td></tr>';

        } catch(err) { 
            console.error(err); 
            const commBody = document.getElementById('agent-pending-committee-collections-tbody');
            if (commBody) {
                commBody.innerHTML = `<tr><td colspan="7" class="text-danger text-center">Error: ${err.message || 'Unknown Error'}</td></tr>`;
            }
        }
    }

    // Approve Collection Action
    window.approveCollection = async function(id) {
        if(!confirm("Are you sure you want to approve this collection and settle the user's due amount?")) return;
        try {
            const res = await fetch('/api/admin/agents/collections/' + id + '/approve', {
                method: 'POST',
                headers: getHeaders()
            });
            const data = await res.json();
            if(data.status === true || data.status === 'success') {
                alert(data.message);
                loadAgentsView();
            } else {
                alert(data.message || "Failed to approve. Please try again or re-login.");
            }
        } catch(err) { console.error(err); alert("An error occurred: " + err.message); }
    }