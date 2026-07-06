
    
    // Core State
    let authToken = localStorage.getItem('admin_token');
    
    // Elements
    const loginScreen = document.getElementById('login-screen');
    const appShell = document.getElementById('app-shell');
    const loginForm = document.getElementById('login-form');
    const logoutBtn = document.getElementById('logout-btn');
    const errorMsg = document.getElementById('login-error');
    const navLinks = document.querySelectorAll('.nav-link');
    const viewSections = document.querySelectorAll('.view-section');

    const modal = document.getElementById('global-modal');
    const modalTitle = document.getElementById('modal-title');
    const modalBody = document.getElementById('modal-body');

    // Chart instances
    let lineChartInstance = null;
    let doughnutChartInstance = null;

    // Base Fetch Headers
    const getHeaders = () => ({
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'Authorization': `Bearer ${authToken}`
    });

    // ----- DARK MODE CONFIG -----
    const toggleBtn = document.getElementById('dark-mode-toggle');
    if (toggleBtn) {
        if (localStorage.getItem('theme') === 'dark') {
            document.body.classList.add('dark-theme');
            toggleBtn.innerHTML = '<i class="fa-regular fa-sun"></i>';
        }
        
        toggleBtn.addEventListener('click', () => {
            document.body.classList.toggle('dark-theme');
            const isDark = document.body.classList.contains('dark-theme');
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
            toggleBtn.innerHTML = isDark ? '<i class="fa-regular fa-sun"></i>' : '<i class="fa-regular fa-moon"></i>';
            
            // Re-render charts to update grid/axis colors
            if (typeof renderCharts === 'function' && document.getElementById('lineChart')) {
                loadDashboardData();
            }
            if (typeof loadCollectionsOverviewData === 'function' && document.getElementById('collectionsTrendChart')) {
                loadCollectionsOverviewData();
            }
        });
    }

    // ----- SIDEBAR MINIMIZE CONFIG -----
    const sidebarToggle = document.getElementById('sidebar-toggle');
    const sidebar = document.querySelector('.sidebar');
    if (sidebarToggle && sidebar) {
        if (localStorage.getItem('sidebar-minimized') === 'true') {
            sidebar.classList.add('minimized');
        }
        
        sidebarToggle.addEventListener('click', () => {
            sidebar.classList.toggle('minimized');
            localStorage.setItem('sidebar-minimized', sidebar.classList.contains('minimized') ? 'true' : 'false');
        });

        // Generate tooltips for minimized navigation
        const links = sidebar.querySelectorAll('.nav-menu a, .sidebar-footer a');
        links.forEach(link => {
            const text = link.querySelector('span');
            if (text) {
                link.setAttribute('title', text.textContent.trim());
            }
        });
    }


    // ----- ROUTING (Simple Hash Router) -----
    function handleRoute() {
        const hash = window.location.hash || '#dashboard';
        let targetView = hash.replace('#', '');
        let paramId = null;

        if (targetView.startsWith('committee-details-')) {
            paramId = targetView.replace('committee-details-', '');
            targetView = 'committee-details';
        } else if (targetView === 'committees-active') {
            paramId = 'active';
            targetView = 'committees';
        }

        // Update nav active states
        navLinks.forEach(link => {
            const href = link.getAttribute('href');
            if (href === hash || (targetView === 'committee-details' && href === '#collection-committees') || (targetView === 'committees' && href === '#committees') || (targetView === 'payments' && href === '#payouts')) {
                link.classList.add('active');
            } else {
                link.classList.remove('active');
            }
        });

        // Show/hide sections
        viewSections.forEach(section => {
            if (section.id === `view-${targetView}`) {
                section.style.display = 'block';
                // Trigger data load based on view
                if (targetView === 'dashboard') loadDashboardData();
                if (targetView === 'committees') loadCommitteesData(paramId);
                if (targetView === 'members') loadMembersData();
                if (targetView === 'installments') loadInstallmentsData();
                if (targetView === 'lotteries') loadLotteriesData();
                if (targetView === 'loans') { loadLoansData(); loadBalanceSheetData(); }
                if (targetView === 'payouts' || targetView === 'payments') loadPayoutsData();
                if (targetView === 'collection-committees') loadCollectionCommittees();
                if (targetView === 'committee-details') loadCommitteeDetails(paramId);
                if (targetView === 'paid-members') loadPaidMembersData();
                if (targetView === 'due-members') loadDueMembersData();
                if (targetView === 'pnl') loadPnLData();
                if (targetView === 'balance-sheet') loadBalanceSheetData();
                if (targetView === 'agents') { loadAgentsView(); loadAgentsList(); }
                if (targetView === 'kyc') loadKycData();
                if (targetView === 'collections') loadCollectionsOverviewData();
                if (targetView === 'settings') loadSettingsData();
                if (targetView === 'member-ledger') { document.getElementById('member-ledger-tbody').innerHTML=''; document.getElementById('ledger-member-name').textContent=''; }
                if (targetView === 'committee-ledger') { document.getElementById('committee-ledger-tbody').innerHTML=''; document.getElementById('ledger-committee-name').textContent=''; }
            } else {
                section.style.display = 'none';
            }
        });
    }
    window.addEventListener('hashchange', handleRoute);