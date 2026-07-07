<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('terms_conditions', function (Blueprint $table) {
            $table->id();
            $table->string('title')->default('Terms & Conditions');
            $table->longText('content');
            $table->timestamps();
        });

        // Seed default terms
        DB::table('terms_conditions')->insert([
            'title' => 'Terms & Conditions',
            'content' => "Welcome to Janta Trader. By joining the communities within the Janta Trader ecosystem, you agree to comply with and be bound by the following terms and conditions. These terms ensure a secure and profitable environment for all members of our exclusive circle.\n\n1. Membership Eligibility\nMembership is restricted to individuals who have reached the legal age of majority in their jurisdiction. All applicants must undergo a mandatory identity verification (KYC) process and provide proof of financial standing to maintain the group's accreditation status.\n\n2. Investment Contributions\nTo maintain active status, members must adhere to the contribution schedule. A monthly/weekly/daily commitment must be paid by the 5th of every month. Late fees of 2% apply to any contribution delayed beyond the 10th.\n\n3. Withdrawal Policy\nElite funds are subject to an initial lock-in period to ensure capital stability. Subsequent withdrawals require a written notice of at least 30 business days. Early withdrawals may be subject to a 5% liquidity adjustment fee.\n\n4. Risk Disclosure\nInvestments are subject to market risks. While our managed portfolio targets a high benchmark return, these returns are not guaranteed. Past performance is not indicative of future results.\n\n5. Data Privacy & Security\nYour financial data is protected using AES-256 encryption. We do not share your personal information with third-party marketers.",
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('terms_conditions');
    }
};
