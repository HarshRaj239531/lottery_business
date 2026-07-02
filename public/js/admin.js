document.addEventListener('DOMContentLoaded', () => {
    
    // Core State
    let authToken = localStorage.getItem('admin_token');
    
    // Elements
    const loginScreen = document.getElementById('login-screen');
    const appShell = document.getElementById('app-shell');
    const loginForm = document.getElementById('login-form');
    const logoutBtn = document.getElementById('logout-btn');
    const errorMsg = document.getElementById('login-error');
    const navLinks = document.querySelectorAll('.nav-link');
    const viewSections = document.querySelectorAll('.view-section');

    const modal = document.getElementById('global-modal');
    const modalTitle = document.getElementById('modal-title');
    const modalBody = document.getElementById('modal-body');

    // Chart instances
    let lineChartInstance = null;
    let doughnutChartInstance = null;

    // Base Fetch Headers
    const getHeaders = () => ({
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'Authorization': `Bearer ${authToken}`
    });

    // ----- INITIALIZATION -----
    if (authToken) {
        showApp();
    } else {
        showLogin();
    }

    // ----- ROUTING (Simple Hash Router) -----
    function handleRoute() {
        const hash = window.location.hash || '#dashboard';
        let targetView = hash.replace('#', '');
        let paramId = null;

        if (targetView.startsWith('committee-details-')) {
            paramId = targetView.replace('committee-details-', '');
            targetView = 'committee-details';
        } else if (targetView === 'committees-active') {
            paramId = 'active';
            targetView = 'committees';
        }

        // Update nav active states
        navLinks.forEach(link => {
            const href = link.getAttribute('href');
            if (href === hash || (targetView === 'committee-details' && href === '#collection-committees') || (targetView === 'committees' && href === '#committees')) {
                link.classList.add('active');
            } else {
                link.classList.remove('active');
            }
        });

        // Show/hide sections
        viewSections.forEach(section => {
            if (section.id === `view-${targetView}`) {
                section.style.display = 'block';
                // Trigger data load based on view
                if (targetView === 'dashboard') loadDashboardData();
                if (targetView === 'committees') loadCommitteesData(paramId);
                if (targetView === 'members') loadMembersData();
                if (targetView === 'installments') loadInstallmentsData();
                if (targetView === 'lotteries') loadLotteriesData();
                if (targetView === 'lotteries') loadLotteriesData();
                if (targetView === 'loans') { loadLoansData(); loadBalanceSheetData(); }
                if (targetView === 'payouts') loadPayoutsData();
                if (targetView === 'collection-committees') loadCollectionCommittees();
                if (targetView === 'committee-details') loadCommitteeDetails(paramId);
                if (targetView === 'paid-members') loadPaidMembersData();
                if (targetView === 'due-members') loadDueMembersData();
                if (targetView === 'pnl') loadPnLData();
                if (targetView === 'balance-sheet') loadBalanceSheetData();
                if (targetView === 'agents') loadAgentsView();
                if (targetView === 'member-ledger') { document.getElementById('member-ledger-tbody').innerHTML=''; document.getElementById('ledger-member-name').textContent=''; }
                if (targetView === 'committee-ledger') { document.getElementById('committee-ledger-tbody').innerHTML=''; document.getElementById('ledger-committee-name').textContent=''; }
            } else {
                section.style.display = 'none';
            }
        });
    }
    window.addEventListener('hashchange', handleRoute);

    // ----- UI TOGGLES -----
    function showApp() {
        loginScreen.style.display = 'none';
        appShell.style.display = 'flex';
        handleRoute(); // Load current route
    }

    function showLogin() {
        appShell.style.display = 'none';
        loginScreen.style.display = 'flex';
    }

    window.openModal = function(type, id = null) {
        modal.style.display = 'flex';
        let html = '';

        if (type === 'create-committee') {
            modalTitle.textContent = 'New Committee';
            html = `
                <form id="modal-form" onsubmit="submitForm(event, 'committees')">
                    <div class="input-group"><label>Name</label><div class="input-field"><input type="text" id="c_name" required></div></div>
                    <div class="input-group"><label>Amount (₹)</label><div class="input-field"><input type="number" id="c_amount" required></div></div>
                    <div class="input-group"><label>Total Members Limit</label><div class="input-field"><input type="number" id="c_total" required></div></div>
                    <div class="input-group"><label>Duration (Months)</label><div class="input-field"><input type="number" id="c_duration" required></div></div>
                    <div class="input-group"><label>Frequency</label>
                        <select id="c_freq" class="input-field" style="width:100%; border:none; background:transparent;">
                            <option value="daily">Daily</option><option value="weekly">Weekly</option><option value="monthly" selected>Monthly</option>
                        </select>
                    </div>
                    <div class="input-group"><label>Return Percentage (%)</label><div class="input-field"><input type="number" step="0.01" min="0" max="100" id="c_return" placeholder="e.g. 10.50"></div></div>
                    <button type="submit" class="btn-primary">Create Committee</button>
                </form>
            `;
        } 
        else if (type === 'create-member') {
            modalTitle.textContent = 'New User / Agent';
            html = `
                <form id="modal-form" onsubmit="submitForm(event, 'members')">
                    <div class="input-group"><label>Role</label>
                        <select id="m_role" class="input-field" style="width:100%; border:none; background:transparent;">
                            <option value="member" selected>Member</option>
                            <option value="agent">Agent</option>
                        </select>
                    </div>
                    <div class="input-group"><label>Full Name</label><div class="input-field"><input type="text" id="m_name" required></div></div>
                    <div class="input-group"><label>Email</label><div class="input-field"><input type="email" id="m_email" required></div></div>
                    <div class="input-group"><label>Phone</label><div class="input-field"><input type="text" id="m_phone"></div></div>
                    <div class="input-group"><label>Address</label><div class="input-field"><input type="text" id="m_address"></div></div>
                    <div class="input-group"><label>Password</label><div class="input-field"><input type="password" id="m_pass" required></div></div>
                    <div class="input-group"><label>Enroll in Committee ID (Optional)</label><div class="input-field"><input type="number" id="m_enroll_comm_id"></div></div>
                    <button type="submit" class="btn-primary">Create User</button>
                </form>
            `;
        }
        else if (type === 'enroll-member') {
            modalTitle.textContent = 'Enroll Member in Committee';
            html = `
                <form id="modal-form" onsubmit="submitForm(event, 'enroll-member', ${id})">
                    <div class="input-group"><label>Committee ID</label><div class="input-field"><input type="number" id="em_comm_id" required></div></div>
                    <button type="submit" class="btn-primary">Enroll Member</button>
                </form>
            `;
        }
        else if (type === 'collect-installment') {
            modalTitle.textContent = 'Collect Payment';
            html = `
                <form id="modal-form" onsubmit="submitForm(event, 'installments')">
                    <div class="input-group"><label>User ID</label><div class="input-field"><input type="number" id="i_user" required></div></div>
                    <div class="input-group"><label>Committee ID</label><div class="input-field"><input type="number" id="i_comm" required></div></div>
                    <div class="input-group"><label>Amount Collected (₹)</label><div class="input-field"><input type="number" id="i_amount" required></div></div>
                    <div class="input-group"><label>Due Date</label><div class="input-field"><input type="date" id="i_due" required></div></div>
                    <button type="submit" class="btn-primary">Record Payment</button>
                </form>
            `;
        }
        else if (type === 'edit-committee') {
            modalTitle.textContent = 'Edit Committee #' + id;
            html = `
                <form id="modal-form" onsubmit="submitForm(event, 'edit-committee', ${id})">
                    <div class="input-group"><label>Name</label><div class="input-field"><input type="text" id="ec_name" required></div></div>
                    <div class="input-group"><label>Amount (₹)</label><div class="input-field"><input type="number" id="ec_amount" required></div></div>
                    <div class="input-group"><label>Total Members Limit</label><div class="input-field"><input type="number" id="ec_total" required></div></div>
                    <div class="input-group"><label>Duration (Months)</label><div class="input-field"><input type="number" id="ec_duration" required></div></div>
                    <div class="input-group"><label>Frequency</label>
                        <select id="ec_freq" class="input-field" style="width:100%; border:none; background:transparent;">
                            <option value="daily">Daily</option><option value="weekly">Weekly</option><option value="monthly">Monthly</option>
                        </select>
                    </div>
                    <div class="input-group"><label>Return Percentage (%)</label><div class="input-field"><input type="number" step="0.01" min="0" max="100" id="ec_return"></div></div>
                    <div class="input-group"><label>Status</label>
                        <select id="ec_status" class="input-field" style="width:100%; border:none; background:transparent;">
                            <option value="active">Active</option>
                            <option value="completed">Completed</option>
                            <option value="defaulted">Defaulted</option>
                        </select>
                    </div>
                    <button type="submit" class="btn-primary">Update Committee</button>
                </form>
            `;
        }
        else if (type === 'draw-lottery') {
            modalTitle.textContent = 'Draw Lottery Winner';
            html = `
                <form id="modal-form" onsubmit="submitForm(event, 'lotteries')">
                    <div class="input-group"><label>Committee ID</label><div class="input-field"><input type="number" id="l_comm" required></div></div>
                    <button type="submit" class="btn-primary">Draw Winner Now <i class="fa-solid fa-wand-magic-sparkles"></i></button>
                </form>
            `;
        }

        else if (type === 'create-loan') {
            modalTitle.textContent = 'Create New Loan';
            html = `
                <form id="modal-form" onsubmit="submitForm(event, 'loans')">
                    <div style="display:flex; gap:20px; flex-wrap:wrap;">
                        <div style="flex:1; min-width:250px;">
                            <h3 style="margin-bottom:10px; font-size:1rem;">Borrower Details</h3>
                            <div class="input-group"><label>Full Name</label><div class="input-field"><input type="text" id="ln_name"></div></div>
                            <div class="input-group"><label>Email</label><div class="input-field"><input type="email" id="ln_email"></div></div>
                            <div class="input-group"><label>Phone</label><div class="input-field"><input type="text" id="ln_phone"></div></div>
                            <div class="input-group"><label>Password (For new user)</label><div class="input-field"><input type="password" id="ln_pass"></div></div>
                            <div class="input-group"><label>Address</label><div class="input-field"><input type="text" id="ln_address"></div></div>
                            
                            <h3 style="margin-top:15px; margin-bottom:10px; font-size:1rem;">KYC Documents</h3>
                            <div class="input-group"><label>Photo</label><div class="input-field"><input type="file" id="ln_photo" accept="image/*"></div></div>
                            <div class="input-group"><label>ID Proof</label><div class="input-field"><input type="file" id="ln_id_proof" accept=".jpg,.png,.pdf"></div></div>
                            <div class="input-group"><label>Aadhar Card</label><div class="input-field"><input type="file" id="ln_aadhar" accept=".jpg,.png,.pdf"></div></div>
                            <div class="input-group"><label>PAN Card</label><div class="input-field"><input type="file" id="ln_pan" accept=".jpg,.png,.pdf"></div></div>
                        </div>
                        <div style="flex:1; min-width:250px;">
                            <h3 style="margin-bottom:10px; font-size:1rem;">Loan Terms</h3>
                            <div class="input-group"><label>Principal Amount (₹)</label><div class="input-field"><input type="number" id="ln_amount" required></div></div>
                            <div class="input-group"><label>Interest Rate (%)</label><div class="input-field"><input type="number" step="0.01" id="ln_rate" required placeholder="e.g. 12.5"></div></div>
                            <div class="input-group"><label>Duration (Months)</label><div class="input-field"><input type="number" id="ln_duration" required></div></div>
                            <div class="input-group"><label>Payment Frequency</label>
                                <select id="ln_freq" class="input-field" style="width:100%; border:none; background:transparent;">
                                    <option value="daily">Daily</option><option value="weekly">Weekly</option><option value="monthly" selected>Monthly</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn-primary" style="margin-top:20px; width:100%;">Create Loan & Generate EMIs</button>
                </form>
            `;
        }

        modalBody.innerHTML = html;
    };

    window.closeModal = function() {
        modal.style.display = 'none';
        modalBody.innerHTML = '';
    };

    window.openEditCommitteeModal = async function(id) {
        try {
            const res = await fetch(`/api/admin/committees/${id}`, { headers: getHeaders() });
            const data = await res.json();
            openModal('edit-committee', id);
            document.getElementById('ec_name').value = data.name;
            document.getElementById('ec_amount').value = data.amount;
            document.getElementById('ec_total').value = data.total_members;
            document.getElementById('ec_duration').value = data.duration;
            document.getElementById('ec_freq').value = data.payment_frequency;
            document.getElementById('ec_return').value = data.return_percentage || '';
            document.getElementById('ec_status').value = data.status;
        } catch (err) { console.error("Error fetching committee:", err); }
    };

    window.deleteCommittee = async function(id) {
        if (!confirm("Are you sure you want to delete this committee? This action cannot be undone.")) return;
        try {
            const res = await fetch(`/api/admin/committees/${id}`, {
                method: 'DELETE',
                headers: getHeaders()
            });
            const data = await res.json();
            if (res.ok) {
                alert('Success: ' + (data.message || 'Committee Deleted'));
                handleRoute(); // Refresh current view
            } else {
                alert('Error: ' + (data.message || 'Failed to delete committee'));
            }
        } catch (err) {
            console.error(err);
            alert('Request failed');
        }
    };

    // ----- FORM SUBMISSIONS -----
    window.submitForm = async function(e, entity, id = null) {
        e.preventDefault();
        let endpoint = '';
        let bodyData = {};

        try {
            if (entity === 'committees') {
                endpoint = '/api/admin/committees';
                bodyData = {
                    name: document.getElementById('c_name').value,
                    amount: document.getElementById('c_amount').value,
                    total_members: document.getElementById('c_total').value,
                    duration: document.getElementById('c_duration').value,
                    payment_frequency: document.getElementById('c_freq').value,
                    start_date: new Date().toISOString().split('T')[0],
                    end_date: (function() {
                        let d = new Date();
                        let dur = parseInt(document.getElementById('c_duration').value);
                        d.setMonth(d.getMonth() + dur);
                        return d.toISOString().split('T')[0];
                    })(),
                    return_percentage: document.getElementById('c_return').value || 0,
                    status: 'active'
                };
            } else if (entity === 'edit-committee') {
                endpoint = `/api/admin/committees/${id}`;
                bodyData = {
                    name: document.getElementById('ec_name').value,
                    amount: document.getElementById('ec_amount').value,
                    total_members: document.getElementById('ec_total').value,
                    duration: document.getElementById('ec_duration').value,
                    payment_frequency: document.getElementById('ec_freq').value,
                    start_date: new Date().toISOString().split('T')[0], // Retain current logic or pass original
                    end_date: (function() {
                        let d = new Date();
                        let dur = parseInt(document.getElementById('ec_duration').value);
                        d.setMonth(d.getMonth() + dur);
                        return d.toISOString().split('T')[0];
                    })(),
                    return_percentage: document.getElementById('ec_return').value || 0,
                    status: document.getElementById('ec_status').value
                };
            } else if (entity === 'members') {
                endpoint = '/api/admin/users';
                bodyData = {
                    role: document.getElementById('m_role').value,
                    name: document.getElementById('m_name').value,
                    email: document.getElementById('m_email').value,
                    phone: document.getElementById('m_phone').value,
                    address: document.getElementById('m_address').value,
                    password: document.getElementById('m_pass').value,
                };
            } else if (entity === 'installments') {
                endpoint = '/api/admin/installments/collect';
                bodyData = {
                    user_id: document.getElementById('i_user').value,
                    committee_id: document.getElementById('i_comm').value,
                    amount: document.getElementById('i_amount').value,
                    due_date: document.getElementById('i_due').value,
                    paid_date: new Date().toISOString().split('T')[0],
                    status: 'paid'
                };
            } else if (entity === 'lotteries') {
                const commId = document.getElementById('l_comm').value;
                endpoint = `/api/admin/lotteries/draw/${commId}`;
                bodyData = {};
            } else if (entity === 'enroll-member') {
                endpoint = `/api/admin/members/${id}/enroll`;
                bodyData = {
                    committee_id: document.getElementById('em_comm_id').value
                };
            } else if (entity === 'loans') {
                endpoint = '/api/admin/loans';
                bodyData = new FormData();
                bodyData.append('name', document.getElementById('ln_name').value);
                bodyData.append('email', document.getElementById('ln_email').value);
                bodyData.append('phone', document.getElementById('ln_phone').value);
                bodyData.append('password', document.getElementById('ln_pass').value);
                bodyData.append('address', document.getElementById('ln_address').value);
                bodyData.append('amount', document.getElementById('ln_amount').value);
                bodyData.append('interest_rate_percent', document.getElementById('ln_rate').value);
                bodyData.append('duration_months', document.getElementById('ln_duration').value);
                bodyData.append('payment_frequency', document.getElementById('ln_freq').value);
                if(document.getElementById('ln_photo').files[0]) bodyData.append('photo', document.getElementById('ln_photo').files[0]);
                if(document.getElementById('ln_id_proof').files[0]) bodyData.append('id_proof', document.getElementById('ln_id_proof').files[0]);
                if(document.getElementById('ln_aadhar').files[0]) bodyData.append('aadhar_card', document.getElementById('ln_aadhar').files[0]);
                if(document.getElementById('ln_pan').files[0]) bodyData.append('pan_card', document.getElementById('ln_pan').files[0]);
            }

            let options = {
                method: 'POST',
                headers: getHeaders()
            };
            if (bodyData instanceof FormData) {
                delete options.headers['Content-Type'];
                options.body = bodyData;
            } else {
                options.body = JSON.stringify(bodyData);
            }

            // Some entities define their own method
            if (entity === 'edit-committee') options.method = 'PUT';

            const res = await fetch(endpoint, options);

            const data = await res.json();
            if (res.ok) {
                // If creating member and a committee ID was specified, automatically enroll them
                if (entity === 'members' && document.getElementById('m_enroll_comm_id') && document.getElementById('m_enroll_comm_id').value) {
                    try {
                        const enrollRes = await fetch(`/api/admin/members/${data.data.id}/enroll`, {
                            method: 'POST',
                            headers: getHeaders(),
                            body: JSON.stringify({ committee_id: document.getElementById('m_enroll_comm_id').value })
                        });
                        if (enrollRes.ok) {
                            alert('Success: Member created and enrolled in committee');
                        } else {
                            const errData = await enrollRes.json();
                            alert('Member created, but enrollment failed: ' + (errData.message || 'Error'));
                        }
                    } catch(e) {
                        alert('Member created, but failed to enroll.');
                    }
                } else {
                    alert('Success: ' + (data.message || 'Action completed'));
                }
                
                closeModal();
                handleRoute(); // Refresh current view
            } else {
                alert('Error: ' + (data.message || 'Validation failed'));
            }
        } catch (err) {
            console.error(err);
            alert('Request failed');
        }
    };

    window.sendDueWarnings = async function() {
        if (!confirm("Are you sure you want to send SMS warnings to all members with overdue payments?")) return;
        
        try {
            const res = await fetch('/api/admin/installments/send-warnings', {
                method: 'POST',
                headers: getHeaders()
            });
            const data = await res.json();
            if (res.ok) {
                alert('Success: ' + data.message);
            } else {
                alert('Error: ' + (data.message || 'Failed to send warnings'));
            }
        } catch (err) {
            console.error(err);
            alert('Request failed');
        }
    };

    window.sendPaymentReminders = async function() {
        if (!confirm("Are you sure you want to send SMS payment reminders to members with upcoming dues (next 3 days)?")) return;
        
        try {
            const res = await fetch('/api/admin/installments/send-payment-reminders', {
                method: 'POST',
                headers: getHeaders()
            });
            const data = await res.json();
            if (res.ok) {
                alert('Success: ' + data.message);
            } else {
                alert('Error: ' + (data.message || 'Failed to send payment reminders'));
            }
        } catch (err) {
            console.error(err);
            alert('Request failed');
        }
    };

    // ----- AUTHENTICATION -----
    loginForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const email = document.getElementById('login-email').value;
        const password = document.getElementById('login-password').value;

        try {
            const res = await fetch('/api/admin/login', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify({ email, password })
            });
            const data = await res.json();
            if (res.ok && data.success) {
                authToken = data.data.token;
                localStorage.setItem('admin_token', authToken);
                errorMsg.style.display = 'none';
                showApp();
            } else {
                errorMsg.textContent = data.message || "Invalid credentials";
                errorMsg.style.display = 'block';
            }
        } catch (err) {
            errorMsg.textContent = "Server connection error.";
            errorMsg.style.display = 'block';
        }
    });

    logoutBtn.addEventListener('click', async () => {
        try { await fetch('/api/admin/logout', { method: 'POST', headers: getHeaders() }); } catch(e) {}
        localStorage.removeItem('admin_token');
        authToken = null;
        window.location.hash = '';
        showLogin();
    });

    // ----- DATA FETCHING -----
    async function loadDashboardData() {
        try {
            const res = await fetch('/api/admin/dashboard', { headers: getHeaders() });
            if(res.status === 401) { logoutBtn.click(); return; }
            const payload = await res.json();
            if(!payload.data) return;
            const stats = payload.data;
            document.getElementById('stat-collection').textContent = '₹' + (stats.today_collection || 0);
            document.getElementById('stat-members').textContent = stats.total_members || 0;
            document.getElementById('stat-paid-members').textContent = stats.paid_members_count || 0;
            document.getElementById('stat-due-amount').textContent = '₹' + (stats.total_due_amount || 0);
            renderCharts();
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
                            <td style="color:#10b981; font-weight:600;">₹${m.total_paid || 0}</td>
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
                            <td style="color:#10b981;">₹${m.total_paid || 0}</td>
                            <td style="color:#ef4444; font-weight:600;">₹${m.overdue_amount || 0}</td>
                        </tr>
                    `;
                });
            }
        } catch(err) { console.error(err); }
    }

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
                revTbody.innerHTML += `<tr><td>${acc.name}</td><td style="text-align:right">₹${acc.balance}</td></tr>`;
            });

            const expTbody = document.getElementById('pnl-expense-tbody');
            expTbody.innerHTML = '';
            data.expenses.forEach(acc => {
                expTbody.innerHTML += `<tr><td>${acc.name}</td><td style="text-align:right">₹${acc.balance}</td></tr>`;
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
                assetsTbody.innerHTML += `<tr><td>${acc.name}</td><td style="text-align:right">₹${acc.balance}</td></tr>`;
            });

            const liabTbody = document.getElementById('bs-liabilities-tbody');
            liabTbody.innerHTML = '';
            data.liabilities.forEach(acc => {
                liabTbody.innerHTML += `<tr><td>${acc.name}</td><td style="text-align:right">₹${acc.balance}</td></tr>`;
            });
            // Add Net Profit to Equity/Liabilities side
            liabTbody.innerHTML += `<tr><td><strong>Net Profit (Equity)</strong></td><td style="text-align:right"><strong>₹${data.net_profit}</strong></td></tr>`;
            
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
                        <td style="color:#10b981;">${entry.debit > 0 ? '₹'+entry.debit : '-'}</td>
                        <td style="color:#ef4444;">${entry.credit > 0 ? '₹'+entry.credit : '-'}</td>
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
                        <td style="color:#10b981;">${entry.debit > 0 ? '₹'+entry.debit : '-'}</td>
                        <td style="color:#ef4444;">${entry.credit > 0 ? '₹'+entry.credit : '-'}</td>
                    </tr>
                `;
            });
        } catch(e) { console.error(e); alert('Error fetching ledger'); }
    });

    async function loadCommitteesData(status = null) {
        try {
            let url = '/api/admin/committees';
            if (status === 'active') url += '?status=active';
            const res = await fetch(url, { headers: getHeaders() });
            const data = await res.json();
            const tbody = document.getElementById('committees-tbody');
            tbody.innerHTML = '';
            if(Array.isArray(data)) {
                data.forEach(c => {
                    tbody.innerHTML += `
                        <tr>
                            <td>#${c.id}</td>
                            <td><strong>${c.name}</strong></td>
                            <td>₹${c.amount}</td>
                            <td>${c.duration} Months</td>
                            <td><span style="background:#8b5cf6; color:white; padding:4px 10px; border-radius:12px; font-size:0.8rem; text-transform:capitalize;">${c.payment_frequency}</span></td>
                            <td>${c.return_percentage}%</td>
                            <td><span style="color:#10b981"><i class="fa-solid fa-circle text-sm"></i> ${c.status}</span></td>
                            <td>
                                <button class="btn-secondary" onclick="window.location.hash='#committee-details-${c.id}'" style="padding:4px 8px; font-size:0.8rem; margin-right: 5px; color:#3b82f6; border-color: rgba(59, 130, 246, 0.3); background: rgba(59, 130, 246, 0.1);"><i class="fa-solid fa-users"></i> Members</button>
                                <button class="btn-secondary" onclick="openEditCommitteeModal(${c.id})" style="padding:4px 8px; font-size:0.8rem; margin-right: 5px;"><i class="fa-solid fa-pen"></i> Edit</button>
                                <button class="btn-secondary text-danger" onclick="deleteCommittee(${c.id})" style="padding:4px 8px; font-size:0.8rem; background: rgba(244, 63, 94, 0.1); border: 1px solid rgba(244, 63, 94, 0.3); color: #f43f5e;"><i class="fa-solid fa-trash"></i> Delete</button>
                            </td>
                        </tr>
                    `;
                });
            }
        } catch (err) { console.error(err); }
    }

    window.switchMemberTab = function(tab) {
        const btnAgents = document.getElementById('tab-btn-agents');
        const btnComm = document.getElementById('tab-btn-committee-members');
        const btnLoan = document.getElementById('tab-btn-loan-members');
        const tableAgents = document.getElementById('table-agents');
        const tableComm = document.getElementById('table-committee-members');
        const tableLoan = document.getElementById('table-loan-members');

        btnAgents.className = 'btn-secondary';
        btnComm.className = 'btn-secondary';
        btnLoan.className = 'btn-secondary';
        
        tableAgents.style.display = 'none';
        tableComm.style.display = 'none';
        tableLoan.style.display = 'none';

        if (tab === 'agents') {
            btnAgents.className = 'btn-primary';
            tableAgents.style.display = 'block';
        } else if (tab === 'committee') {
            btnComm.className = 'btn-primary';
            tableComm.style.display = 'block';
        } else if (tab === 'loan') {
            btnLoan.className = 'btn-primary';
            tableLoan.style.display = 'block';
        }
    };

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
            const membersList = Array.isArray(data) ? data : (data.data ? data.data : []);
            
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
                                <td><span style="background:#8b5cf6; color:white; padding:4px 10px; border-radius:12px; font-size:0.8rem;">Agent</span></td>
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

    async function loadInstallmentsData() {
        try {
            // 1. Committee Installments
            const resComm = await fetch('/api/admin/installments', { headers: getHeaders() });
            const dataComm = await resComm.json();
            const commTbody = document.getElementById('installments-tbody');
            commTbody.innerHTML = '';
            if(Array.isArray(dataComm)) {
                dataComm.forEach(i => {
                    const userName = i.user ? i.user.name : i.user_id;
                    const commName = i.committee ? i.committee.name : i.committee_id;
                    let badgeColor = i.status === 'paid' ? '#10b981' : '#f59e0b';
                    commTbody.innerHTML += `
                        <tr>
                            <td>#${i.id}</td>
                            <td>${userName}</td>
                            <td>${commName}</td>
                            <td>₹${i.amount}</td>
                            <td>${i.paid_date ? i.paid_date : '<span style="color:#ef4444; font-size:0.8rem;">Not Paid Yet</span>'}</td>
                            <td><span style="color:${badgeColor}"><i class="fa-solid fa-circle text-sm"></i> ${i.status}</span></td>
                        </tr>
                    `;
                });
            }

            // 2. Loan Installments
            const resLoan = await fetch('/api/admin/loan-installments', { headers: getHeaders() });
            const payloadLoan = await resLoan.json();
            const dataLoan = payloadLoan.data || [];
            const loanTbody = document.getElementById('loan-installments-tbody');
            if(loanTbody) {
                loanTbody.innerHTML = '';
                if(Array.isArray(dataLoan)) {
                    dataLoan.forEach(i => {
                        const userName = (i.loan && i.loan.user) ? i.loan.user.name : 'Unknown User';
                        const loanIdDisplay = i.loan ? `Loan #${i.loan.id}` : 'Unknown Loan';
                        let badgeColor = i.status === 'paid' ? '#10b981' : '#f59e0b';
                        loanTbody.innerHTML += `
                            <tr>
                                <td>#${i.id}</td>
                                <td>${userName}</td>
                                <td>${loanIdDisplay}</td>
                                <td>₹${i.total_amount}</td>
                                <td>${i.paid_date ? i.paid_date : '<span style="color:#ef4444; font-size:0.8rem;">Not Paid Yet</span>'}</td>
                                <td><span style="color:${badgeColor}"><i class="fa-solid fa-circle text-sm"></i> ${i.status}</span></td>
                            </tr>
                        `;
                    });
                }
            }
        } catch (err) { console.error(err); }
    }

    async function loadLoansData() {
        try {
            const res = await fetch('/api/admin/loans', { headers: getHeaders() });
            const payload = await res.json();
            const data = payload.data || [];
            const tbody = document.getElementById('loans-tbody');
            tbody.innerHTML = '';
            if(Array.isArray(data)) {
                data.forEach(l => {
                    const userName = l.user ? l.user.name : 'Unknown';
                    tbody.innerHTML += `
                        <tr id="loan-row-${l.id}">
                            <td>#${l.id}</td>
                            <td><strong>${userName}</strong></td>
                            <td style="color:#10b981;">₹${l.amount}</td>
                            <td>${l.interest_rate_percent}%</td>
                            <td>${l.duration_months} M</td>
                            <td style="text-transform:capitalize;">${l.payment_frequency}</td>
                            <td><span style="color:${l.status === 'active' ? '#3b82f6' : '#10b981'}"><i class="fa-solid fa-circle text-sm"></i> ${l.status}</span></td>
                            <td>
                                <button class="btn-primary" onclick="toggleLoanDetails(${l.id})" style="padding:4px 8px; font-size:0.8rem;"><i class="fa-solid fa-chevron-down"></i> Installments</button>
                            </td>
                        </tr>
                        <tr id="loan-details-${l.id}" style="display:none; background: rgba(0,0,0,0.02);">
                            <td colspan="8" style="padding: 0;">
                                <div id="loan-details-content-${l.id}" style="padding: 20px; border-top: 1px solid rgba(0,0,0,0.05);">
                                    <div style="text-align:center; color:#888;"><i class="fa-solid fa-spinner fa-spin"></i> Loading installments...</div>
                                </div>
                            </td>
                        </tr>
                    `;
                });
            }
        } catch (err) { console.error(err); }
    }

    window.toggleLoanDetails = async function(id) {
        const detailRow = document.getElementById(`loan-details-${id}`);
        if (detailRow.style.display === 'table-row') {
            detailRow.style.display = 'none';
            return;
        }
        
        detailRow.style.display = 'table-row';
        const contentDiv = document.getElementById(`loan-details-content-${id}`);

        try {
            const res = await fetch(`/api/admin/loans/${id}`, { headers: getHeaders() });
            const payload = await res.json();
            const loan = payload.data;
            const installments = loan.installments || [];

            let total = installments.length;
            let paid = installments.filter(i => i.status === 'paid').length;
            let pending = total - paid;

            let html = `
                <div style="display: flex; gap: 20px; margin-bottom: 15px; flex-wrap: wrap;">
                    <div style="background: rgba(16, 185, 129, 0.1); color: #10b981; padding: 10px 15px; border-radius: 8px; flex: 1; text-align: center;">
                        <h4 style="margin:0; font-size: 0.85rem; text-transform: uppercase;">Total Installments</h4>
                        <div style="font-size: 1.5rem; font-weight: bold;">${total}</div>
                    </div>
                    <div style="background: rgba(59, 130, 246, 0.1); color: #3b82f6; padding: 10px 15px; border-radius: 8px; flex: 1; text-align: center;">
                        <h4 style="margin:0; font-size: 0.85rem; text-transform: uppercase;">Paid</h4>
                        <div style="font-size: 1.5rem; font-weight: bold;">${paid}</div>
                    </div>
                    <div style="background: rgba(244, 63, 94, 0.1); color: #f43f5e; padding: 10px 15px; border-radius: 8px; flex: 1; text-align: center;">
                        <h4 style="margin:0; font-size: 0.85rem; text-transform: uppercase;">Due / Pending</h4>
                        <div style="font-size: 1.5rem; font-weight: bold;">${pending}</div>
                    </div>
                </div>
                
                <table class="data-table" style="font-size: 0.85rem; box-shadow: none; border: 1px solid rgba(0,0,0,0.05);">
                    <thead style="background: rgba(0,0,0,0.02);">
                        <tr>
                            <th>#</th>
                            <th>Due Date</th>
                            <th>Principal (₹)</th>
                            <th>Interest (₹)</th>
                            <th>Total (₹)</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
            `;

            installments.forEach((inst, index) => {
                let statusBadge = inst.status === 'paid' 
                    ? '<span style="color:#10b981"><i class="fa-solid fa-check"></i> Paid</span>' 
                    : '<span style="color:#f59e0b"><i class="fa-solid fa-clock"></i> Pending</span>';
                
                let actionBtn = inst.status === 'pending'
                    ? `<button class="btn-primary" onclick="collectLoanInstallment(${inst.id}, ${id})" style="padding: 2px 8px; font-size: 0.75rem;">Collect</button>`
                    : `<span style="color: #94a3b8; font-size: 0.75rem;">Paid on ${inst.paid_date}</span>`;

                html += `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${inst.due_date}</td>
                        <td>₹${inst.principal_component}</td>
                        <td>₹${inst.interest_component}</td>
                        <td><strong>₹${inst.total_amount}</strong></td>
                        <td>${statusBadge}</td>
                        <td>${actionBtn}</td>
                    </tr>
                `;
            });

            html += `</tbody></table>`;
            contentDiv.innerHTML = html;

        } catch (err) {
            contentDiv.innerHTML = `<div style="color:red; text-align:center;">Failed to load installments.</div>`;
            console.error(err);
        }
    };

    window.collectLoanInstallment = async function(installmentId, loanId) {
        if (!confirm("Confirm payment collection for this installment?")) return;
        try {
            const res = await fetch(`/api/admin/loans/${installmentId}/collect`, {
                method: 'POST',
                headers: getHeaders()
            });
            const data = await res.json();
            if (res.ok) {
                alert('Success: ' + data.message);
                // Refresh the slide down section
                document.getElementById(`loan-details-${loanId}`).style.display = 'none';
                loadLoansData();
                loadBalanceSheetData();
            } else {
                alert('Error: ' + data.message);
            }
        } catch (err) {
            console.error(err);
            alert('Failed to collect payment');
        }
    };

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
                            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 10px;">
                                <span style="font-size: 0.8rem; color:#888;">ID: #${l.id}</span>
                                <span style="font-size: 0.8rem; background: rgba(139, 92, 246, 0.1); color: #8b5cf6; padding: 2px 8px; border-radius: 12px;"><i class="fa-solid fa-calendar-day"></i> ${l.draw_date}</span>
                            </div>
                            <h3 style="margin: 0 0 5px 0; font-size: 1.1rem; color:#333;">${winnerName} <i class="fa-solid fa-trophy" style="color: #f59e0b; font-size: 0.9rem;"></i></h3>
                            <p style="margin: 0; color:#666; font-size: 0.9rem;">Committee: <strong>${commName}</strong></p>
                        </div>
                    `;
                });
            } else {
                container.innerHTML = `<div style="grid-column: 1/-1; text-align: center; padding: 40px; color: #888; background: white; border-radius: 12px;">No lottery winners yet.</div>`;
            }
        } catch (err) { console.error(err); }
    }

    async function loadPayoutsData() {
        try {
            const res = await fetch('/api/admin/payouts', { headers: getHeaders() });
            const data = await res.json();
            const tbody = document.getElementById('payouts-tbody');
            tbody.innerHTML = '';
            if(Array.isArray(data)) {
                data.forEach(p => {
                    const userName = p.user ? p.user.name : p.user_id;
                    const commName = p.committee ? p.committee.name : p.committee_id;
                    const actionBtn = p.status === 'pending' ? `<button class="btn-primary" onclick="payPayout(${p.id})"><i class="fa-solid fa-building-columns"></i> Pay to Bank</button>` : `<span class="badge" style="background:#10b981"><i class="fa-solid fa-check"></i> Paid on ${p.paid_date}</span>`;
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
                            <td><span style="color:#10b981"><i class="fa-solid fa-circle text-sm"></i> ${c.status}</span></td>
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
                            <td style="color:#10b981;">₹${m.total_deposited}</td>
                            <td style="color:#f43f5e;">₹${m.total_due}</td>
                            <td>₹${m.total_expected}</td>
                            <td>${m.installments_remaining} left (of ${m.total_installments})</td>
                        </tr>
                    `;
                });
            }
        } catch (err) { console.error(err); }
    }

    // ----- CHARTS (Chart.js) -----
    function renderCharts() {
        const ctxLine = document.getElementById('lineChart').getContext('2d');
        if (lineChartInstance) lineChartInstance.destroy();
        lineChartInstance = new Chart(ctxLine, {
            type: 'line',
            data: {
                labels: ['03-03', '03-09', '03-10', '03-11', '03-12', '03-13', '03-14'],
                datasets: [
                    { label: 'Total Collected', data: [100, 150, 175, 160, 200, 180, 240], borderColor: '#a855f7', backgroundColor: 'rgba(168, 85, 247, 0.1)', tension: 0.4, fill: true },
                    { label: 'Online Users', data: [40, 70, 100, 95, 120, 110, 180], borderColor: '#0ea5e9', tension: 0.4 }
                ]
            },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'top', align: 'end' } }, scales: { y: { beginAtZero: true, grid: { borderDash: [5, 5] } }, x: { grid: { display: false } } } }
        });

        const ctxDoughnut = document.getElementById('doughnutChart').getContext('2d');
        if (doughnutChartInstance) doughnutChartInstance.destroy();
        doughnutChartInstance = new Chart(ctxDoughnut, {
            type: 'doughnut',
            data: { labels: ['Active', 'Completed', 'Defaulted'], datasets: [{ data: [68, 20, 12], backgroundColor: ['#22d3ee', '#fbbf24', '#f43f5e'], borderWidth: 0, hoverOffset: 4 }] },
            options: { responsive: true, maintainAspectRatio: false, cutout: '70%', plugins: { legend: { position: 'bottom' } } }
        });
    }

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
                commAssetsBody.innerHTML += `<tr><td>${a.name}</td><td style="text-align:right;">₹${parseFloat(a.balance).toFixed(2)}</td></tr>`;
            });
            
            const commLiabBody = document.getElementById('bs-comm-liabilities-tbody');
            commLiabBody.innerHTML = '';
            data.committee.liabilities.forEach(l => {
                commLiabBody.innerHTML += `<tr><td>${l.name}</td><td style="text-align:right;">₹${parseFloat(l.balance).toFixed(2)}</td></tr>`;
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
                loanAssetsBody.innerHTML += `<tr><td>${a.name}</td><td style="text-align:right;">₹${parseFloat(a.balance).toFixed(2)}</td></tr>`;
            });
            
            const loanLiabBody = document.getElementById('bs-loan-liabilities-tbody');
            loanLiabBody.innerHTML = '';
            data.loan.liabilities.forEach(l => {
                loanLiabBody.innerHTML += `<tr><td>${l.name}</td><td style="text-align:right;">₹${parseFloat(l.balance).toFixed(2)}</td></tr>`;
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
                                <td><strong>${b.user_name}</strong><br><small style="color:#888;">ID: #${b.id} | ${b.interest_rate}</small></td>
                                <td>₹${parseFloat(b.principal).toFixed(2)}</td>
                                <td style="color:#10b981; font-weight:bold;">₹${parseFloat(b.total_expected_interest).toFixed(2)}</td>
                                <td style="color:#8b5cf6;">₹${parseFloat(b.recovered_interest).toFixed(2)}</td>
                                <td>₹${parseFloat(b.total_recovered).toFixed(2)} / <span style="color:#888;">₹${parseFloat(b.total_expected_return).toFixed(2)}</span></td>
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
                                <td style="color:#10b981; font-weight:bold;">₹${parseFloat(c.amount_collected).toFixed(2)}</td>
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
                                <td style="color:#10b981; font-weight:bold;">₹${parseFloat(c.amount_collected).toFixed(2)}</td>
                                <td>${new Date(c.collected_at).toLocaleDateString()}</td>
                                <td>
                                    <button class="btn-primary" style="padding: 5px 10px; font-size: 0.8rem;" onclick="approveCollection(${c.id})">Approve</button>
                                </td>
                            </tr>
                        `;
                    }
                });
            }

            if(!hasComm) commBody.innerHTML = '<tr><td colspan="7" style="text-align:center;">No pending committee collections.</td></tr>';
            if(!hasLoan) loanBody.innerHTML = '<tr><td colspan="7" style="text-align:center;">No pending loan collections.</td></tr>';

        } catch(err) { 
            console.error(err); 
            const commBody = document.getElementById('agent-pending-committee-collections-tbody');
            if (commBody) {
                commBody.innerHTML = `<tr><td colspan="7" style="color:red; text-align:center;">Error: ${err.message || 'Unknown Error'}</td></tr>`;
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

});
