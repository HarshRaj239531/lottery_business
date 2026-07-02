<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MemberController;
use App\Http\Controllers\Admin\CommitteeController;
use App\Http\Controllers\Admin\InstallmentController;
use App\Http\Controllers\Admin\LotteryController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\NotificationController;

Route::prefix('admin')->group(function () {

    // 🔐 Admin Login (No Auth Required)
    Route::post('/login', [AuthController::class, 'login']);
});

// 📱 Public User Routes

Route::post('/login', [\App\Http\Controllers\Api\UserAuthController::class, 'login']);

// 🛡️ Protected Routes
Route::middleware(['auth:sanctum'])->group(function () {

    // 🏢 Member App APIs
    Route::middleware('role:member|agent')->prefix('member')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Api\MemberDashboardController::class, 'index']);
        Route::get('/profile', [\App\Http\Controllers\Api\MemberDashboardController::class, 'profile']);
        Route::get('/documents', [\App\Http\Controllers\Api\MemberDashboardController::class, 'documents']);
        Route::post('/documents/upload', [\App\Http\Controllers\Api\MemberDashboardController::class, 'uploadDocument']);
        Route::post('/bank-account', [\App\Http\Controllers\Api\MemberDashboardController::class, 'updateBankAccount']);
        Route::get('/lotteries', [\App\Http\Controllers\Api\MemberDashboardController::class, 'lotteries']);
        Route::get('/loans/{id}', [\App\Http\Controllers\Api\MemberDashboardController::class, 'showLoan']);
        Route::get('/installments', [\App\Http\Controllers\Api\MemberDashboardController::class, 'installments']);
        Route::get('/loans', [\App\Http\Controllers\Api\MemberDashboardController::class, 'loans']);
        Route::get('/committees', [\App\Http\Controllers\Api\MemberDashboardController::class, 'committees']);
        Route::get('/passbook', [\App\Http\Controllers\Api\MemberDashboardController::class, 'passbook']);
    });

    // 🕵️‍♂️ Agent App APIs
    Route::middleware('role:agent')->prefix('agent')->group(function () {
        Route::get('/profile', [\App\Http\Controllers\Api\AgentDashboardController::class, 'profile']);
        Route::get('/search-member', [\App\Http\Controllers\Api\AgentDashboardController::class, 'searchMember']);
        Route::get('/committee-members', [\App\Http\Controllers\Api\AgentDashboardController::class, 'committeeMembers']);
        Route::get('/loan-members', [\App\Http\Controllers\Api\AgentDashboardController::class, 'loanMembers']);
        Route::post('/collections/submit', [\App\Http\Controllers\Api\AgentDashboardController::class, 'submitCollection']);
        Route::get('/targets', [\App\Http\Controllers\Api\AgentDashboardController::class, 'myTargets']);
        Route::get('/collections', [\App\Http\Controllers\Api\AgentDashboardController::class, 'myCollections']);
    });
});

Route::prefix('admin')->group(function () {
    // 🔒 Protected Admin Routes
    Route::middleware(['auth:sanctum', 'role:Super Admin'])->group(function () {

        // 🚪 Logout
        Route::post('/logout', [AuthController::class, 'logout']);

        // 👤 Users
        Route::apiResource('users', UserController::class);

        // 📊 Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index']);
        Route::get('/dashboard/daily-collection', [DashboardController::class, 'dailyCollection']);
        Route::get('/dashboard/monthly-profit', [DashboardController::class, 'monthlyProfit']);
        Route::get('/dashboard/paid-members', [DashboardController::class, 'paidMembersList']);
        Route::get('/dashboard/due-members', [DashboardController::class, 'dueMembersList']);

        // 💰 Accounting
        Route::get('/accounting/member-ledger/{id}', [\App\Http\Controllers\Admin\AccountingController::class, 'memberLedger']);
        Route::get('/accounting/committee-ledger/{id}', [\App\Http\Controllers\Admin\AccountingController::class, 'committeeLedger']);
        Route::get('/accounting/pnl', [\App\Http\Controllers\Admin\AccountingController::class, 'profitAndLoss']);
        Route::get('/accounting/balance-sheet', [\App\Http\Controllers\Admin\AccountingController::class, 'balanceSheet']);

        // 👥 Members
        Route::post('/members/{id}/change-password', [MemberController::class, 'changePassword']);
        Route::post('/members/{user}/enroll', [MemberController::class, 'enroll']);
        Route::get('/members/{id}/impersonate', [MemberController::class, 'impersonate']);
        Route::apiResource('members', MemberController::class);

        // 🏦 Committees
        Route::get('/committees/{committee}/collection-stats', [CommitteeController::class, 'collectionStats']);
        Route::apiResource('committees', CommitteeController::class);

        // 💳 Loans
        Route::get('/loan-installments', [\App\Http\Controllers\Admin\LoanController::class, 'installments']);
        Route::post('/loans/{id}/collect', [\App\Http\Controllers\Admin\LoanController::class, 'collect']);
        Route::apiResource('loans', \App\Http\Controllers\Admin\LoanController::class);

        // 💰 Installments
        Route::post('/installments/{id}/collect', [\App\Http\Controllers\Admin\InstallmentController::class, 'collect']);
        Route::get('/installments/pending', [\App\Http\Controllers\Admin\InstallmentController::class, 'pending']);
        Route::apiResource('installments', \App\Http\Controllers\Admin\InstallmentController::class);

        // 🎟️ Lotteries
        Route::post('/lotteries', [\App\Http\Controllers\Admin\LotteryController::class, 'store']);
        Route::post('/lotteries/{id}/draw', [\App\Http\Controllers\Admin\LotteryController::class, 'drawWinner']);
        Route::get('/lotteries/{id}/winners', [\App\Http\Controllers\Admin\LotteryController::class, 'getWinnersByLottery']);

        // 🕵️‍♂️ Agent Management (Admin)
        Route::post('/agents/targets', [\App\Http\Controllers\Admin\AgentController::class, 'assignTarget']);
        Route::get('/agents/targets', [\App\Http\Controllers\Admin\AgentController::class, 'getTargets']);
        Route::get('/agents/collections', [\App\Http\Controllers\Admin\AgentController::class, 'getCollections']);
        Route::post('/agents/collections/{id}/approve', [\App\Http\Controllers\Admin\AgentController::class, 'approveCollection']);
        Route::post('/agents/collections/{id}/reject', [\App\Http\Controllers\Admin\AgentController::class, 'rejectCollection']);

        // 💰 Payouts
        Route::get('/payouts', [\App\Http\Controllers\Admin\PayoutController::class, 'index']);
        Route::post('/payouts/{id}/pay', [\App\Http\Controllers\Admin\PayoutController::class, 'pay']);

        // 🎯 Lottery
        Route::post('/lotteries/draw/{committee_id}', [LotteryController::class, 'draw']);
        Route::apiResource('lotteries', LotteryController::class)->except(['store', 'update']);

        // 📈 Reports
        Route::get('/reports/collection', [ReportController::class, 'collectionReport']);
        Route::get('/reports/monthly', [ReportController::class, 'monthlyReport']);
        Route::get('/reports/committees', [ReportController::class, 'committeeReport']);
        Route::get('/reports/date-range/{start}/{end}', [ReportController::class, 'dateRangeReport']);

        // 📲 Notifications
        Route::post('/notifications/sms', [NotificationController::class, 'sendSMS']);
        Route::post('/notifications/whatsapp', [NotificationController::class, 'sendWhatsApp']);

        // 👮 KYC Verification
        Route::post('/kyc/submit', [\App\Http\Controllers\Admin\KycController::class, 'submit']);

    });
});