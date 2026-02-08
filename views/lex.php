<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lex - Seismo</title>
    <link rel="stylesheet" href="<?= getBasePath() ?>/assets/css/style.css">
</head>
<body>
    <div class="container">
        <!-- Navigation Menu -->
        <nav class="main-nav">
            <a href="?action=index" class="nav-link">
                <svg class="logo-icon" viewBox="0 0 24 16" xmlns="http://www.w3.org/2000/svg">
                    <rect width="24" height="16" fill="#FFFFC5"/>
                    <path d="M0,8 L4,12 L6,4 L10,10 L14,2 L18,8 L20,6 L24,8" stroke="#000000" stroke-width="1.5" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Feed
            </a>
            <a href="?action=feeds" class="nav-link">RSS</a>
            <a href="?action=lex" class="nav-link active" style="background-color: #f5f562; color: #000000;">Lex</a>
            <a href="?action=mail" class="nav-link">Mail</a>
            <a href="?action=substack" class="nav-link">Substack</a>
            <a href="?action=settings" class="nav-link">Settings</a>
            <a href="?action=about" class="nav-link">About</a>
        </nav>

        <header>
            <h1>Lex</h1>
            <p class="subtitle">EU &amp; Swiss legislation — finalized acts via SPARQL</p>
        </header>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="message message-success"><?= htmlspecialchars($_SESSION['success']) ?></div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="message message-error"><?= htmlspecialchars($_SESSION['error']) ?></div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <!-- Source Filter Tags -->
        <form method="get" action="" id="lex-filter-form">
            <input type="hidden" name="action" value="lex">
            <input type="hidden" name="sources_submitted" value="1">
            <div class="tag-filter-section" style="margin-bottom: 16px;">
                <div class="tag-filter-list">
                    <?php $euActive = in_array('eu', $activeSources); ?>
                    <label class="tag-filter-pill<?= $euActive ? ' tag-filter-pill-active' : '' ?>"<?= $euActive ? ' style="background-color: #f5f562;"' : '' ?>>
                        <input type="checkbox" name="sources[]" value="eu" <?= $euActive ? 'checked' : '' ?> onchange="this.form.submit()">
                        <span>🇪🇺 EU</span>
                    </label>
                    <?php $chActive = in_array('ch', $activeSources); ?>
                    <label class="tag-filter-pill<?= $chActive ? ' tag-filter-pill-active' : '' ?>"<?= $chActive ? ' style="background-color: #f5f562;"' : '' ?>>
                        <input type="checkbox" name="sources[]" value="ch" <?= $chActive ? 'checked' : '' ?> onchange="this.form.submit()">
                        <span>🇨🇭 Switzerland</span>
                    </label>
                </div>
            </div>
        </form>

        <div class="latest-entries-section">
            <div class="section-title-row">
                <h2 class="section-title" style="font-size: 14px; font-weight: 400; color: #666;">
                    <?php
                        $refreshParts = [];
                        if (!empty($lastLexRefreshDateEu)) $refreshParts[] = '🇪🇺 ' . $lastLexRefreshDateEu;
                        if (!empty($lastLexRefreshDateCh)) $refreshParts[] = '🇨🇭 ' . $lastLexRefreshDateCh;
                        if (!empty($refreshParts)):
                    ?>
                        Refreshed: <?= implode(' · ', $refreshParts) ?>
                    <?php else: ?>
                        Refreshed: Never
                    <?php endif; ?>
                </h2>
            </div>

            <?php if (empty($lexItems)): ?>
                <div class="empty-state">
                    <p>No legislation fetched yet. Click <strong>Refresh EU</strong> or <strong>Refresh CH</strong> to query the legislation databases.</p>
                </div>
            <?php else: ?>
                <?php
                    // Check if both sources are active (merged view)
                    $showSourceTag = (in_array('eu', $activeSources) && in_array('ch', $activeSources));
                ?>
                <?php foreach ($lexItems as $item): ?>
                    <?php
                        $source = $item['source'] ?? 'eu';
                        $isEu = ($source === 'eu');
                        $sourceEmoji = $isEu ? '🇪🇺' : '🇨🇭';
                        $sourceLabel = $isEu ? 'EU' : 'CH';
                        $docType = htmlspecialchars($item['document_type'] ?? 'Legislation');
                        $itemUrl = htmlspecialchars($item['eurlex_url'] ?? '#');
                        $linkLabel = $isEu ? 'EUR-Lex →' : 'Fedlex →';
                    ?>
                    <div class="entry-card">
                        <div class="entry-header">
                            <?php if ($showSourceTag): ?>
                                <span class="entry-tag" style="background-color: #f5f562; border-color: #000000;">
                                    <?= $sourceEmoji ?> <?= $sourceLabel ?>
                                </span>
                            <?php else: ?>
                                <span></span>
                            <?php endif; ?>
                            <span class="entry-tag" style="background-color: #f5f5f5;">
                                <?= $docType ?>
                            </span>
                        </div>
                        <h3 class="entry-title">
                            <a href="<?= $itemUrl ?>" target="_blank" rel="noopener">
                                <?= htmlspecialchars($item['title']) ?>
                            </a>
                        </h3>
                        <div class="entry-actions">
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <span style="font-size: 13px; color: #666666; font-family: monospace;"><?= htmlspecialchars($item['celex']) ?></span>
                                <a href="<?= $itemUrl ?>" target="_blank" rel="noopener" class="entry-link"><?= $linkLabel ?></a>
                            </div>
                            <?php if ($item['document_date']): ?>
                                <span class="entry-date"><?= date('d.m.Y', strtotime($item['document_date'])) ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Floating Refresh Button -->
    <a href="?action=refresh_all_lex" class="floating-refresh-btn" title="Fetch latest legislation from EU CELLAR and Fedlex">Refresh</a>
</body>
</html>
