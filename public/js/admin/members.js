
    async function loadMembersData() {
        try {
            const res = await fetch('/api/admin/members', { headers: getHeaders() });
            const data = await res.json();
            
            const tbodyAgents = document.getElementById('members-agents-tbody');
            const tbodyComm = document.getElementById('members-committee-tbody');
            const tbodyLoan = document.getElementById('members-loan-tbody');
            
            tbodyAgents.innerHTML = '';
            tbodyComm.innerHTML = '';
            tbodyLoan.innerHTML = '';
            
            // Check if response is paginated (data.data) or a flat array (data)
            const membersList = Array.isArray(data?.data?.data)
                ? data.data.data
                : (Array.isArray(data?.data)
                    ? data.data
                    : (Array.isArray(data)
                        ? data
                        : []));
            
            if(membersList.length > 0) {
                membersList.forEach(m => {
                    const isAgent = m.roles && m.roles.some(r => r.name === 'agent');

                    // Agents tab
                    if (isAgent) {
                        tbodyAgents.innerHTML += `
                            <tr>
                                <td>#${m.id}</td>
                                <td><strong>${m.name}</strong></td>
                                <td>${m.email}</td>
                                <td>${m.phone || '-'}</td>
                                <td>${m.address || '-'}</td>
                                <td><span class="badge badge-primary">Agent</span></td>
                                <td>
                                    <button class="btn-primary" onclick="impersonateMember(${m.id})" style="padding:4px 8px; font-size:0.8rem; margin-right:5px;"><i class="fa-solid fa-eye"></i> View</button>
                                    <button class="btn-secondary" onclick="openChangePasswordModal(${m.id}, '${m.name.replace(/'/g, "\\'")}')" style="padding:4px 8px; font-size:0.8rem;"><i class="fa-solid fa-key"></i> Password</button>
                                </td>
                            </tr>
                        `;
                    }

                    // Committee Members Tab (all non-agent members)
                    if (!isAgent) {
                        let commActionHtml = '';
                        if (m.committees_count > 0) {
                            commActionHtml = `<span style="color:#10b981; font-weight:500; font-size:0.85rem;"><i class="fa-solid fa-check-circle"></i> Enrolled</span>`;
                        } else {
                            commActionHtml = `<button class="btn-primary" onclick="openModal('enroll-member', ${m.id})" style="padding:4px 8px; font-size:0.8rem;"><i class="fa-solid fa-plus"></i> Enroll</button>`;
                        }

                        tbodyComm.innerHTML += `
                            <tr>
                                <td>#${m.id}</td>
                                <td><strong>${m.name}</strong></td>
                                <td>${m.email}</td>
                                <td>${m.phone || '-'}</td>
                                <td>${m.address || '-'}</td>
                                <td>${commActionHtml}</td>
                                <td>
                                    <button class="btn-primary" onclick="impersonateMember(${m.id})" style="padding:4px 8px; font-size:0.8rem; margin-right:5px;"><i class="fa-solid fa-eye"></i> View</button>
                                    <button class="btn-secondary" onclick="openChangePasswordModal(${m.id}, '${m.name.replace(/'/g, "\\'")}')" style="padding:4px 8px; font-size:0.8rem;"><i class="fa-solid fa-key"></i> Password</button>
                                </td>
                            </tr>
                        `;
                    }

                    // Loan Members Tab (only if has loan and is not agent)
                    if (!isAgent && m.loans && m.loans.length > 0) {
                        m.loans.forEach(loan => {
                            tbodyLoan.innerHTML += `
                                <tr>
                                    <td>#${loan.id}</td>
                                    <td><strong>${m.name}</strong></td>
                                    <td>${m.email}</td>
                                    <td>${m.phone || '-'}</td>
                                    <td>${m.address || '-'}</td>
                                    <td><span style="color:#10b981; font-weight:500; font-size:0.85rem;"><i class="fa-solid fa-check-circle"></i> Active Loan</span></td>
                                    <td>
                                        <button class="btn-primary" onclick="impersonateMember(${m.id})" style="padding:4px 8px; font-size:0.8rem; margin-right:5px;"><i class="fa-solid fa-eye"></i> View</button>
                                        <button class="btn-secondary" onclick="openChangePasswordModal(${m.id}, '${m.name.replace(/'/g, "\\'")}')" style="padding:4px 8px; font-size:0.8rem;"><i class="fa-solid fa-key"></i> Password</button>
                                    </td>
                                </tr>
                            `;
                        });
                    }
                });
            }
        } catch (err) { console.error(err); }
    }

    // Change Password Logic
    window.openChangePasswordModal = function(id, name) {
        document.getElementById('change-password-member-id').value = id;
        document.getElementById('change-password-member-name').innerText = name;
        document.getElementById('new-password').value = '';
        document.getElementById('confirm-new-password').value = '';
        openModal('change-password');
    };

    window.submitChangePassword = async function(e) {
        e.preventDefault();
        const id = document.getElementById('change-password-member-id').value;
        const pwd = document.getElementById('new-password').value;
        const confirmPwd = document.getElementById('confirm-new-password').value;

        if (pwd !== confirmPwd) {
            alert("Passwords do not match!");
            return;
        }

        try {
            const res = await fetch(`/api/admin/members/${id}/change-password`, {
                method: 'POST',
                headers: getHeaders(),
                body: JSON.stringify({ password: pwd })
            });

            if (res.ok) {
                alert("Password changed successfully.");
                closeModal('change-password');
            } else {
                const errData = await res.json();
                alert(errData.message || "Failed to change password.");
            }
        } catch (err) {
            console.error(err);
            alert("An error occurred.");
        }
    };

    window.impersonateMember = async function(id) {
        try {
            const res = await fetch(`/api/admin/members/${id}/impersonate`, { headers: getHeaders() });
            const data = await res.json();
            if (res.ok) {
                // Store token for member
                localStorage.setItem('member_token', data.token);
                // Redirect to member dashboard
                window.location.href = data.redirect_url;
            } else {
                alert('Error: ' + data.message);
            }
        } catch (err) {
            console.error(err);
            alert('Failed to impersonate member');
        }
    };