<?php
$keys = ['success', 'error', 'warning', 'info'];
foreach ($keys as $k):
    $f = getFlash($k);
    if ($f):
        $alertType = $f['type'] ?? 'info';
        if ($alertType === 'error') $alertType = 'danger';
?>
<div class="alert alert-<?= $alertType ?> alert-dismissible fade show shadow-sm" role="alert">
    <?php if ($alertType === 'success'): ?><i class="fas fa-check-circle me-2"></i><?php endif; ?>
    <?php if ($alertType === 'danger'): ?><i class="fas fa-exclamation-circle me-2"></i><?php endif; ?>
    <?php if ($alertType === 'warning'): ?><i class="fas fa-exclamation-triangle me-2"></i><?php endif; ?>
    <?php if ($alertType === 'info'): ?><i class="fas fa-info-circle me-2"></i><?php endif; ?>
    <?= htmlspecialchars($f['message']) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php
    endif;
endforeach;
?>
