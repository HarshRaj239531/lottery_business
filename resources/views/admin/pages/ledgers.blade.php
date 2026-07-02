                <!-- Member Ledger View -->
                <div id="view-member-ledger" class="view-section" style="display:none;">
                    <div class="header-action">
                        <h2>Member Ledger</h2>
                    </div>
                    <div class="glass-panel p-4" style="padding: 20px;">
                        <div style="display: flex; gap: 10px; margin-bottom: 20px;">
                            <input type="number" id="ledger-member-id" placeholder="Enter Member ID" style="padding: 10px; border-radius: 5px; border: 1px solid #334155; background: #1e293b; color: white;">
                            <button id="btn-load-member-ledger" class="btn-primary">View Ledger</button>
                        </div>
                        <h3 id="ledger-member-name" style="margin-bottom: 15px; color: #818cf8;"></h3>
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Description</th>
                                    <th>Account</th>
                                    <th style="color:#10b981;">Debit (₹)</th>
                                    <th style="color:#ef4444;">Credit (₹)</th>
                                </tr>
                            </thead>
                            <tbody id="member-ledger-tbody"></tbody>
                        </table>
                    </div>
                </div>

                <!-- Committee Ledger View -->
                <div id="view-committee-ledger" class="view-section" style="display:none;">
                    <div class="header-action">
                        <h2>Committee Ledger</h2>
                    </div>
                    <div class="glass-panel p-4" style="padding: 20px;">
                        <div style="display: flex; gap: 10px; margin-bottom: 20px;">
                            <input type="number" id="ledger-committee-id" placeholder="Enter Committee ID" style="padding: 10px; border-radius: 5px; border: 1px solid #334155; background: #1e293b; color: white;">
                            <button id="btn-load-committee-ledger" class="btn-primary">View Ledger</button>
                        </div>
                        <h3 id="ledger-committee-name" style="margin-bottom: 15px; color: #818cf8;"></h3>
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Description</th>
                                    <th>Account</th>
                                    <th style="color:#10b981;">Debit (₹)</th>
                                    <th style="color:#ef4444;">Credit (₹)</th>
                                </tr>
                            </thead>
                            <tbody id="committee-ledger-tbody"></tbody>
                        </table>
                    </div>
                </div>
