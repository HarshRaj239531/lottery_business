                <!-- Members View -->
                <div id="view-members" class="view-section" style="display:none;">
                    <div class="header-action" style="margin-bottom: 10px;">
                        <h2>Members Management</h2>
                        <button class="btn-primary" onclick="openModal('create-member')"><i class="fa-solid fa-user-plus"></i> New Member</button>
                    </div>

                    <!-- Tabs Container -->
                    <div class="tabs-container" style="display: flex; gap: 10px; margin-bottom: 20px;">
                        <button class="btn-primary" id="tab-btn-agents" onclick="switchMemberTab('agents')"><i class="fa-solid fa-user-tie"></i> Agents</button>
                        <button class="btn-secondary" id="tab-btn-committee-members" onclick="switchMemberTab('committee')"><i class="fa-solid fa-users-rectangle"></i> Committee Members</button>
                        <button class="btn-secondary" id="tab-btn-loan-members" onclick="switchMemberTab('loan')"><i class="fa-solid fa-hand-holding-dollar"></i> Loan Members</button>
                    </div>

                    <!-- Agents Table -->
                    <div class="glass-panel p-4" id="table-agents" style="padding: 20px;">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Address</th>
                                    <th>Role</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="members-agents-tbody"></tbody>
                        </table>
                    </div>

                    <!-- Committee Members Table -->
                    <div class="glass-panel p-4" id="table-committee-members" style="padding: 20px; display: none;">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Address</th>
                                    <th>Committee Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="members-committee-tbody"></tbody>
                        </table>
                    </div>

                    <!-- Loan Members Table -->
                    <div class="glass-panel p-4" id="table-loan-members" style="padding: 20px; display: none;">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Address</th>
                                    <th>Loan Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="members-loan-tbody"></tbody>
                        </table>
                    </div>
                </div>

                <!-- Paid Members View -->
                <div id="view-paid-members" class="view-section" style="display:none;">
                    <div class="header-action">
                        <h2>Paid Members (Up-to-Date)</h2>
                        <button class="btn-secondary" onclick="window.history.back()"><i class="fa-solid fa-arrow-left"></i> Back</button>
                    </div>
                    <div class="glass-panel p-4" style="padding: 20px;">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Member ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Total Paid (₹)</th>
                                </tr>
                            </thead>
                            <tbody id="paid-members-tbody">
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Due Members View -->
                <div id="view-due-members" class="view-section" style="display:none;">
                    <div class="header-action">
                        <h2>Members with Due Amount</h2>
                        <button class="btn-secondary" onclick="window.history.back()"><i class="fa-solid fa-arrow-left"></i> Back</button>
                    </div>
                    <div class="glass-panel p-4" style="padding: 20px;">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Member ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Total Paid (₹)</th>
                                    <th>Overdue Amount (₹)</th>
                                </tr>
                            </thead>
                            <tbody id="due-members-tbody">
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Change Password Modal -->
                <div id="modal-change-password" class="modal">
                    <div class="modal-content glass-panel" style="max-width: 400px;">
                        <div class="modal-header">
                            <h3 style="margin:0;"><i class="fa-solid fa-key" style="color: #f59e0b;"></i> Change Password</h3>
                            <button class="close-btn" onclick="closeModal('change-password')"><i class="fa-solid fa-times"></i></button>
                        </div>
                        <div class="modal-body">
                            <form id="form-change-password" onsubmit="submitChangePassword(event)">
                                <input type="hidden" id="change-password-member-id">
                                <p style="margin-bottom: 15px; font-size: 0.9rem; color: #64748b;">Changing password for: <strong id="change-password-member-name"></strong></p>
                                
                                <div class="form-group">
                                    <label>New Password</label>
                                    <input type="password" id="new-password" class="form-control" required minlength="6" placeholder="Enter new password">
                                </div>
                                <div class="form-group">
                                    <label>Confirm Password</label>
                                    <input type="password" id="confirm-new-password" class="form-control" required minlength="6" placeholder="Confirm new password">
                                </div>
                                
                                <div style="margin-top:20px; display:flex; justify-content:flex-end; gap:10px;">
                                    <button type="button" class="btn-secondary" onclick="closeModal('change-password')">Cancel</button>
                                    <button type="submit" class="btn-primary" style="background:#f59e0b;">Change Password</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
