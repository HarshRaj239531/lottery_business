<!-- Members Management View -->
<div id="view-members" class="view-section" style="display:none;">
    
    <!-- Header with uppercase tracking category (Dawadukkan Design) -->
    <div style="margin-bottom: 28px;">
        <p style="font-size: 0.7rem; font-weight: 700; letter-spacing: 0.25em; text-transform: uppercase; margin-bottom: 6px; color: var(--primary);">
            Workspace Management
        </p>
        <div class="flex-row" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
            <h2 style="font-size: 1.8rem; font-weight: 700; color: #0f172a; letter-spacing: -0.02em; margin: 0;">Tenant Members</h2>
            <div class="header-btns" style="display: flex; gap: 12px;">
                <button class="btn-secondary" onclick="alert('Exporting CSV...')">Export CSV</button>
                <button class="btn-primary" onclick="openModal('create-member')"><i class="fa-solid fa-user-plus"></i> Add New Member</button>
            </div>
        </div>
        <p style="font-size: 0.875rem; color: var(--text-muted); margin-top: 6px;">Monitor and manage your global community membership database.</p>
    </div>

    <!-- Members Metrics Grid (Dawadukkan Design - Value Top, Icon Right, Circle Overlays) -->
    <div class="stats-grid" style="margin-bottom: 28px;">
        
        <!-- Card 1: Total Members -->
        <div class="stat-card relative-card">
            <div class="shape-circle-1"></div>
            <div class="shape-circle-2"></div>
            <div class="shape-circle-3"></div>
            <div style="display: flex; justify-content: space-between; align-items: center; width: 100%; position: relative; z-index: 1;">
                <div class="flex-column" style="gap: 2px; flex: 1; min-width: 0;">
                    <h2 id="members-metric-total" style="font-size: 1.5rem; font-weight: 700; color: #111827; line-height: 1.1;">12,482</h2>
                    <span style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600;">Total Members</span>
                    <div style="display:flex; align-items:center; gap:4px; margin-top: 4px;">
                        <i class="fa-solid fa-arrow-trend-up" style="color: var(--success); font-size: 0.7rem;"></i>
                        <span style="font-size: 0.65rem; color: var(--text-muted); font-weight: 600;">+4% vs yesterday</span>
                    </div>
                </div>
                <div class="stat-icon-wrapper" style="background-color: var(--primary-light); color: var(--primary); flex-shrink: 0;">
                    <i class="fa-solid fa-users"></i>
                </div>
            </div>
        </div>

        <!-- Card 2: Active Now -->
        <div class="stat-card relative-card">
            <div class="shape-circle-1"></div>
            <div class="shape-circle-2"></div>
            <div class="shape-circle-3"></div>
            <div style="display: flex; justify-content: space-between; align-items: center; width: 100%; position: relative; z-index: 1;">
                <div class="flex-column" style="gap: 2px; flex: 1; min-width: 0;">
                    <h2 id="members-metric-active" style="font-size: 1.5rem; font-weight: 700; color: #111827; line-height: 1.1;">8,921</h2>
                    <span style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600;">Active Now</span>
                    <div style="display:flex; align-items:center; gap:4px; margin-top: 4px;">
                        <span class="badge" style="background-color: #f3e8ff; color: #7c3aed; padding: 1px 6px; font-size: 0.6rem; border-radius: 4px; font-weight: 700;">Stable</span>
                    </div>
                </div>
                <div class="stat-icon-wrapper" style="background-color: #f3e8ff; color: #7c3aed; flex-shrink: 0;">
                    <i class="fa-solid fa-circle-play"></i>
                </div>
            </div>
        </div>

        <!-- Card 3: Pending Approval -->
        <div class="stat-card relative-card">
            <div class="shape-circle-1"></div>
            <div class="shape-circle-2"></div>
            <div class="shape-circle-3"></div>
            <div style="display: flex; justify-content: space-between; align-items: center; width: 100%; position: relative; z-index: 1;">
                <div class="flex-column" style="gap: 2px; flex: 1; min-width: 0;">
                    <h2 id="members-metric-pending" style="font-size: 1.5rem; font-weight: 700; color: #111827; line-height: 1.1;">143</h2>
                    <span style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600;">Pending Approval</span>
                    <div style="display:flex; align-items:center; gap:4px; margin-top: 4px;">
                        <span class="badge badge-failed" style="padding: 1px 6px; font-size: 0.6rem; border-radius: 4px; font-weight: 700;">Action Required</span>
                    </div>
                </div>
                <div class="stat-icon-wrapper" style="background-color: var(--danger-bg); color: var(--danger); flex-shrink: 0;">
                    <i class="fa-solid fa-user-clock"></i>
                </div>
            </div>
        </div>

        <!-- Card 4: Total Assets -->
        <div class="stat-card relative-card">
            <div class="shape-circle-1"></div>
            <div class="shape-circle-2"></div>
            <div class="shape-circle-3"></div>
            <div style="display: flex; justify-content: space-between; align-items: center; width: 100%; position: relative; z-index: 1;">
                <div class="flex-column" style="gap: 2px; flex: 1; min-width: 0;">
                    <h2 id="members-metric-assets" style="font-size: 1.5rem; font-weight: 700; color: #111827; line-height: 1.1;">$4.2M</h2>
                    <span style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600;">Total Assets</span>
                    <div style="display:flex; align-items:center; gap:4px; margin-top: 4px;">
                        <i class="fa-solid fa-arrow-trend-up" style="color: var(--success); font-size: 0.7rem;"></i>
                        <span style="font-size: 0.65rem; color: var(--text-muted); font-weight: 600;">+12% vs last month</span>
                    </div>
                </div>
                <div class="stat-icon-wrapper" style="background-color: var(--success-bg); color: var(--success); flex-shrink: 0;">
                    <i class="fa-solid fa-piggy-bank"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Section Card for Members Directory -->
    <div class="panel-card" style="margin-bottom: 28px;">
        <div class="panel-card-header" style="flex-direction: column; align-items: flex-start; gap: 4px; border-bottom: 1px solid var(--border-color); padding-bottom: 16px; margin-bottom: 20px;">
            <h3 style="font-size: 0.95rem; font-weight: 700; color: #111827; text-transform: uppercase; letter-spacing: 0.03em;">Recent Members</h3>
            <span style="font-size: 0.75rem; color: var(--text-muted);">View and manage all registered community members.</span>
        </div>
        
        <!-- Filters Bar (Dawadukkan Design) -->
        <div class="filter-bar">
            <div class="filter-input" style="flex: 2;">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" id="member-search-input" placeholder="Search by Member ID, Customer Name or Phone...">
            </div>
            <select class="filter-select" id="member-community-filter">
                <option value="">All Communities</option>
                <option value="Tech Hub East">Tech Hub East</option>
                <option value="Pacific Investors">Pacific Investors</option>
                <option value="Euro Green Group">Euro Green Group</option>
            </select>
            <select class="filter-select" id="member-status-filter">
                <option value="">All Statuses</option>
                <option value="active">Active</option>
                <option value="pending">Pending</option>
                <option value="inactive">Inactive</option>
            </select>
        </div>

        <!-- Members Table (Dawadukkan Design - Column Layouts & Tinted Icon Boxes) -->
        <div class="table-responsive">
            <table class="data-table" id="members-main-table">
                <thead>
                    <tr>
                        <th>Member Details</th>
                        <th>Member ID</th>
                        <th>Community Group</th>
                        <th>Total Investment</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="members-table-tbody">
                    <tr>
                        <td>
                            <div class="user-avatar-group">
                                <!-- Tinted icon container (Dawadukkan Design) -->
                                <div style="display:flex; width: 32px; height: 32px; align-items:center; justify-content:center; border-radius: 6px; background-color: var(--primary-light); color: var(--primary); flex-shrink: 0;">
                                    <i class="fa-solid fa-user" style="font-size:0.85rem;"></i>
                                </div>
                                <div class="flex-column" style="gap: 1px;">
                                    <span class="user-detail-name">Elena Rodriguez</span>
                                    <span class="user-detail-sub">elena.r@example.com</span>
                                </div>
                            </div>
                        </td>
                        <td>#MEM-90210</td>
                        <td>Tech Hub East</td>
                        <td class="font-semibold">$45,000.00</td>
                        <td><span class="badge badge-success">Active</span></td>
                        <td>
                            <a href="javascript:void(0)" onclick="impersonateMember(1)" style="font-size: 0.85rem; font-weight: 700; color: var(--primary); text-decoration: none;">View Details</a>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="user-avatar-group">
                                <div style="display:flex; width: 32px; height: 32px; align-items:center; justify-content:center; border-radius: 6px; background-color: var(--primary-light); color: var(--primary); flex-shrink: 0;">
                                    <i class="fa-solid fa-user" style="font-size:0.85rem;"></i>
                                </div>
                                <div class="flex-column" style="gap: 1px;">
                                    <span class="user-detail-name">Marcus Chen</span>
                                    <span class="user-detail-sub">m.chen@corporate.net</span>
                                </div>
                            </div>
                        </td>
                        <td>#MEM-77421</td>
                        <td>Pacific Investors</td>
                        <td class="font-semibold">$128,500.00</td>
                        <td><span class="badge badge-pending">Pending</span></td>
                        <td>
                            <a href="javascript:void(0)" onclick="impersonateMember(2)" style="font-size: 0.85rem; font-weight: 700; color: var(--primary); text-decoration: none;">View Details</a>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="user-avatar-group">
                                <div style="display:flex; width: 32px; height: 32px; align-items:center; justify-content:center; border-radius: 6px; background-color: var(--primary-light); color: var(--primary); flex-shrink: 0;">
                                    <i class="fa-solid fa-user" style="font-size:0.85rem;"></i>
                                </div>
                                <div class="flex-column" style="gap: 1px;">
                                    <span class="user-detail-name">Sarah Jenkins</span>
                                    <span class="user-detail-sub">sarah.j@design.co</span>
                                </div>
                            </div>
                        </td>
                        <td>#MEM-44219</td>
                        <td>Euro Green Group</td>
                        <td class="font-semibold">$12,200.00</td>
                        <td><span class="badge badge-success">Active</span></td>
                        <td>
                            <a href="javascript:void(0)" onclick="impersonateMember(3)" style="font-size: 0.85rem; font-weight: 700; color: var(--primary); text-decoration: none;">View Details</a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination (Dawadukkan Design) -->
        <div style="display:flex; justify-content:space-between; align-items:center; margin-top:24px; padding-top:16px; border-top:1px solid var(--border-color);">
            <span class="text-sm text-muted">Showing 1 to 3 of 12,482 members</span>
            <div style="display:flex; gap:6px;">
                <button class="btn-secondary" style="padding: 6px 12px; font-size:0.8rem; border-radius: 6px;"><i class="fa-solid fa-chevron-left"></i></button>
                <button class="btn-primary" style="padding: 6px 12px; font-size:0.8rem; background-color: var(--primary); border-radius: 6px;">1</button>
                <button class="btn-secondary" style="padding: 6px 12px; font-size:0.8rem; border-radius: 6px;">2</button>
                <button class="btn-secondary" style="padding: 6px 12px; font-size:0.8rem; border-radius: 6px;">3</button>
                <span style="align-self:center; color: var(--text-muted); padding: 0 4px;">...</span>
                <button class="btn-secondary" style="padding: 6px 12px; font-size:0.8rem; border-radius: 6px;">312</button>
                <button class="btn-secondary" style="padding: 6px 12px; font-size:0.8rem; border-radius: 6px;"><i class="fa-solid fa-chevron-right"></i></button>
            </div>
        </div>
    </div>

    <!-- Members bottom features (Dawadukkan Design) -->
    <div class="layout-grid-2-1" style="grid-template-columns: 1.1fr 1fr; margin-bottom: 0;">
        
        <!-- Member Growth Insights -->
        <div class="status-panel-dark" style="justify-content: space-between; border-radius: 12px;">
            <div>
                <h4 style="color:#ffffff; opacity: 1; font-size: 0.95rem; font-weight:700;">Member Growth Insights</h4>
                <p style="margin-top: 12px; font-size: 0.85rem; line-height: 1.5; color: #e6f4f1; opacity:0.9;">
                    You've seen a 12% increase in new member registrations this month compared to last. Consider reviewing the "Pending Approval" queue to maintain onboarding momentum.
                </p>
            </div>
            <button class="btn-secondary" style="background-color: #ffffff; color: var(--primary); font-weight: 700; width: 100%; border: none; margin-top:24px; border-radius: 6px;" onclick="alert('Opening growth report...')">Review Growth Report</button>
        </div>

        <!-- Bulk KYC Verification -->
        <div class="panel-card" style="display:flex; flex-direction:column; justify-content:space-between; margin-bottom:0; border-radius: 12px; padding:20px;">
            <div>
                <div style="display:flex; align-items:center; gap:12px; margin-bottom:12px;">
                    <div style="background-color: var(--primary-light); color: var(--primary); padding:8px; border-radius:8px; display:flex; align-items:center; justify-content:center;">
                        <i class="fa-solid fa-user-shield" style="font-size:1.1rem;"></i>
                    </div>
                    <h3 style="font-size: 0.95rem; font-weight: 700; color:#111827;">Bulk KYC Verification</h3>
                </div>
                <p style="font-size:0.85rem; color:var(--text-muted); line-height:1.5;">
                    Select multiple pending members to perform a bulk identity verification check using our integrated provider.
                </p>
            </div>
            <div style="display:flex; align-items:center; justify-content:space-between; margin-top: 24px;">
                <button class="btn-primary" style="padding: 10px 20px; border-radius:6px; background-color: var(--primary);" onclick="window.location.hash='#kyc'">Start Bulk Action</button>
                <a href="javascript:void(0)" style="font-size: 0.85rem; font-weight: 700; color: var(--text-muted); text-decoration: none;" onclick="alert('Showing how bulk KYC verification works...')">How it works</a>
            </div>
        </div>

    </div>
</div>
