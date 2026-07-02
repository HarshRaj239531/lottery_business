<!-- Collections Overview View -->
<div id="view-collections" class="view-section" style="display:none;">
    
    <!-- Header with uppercase tracking category (Dawadukkan Design) -->
    <div style="margin-bottom: 28px;">
        <p style="font-size: 0.7rem; font-weight: 700; letter-spacing: 0.25em; text-transform: uppercase; margin-bottom: 6px; color: var(--primary);">
            Transaction Registry
        </p>
        <div class="flex-row" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
            <h2 style="font-size: 1.8rem; font-weight: 700; color: #0f172a; letter-spacing: -0.02em; margin: 0;">Collections Overview</h2>
            <div class="header-btns" style="display: flex; gap: 12px;">
                <button class="btn-secondary" onclick="alert('Filtering by Agent...')">Filter by Agent</button>
                <button class="btn-secondary" onclick="alert('Filtering by Region...')">Filter by Region</button>
                <button class="btn-primary" onclick="alert('Exporting Report...')">Export Report</button>
            </div>
        </div>
        <p style="font-size: 0.875rem; color: var(--text-muted); margin-top: 6px;">Real-time tracking of agent field collections and targets.</p>
    </div>

    <!-- Collections Metric Cards Grid (Dawadukkan Design - Value Top, Icon Right, Circle Overlays) -->
    <div class="stats-grid" style="margin-bottom: 28px;">
        <div class="stat-card relative-card">
            <div class="shape-circle-1"></div>
            <div class="shape-circle-2"></div>
            <div class="shape-circle-3"></div>
            <div style="display: flex; justify-content: space-between; align-items: center; width: 100%; position: relative; z-index: 1;">
                <div class="flex-column" style="gap: 2px; flex: 1; min-width: 0;">
                    <h2 id="coll-metric-collected" style="font-size: 1.5rem; font-weight: 700; color: #111827; line-height: 1.1;">₹1.2Cr</h2>
                    <span style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600;">Total Collected Today</span>
                    <div style="display:flex; align-items:center; gap:4px; margin-top: 4px;">
                        <i class="fa-solid fa-arrow-trend-up" style="color: var(--success); font-size: 0.7rem;"></i>
                        <span style="font-size: 0.65rem; color: var(--text-muted); font-weight: 600;">+15% vs yesterday</span>
                    </div>
                </div>
                <div class="stat-icon-wrapper" style="background-color: var(--primary-light); color: var(--primary); flex-shrink: 0;">
                    <i class="fa-solid fa-wallet"></i>
                </div>
            </div>
        </div>

        <div class="stat-card relative-card">
            <div class="shape-circle-1"></div>
            <div class="shape-circle-2"></div>
            <div class="shape-circle-3"></div>
            <div style="display: flex; justify-content: space-between; align-items: center; width: 100%; position: relative; z-index: 1;">
                <div class="flex-column" style="gap: 2px; flex: 1; min-width: 0;">
                    <h2 id="coll-metric-agents" style="font-size: 1.5rem; font-weight: 700; color: #111827; line-height: 1.1;">62</h2>
                    <span style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600;">Active Agents</span>
                    <div style="display:flex; align-items:center; gap:4px; margin-top: 4px;">
                        <span class="badge badge-success" style="padding: 1px 6px; font-size: 0.6rem; border-radius: 4px; font-weight: 700;">Live</span>
                    </div>
                </div>
                <div class="stat-icon-wrapper" style="background-color: var(--success-bg); color: var(--success); flex-shrink: 0;">
                    <i class="fa-solid fa-user-check"></i>
                </div>
            </div>
        </div>

        <div class="stat-card relative-card">
            <div class="shape-circle-1"></div>
            <div class="shape-circle-2"></div>
            <div class="shape-circle-3"></div>
            <div style="display: flex; justify-content: space-between; align-items: center; width: 100%; position: relative; z-index: 1;">
                <div class="flex-column" style="gap: 2px; flex: 1; min-width: 0;">
                    <h2 id="coll-metric-success" style="font-size: 1.5rem; font-weight: 700; color: #111827; line-height: 1.1;">94.5%</h2>
                    <span style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600;">Collection Success Rate</span>
                    <div style="display:flex; align-items:center; gap:4px; margin-top: 4px;">
                        <span class="badge badge-success" style="padding: 1px 6px; font-size: 0.6rem; border-radius: 4px; font-weight: 700;">High</span>
                    </div>
                </div>
                <div class="stat-icon-wrapper" style="background-color: #eff6ff; color: #2563eb; flex-shrink: 0;">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
            </div>
        </div>

        <div class="stat-card relative-card">
            <div class="shape-circle-1"></div>
            <div class="shape-circle-2"></div>
            <div class="shape-circle-3"></div>
            <div style="display: flex; justify-content: space-between; align-items: center; width: 100%; position: relative; z-index: 1;">
                <div class="flex-column" style="gap: 4px; flex: 1; min-width: 0;">
                    <h2 id="coll-metric-progress" style="font-size: 1.45rem; font-weight: 700; color: #111827; line-height: 1.1;">82%</h2>
                    <span style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600;">Monthly Target Progress</span>
                    <div class="progress-container" style="margin-top:4px;">
                        <div class="progress-bar-bg" style="height: 5px;">
                            <div class="progress-bar-fill" style="width: 82%;"></div>
                        </div>
                    </div>
                </div>
                <div class="stat-icon-wrapper" style="background-color: var(--warning-bg); color: var(--warning); flex-shrink: 0;">
                    <i class="fa-solid fa-chart-line"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Layout Grid (2-1 Split) -->
    <div class="layout-grid-2-1">
        
        <!-- Collection Trends Chart -->
        <div class="panel-card" style="margin-bottom:0;">
            <div class="panel-card-header">
                <h3 style="font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.05em; color: #374151;">Collection Trends (Last 7 Days)</h3>
                <span style="font-size:0.75rem; color:var(--text-muted); font-weight:600;">Values in Lakhs (₹)</span>
            </div>
            <div class="chart-wrapper">
                <canvas id="collectionsTrendChart"></canvas>
            </div>
        </div>

        <!-- Collection Methods Doughnut -->
        <div class="panel-card" style="margin-bottom:0;">
            <div class="panel-card-header">
                <h3 style="font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.05em; color: #374151;">Collection Methods</h3>
            </div>
            <div class="chart-wrapper" style="height: 180px;">
                <canvas id="collectionsMethodsChart"></canvas>
                <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center;">
                    <div style="font-size: 1.35rem; font-weight: 700; color: #0f172a; line-height: 1;">70%</div>
                    <div style="font-size: 0.6rem; font-weight: 600; color: #64748b; margin-top: 4px; letter-spacing: 0.05em;">DIGITAL</div>
                </div>
            </div>
            <div class="flex-column gap-12" style="margin-top: 28px;">
                <div style="display:flex; justify-content:space-between; align-items:center; font-size:0.8rem;">
                    <span style="display:flex; align-items:center; gap:8px; font-weight: 600; color: #374151;"><i class="fa-solid fa-circle" style="color: #004d40; font-size:0.75rem;"></i> UPI & Wallet</span>
                    <span class="font-semibold" style="color: #111827;">70%</span>
                </div>
                <div style="display:flex; justify-content:space-between; align-items:center; font-size:0.8rem;">
                    <span style="display:flex; align-items:center; gap:8px; font-weight: 600; color: #374151;"><i class="fa-solid fa-circle" style="color: #0ea5e9; font-size:0.75rem;"></i> Cash</span>
                    <span class="font-semibold" style="color: #111827;">30%</span>
                </div>
            </div>
        </div>

    </div>

    <!-- Recent Collections Database Table -->
    <div class="panel-card" style="margin-top:24px; margin-bottom:0;">
        <div class="panel-card-header" style="flex-direction: column; align-items: flex-start; gap: 4px; border-bottom: 1px solid var(--border-color); padding-bottom: 16px; margin-bottom: 20px;">
            <h3 style="font-size: 0.95rem; font-weight: 700; color: #111827; text-transform: uppercase; letter-spacing: 0.03em;">Recent Collections</h3>
            <span style="font-size: 0.75rem; color: var(--text-muted);" id="collections-table-count">Showing 4 of 128 collections today</span>
        </div>
        
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Agent Name</th>
                        <th>Member Name</th>
                        <th>Amount</th>
                        <th>Method</th>
                        <th>Time</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody id="collections-table-tbody">
                    <!-- Populated dynamically by admin.js; Fallbacks match Figma -->
                    <tr>
                        <td>
                            <div class="user-avatar-group">
                                <div style="display:flex; width: 32px; height: 32px; align-items:center; justify-content:center; border-radius: 6px; background-color: var(--primary-light); color: var(--primary); flex-shrink: 0;">
                                    <i class="fa-solid fa-file-invoice" style="font-size:0.85rem;"></i>
                                </div>
                                <span class="user-detail-name" style="margin-left:8px;">Arun Kumar</span>
                            </div>
                        </td>
                        <td>Ramesh Sharma</td>
                        <td class="font-semibold">₹45,000</td>
                        <td><span class="badge badge-neutral">UPI</span></td>
                        <td>10:45 AM</td>
                        <td><span class="badge badge-success">Success</span></td>
                    </tr>
                    <tr>
                        <td>
                            <div class="user-avatar-group">
                                <div style="display:flex; width: 32px; height: 32px; align-items:center; justify-content:center; border-radius: 6px; background-color: var(--primary-light); color: var(--primary); flex-shrink: 0;">
                                    <i class="fa-solid fa-file-invoice" style="font-size:0.85rem;"></i>
                                </div>
                                <span class="user-detail-name" style="margin-left:8px;">Priya Singh</span>
                            </div>
                        </td>
                        <td>Sunita Devi</td>
                        <td class="font-semibold">₹12,500</td>
                        <td><span class="badge badge-neutral">Cash</span></td>
                        <td>10:42 AM</td>
                        <td>
                            <div style="display:flex; gap:6px;">
                                <button class="btn-primary" style="padding:4px 8px; font-size:0.75rem; background-color: var(--accent); border-radius: 4px;" onclick="approveCollectionFromOverview(1)">Approve</button>
                                <button class="btn-secondary text-danger" style="padding:4px 8px; font-size:0.75rem; background:rgba(239,68,68,0.05); border:1px solid rgba(239,68,68,0.1); border-radius: 4px;" onclick="rejectCollectionFromOverview(1)">Reject</button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div style="display:flex; justify-content:flex-end; align-items:center; margin-top:20px; padding-top:16px; border-top: 1px solid var(--border-color);">
            <div style="display:flex; gap:6px;">
                <button class="btn-secondary" style="padding: 6px 12px; font-size:0.8rem; border-radius: 6px;"><i class="fa-solid fa-chevron-left"></i></button>
                <button class="btn-secondary" style="padding: 6px 12px; font-size:0.8rem; border-radius: 6px;"><i class="fa-solid fa-chevron-right"></i></button>
            </div>
        </div>
    </div>
</div>
