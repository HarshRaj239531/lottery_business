<!-- Payments / Payouts View -->
<div id="view-payments" class="view-section" style="display:none;">
    <div class="header-action">
        <div>
            <h2>Payments & Disbursements</h2>
            <p>Approve draw payout settlements, view history of bank clearances and field collection vouchers.</p>
        </div>
    </div>

    <!-- Payment Gateway Settings -->
    <div class="panel-card" style="margin-bottom: 24px;">
        <div class="panel-card-header">
            <h3><i class="fa-solid fa-qrcode" style="color: var(--primary);"></i> Payment Gateway Settings</h3>
        </div>
        <div style="padding: 20px;">
            <form id="payment-settings-form" enctype="multipart/form-data">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div>
                        <div class="input-group" style="margin-bottom: 12px;">
                            <label>Admin WhatsApp Phone Number</label>
                            <div class="input-field" style="background: #f3f4f6; border: 1px solid #e5e7eb; border-radius: 12px; padding: 12px 16px; display: flex; align-items: center; gap: 12px;">
                                <i class="fa-brands fa-whatsapp" style="color: #25d366;"></i>
                                <input type="text" name="admin_phone" id="pay-setting-phone" placeholder="+919999999999" style="border: none; background: transparent; outline: none; width: 100%;">
                            </div>
                        </div>
                        <div class="input-group" style="margin-top: 15px; margin-bottom: 12px;">
                            <label>Upload Payment QR Code Screenshot</label>
                            <div class="input-field" style="background: #f3f4f6; border: 1px solid #e5e7eb; border-radius: 12px; padding: 12px 16px; display: flex; align-items: center; gap: 12px;">
                                <i class="fa-solid fa-upload" style="color: #9ca3af;"></i>
                                <input type="file" name="qr_code" id="pay-setting-qrcode" accept="image/*" style="border: none; background: transparent; outline: none; width: 100%;">
                            </div>
                        </div>
                        <button type="submit" class="btn-primary" style="margin-top: 20px; width: 100%; display: flex; align-items: center; justify-content: center; gap: 8px;">
                            <i class="fa-solid fa-save"></i> Save Payment Settings
                        </button>
                    </div>
                    <div style="text-align: center; border-left: 1px solid #e5e7eb; padding-left: 20px; display: flex; flex-direction: column; align-items: center; justify-content: center;">
                        <h4 style="margin: 0 0 10px 0; color: #64748b; font-size: 0.9rem;">Current QR Code</h4>
                        <div id="pay-setting-qr-preview" style="border: 1px dashed #cbd5e1; border-radius: 12px; padding: 10px; width: 150px; height: 150px; display: flex; align-items: center; justify-content: center; overflow: hidden; background: #f8fafc;">
                            <span style="color: #94a3b8; font-size: 0.85rem;">No QR Code Set</span>
                        </div>
                    </div>
                </div>
            </form>
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
