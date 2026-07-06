<!-- Materials & Stock View -->
<div id="view-materials" class="view-section" style="display:none;">
    <div class="header-action">
        <h2>Materials Management</h2>
        <button class="btn-primary" onclick="openModal('create-material')"><i class="fa-solid fa-plus"></i> New Material</button>
    </div>
    
    <div class="glass-panel p-4" style="padding: 20px; margin-bottom: 30px;">
        <table class="data-table" id="materials-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Price (₹)</th>
                    <th>Unit</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="materials-tbody">
                <!-- Data injected via JS -->
            </tbody>
        </table>
    </div>

    <div class="header-action">
        <h2>Stock Transactions Management</h2>
        <button class="btn-primary" onclick="openModal('create-material-stock')"><i class="fa-solid fa-plus"></i> New Stock Transaction</button>
    </div>
    
    <div class="glass-panel p-4" style="padding: 20px;">
        <table class="data-table" id="material-stocks-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>User</th>
                    <th>Material</th>
                    <th>Title</th>
                    <th>Amount (₹)</th>
                    <th>Type</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="material-stocks-tbody">
                <!-- Data injected via JS -->
            </tbody>
        </table>
    </div>
</div>
