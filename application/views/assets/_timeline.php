<?php
/**
 * Timeline riwayat aset.
 * @var array $events hasil Asset_history_model::timeline()
 * @var array $types  Asset_history_model::TYPES
 */
$tones = [
    'primary' => '#0f5f73', 'success' => '#2e7d4f', 'info' => '#3d6596', 'warning' => '#c98a1b',
    'danger' => '#b4321f', 'secondary' => '#9a9da3', 'dark' => '#5a5c69',
];
?>
<ul class="ad-timeline">
    <?php foreach ($events as $e) : ?>
        <li data-type="<?= $e['type']; ?>">
            <span class="ad-dot" style="background: <?= $tones[$e['tone']] ?? '#9a9da3'; ?>;" title="<?= html_escape($types[$e['type']]['label'] ?? $e['type']); ?>">
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
