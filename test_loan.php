$user = \App\Models\User::firstOrCreate(['email' => 'loan_test@example.com'], [
    'name' => 'Demo Loan User',
    'phone' => '9998887776',
    'password' => bcrypt('password'),
    'address' => 'Demo Address',
    'is_phone_verified' => true
]);
if (!$user->hasRole('member')) {
    $user->assignRole('member');
}

$request = new \Illuminate\Http\Request([
    'user_id' => $user->id,
    'amount' => 50000,
    'interest_rate_percent' => 2,
    'duration_months' => 6,
    'payment_frequency' => 'monthly'
]);

app(\App\Http\Controllers\Admin\LoanController::class)->store($request);

$request2 = new \Illuminate\Http\Request([
    'user_id' => $user->id,
    'amount' => 100000,
    'interest_rate_percent' => 5,
    'duration_months' => 12,
    'payment_frequency' => 'monthly'
]);
app(\App\Http\Controllers\Admin\LoanController::class)->store($request2);

echo 'Data inserted!';
