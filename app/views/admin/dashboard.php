<?php
/**
 * View: PESO Admin Command Center Dashboard
 * Sprint 6 — Admin Command Center Overhaul
 *
 * Variables expected from AdminController:
 *   @var array $geography        [ ['barangay_name' => ..., 'seeker_count' => ...], ... ]
 *   @var array $top_skills       [ ['skill_name' => ..., 'demand_count' => ...], ... ]
 *   @var array $pending_employers[ ['employer_id', 'company_name', 'contact_person',
 *                                   'company_phone', 'business_permit'], ... ]
 *   @var array $pending_skills   [ ['skill_id', 'skill_name', 'suggested_by',
 *                                   'category_name'], ... ]
 *   @var string $admin_name      Display name for the logged-in admin.
 */
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="S.I.K.A.P. Hub V2 — PESO Admin Command Center. Manage employer verifications, custom skill approvals, and platform analytics.">
    <title>Admin Command Center | S.I.K.A.P. Hub V2</title>

    <style>
        /* =========================================================
           1. CSS VARIABLE DESIGN SYSTEM (Blueprint Rule §3)
           ========================================================= */
        :root {
            --primary:    #0056b3;
            --secondary:  #6c757d;
            --danger:     #e74c3c;
            --background: #f4f7f6;
            --border:     #ced4da;

            /* Extended palette — derived from blueprint primaries */
            --primary-dark:   #003d80;
            --primary-light:  #e8f1fb;
            --success:        #1a8a4a;
            --success-light:  #d4edda;
            --warning:        #e67e22;
            --warning-light:  #fef3e2;
            --surface:        #ffffff;
            --surface-alt:    #f8f9fa;
            --text-primary:   #1a202c;
            --text-secondary: #4a5568;
            --text-muted:     #718096;
            --shadow-sm:      0 1px 3px rgba(0,0,0,.08), 0 1px 2px rgba(0,0,0,.06);
            --shadow-md:      0 4px 12px rgba(0,0,0,.10), 0 2px 4px rgba(0,0,0,.06);
            --shadow-lg:      0 10px 30px rgba(0,0,0,.12), 0 4px 8px rgba(0,0,0,.06);
            --radius:         10px;
            --radius-sm:      6px;
            --nav-height:     64px;
            --transition:     all 0.2s ease;
        }

        /* =========================================================
           2. RESET & BASE
           ========================================================= */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        html { font-size: 16px; scroll-behavior: smooth; }

        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background: var(--background);
            color: var(--text-primary);
            min-height: 100vh;
            padding-top: var(--nav-height);
        }

        /* =========================================================
           3. GLOBAL NAV (matches Employer / Seeker dashboards)
           ========================================================= */
        .global-nav {
            position: fixed;
            top: 0; left: 0; right: 0;
            height: var(--nav-height);
            background: var(--primary-dark);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 2rem;
            z-index: 1000;
            box-shadow: 0 2px 8px rgba(0,0,0,.25);
        }

        .nav-brand {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
        }

        .nav-brand-icon {
            width: 36px;
            height: 36px;
            background: var(--primary);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
        }

        .nav-brand-text {
            display: flex;
            flex-direction: column;
        }

        .nav-brand-title {
            color: #ffffff;
            font-size: 0.95rem;
            font-weight: 700;
            letter-spacing: 0.02em;
            line-height: 1.1;
        }

        .nav-brand-sub {
            color: rgba(255,255,255,0.55);
            font-size: 0.68rem;
            font-weight: 400;
            letter-spacing: 0.06em;
            text-transform: uppercase;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        .nav-link {
            color: rgba(255,255,255,0.75);
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 500;
            padding: 0.45rem 0.85rem;
            border-radius: var(--radius-sm);
            transition: var(--transition);
        }

        .nav-link:hover,
        .nav-link.active {
            color: #ffffff;
            background: rgba(255,255,255,0.12);
        }

        .nav-divider {
            width: 1px;
            height: 22px;
            background: rgba(255,255,255,0.2);
            margin: 0 0.5rem;
        }

        .nav-user {
            display: flex;
            align-items: center;
            gap: 0.6rem;
        }

        .nav-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 0.8rem;
            font-weight: 700;
            border: 2px solid rgba(255,255,255,0.3);
        }

        .nav-user-name {
            color: rgba(255,255,255,0.85);
            font-size: 0.82rem;
            font-weight: 500;
        }

        .nav-logout {
            color: rgba(255,255,255,0.65);
            text-decoration: none;
            font-size: 0.8rem;
            padding: 0.4rem 0.75rem;
            border-radius: var(--radius-sm);
            border: 1px solid rgba(255,255,255,0.2);
            transition: var(--transition);
        }

        .nav-logout:hover {
            color: #ffffff;
            border-color: rgba(255,255,255,0.5);
            background: rgba(255,255,255,0.08);
        }

        /* =========================================================
           4. PAGE LAYOUT
           ========================================================= */
        .page-wrapper {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem 1.5rem 4rem;
        }

        /* =========================================================
           5. PAGE HEADER
           ========================================================= */
        .page-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .page-header-left h1 {
            font-size: 1.6rem;
            font-weight: 700;
            color: var(--text-primary);
            line-height: 1.2;
        }

        .page-header-left p {
            color: var(--text-muted);
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }

        .badge-role {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.2rem 0.65rem;
            background: var(--primary-light);
            color: var(--primary);
            border-radius: 20px;
            font-size: 0.72rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            margin-bottom: 0.4rem;
        }

        .page-header-right {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        /* =========================================================
           6. BUTTONS
           ========================================================= */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.6rem 1.25rem;
            border-radius: var(--radius-sm);
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            border: none;
            text-decoration: none;
            transition: var(--transition);
            white-space: nowrap;
        }

        .btn:active { transform: scale(0.97); }

        .btn-primary {
            background: var(--primary);
            color: #ffffff;
        }

        .btn-primary:hover { background: var(--primary-dark); }

        .btn-export {
            background: var(--danger);
            color: #ffffff;
        }

        .btn-export:hover { background: #c0392b; }

        .btn-success {
            background: var(--success);
            color: #ffffff;
        }

        .btn-success:hover { background: #146b38; }

        .btn-danger {
            background: var(--danger);
            color: #ffffff;
        }

        .btn-danger:hover { background: #c0392b; }

        .btn-outline {
            background: transparent;
            color: var(--primary);
            border: 1.5px solid var(--primary);
        }

        .btn-outline:hover {
            background: var(--primary-light);
        }

        .btn-sm {
            padding: 0.38rem 0.85rem;
            font-size: 0.78rem;
        }

        /* =========================================================
           7. CARDS
           ========================================================= */
        .card {
            background: var(--surface);
            border-radius: var(--radius);
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border);
            overflow: hidden;
            transition: box-shadow 0.2s ease;
        }

        .card:hover { box-shadow: var(--shadow-md); }

        .card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1.1rem 1.4rem 0.9rem;
            border-bottom: 1px solid var(--border);
            background: var(--surface-alt);
        }

        .card-header-left {
            display: flex;
            align-items: center;
            gap: 0.6rem;
        }

        .card-icon {
            width: 34px;
            height: 34px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            flex-shrink: 0;
        }

        .card-icon-blue   { background: var(--primary-light); }
        .card-icon-orange { background: var(--warning-light); }
        .card-icon-green  { background: var(--success-light); }
        .card-icon-purple { background: #f3e8ff; }

        .card-title {
            font-size: 0.95rem;
            font-weight: 700;
            color: var(--text-primary);
        }

        .card-subtitle {
            font-size: 0.75rem;
            color: var(--text-muted);
            margin-top: 0.1rem;
        }

        .card-body { padding: 1.25rem 1.4rem; }

        /* =========================================================
           8. METRICS GRID (2-column)
           ========================================================= */
        .metrics-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.25rem;
            margin-bottom: 1.5rem;
        }

        /* =========================================================
           9. QUEUE SECTIONS (full-width)
           ========================================================= */
        .queue-section { margin-bottom: 1.5rem; }

        /* =========================================================
           10. TABLES
           ========================================================= */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.875rem;
        }

        .data-table thead th {
            background: var(--surface-alt);
            color: var(--text-secondary);
            font-weight: 600;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            padding: 0.75rem 1rem;
            text-align: left;
            border-bottom: 1px solid var(--border);
            white-space: nowrap;
        }

        .data-table tbody tr {
            border-bottom: 1px solid var(--border);
            transition: background 0.15s ease;
        }

        .data-table tbody tr:last-child { border-bottom: none; }

        .data-table tbody tr:hover { background: var(--surface-alt); }

        .data-table tbody td {
            padding: 0.85rem 1rem;
            color: var(--text-primary);
            vertical-align: middle;
        }

        /* Bar chart representation for counts */
        .bar-cell {
            display: flex;
            align-items: center;
            gap: 0.6rem;
        }

        .bar-track {
            flex: 1;
            height: 6px;
            background: var(--border);
            border-radius: 99px;
            overflow: hidden;
        }

        .bar-fill {
            height: 100%;
            border-radius: 99px;
            background: var(--primary);
            transition: width 0.6s ease;
        }

        .bar-fill-orange { background: var(--warning); }

        .bar-count {
            font-weight: 700;
            color: var(--primary);
            min-width: 28px;
            text-align: right;
            font-size: 0.82rem;
        }

        /* =========================================================
           11. RANK BADGE
           ========================================================= */
        .rank-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 22px;
            height: 22px;
            border-radius: 50%;
            font-size: 0.7rem;
            font-weight: 700;
            color: white;
            flex-shrink: 0;
        }

        .rank-1 { background: #f59e0b; }
        .rank-2 { background: var(--secondary); }
        .rank-3 { background: #cd7f32; }
        .rank-n { background: var(--border); color: var(--text-muted); }

        /* =========================================================
           12. STATUS BADGES
           ========================================================= */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            padding: 0.2rem 0.6rem;
            border-radius: 20px;
            font-size: 0.72rem;
            font-weight: 600;
        }

        .badge-warning  { background: var(--warning-light);  color: var(--warning); }
        .badge-success  { background: var(--success-light);  color: var(--success); }
        .badge-danger   { background: #fdecea;               color: var(--danger); }
        .badge-info     { background: var(--primary-light);  color: var(--primary); }

        /* =========================================================
           13. INLINE ACTION FORMS
           ========================================================= */
        .action-form {
            display: flex;
            gap: 0.5rem;
            align-items: center;
            flex-wrap: wrap;
        }

        /* =========================================================
           14. EMPTY STATE
           ========================================================= */
        .empty-state {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2.5rem 1rem;
            color: var(--text-muted);
            gap: 0.5rem;
        }

        .empty-icon {
            font-size: 2.2rem;
            opacity: 0.5;
        }

        .empty-state p {
            font-size: 0.875rem;
        }

        /* =========================================================
           15. DOCUMENT LINK
           ========================================================= */
        .doc-link {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            color: var(--primary);
            text-decoration: none;
            font-size: 0.8rem;
            font-weight: 500;
            padding: 0.3rem 0.65rem;
            border-radius: var(--radius-sm);
            border: 1px solid var(--border);
            transition: var(--transition);
        }

        .doc-link:hover {
            background: var(--primary-light);
            border-color: var(--primary);
        }

        /* =========================================================
           16. SKILL SUGGESTED-BY CELL
           ========================================================= */
        .skill-meta {
            display: flex;
            flex-direction: column;
        }

        .skill-name-cell {
            font-weight: 600;
            color: var(--text-primary);
        }

        .skill-suggested {
            font-size: 0.75rem;
            color: var(--text-muted);
        }

        /* =========================================================
           17. TOAST NOTIFICATION SYSTEM
           ========================================================= */
        #toast-container {
            position: fixed;
            bottom: 1.75rem;
            right: 1.75rem;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 0.65rem;
            pointer-events: none;
        }

        .toast {
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            min-width: 300px;
            max-width: 400px;
            padding: 1rem 1.1rem;
            border-radius: var(--radius);
            box-shadow: var(--shadow-lg);
            pointer-events: all;
            animation: toastSlideIn 0.35s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
            position: relative;
            overflow: hidden;
        }

        .toast::before {
            content: '';
            position: absolute;
            bottom: 0; left: 0;
            height: 3px;
            width: 100%;
            animation: toastProgress 4s linear forwards;
        }

        .toast-success          { background: var(--surface); border-left: 4px solid var(--success); }
        .toast-success::before  { background: var(--success); }
        .toast-error            { background: var(--surface); border-left: 4px solid var(--danger); }
        .toast-error::before    { background: var(--danger); }
        .toast-info             { background: var(--surface); border-left: 4px solid var(--primary); }
        .toast-info::before     { background: var(--primary); }

        .toast-icon { font-size: 1.2rem; flex-shrink: 0; margin-top: 0.05rem; }

        .toast-body { flex: 1; }

        .toast-title {
            font-size: 0.875rem;
            font-weight: 700;
            color: var(--text-primary);
            line-height: 1.3;
        }

        .toast-message {
            font-size: 0.8rem;
            color: var(--text-secondary);
            margin-top: 0.15rem;
            line-height: 1.4;
        }

        .toast-close {
            background: none;
            border: none;
            cursor: pointer;
            color: var(--text-muted);
            font-size: 0.9rem;
            padding: 0;
            line-height: 1;
            flex-shrink: 0;
            transition: color 0.15s;
        }

        .toast-close:hover { color: var(--text-primary); }

        .toast-hiding {
            animation: toastSlideOut 0.25s ease-in forwards;
        }

        @keyframes toastSlideIn {
            from { transform: translateX(120%); opacity: 0; }
            to   { transform: translateX(0);    opacity: 1; }
        }

        @keyframes toastSlideOut {
            from { transform: translateX(0);    opacity: 1; }
            to   { transform: translateX(120%); opacity: 0; }
        }

        @keyframes toastProgress {
            from { width: 100%; }
            to   { width: 0%; }
        }

        /* =========================================================
           18. RESPONSIVE
           ========================================================= */
        @media (max-width: 900px) {
            .metrics-grid { grid-template-columns: 1fr; }
        }

        @media (max-width: 640px) {
            .page-wrapper { padding: 1.25rem 1rem 3rem; }
            .page-header  { flex-direction: column; }
            .global-nav   { padding: 0 1rem; }
            .nav-links    { display: none; }  /* simplify mobile nav */
        }
    </style>
</head>

<body>

<!-- ================================================================
     GLOBAL NAV
     ================================================================ -->
<nav class="global-nav" role="navigation" aria-label="Admin main navigation">

    <a href="/sikaphub_v2/admin/dashboard" class="nav-brand" aria-label="S.I.K.A.P. Hub Home">
        <div class="nav-brand-icon">🏛️</div>
        <div class="nav-brand-text">
            <span class="nav-brand-title">S.I.K.A.P. Hub</span>
            <span class="nav-brand-sub">PESO Admin Portal</span>
        </div>
    </a>

    <div class="nav-links">
        <a href="/sikaphub_v2/admin/dashboard"  class="nav-link active">Dashboard</a>
        <a href="/sikaphub_v2/admin/employers"  class="nav-link">Employers</a>
        <a href="/sikaphub_v2/admin/seekers"    class="nav-link">Seekers</a>
        <a href="/sikaphub_v2/admin/jobs"       class="nav-link">Job Postings</a>
        <a href="/sikaphub_v2/admin/skills"     class="nav-link">Skills</a>
        <div class="nav-divider"></div>
        <a href="/sikaphub_v2/admin/audit-logs" class="nav-link">Audit Log</a>
    </div>

    <div class="nav-user">
        <div class="nav-avatar" title="Logged in as <?php echo htmlspecialchars($admin_name ?? 'Admin'); ?>">
            <?php echo strtoupper(substr($admin_name ?? 'A', 0, 1)); ?>
        </div>
        <span class="nav-user-name"><?php echo htmlspecialchars($admin_name ?? 'PESO Admin'); ?></span>
        <a href="/sikaphub_v2/admin/logout" class="nav-logout" id="nav-logout-btn">Logout</a>
    </div>

</nav>


<!-- ================================================================
     TOAST CONTAINER  (Blueprint Rule §2 — No dead ends in Views)
     ================================================================ -->
<div id="toast-container" role="region" aria-label="Notifications" aria-live="polite"></div>


<!-- ================================================================
     PAGE WRAPPER
     ================================================================ -->
<main class="page-wrapper">

    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-left">
            <div class="badge-role">🔐 PESO Administrator</div>
            <h1>Command Center</h1>
            <p>Platform overview, verification queues, and skill moderation.</p>
        </div>
        <div class="page-header-right">
            <a href="/sikaphub_v2/admin/export" class="btn btn-export" id="btn-export-pdf">
                📄 Export to PDF
            </a>
        </div>
    </div>


    <!-- ============================================================
         METRICS GRID — Seekers by Barangay + Top 5 In-Demand Skills
         ============================================================ -->
    <section class="metrics-grid" aria-label="Analytics overview">

        <!-- Card: Registered Seekers by Barangay -->
        <div class="card">
            <div class="card-header">
                <div class="card-header-left">
                    <div class="card-icon card-icon-blue">📍</div>
                    <div>
                        <div class="card-title">Seekers by Barangay</div>
                        <div class="card-subtitle">Geographic distribution across Guimba</div>
                    </div>
                </div>
            </div>
            <div class="card-body" style="padding: 0;">

                <?php if (empty($geography)): ?>
                    <div class="empty-state">
                        <div class="empty-icon">🗺️</div>
                        <p>No geographic data available yet.</p>
                    </div>
                <?php else:
                    $maxGeo = max(array_column($geography, 'seeker_count')) ?: 1;
                ?>
                    <table class="data-table" aria-label="Registered seekers by barangay">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Barangay</th>
                                <th>Seekers</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($geography as $i => $geo): ?>
                                <tr>
                                    <td style="color: var(--text-muted); width: 36px; font-size: 0.75rem;">
                                        <?php echo $i + 1; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($geo['barangay_name']); ?></td>
                                    <td>
                                        <div class="bar-cell">
                                            <div class="bar-track">
                                                <div class="bar-fill"
                                                     style="width: <?php echo round(($geo['seeker_count'] / $maxGeo) * 100); ?>%">
                                                </div>
                                            </div>
                                            <span class="bar-count">
                                                <?php echo htmlspecialchars($geo['seeker_count']); ?>
                                            </span>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>

            </div>
        </div><!-- /card -->


        <!-- Card: Top 5 In-Demand Skills -->
        <div class="card">
            <div class="card-header">
                <div class="card-header-left">
                    <div class="card-icon card-icon-orange">🏅</div>
                    <div>
                        <div class="card-title">Top 5 In-Demand Skills</div>
                        <div class="card-subtitle">Based on active job requirements</div>
                    </div>
                </div>
            </div>
            <div class="card-body" style="padding: 0;">

                <?php if (empty($top_skills)): ?>
                    <div class="empty-state">
                        <div class="empty-icon">📊</div>
                        <p>No skill demand data available yet.</p>
                    </div>
                <?php else:
                    $maxSkill = max(array_column($top_skills, 'demand_count')) ?: 1;
                ?>
                    <table class="data-table" aria-label="Top 5 in-demand skills">
                        <thead>
                            <tr>
                                <th>Rank</th>
                                <th>Skill</th>
                                <th>Demand</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($top_skills as $rank => $skill): ?>
                                <tr>
                                    <td style="width: 48px;">
                                        <?php
                                            $badgeClass = match($rank) {
                                                0 => 'rank-1',
                                                1 => 'rank-2',
                                                2 => 'rank-3',
                                                default => 'rank-n',
                                            };
                                        ?>
                                        <span class="rank-badge <?php echo $badgeClass; ?>">
                                            <?php echo $rank + 1; ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars($skill['skill_name']); ?></td>
                                    <td>
                                        <div class="bar-cell">
                                            <div class="bar-track">
                                                <div class="bar-fill bar-fill-orange"
                                                     style="width: <?php echo round(($skill['demand_count'] / $maxSkill) * 100); ?>%">
                                                </div>
                                            </div>
                                            <span class="bar-count" style="color: var(--warning);">
                                                <?php echo htmlspecialchars($skill['demand_count']); ?>
                                            </span>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>

            </div>
        </div><!-- /card -->

    </section><!-- /metrics-grid -->


    <!-- ============================================================
         QUEUE 1 — Employer Verification
         ============================================================ -->
    <section class="queue-section" aria-label="Employer verification queue">
        <div class="card">
            <div class="card-header">
                <div class="card-header-left">
                    <div class="card-icon card-icon-orange">🏢</div>
                    <div>
                        <div class="card-title">Verification Queue: Pending Employers</div>
                        <div class="card-subtitle">
                            Review business permits before granting employer publishing rights.
                        </div>
                    </div>
                </div>
                <?php if (!empty($pending_employers)): ?>
                    <span class="badge badge-warning">
                        <?php echo count($pending_employers); ?> Pending
                    </span>
                <?php endif; ?>
            </div>
            <div class="card-body" style="padding: 0;">

                <?php if (empty($pending_employers)): ?>
                    <div class="empty-state">
                        <div class="empty-icon">✅</div>
                        <p>All employers are verified. No pending requests.</p>
                    </div>
                <?php else: ?>
                    <table class="data-table" aria-label="Pending employer verifications">
                        <thead>
                            <tr>
                                <th>Company</th>
                                <th>Contact Info</th>
                                <th>Business Permit</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pending_employers as $emp): ?>
                                <tr>
                                    <td>
                                        <strong><?php echo htmlspecialchars($emp['company_name']); ?></strong>
                                    </td>
                                    <td>
                                        <?php echo htmlspecialchars($emp['contact_person']); ?><br>
                                        <small style="color: var(--text-muted);">
                                            <?php echo htmlspecialchars($emp['company_phone']); ?>
                                        </small>
                                    </td>
                                    <td>
                                        <a href="/sikaphub_v2/admin/view-document?file=<?php echo urlencode($emp['business_permit']); ?>"
                                           target="_blank"
                                           rel="noopener noreferrer"
                                           class="doc-link"
                                           aria-label="View business permit for <?php echo htmlspecialchars($emp['company_name']); ?>">
                                            📄 View Document
                                        </a>
                                    </td>
                                    <td>
                                        <form method="POST"
                                              action="/sikaphub_v2/admin/verify-employer"
                                              class="action-form"
                                              id="emp-form-<?php echo (int)$emp['employer_id']; ?>">

                                            <?php echo CSRF::csrfField(); ?>

                                            <input type="hidden"
                                                   name="employer_id"
                                                   value="<?php echo (int)$emp['employer_id']; ?>">

                                            <button type="submit"
                                                    name="status"
                                                    value="Verified"
                                                    class="btn btn-success btn-sm"
                                                    id="btn-approve-emp-<?php echo (int)$emp['employer_id']; ?>">
                                                ✅ Approve
                                            </button>

                                            <button type="submit"
                                                    name="status"
                                                    value="Rejected"
                                                    class="btn btn-danger btn-sm"
                                                    id="btn-reject-emp-<?php echo (int)$emp['employer_id']; ?>">
                                                ✖ Reject
                                            </button>

                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>

            </div>
        </div>
    </section><!-- /queue 1 -->


    <!-- ============================================================
         QUEUE 2 — Pending Custom Skills (NEW FEATURE)
         ============================================================ -->
    <section class="queue-section" aria-label="Custom skill moderation queue">
        <div class="card">
            <div class="card-header">
                <div class="card-header-left">
                    <div class="card-icon card-icon-purple">💡</div>
                    <div>
                        <div class="card-title">Pending Custom Skills Queue</div>
                        <div class="card-subtitle">
                            Review skills suggested by job seekers. Approve to add to the master list.
                        </div>
                    </div>
                </div>
                <?php if (!empty($pending_skills)): ?>
                    <span class="badge badge-info">
                        <?php echo count($pending_skills); ?> Awaiting Review
                    </span>
                <?php endif; ?>
            </div>
            <div class="card-body" style="padding: 0;">

                <?php if (empty($pending_skills)): ?>
                    <div class="empty-state">
                        <div class="empty-icon">🧩</div>
                        <p>No custom skills pending review at this time.</p>
                    </div>
                <?php else: ?>
                    <table class="data-table" aria-label="Pending custom skill submissions">
                        <thead>
                            <tr>
                                <th>Skill Name</th>
                                <th>Category</th>
                                <th>Suggested By</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pending_skills as $ps): ?>
                                <tr>
                                    <td>
                                        <div class="skill-meta">
                                            <span class="skill-name-cell">
                                                <?php echo htmlspecialchars($ps['skill_name']); ?>
                                            </span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-info">
                                            <?php echo htmlspecialchars($ps['category_name'] ?? 'Uncategorized'); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="skill-suggested">
                                            <?php echo htmlspecialchars($ps['suggested_by'] ?? 'Unknown User'); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <!-- APPROVE -->
                                        <form method="POST"
                                              action="/sikaphub_v2/admin/approve-skill"
                                              class="action-form"
                                              id="skill-form-approve-<?php echo (int)$ps['skill_id']; ?>"
                                              style="display: inline-flex;">

                                            <?php echo CSRF::csrfField(); ?>

                                            <input type="hidden"
                                                   name="skill_id"
                                                   value="<?php echo (int)$ps['skill_id']; ?>">

                                            <button type="submit"
                                                    name="action"
                                                    value="approve"
                                                    class="btn btn-success btn-sm"
                                                    id="btn-approve-skill-<?php echo (int)$ps['skill_id']; ?>">
                                                ✅ Approve
                                            </button>

                                        </form>

                                        <!-- DELETE (separate form to prevent accidental combine) -->
                                        <form method="POST"
                                              action="/sikaphub_v2/admin/approve-skill"
                                              class="action-form"
                                              id="skill-form-delete-<?php echo (int)$ps['skill_id']; ?>"
                                              style="display: inline-flex; margin-left: 0.5rem;"
                                              onsubmit="return confirmSkillDelete(event, '<?php echo htmlspecialchars($ps['skill_name'], ENT_QUOTES); ?>')">

                                            <?php echo CSRF::csrfField(); ?>

                                            <input type="hidden"
                                                   name="skill_id"
                                                   value="<?php echo (int)$ps['skill_id']; ?>">

                                            <button type="submit"
                                                    name="action"
                                                    value="delete"
                                                    class="btn btn-danger btn-sm"
                                                    id="btn-delete-skill-<?php echo (int)$ps['skill_id']; ?>">
                                                🗑 Delete
                                            </button>

                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>

            </div>
        </div>
    </section><!-- /queue 2 -->

</main><!-- /page-wrapper -->


<!-- ================================================================
     GLOBAL TOAST INTERCEPTOR (Blueprint Rule §2 — NO DEAD ENDS)
     Catches: ?success=verified | ?success=skill_updated | ?error=*
     ================================================================ -->
<script>
    'use strict';

    /* ------------------------------------------------------------------
       Toast Factory
       type: 'success' | 'error' | 'info'
    ------------------------------------------------------------------ */
    function showToast(title, message, type = 'success', durationMs = 4000) {
        const container = document.getElementById('toast-container');
        if (!container) return;

        const iconMap = { success: '✅', error: '❌', info: 'ℹ️' };

        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        toast.setAttribute('role', 'alert');
        toast.innerHTML = `
            <div class="toast-icon">${iconMap[type] ?? 'ℹ️'}</div>
            <div class="toast-body">
                <div class="toast-title">${title}</div>
                ${message ? `<div class="toast-message">${message}</div>` : ''}
            </div>
            <button class="toast-close" aria-label="Dismiss notification">✕</button>
        `;

        /* Close on button click */
        toast.querySelector('.toast-close').addEventListener('click', () => dismissToast(toast));

        container.appendChild(toast);

        /* Auto-dismiss */
        const timer = setTimeout(() => dismissToast(toast), durationMs);
        toast._timer = timer;
    }

    function dismissToast(toast) {
        clearTimeout(toast._timer);
        toast.classList.add('toast-hiding');
        toast.addEventListener('animationend', () => toast.remove(), { once: true });
    }

    /* ------------------------------------------------------------------
       URL Parameter Interceptor — runs on DOMContentLoaded
       Strips the query param from the URL after reading (clean UI).
    ------------------------------------------------------------------ */
    document.addEventListener('DOMContentLoaded', () => {

        const params   = new URLSearchParams(window.location.search);
        const success  = params.get('success');
        const error    = params.get('error');
        const info     = params.get('info');

        const successMessages = {
            'verified': {
                title:   'Employer Verified',
                message: 'The employer\'s status has been updated successfully.'
            },
            'rejected': {
                title:   'Employer Rejected',
                message: 'The employer application has been rejected.'
            },
            'skill_updated': {
                title:   'Skill Queue Updated',
                message: 'The custom skill status has been saved to the master list.'
            },
            'skill_approved': {
                title:   'Skill Approved',
                message: 'The skill has been approved and added to the master skills list.'
            },
            'skill_deleted': {
                title:   'Skill Deleted',
                message: 'The custom skill suggestion has been permanently removed.'
            },
        };

        const errorMessages = {
            'unauthorized':     { title: 'Unauthorized', message: 'You do not have permission to perform that action.' },
            'invalid_id':       { title: 'Invalid Request', message: 'The resource ID was missing or malformed.' },
            'db_error':         { title: 'Database Error', message: 'A database error occurred. Please try again.' },
            'csrf_mismatch':    { title: 'Security Error', message: 'CSRF token mismatch. Please refresh and try again.' },
        };

        const infoMessages = {
            'export_queued': {
                title:   'Export Queued',
                message: 'The PDF is generating in the background.'
            },
            'under_construction': {
                title:   'Coming Soon',
                message: 'This module is currently under construction.'
            },
        };

        if (success && successMessages[success]) {
            const { title, message } = successMessages[success];
            showToast(title, message, 'success');
        } else if (success) {
            /* Catch-all for unmapped successes */
            showToast('Action Successful', 'The operation completed successfully.', 'success');
        }

        if (error && errorMessages[error]) {
            const { title, message } = errorMessages[error];
            showToast(title, message, 'error');
        } else if (error) {
            showToast('An Error Occurred', decodeURIComponent(error).replace(/\+/g, ' '), 'error');
        }

        if (info && infoMessages[info]) {
            const { title, message } = infoMessages[info];
            showToast(title, message, 'info');
        } else if (info) {
            /* Catch-all for unmapped info codes */
            showToast('Notice', decodeURIComponent(info).replace(/\+/g, ' '), 'info');
        }

        /* Clean the URL — remove query params without reloading */
        if (success || error || info) {
            const cleanUrl = window.location.pathname;
            window.history.replaceState({}, document.title, cleanUrl);
        }
    });

    /* ------------------------------------------------------------------
       Skill Delete Confirmation Guard
    ------------------------------------------------------------------ */
    function confirmSkillDelete(event, skillName) {
        if (!window.confirm(`Delete "${skillName}" permanently? This action cannot be undone.`)) {
            event.preventDefault();
            return false;
        }
        return true;
    }
</script>

</body>
</html>