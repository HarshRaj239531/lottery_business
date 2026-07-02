                <!-- Agent Management View -->
                <div id="view-agents" class="view-section" style="display:none;">
                    <div class="header-action">
                        <h2>Agent Management & Collections</h2>
                    </div>

                    <!-- Committee Pending Collections -->
                    <h3 style="margin-top: 20px; margin-bottom: 15px; color: #f59e0b;"><i class="fa-solid fa-users-rectangle"></i> Pending Committee Collections</h3>
                    <div class="glass-panel" style="padding: 10px; overflow-x: auto; margin-bottom: 30px;">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Agent</th>
                                    <th>Member</th>
                                    <th>Committee Details</th>
                                    <th>Amount (₹)</th>
                                    <th>Collected At</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="agent-pending-committee-collections-tbody">
                                <!-- Pending committee collections will be loaded here -->
                            </tbody>
                        </table>
                    </div>

                    <!-- Loan Pending Collections -->
                    <h3 style="margin-top: 20px; margin-bottom: 15px; color: #10b981;"><i class="fa-solid fa-hand-holding-dollar"></i> Pending Loan Collections</h3>
                    <div class="glass-panel" style="padding: 10px; overflow-x: auto; margin-bottom: 30px;">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Agent</th>
                                    <th>Member</th>
                                    <th>Loan Details</th>
                                    <th>Amount (₹)</th>
                                    <th>Collected At</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="agent-pending-loan-collections-tbody">
                                <!-- Pending loan collections will be loaded here -->
                            </tbody>
                        </table>
                    </div>
                </div>
