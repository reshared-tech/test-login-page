<div class="pagination">
    <span>Per: (<?= $size ?>)</span>
    <span>Total: <?= $total ?></span>
    <a class="nav-btn <?= $pre ? '' : 'disabled' ?>" href="<?= pageUrl($pre) ?>">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="15 18 9 12 15 6"></polyline>
        </svg>
    </a>

    <?php foreach ($pages as $p): ?>
        <?php if ($p === '...'): ?>
            <div class="page-ellipsis">...</div>
        <?php else: ?>
            <a class="page-btn <?= $p == $page ? 'active' : '' ?>"
               href="<?= pageUrl($p) ?>"><?= $p ?></a>
        <?php endif ?>
    <?php endforeach ?>

    <a class="nav-btn <?= $next ? '' : 'disabled' ?>" href="<?= pageUrl($next) ?>">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="9 18 15 12 9 6"></polyline>
        </svg>
    </a>
</div>