                <!-- Balance Sheet View -->
                <div id="view-balance-sheet" class="view-section" style="display:none;">
                    <div class="header-action">
                        <h2>Balance Sheet</h2>
                    </div>
                    
                    <!-- COMMITTEE BALANCE SHEET -->
                    <h3 style="margin: 20px 0 10px; color: #8b5cf6;"><i class="fa-solid fa-users-rectangle"></i> Committee Financials</h3>
                    <div class="stats-grid mb-4">
                        <div class="stat-card bg-gradient-cyan"><div class="stat-details"><h3 id="bs-comm-assets">₹0</h3><p>Total Assets</p></div></div>
                        <div class="stat-card bg-gradient-orange"><div class="stat-details"><h3 id="bs-comm-liabilities">₹0</h3><p>Total Liabilities</p></div></div>
                    </div>
                    <div class="glass-panel p-4" style="padding: 20px; margin-bottom: 30px;">
                        <div style="display: flex; gap: 20px; flex-wrap: wrap;">
                            <div style="flex: 1; min-width:300px;"><h4 style="color:#3b82f6; margin-bottom: 10px;">Assets</h4><table class="data-table"><tbody id="bs-comm-assets-tbody"></tbody></table></div>
                            <div style="flex: 1; min-width:300px;"><h4 style="color:#f59e0b; margin-bottom: 10px;">Liabilities & Equity</h4><table class="data-table"><tbody id="bs-comm-liabilities-tbody"></tbody></table></div>
                        </div>
                    </div>

                    <!-- LOAN BALANCE SHEET -->
                    <h3 style="margin: 20px 0 10px; color: #10b981;"><i class="fa-solid fa-hand-holding-dollar"></i> Loan Financials</h3>
                    <div class="stats-grid mb-4">
                        <div class="stat-card bg-gradient-cyan" style="background: linear-gradient(135deg, #10b981, #059669);"><div class="stat-details"><h3 id="bs-loan-assets">₹0</h3><p>Total Assets</p></div></div>
                        <div class="stat-card bg-gradient-orange" style="background: linear-gradient(135deg, #f43f5e, #e11d48);"><div class="stat-details"><h3 id="bs-loan-liabilities">₹0</h3><p>Total Liabilities</p></div></div>
                    </div>
                    <div class="glass-panel p-4" style="padding: 20px; margin-bottom: 20px;">
                        <div style="display: flex; gap: 20px; flex-wrap: wrap;">
                            <div style="flex: 1; min-width:300px;"><h4 style="color:#10b981; margin-bottom: 10px;">Assets</h4><table class="data-table"><tbody id="bs-loan-assets-tbody"></tbody></table></div>
                            <div style="flex: 1; min-width:300px;"><h4 style="color:#f43f5e; margin-bottom: 10px;">Liabilities & Equity</h4><table class="data-table"><tbody id="bs-loan-liabilities-tbody"></tbody></table></div>
                        </div>
                    </div>
                    
                    <h4 style="color:#475569; margin-bottom: 10px;"><i class="fa-solid fa-chart-pie"></i> Loan Recovery & Profitability Breakdown</h4>
                    <div class="glass-panel p-4" style="padding: 20px;">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Principal Given</th>
                                    <th>Total Expected Extra (Interest)</th>
                                    <th>Extra Recovered So Far</th>
                                    <th>Total Recovery (P+I)</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody id="bs-loan-breakdown-tbody"></tbody>
                        </table>
                    </div>
                </div>
