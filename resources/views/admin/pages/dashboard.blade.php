                <!-- Dashboard View -->
                <div id="view-dashboard" class="view-section active">
                    <div class="header-action">
                        <h2>Overview Overview</h2>
                        <button class="btn-secondary"><i class="fa-solid fa-download"></i> Export</button>
                    </div>

                    <!-- Stat Cards (Vibrant Gradients) -->
                    <div class="stats-grid">
                        <a href="#collection-committees" class="stat-card bg-gradient-cyan" style="text-decoration: none; color: inherit; cursor: pointer;">
                            <div class="stat-icon"><i class="fa-solid fa-gauge-high"></i></div>
                            <div class="stat-details">
                                <h3 id="stat-collection">₹0</h3>
                                <p>Today's Collection</p>
                            </div>
                        </a>
                        <a href="#committees" class="stat-card bg-gradient-orange" style="text-decoration: none; color: inherit; cursor: pointer;">
                            <div class="stat-icon"><i class="fa-solid fa-users"></i></div>
                            <div class="stat-details">
                                <h3 id="stat-members">0</h3>
                                <p>Total Members</p>
                            </div>
                        </a>
                        <a href="#paid-members" class="stat-card bg-gradient-purple" style="text-decoration: none; color: inherit; cursor: pointer;">
                            <div class="stat-icon"><i class="fa-solid fa-user-check"></i></div>
                            <div class="stat-details">
                                <h3 id="stat-paid-members">0</h3>
                                <p>Paid Members</p>
                            </div>
                        </a>
                        <a href="#due-members" class="stat-card bg-gradient-pink" style="text-decoration: none; color: inherit; cursor: pointer;">
                            <div class="stat-icon"><i class="fa-solid fa-money-bill-wave"></i></div>
                            <div class="stat-details">
                                <h3 id="stat-due-amount">₹0</h3>
                                <p>Due Amount</p>
                            </div>
                        </a>
                    </div>

                    <!-- Charts Grid -->
                    <div class="charts-grid">
                        <div class="chart-card glass-panel col-span-2">
                            <div class="card-header">
                                <h3><i class="fa-solid fa-wifi"></i> Collection Trends</h3>
                            </div>
                            <div class="chart-container">
                                <canvas id="lineChart"></canvas>
                            </div>
                        </div>
                        <div class="chart-card glass-panel">
                            <div class="card-header">
                                <h3><i class="fa-solid fa-chart-simple"></i> Member Rate Report</h3>
                            </div>
                            <div class="chart-container">
                                <canvas id="doughnutChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
