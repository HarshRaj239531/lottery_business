<!-- Terms & Conditions View -->
<div id="view-terms" class="view-section" style="display:none;">
    <div class="header-action">
        <div>
            <h2>Terms & Conditions</h2>
            <p>Manage the dynamic Terms & Conditions agreement displayed to members in the mobile application.</p>
        </div>
        <button class="btn-primary" onclick="submitTermsForm(event)"><i class="fa-solid fa-save"></i> Save Contract Changes</button>
    </div>

    <div class="panel-card" style="max-width: 800px; margin: 0 auto 24px auto;">
        <div class="panel-card-header" style="margin-bottom:20px;">
            <h3 style="font-size: 1.1rem; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-file-contract" style="color: var(--primary);"></i>
                Contract Agreement Form
            </h3>
        </div>
        <form id="terms-form" class="flex-column gap-20">
            <div class="input-group">
                <label for="terms-title">Document Title</label>
                <div class="input-field">
                    <input type="text" id="terms-title" required placeholder="Terms & Conditions">
                </div>
            </div>
            <div class="input-group" style="margin-top: 16px;">
                <label for="terms-content">Document Content</label>
                <textarea id="terms-content" style="width: 100%; min-height: 350px; border-radius: 12px; border: 1px solid #e5e7eb; padding: 16px; font-family: 'Outfit', sans-serif; font-size: 0.95rem; line-height: 1.5; color: #111827; background: #f3f4f6; outline: none; transition: all 0.3s; box-sizing: border-box;" onfocus="this.style.borderColor='var(--primary)'; this.style.backgroundColor='#fff';" onblur="this.style.borderColor='#e5e7eb'; this.style.backgroundColor='#f3f4f6';" required placeholder="Enter contract body text..."></textarea>
            </div>
            <div id="terms-success" class="success-msg" style="display:none; background: #ecfdf5; border: 1px solid #a7f3d0; color: #065f46; padding: 12px; border-radius: 8px; font-size: 0.9rem; text-align: center; margin-top: 16px;">Terms & Conditions updated successfully!</div>
            <div id="terms-error" class="error-msg" style="display:none; background: #fef2f2; border: 1px solid #fca5a5; color: #991b1b; padding: 12px; border-radius: 8px; font-size: 0.9rem; text-align: center; margin-top: 16px;"></div>
        </form>
    </div>
</div>
