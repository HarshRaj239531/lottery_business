                <!-- Loans View -->
                <div id="view-loans" class="view-section" style="display:none;">
                    <div class="header-action">
                        <h2>Loans Management</h2>
                        <button class="btn-primary" onclick="openModal('create-loan')"><i class="fa-solid fa-plus"></i> Create Loan</button>
                    </div>
                    <div class="glass-panel p-4" style="padding: 20px;">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>User Name</th>
                                    <th>Principal (₹)</th>
                                    <th>Interest (%)</th>
                                    <th>Duration</th>
                                    <th>Freq</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="loans-tbody"></tbody>
                        </table>
                    </div>
                </div>
