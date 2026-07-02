<!-- Payments / Payouts View -->
<div id="view-payments" class="view-section" style="display:none;">
    <div class="header-action">
        <div>
            <h2>Payments & Disbursements</h2>
            <p>Approve draw payout settlements, view history of bank clearances and field collection vouchers.</p>
        </div>
    </div>

    <!-- Payouts Table -->
    <div class="panel-card">
        <div class="panel-card-header">
            <h3>Pending Settlements (Payouts)</h3>
        </div>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Member</th>
                        <th>Committee</th>
                        <th>Total Deposits</th>
                        <th>Return Amount</th>
                        <th>Total Payout</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="payments-payouts-tbody">
                    <!-- Populated dynamically by admin.js -->
                </tbody>
            </table>
        </div>
    </div>
</div>
