<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FinAdmin - Enterprise Finance Portal</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="/css/admin.css?v={{ time() }}">
    <!-- Chart.js for beautiful graphs -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- jQuery & DataTables -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
</head>
<body>

    <!-- LOGIN SCREEN -->
    <div id="login-screen" class="glass-overlay">
        <div class="login-card">
            <div class="login-header">
                <i class="fa-solid fa-shield-halved"></i>
                <h2>FinAdmin Portal</h2>
                <p>Welcome back! Please login to your administrative account.</p>
            </div>
            <form id="login-form">
                <div class="input-group">
                    <label>Email Address</label>
                    <div class="input-field">
                        <i class="fa-regular fa-envelope"></i>
                        <input type="email" id="login-email" required placeholder="admin@example.com">
                    </div>
                </div>
                <div class="input-group">
                    <label>Password</label>
                    <div class="input-field">
                        <i class="fa-solid fa-lock"></i>
                        <input type="password" id="login-password" required placeholder="••••••••">
                    </div>
                </div>
                <button type="submit" class="btn-primary" style="width: 100%;">Login <i class="fa-solid fa-arrow-right"></i></button>
                <div id="login-error" class="error-msg" style="display:none;"></div>
            </form>
        </div>
    </div>

    <!-- MAIN DASHBOARD SHELL -->
    <div id="app-shell" style="display:none;">
        
        <!-- SIDEBAR -->
        <aside class="sidebar">
            <div class="brand">
                <i class="fa-solid fa-wallet"></i>
                <div class="brand-details">
                    <span class="brand-name">FinAdmin</span>
                    <span class="brand-sub">Enterprise Finance</span>
                </div>
            </div>
            
            <ul class="nav-menu">
                <li class="nav-title">Finance Core</li>
                <li><a href="#dashboard" class="nav-link active" data-view="dashboard"><i class="fa-solid fa-grid-2"></i> <span>Dashboard</span></a></li>
                <li><a href="#members" class="nav-link" data-view="members"><i class="fa-solid fa-user-group"></i> <span>Members</span></a></li>
                <li><a href="#agents" class="nav-link" data-view="agents"><i class="fa-solid fa-user-tie"></i> <span>Agents</span></a></li>
                <li><a href="#kyc" class="nav-link" data-view="kyc"><i class="fa-solid fa-shield-heart"></i> <span>KYC</span></a></li>
                <li><a href="#loans" class="nav-link" data-view="loans"><i class="fa-solid fa-hand-holding-dollar"></i> <span>Loans</span></a></li>
                <li><a href="#collections" class="nav-link" data-view="collections"><i class="fa-solid fa-vault"></i> <span>Collections</span></a></li>
                <li><a href="#payments" class="nav-link" data-view="payments"><i class="fa-solid fa-money-bill-transfer"></i> <span>Payments</span></a></li>
                <li><a href="#settings" class="nav-link" data-view="settings"><i class="fa-solid fa-sliders"></i> <span>Settings</span></a></li>
                
                <li class="nav-title" style="margin-top: 15px; display: flex; justify-content: space-between; align-items: center; cursor: pointer;" onclick="$('#accounting-submenu').slideToggle()">
                    <span>Accounting & Ops</span>
                    <i class="fa-solid fa-chevron-down" style="font-size: 0.7rem;"></i>
                </li>
                <div id="accounting-submenu" style="display: none;">
                    <li><a href="#committees" class="nav-link" data-view="committees"><i class="fa-solid fa-users-rectangle"></i> <span>Committees</span></a></li>
                    <li><a href="#installments" class="nav-link" data-view="installments"><i class="fa-solid fa-file-invoice-dollar"></i> <span>Installments</span></a></li>
                    <li><a href="#lotteries" class="nav-link" data-view="lotteries"><i class="fa-solid fa-trophy"></i> <span>Lotteries</span></a></li>
                    <li><a href="#payouts" class="nav-link" data-view="payouts"><i class="fa-solid fa-receipt"></i> <span>Payouts</span></a></li>
                    <li><a href="#pnl" class="nav-link" data-view="pnl"><i class="fa-solid fa-chart-line"></i> <span>Profit & Loss</span></a></li>
                    <li><a href="#balance-sheet" class="nav-link" data-view="balance-sheet"><i class="fa-solid fa-scale-balanced"></i> <span>Balance Sheet</span></a></li>
                    <li><a href="#member-ledger" class="nav-link" data-view="member-ledger"><i class="fa-solid fa-file-invoice"></i> <span>Member Ledger</span></a></li>
                    <li><a href="#committee-ledger" class="nav-link" data-view="committee-ledger"><i class="fa-solid fa-receipt"></i> <span>Comm Ledger</span></a></li>
                </div>
            </ul>

            <div class="sidebar-footer">
                <a href="javascript:void(0)" id="logout-btn" class="nav-link text-danger"><i class="fa-solid fa-arrow-right-from-bracket"></i> <span>Logout</span></a>
            </div>
        </aside>

        <!-- MAIN CONTENT -->
        <main class="main-wrapper">
            
            <!-- TOPBAR -->
            <header class="topbar">
                <div style="display: flex; align-items: center; gap: 16px;">
                    <button id="sidebar-toggle" class="topbar-btn" title="Toggle Sidebar"><i class="fa-solid fa-bars"></i></button>
                    <div class="search-box">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="text" id="global-search-input" placeholder="Search members, loans, or transactions...">
                    </div>
                </div>
                <div class="topbar-actions">
                    <button class="topbar-btn" id="dark-mode-toggle" style="margin-right: 4px;" title="Toggle Theme"><i class="fa-regular fa-moon"></i></button>
                    <button class="topbar-btn"><i class="fa-regular fa-bell"></i><span class="dot"></span></button>
                    <button class="topbar-btn"><i class="fa-regular fa-circle-question"></i></button>
                    <span style="color: #cbd5e1; font-weight: 300;">|</span>
                    <a href="#settings" class="user-profile">
                        <div class="user-info">
                            <span class="user-name">Admin User</span>
                            <span class="user-role">SUPER ADMIN</span>
                        </div>
                        <img src="https://ui-avatars.com/api/?name=Admin+User&background=004d40&color=fff&bold=true" alt="AdminAvatar">
                    </a>
                </div>
            </header>

            <!-- VIEWS CONTAINER -->
            <div id="views-container" class="content-area">
                @include('admin.pages.dashboard')
                @include('admin.pages.committees')
                @include('admin.pages.members')
                @include('admin.pages.loans')
                @include('admin.pages.installments')
                @include('admin.pages.payouts')
                @include('admin.pages.pnl')
                @include('admin.pages.balance-sheet')
                @include('admin.pages.ledgers')
                @include('admin.pages.agents')
                @include('admin.pages.kyc')
                @include('admin.pages.collections')
                @include('admin.pages.payments')
                @include('admin.pages.settings')
            </div>
        </main>
    </div>

    <!-- GLOBAL MODAL (Hidden by default) -->
    <div id="global-modal" class="modal-overlay" style="display:none; z-index: 2000;">
        <div class="modal-card-new">
            <div class="modal-header" style="display: flex; justify-content: space-between; margin-bottom: 20px; align-items: center;">
                <h3 id="modal-title" style="font-size: 1.25rem; font-weight: 700; color: var(--primary);">Modal</h3>
                <button class="btn-secondary" style="padding: 6px 10px; border-radius: 50%;" onclick="closeModal()"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <div class="modal-body" id="modal-body">
                <!-- Dynamic Form Injected Here -->
            </div>
        </div>
    </div>

    <script src="/js/admin/core.js?v={{ time() }}"></script>
    <script src="/js/admin/ui.js?v={{ time() }}"></script>
    <script src="/js/admin/forms.js?v={{ time() }}"></script>
    <script src="/js/admin/dashboard.js?v={{ time() }}"></script>
    <script src="/js/admin/accounting.js?v={{ time() }}"></script>
    <script src="/js/admin/committees.js?v={{ time() }}"></script>
    <script src="/js/admin/members.js?v={{ time() }}"></script>
    <script src="/js/admin/installments.js?v={{ time() }}"></script>
    <script src="/js/admin/loans.js?v={{ time() }}"></script>
    <script src="/js/admin/lotteries_payouts.js?v={{ time() }}"></script>
    <script src="/js/admin/collections.js?v={{ time() }}"></script>
    <script src="/js/admin/charts.js?v={{ time() }}"></script>
    <script src="/js/admin/balance_sheet_extra.js?v={{ time() }}"></script>
    <script src="/js/admin/agents.js?v={{ time() }}"></script>
    <script src="/js/admin/kyc_overview.js?v={{ time() }}"></script>
    <script src="/js/admin/init.js?v={{ time() }}"></script>
</body>
</html>
