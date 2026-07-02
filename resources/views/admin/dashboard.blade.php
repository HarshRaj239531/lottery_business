<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lottery Admin Panel</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="/css/admin.css">
    <!-- Chart.js for beautiful graphs -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- jQuery & DataTables -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
</head>
<body>

    <!-- LOGIN SCREEN -->
    <div id="login-screen" class="glass-overlay">
        <div class="login-card glass-panel">
            <div class="login-header">
                <i class="fa-solid fa-cube text-gradient"></i>
                <h2>Admin Login</h2>
                <p>Welcome back! Please login to your account.</p>
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
                <button type="submit" class="btn-primary">Login <i class="fa-solid fa-arrow-right"></i></button>
                <div id="login-error" class="error-msg" style="display:none;"></div>
            </form>
        </div>
    </div>

    <!-- MAIN DASHBOARD SHELL -->
    <div id="app-shell" style="display:none;">
        
        <!-- SIDEBAR -->
        <aside class="sidebar glass-panel">
            <div class="brand">
                <i class="fa-solid fa-dice text-gradient"></i>
                <span>Lottery<span class="font-light">Panel</span></span>
            </div>
            
            <ul class="nav-menu">
                <li class="nav-title">Menu</li>
                <li><a href="#dashboard" class="nav-link active" data-view="dashboard"><i class="fa-solid fa-chart-pie"></i> Dashboard</a></li>
                <li><a href="#committees" class="nav-link" data-view="committees"><i class="fa-solid fa-users-rectangle"></i> Committees</a></li>
                <li><a href="#members" class="nav-link" data-view="members"><i class="fa-solid fa-user-group"></i> Members</a></li>
                <li><a href="#loans" class="nav-link" data-view="loans"><i class="fa-solid fa-hand-holding-dollar"></i> Loans</a></li>
                <li><a href="#installments" class="nav-link" data-view="installments"><i class="fa-solid fa-money-bill-transfer"></i> Installments</a></li>
                <li><a href="#lotteries" class="nav-link" data-view="lotteries"><i class="fa-solid fa-trophy"></i> Lotteries</a></li>
                <li><a href="#payouts" class="nav-link" data-view="payouts"><i class="fa-solid fa-money-check-dollar"></i> Payouts</a></li>
                
                <li class="nav-title" style="margin-top: 15px; font-size: 0.75rem; color: #818cf8; text-transform: uppercase; letter-spacing: 0.05em; padding: 0 20px;">Staff Management</li>
                <li><a href="#agents" class="nav-link" data-view="agents"><i class="fa-solid fa-user-tie"></i> Agents</a></li>

                <li class="nav-title" style="margin-top: 15px; font-size: 0.75rem; color: #818cf8; text-transform: uppercase; letter-spacing: 0.05em; padding: 0 20px;">Accounting</li>
                <li><a href="#pnl" class="nav-link" data-view="pnl"><i class="fa-solid fa-chart-line"></i> Profit & Loss</a></li>
                <li><a href="#balance-sheet" class="nav-link" data-view="balance-sheet"><i class="fa-solid fa-scale-balanced"></i> Balance Sheet</a></li>
                
                <li class="nav-title" style="margin-top: 15px; font-size: 0.75rem; color: #818cf8; text-transform: uppercase; letter-spacing: 0.05em; padding: 0 20px;">Ledgers</li>
                <li><a href="#member-ledger" class="nav-link" data-view="member-ledger"><i class="fa-solid fa-file-invoice"></i> Member Ledger</a></li>
                <li><a href="#committee-ledger" class="nav-link" data-view="committee-ledger"><i class="fa-solid fa-file-invoice-dollar"></i> Committee Ledger</a></li>
            </ul>
        </aside>

        <!-- MAIN CONTENT -->
        <main class="main-wrapper">
            
            <!-- TOPBAR -->
            <header class="topbar">
                <div class="search-box">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" placeholder="Type in to Search...">
                </div>
                <div class="user-profile">
                    <button class="icon-btn"><i class="fa-regular fa-bell"></i><span class="badge">3</span></button>
                    <div class="profile-info">
                        <img src="https://ui-avatars.com/api/?name=Admin+User&background=6366f1&color=fff" alt="Admin" class="avatar">
                        <span class="user-name">Admin User <i class="fa-solid fa-chevron-down text-sm"></i></span>
                    </div>
                    <button id="logout-btn" class="icon-btn text-danger" title="Logout"><i class="fa-solid fa-arrow-right-from-bracket"></i></button>
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
            </div>
        </main>
    </div>

    <!-- GLOBAL MODAL (Hidden by default) -->
    <div id="global-modal" class="glass-overlay" style="display:none; z-index: 2000;">
        <div class="modal-card glass-panel" style="max-width: 500px; width: 100%; padding: 30px;">
            <div class="modal-header" style="display: flex; justify-content: space-between; margin-bottom: 20px;">
                <h3 id="modal-title" style="font-size: 1.5rem; font-weight: 600;">Modal</h3>
                <button class="icon-btn" onclick="closeModal()"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <div class="modal-body" id="modal-body">
                <!-- Dynamic Form Injected Here -->
            </div>
        </div>
    </div>

    <script src="/js/admin.js?v={{ time() }}"></script>
</body>
</html>
