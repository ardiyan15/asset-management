<?php
/**
 * Timeline riwayat aset.
 * @var array $events hasil Asset_history_model::timeline()
 * @var array $types  Asset_history_model::TYPES
 */
$tones = [
    'primary' => '#4e73df', 'success' => '#1cc88a', 'info' => '#36b9cc', 'warning' => '#f6c23e',
    'danger' => '#e74a3b', 'secondary' => '#858796', 'dark' => '#5a5c69',
];
?>
<ul class="ad-timeline">
    <?php foreach ($events as $e) : ?>
        <li data-type="<?= $e['type']; ?>">
            <span class="ad-dot" style="background: <?= $tones[$e['tone']] ?? '#858796'; ?>;" title="<?= html_escape($types[$e['type']]['label'] ?? $e['type']); ?>">
                <i class="fas <?= $types[$e['type']]['icon'] ?? 'fa-circle'; ?>"></i>
            </span>
            <div class="font-weight-bold text-gray-800"><?= html_escape($e['title']); ?></div>
            <?php if ($e['detail']) : ?><div class="small"><?= html_escape($e['detail']); ?></div><?php endif; ?>
            <div class="ad-when">
                <?= date($e['date_only'] ? 'd M Y' : 'd M Y H:i', strtotime($e['at'])); ?>
                <?php if ($e['user']) : ?> · <?= html_escape($e['user']); ?><?php endif; ?>
            </div>
        </li>
    <?php endforeach; ?>
</ul>
