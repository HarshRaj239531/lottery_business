
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
            } else if (entity === 'assign-agent-target') {
                endpoint = '/api/admin/agents/targets';
                bodyData = {
                    agent_id: document.getElementById('target_agent_id').value,
                    target_type: document.getElementById('target_type').value,
                    target_value: document.getElementById('target_value').value,
                    start_date: document.getElementById('target_start').value,
                    end_date: document.getElementById('target_end').value,
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
            } else if (entity === 'materials') {
                endpoint = '/api/admin/materials';
                bodyData = {
                    name: document.getElementById('mat_name').value,
                    price: document.getElementById('mat_price').value,
                    unit: document.getElementById('mat_unit').value,
                    image_url: document.getElementById('mat_image_url').value || null,
                    status: document.getElementById('mat_status').value
                };
            } else if (entity === 'edit-material') {
                endpoint = `/api/admin/materials/${id}`;
                bodyData = {
                    name: document.getElementById('emat_name').value,
                    price: document.getElementById('emat_price').value,
                    unit: document.getElementById('emat_unit').value,
                    image_url: document.getElementById('emat_image_url').value || null,
                    status: document.getElementById('emat_status').value
                };
            } else if (entity === 'material-stocks') {
                endpoint = '/api/admin/material-stocks';
                bodyData = {
                    material_id: document.getElementById('stock_mat_id').value || null,
                    user_id: document.getElementById('stock_user_id').value,
                    title: document.getElementById('stock_title').value,
                    amount: document.getElementById('stock_amount').value,
                    type: document.getElementById('stock_type').value,
                    status: document.getElementById('stock_status').value
                };
            } else if (entity === 'edit-material-stock') {
                endpoint = `/api/admin/material-stocks/${id}`;
                bodyData = {
                    material_id: document.getElementById('estock_mat_id').value || null,
                    user_id: document.getElementById('estock_user_id').value,
                    title: document.getElementById('estock_title').value,
                    amount: document.getElementById('estock_amount').value,
                    type: document.getElementById('estock_type').value,
                    status: document.getElementById('estock_status').value
                };
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
            if (entity === 'edit-committee' || entity === 'edit-material' || entity === 'edit-material-stock') options.method = 'PUT';

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