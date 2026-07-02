                <!-- Installments View -->
                <div id="view-installments" class="view-section" style="display:none;">
                    <div class="header-action">
                        <h2>Installments Collection</h2>
                        <div>
                            <button class="btn-secondary" style="margin-right: 10px; background: rgba(34, 197, 94, 0.2); color: #22c55e; border: 1px solid rgba(34, 197, 94, 0.5);" onclick="sendPaymentReminders()"><i class="fa-solid fa-clock"></i> Send Payment Reminders</button>
                            <button class="btn-secondary" style="margin-right: 10px; background: rgba(244, 63, 94, 0.2); color: #f43f5e; border: 1px solid rgba(244, 63, 94, 0.5);" onclick="sendDueWarnings()"><i class="fa-solid fa-triangle-exclamation"></i> Send Due Warnings</button>
                            <button class="btn-primary" onclick="openModal('collect-installment')"><i class="fa-solid fa-hand-holding-dollar"></i> Collect Payment</button>
                        </div>
                    </div>
                    <div class="tab-controls" style="display: flex; gap: 10px; margin-bottom: 20px;">
                        <button id="tab-btn-committee-installments" class="btn-primary" onclick="switchInstallmentTab('committee')" style="flex: 1; padding: 12px; font-size: 1.1rem; background: #a855f7; border: none; transition: 0.3s; opacity: 1;">
                            <i class="fa-solid fa-users-viewfinder"></i> Committee Installments
                        </button>
                        <button id="tab-btn-loan-installments" class="btn-secondary" onclick="switchInstallmentTab('loan')" style="flex: 1; padding: 12px; font-size: 1.1rem; background: rgba(14, 165, 233, 0.1); color: #0ea5e9; border: 1px solid #0ea5e9; transition: 0.3s; opacity: 0.7;">
                            <i class="fa-solid fa-money-check-dollar"></i> Loan Installments
                        </button>
                    </div>

                    <!-- Committee Installments Table -->
                    <div id="tab-content-committee-installments" class="glass-panel" style="padding: 20px; overflow-x: auto; margin-bottom: 30px; display: block;">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Member Name</th>
                                    <th>Committee</th>
                                    <th>Amount</th>
                                    <th>Paid Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody id="installments-tbody"></tbody>
                        </table>
                    </div>

                    <!-- Loan Installments Table -->
                    <div id="tab-content-loan-installments" class="glass-panel" style="padding: 20px; overflow-x: auto; margin-bottom: 30px; display: none;">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Member Name</th>
                                    <th>Loan ID</th>
                                    <th>Amount</th>
                                    <th>Paid Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody id="loan-installments-tbody"></tbody>
                        </table>
                    </div>
                </div>

                <script>
                    function switchInstallmentTab(tab) {
                        const commContent = document.getElementById('tab-content-committee-installments');
                        const loanContent = document.getElementById('tab-content-loan-installments');
                        const commBtn = document.getElementById('tab-btn-committee-installments');
                        const loanBtn = document.getElementById('tab-btn-loan-installments');

                        if(tab === 'committee') {
                            commContent.style.display = 'block';
                            loanContent.style.display = 'none';
                            
                            commBtn.className = 'btn-primary';
                            commBtn.style.background = '#a855f7';
                            commBtn.style.color = 'white';
                            commBtn.style.border = 'none';
                            commBtn.style.opacity = '1';

                            loanBtn.className = 'btn-secondary';
                            loanBtn.style.background = 'rgba(14, 165, 233, 0.1)';
                            loanBtn.style.color = '#0ea5e9';
                            loanBtn.style.border = '1px solid #0ea5e9';
                            loanBtn.style.opacity = '0.7';
                        } else {
                            commContent.style.display = 'none';
                            loanContent.style.display = 'block';
                            
                            loanBtn.className = 'btn-primary';
                            loanBtn.style.background = '#0ea5e9';
                            loanBtn.style.color = 'white';
                            loanBtn.style.border = 'none';
                            loanBtn.style.opacity = '1';

                            commBtn.className = 'btn-secondary';
                            commBtn.style.background = 'rgba(168, 85, 247, 0.1)';
                            commBtn.style.color = '#a855f7';
                            commBtn.style.border = '1px solid #a855f7';
                            commBtn.style.opacity = '0.7';
                        }
                    }
                </script>

                <!-- Lotteries View -->
                <section id="view-lotteries" class="content-section view-section" style="display:none;">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
                        <h2>Lottery Winners</h2>
                    </div>
                    <div id="lotteries-container" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;">
                        <!-- Cards will be injected here by admin.js -->
                    </div>
                </section>
