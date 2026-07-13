<!-- KYC Verification View -->
<div id="view-kyc" class="view-section" style="display:none;">
    
    <!-- Header with uppercase tracking category (Dawadukkan Design) -->
    <div style="margin-bottom: 28px;">
        <p style="font-size: 0.7rem; font-weight: 700; letter-spacing: 0.25em; text-transform: uppercase; margin-bottom: 6px; color: var(--primary);">
            Compliance & Safety
        </p>
        <div class="flex-row" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
            <h2 style="font-size: 1.8rem; font-weight: 700; color: #0f172a; letter-spacing: -0.02em; margin: 0;">KYC Registry</h2>
            <div class="header-btns" style="display: flex; gap: 12px;">
                <button class="btn-secondary" onclick="alert('Viewing verification guidelines...')">View Guidelines</button>
            </div>
        </div>
        <p style="font-size: 0.875rem; color: var(--text-muted); margin-top: 6px;">Identity validation and document verification registry.</p>
    </div>

    <!-- Main Layout Grid (2-1 column layout) -->
    <div class="layout-grid-2-1">
        
        <!-- Left Column: Verification Form -->
        <div class="flex-column" style="gap: 20px;">
            <div class="panel-card" style="margin-bottom:0;">
                <div class="panel-card-header" style="flex-direction: column; align-items: flex-start; gap: 4px; border-bottom: 1px solid var(--border-color); padding-bottom: 16px; margin-bottom: 20px;">
                    <h3 style="font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.05em; color: #374151;">Submit Verification Documents</h3>
                    <span style="font-size: 0.75rem; color: var(--text-muted);">Fill in identification documents to confirm the identity profile.</span>
                </div>

                <form id="kyc-submit-form" onsubmit="submitKycForm(event)">
                    <!-- Choose User Dropdown -->
                    <div class="input-group" style="margin-bottom: 20px;">
                        <label style="font-size: 0.8rem; font-weight: 600; color: var(--text-main); display: block; margin-bottom: 8px;">Select User Profile</label>
                        <select id="kyc_user_select" class="filter-select" style="width:100%; border: 1px solid #d1d5db; height: 42px; border-radius: 8px;" required>
                            <option value="">-- Choose Member / Agent --</option>
                            <!-- Populated dynamically via javascript -->
                        </select>
                    </div>

                    <!-- Role type card selectors -->
                    <div style="margin-bottom: 20px;">
                        <label style="font-size: 0.8rem; font-weight: 600; color: var(--text-main); display: block; margin-bottom: 8px;">Entity Role Type</label>
                        <div class="role-select-box active" onclick="selectKycRole('member', this)">
                            <input type="radio" name="kyc_role" id="role-member" value="member" checked>
                            <div>
                                <strong style="font-size: 0.85rem; color: var(--text-main);">Member Account</strong>
                                <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 2px;">Standard subscriber/depositor profile verification.</p>
                            </div>
                        </div>
                        <div class="role-select-box" onclick="selectKycRole('agent', this)">
                            <input type="radio" name="kyc_role" id="role-agent" value="agent">
                            <div>
                                <strong style="font-size: 0.85rem; color: var(--text-main);">Agent / Partner</strong>
                                <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 2px;">Field collector operations and target management profile.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Document Category Select -->
                    <div class="input-group" style="margin-bottom: 20px;">
                        <label style="font-size: 0.8rem; font-weight: 600; color: var(--text-main); display: block; margin-bottom: 8px;">Document Category</label>
                        <select id="kyc_doc_type" class="filter-select" style="width:100%; border: 1px solid #d1d5db; height: 42px; border-radius: 8px;" required>
                            <option value="aadhar">Aadhar Card (UIDAI)</option>
                            <option value="pan">PAN Card (Income Tax Dept)</option>
                            <option value="passport">Passport Booklet</option>
                            <option value="voter_id">Voter Identity Card</option>
                        </select>
                    </div>

                    <!-- Warnings alert banner -->
                    <div style="background-color: var(--warning-bg); border-left: 4px solid var(--warning); padding: 14px; border-radius: 8px; display: flex; gap: 10px; align-items: flex-start; margin-bottom: 20px;">
                        <i class="fa-solid fa-triangle-exclamation" style="color: var(--warning); margin-top: 3px;"></i>
                        <span style="font-size: 0.75rem; color: #92400e; line-height: 1.4;">Ensure all documents are original scans. Digital screenshots or black-and-white copies may be rejected by the automated system.</span>
                    </div>

                    <!-- File upload zones -->
                    <div class="upload-zone-wrapper">
                        <div class="upload-zone" onclick="document.getElementById('kyc_file_front').click()">
                            <i class="fa-solid fa-cloud-arrow-up"></i>
                            <span style="font-size: 0.8rem; font-weight: 600; color: var(--text-main);">Front Side Scan</span>
                            <span style="font-size: 0.65rem; color: var(--text-muted);">Click to upload front scan</span>
                            <input type="file" id="kyc_file_front" style="display:none;" accept="image/*" onchange="handleKycFileChange(this, 'front')">
                        </div>
                        <div class="upload-zone" onclick="document.getElementById('kyc_file_back').click()">
                            <i class="fa-solid fa-cloud-arrow-up"></i>
                            <span style="font-size: 0.8rem; font-weight: 600; color: var(--text-main);">Back Side Scan</span>
                            <span style="font-size: 0.65rem; color: var(--text-muted);">Click to upload back scan (Optional)</span>
                            <input type="file" id="kyc_file_back" style="display:none;" accept="image/*" onchange="handleKycFileChange(this, 'back')">
                        </div>
                    </div>

                    <!-- Submit Action Button -->
                    <button type="submit" class="btn-primary" style="width:100%; border-radius: 8px; margin-top: 24px; height: 42px;">Submit Verification Profile</button>
                </form>
            </div>
        </div>

        <!-- Right Column: Dynamic KYC Verification Queue -->
        <div class="flex-column" style="gap: 20px;">
            <div class="panel-card" style="margin-bottom:0; display:flex; flex-direction:column; gap:16px;">
                <div class="panel-card-header" style="flex-direction: column; align-items: flex-start; gap: 4px; border-bottom: 1px solid var(--border-color); padding-bottom: 16px; margin-bottom: 10px; width:100%;">
                    <h3 style="font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.05em; color: #374151;"><i class="fa-solid fa-user-shield" style="margin-right:6px; color:var(--primary);"></i> KYC Verification Center</h3>
                    <span style="font-size: 0.75rem; color: var(--text-muted);">Review and approve uploaded identification documents.</span>
                </div>
                
                <div id="kyc-review-list" style="display:flex; flex-direction:column; gap:12px; max-height: 550px; overflow-y: auto; padding-right:5px;">
                    <div style="text-align:center; padding: 40px; color:var(--text-muted);">
                        <i class="fa-solid fa-spinner fa-spin" style="margin-right: 8px;"></i> Loading verification queue...
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
    function selectKycRole(role, element) {
        document.querySelectorAll('.role-select-box').forEach(el => el.classList.remove('active'));
        element.classList.add('active');
        const memberRadio = document.getElementById('role-member');
        const agentRadio = document.getElementById('role-agent');
        if (role === 'member') {
            memberRadio.checked = true;
        } else {
            agentRadio.checked = true;
        }
    }

    function handleKycFileChange(input, type) {
        if (input.files && input.files[0]) {
            const parent = input.parentElement;
            parent.classList.add('uploaded');
            const icon = parent.querySelector('i');
            if (icon) {
                icon.className = 'fa-solid fa-circle-check';
            }
            const subtitle = parent.querySelector('span:nth-of-type(2)');
            if (subtitle) {
                subtitle.textContent = input.files[0].name + ' (Uploaded)';
            }
        }
    }

    async function submitKycForm(event) {
        event.preventDefault();
        const userId = document.getElementById('kyc_user_select').value;
        const docType = document.getElementById('kyc_doc_type').value;
        const frontFile = document.getElementById('kyc_file_front').files[0];
        const backFile = document.getElementById('kyc_file_back').files[0];

        if (!userId) {
            alert('Please select a member profile first.');
            return;
        }
        if (!frontFile) {
            alert('Please upload front side scan document.');
            return;
        }

        const formData = new FormData();
        formData.append('user_id', userId);
        formData.append('document_type', docType);
        formData.append('front_image', frontFile);
        if (backFile) {
            formData.append('back_image', backFile);
        }

        try {
            const res = await fetch('/api/admin/kyc/submit', {
                method: 'POST',
                headers: {
                    'Authorization': 'Bearer ' + localStorage.getItem('admin_token'),
                    'Accept': 'application/json'
                },
                body: formData
            });
            const data = await res.json();
            if (data.status === true || data.status === 'success') {
                alert(data.message || 'KYC Documents submitted successfully!');
                // Reset form
                document.getElementById('kyc-submit-form').reset();
                document.querySelectorAll('.upload-zone').forEach(z => {
                    z.classList.remove('uploaded');
                    const icon = z.querySelector('i');
                    if (icon) icon.className = 'fa-solid fa-cloud-arrow-up';
                });
                const subFront = document.querySelector('.upload-zone:nth-of-type(1) span:nth-of-type(2)');
                if (subFront) subFront.textContent = 'Click to upload front scan';
                const subBack = document.querySelector('.upload-zone:nth-of-type(2) span:nth-of-type(2)');
                if (subBack) subBack.textContent = 'Click to upload back scan (Optional)';
                
                // Dynamically reload KYC list/dropdown
                if (typeof loadKycData === 'function') {
                    loadKycData();
                }
            } else {
                alert(data.message || 'Failed to submit KYC documents.');
            }
        } catch (e) {
            console.error(e);
            alert('Failed: ' + e.message);
        }
    }
</script>
