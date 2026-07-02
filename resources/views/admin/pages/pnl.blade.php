                <!-- P&L View -->
                <div id="view-pnl" class="view-section" style="display:none;">
                    <div class="header-action">
                        <h2>Profit & Loss Statement</h2>
                    </div>
                    <div class="stats-grid mb-4">
                        <div class="stat-card bg-gradient-cyan">
                            <div class="stat-details">
                                <h3 id="pnl-total-revenue">₹0</h3>
                                <p>Total Revenue (Collections)</p>
                            </div>
                        </div>
                        <div class="stat-card bg-gradient-pink">
                            <div class="stat-details">
                                <h3 id="pnl-total-expense">₹0</h3>
                                <p>Total Expenses (Payouts)</p>
                            </div>
                        </div>
                        <div class="stat-card bg-gradient-purple">
                            <div class="stat-details">
                                <h3 id="pnl-net-profit">₹0</h3>
                                <p>Net Profit</p>
                            </div>
                        </div>
                    </div>
                    <div class="glass-panel p-4" style="padding: 20px;">
                        <div style="display: flex; gap: 20px;">
                            <div style="flex: 1;">
                                <h3 style="color:#10b981; margin-bottom: 15px;">Revenue Accounts</h3>
                                <table class="data-table">
                                    <tbody id="pnl-revenue-tbody"></tbody>
                                </table>
                            </div>
                            <div style="flex: 1;">
                                <h3 style="color:#ef4444; margin-bottom: 15px;">Expense Accounts</h3>
                                <table class="data-table">
                                    <tbody id="pnl-expense-tbody"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
