# FinAdmin - Premium Janta Community Admin Panel & Backend

[![Laravel Framework](https://img.shields.io/badge/Framework-Laravel%2010-red.svg)](https://laravel.com)
[![PHP Version](https://img.shields.io/badge/PHP-8.2%2B-blue.svg)](https://php.net)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](https://opensource.org/licenses/MIT)

**FinAdmin** is a premium, high-fidelity administrative panel and backend registry designed for the **Janta Community & Trader App**. It serves as the core system managing platform members, field agent collections, targets, compliance validation, and kitty/lottery ledgers.

The frontend is aligned with Figma design systems and includes a custom **Dark Mode Toggle** (inspired by Dawadukkan theme structures) and a **Sidebar Minimize Option** with persistent settings and dynamic tooltips.

---

## 🌟 Key Features

### 1. Executive Dashboard (Overview)
- **Welcome Hero Banner**: Welcomes the admin by name and displays real-time calendar dates.
- **KPI Metrics**: Cards displaying Total disbursements, Active Members, Total Collections, and KYC Compliance rates with trend directions.
- **Monthly Collection Curves**: Smooth cubic-bezier line charts (`Chart.js`) with linear fading gradients.
- **Quick Access Tiles**: Responsive navigation buttons with slide-on-hover arrow guides.
- **Recent Activities & Log**: Real-time updates with visual indicators.

### 2. Member & User Directory
- **Community Sorting**: Filter members by community clusters or active/pending statuses.
- **Identity Details**: Lists all user contacts, account numbers, and cumulative deposit valuations.
- **Agent Roles**: Assign or demote users to agent status dynamically.
- **Impersonation Mode**: Secure route token generation allowing admins to view the app shell from a member's account.

### 3. Field Agents Management
- **Target Tracking**: Set targets based on collection values (₹) or transaction counts.
- **Performance Logs**: Visual progress bars representing target completion ratios.
- **Region Management**: Map agents to specific urban centers or rural clusters.

### 4. KYC Compliance & Safety Center
- **Document Submissions**: Direct upload slots for identification documents (Aadhar, PAN, Voter ID, Passport).
- **OCR Checks & Checklist**: Validation checklists tracking upload statuses.
- **Biometric Face-Match**: Simulated camera feed preview for face-matching compliance checks.

### 5. Collections Registry
- **Real-Time Feed**: Track agent collections immediately with voucher logs.
- **Approval System**: One-click approvals or rejections of field collection vouchers.
- **Interactive Metrics**: Collections method charts showing digital vs cash payment ratios.

### 6. Accounting & Ops Core
- **Committees (Kitty Systems)**: Manage active lotteries, limit sizes, and monthly payment frequencies.
- **Lotteries Winner Selector**: Built-in lottery drawing algorithm to pick winners from active deposits.
- **Payouts Dashboard**: Manage cash bank transfers to lottery winners.
- **Financial Statements**: Dynamic generation of **Profit & Loss** statements and **Balance Sheet** ledgers.
- **General Ledgers**: Inspect individual Member and Committee ledger accounts.

---

## 🛠️ Technology Stack

- **Backend**: Laravel 10 (PHP 8.2), Eloquent ORM.
- **Frontend**: Blade Templates, Vanilla CSS, Vanilla JS, Chart.js, jQuery, DataTables.
- **Environment**: Docker Containers using Laravel Sail.
- **Database**: MySQL.

---

## 🚀 Installation & Local Setup

The project is fully containerized using **Laravel Sail** (Docker Compose). Follow these steps to set up the development environment locally:

### 1. Clone the Repository
```bash
git clone https://github.com/your-username/lottery_business.git
cd lottery_business
```

### 2. Configure Environment variables
Copy the environment template:
```bash
cp .env.example .env
```
Ensure database credentials inside `.env` align with your local settings (default Sail database is `laravel` with username `sail`).

### 3. Start Docker Containers (Laravel Sail)
Spin up the local containerized stack:
```bash
./vendor/bin/sail up -d
```

### 4. Install Project Dependencies
Run composer to install PHP packages:
```bash
./vendor/bin/sail composer install
```

### 5. Generate Application Key
```bash
./vendor/bin/sail artisan key:generate
```

### 6. Run Migrations & Seeders
Configure tables and seed initial mock accounts (admin credentials, members, agents, and accounting entries):
```bash
./vendor/bin/sail artisan migrate:fresh --seed
```

### 7. Access the Application
Open your browser and navigate to:
```
http://localhost
```
Use the seeded credentials to log in:
- **Email**: `admin@example.com`
- **Password**: `password`

---

## 📂 Project Folder Structure

- `app/`
  - `Http/Controllers/Admin/` - Contains Controllers for Members, Agents, KYC uploads, and Accounting.
  - `Models/` - Database schemas for Committees, Loans, Installments, KYC, and Transactions.
  - `Services/` - Financial aggregators and business logic classes (e.g., `DashboardService`).
- `database/`
  - `migrations/` - Database tables definitions.
  - `seeders/` - Database mocks setup.
- `public/`
  - `css/admin.css` - Custom theme rules, including dark mode and minimized sidebar states.
  - `js/admin.js` - Routing, Chart.js templates, modals, and API transaction scripts.
- `resources/views/admin/`
  - `dashboard.blade.php` - Main app view wrapper (includes topbar and sidebar).
  - `pages/` - Individual page panels: `dashboard`, `members`, `agents`, `kyc`, `collections`.
- `routes/api.php` - Secure backend API routes.

---

## 🛡️ License

This project is open-sourced software licensed under the [MIT license](LICENSE).
