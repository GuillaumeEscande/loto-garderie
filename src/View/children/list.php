<?php
$pageTitle = 'Garderie — Liste des enfants';
require dirname(__DIR__) . '/partials/header.php';
?>
    <header class="bg-emerald-600 text-white shadow">
        <div class="max-w-2xl mx-auto px-4 py-4 flex justify-between items-start">
            <div>
                <h1 class="text-xl font-semibold">Garderie</h1>
                <p class="text-emerald-100 text-sm">Liste des enfants</p>
            </div>
            <a href="index.php?action=logout" class="text-emerald-100 text-sm hover:text-white whitespace-nowrap">Déconnexion</a>
        </div>
    </header>

    <main class="max-w-2xl mx-auto px-4 py-6">
        <a href="index.php?action=child_new" class="flex items-center justify-center gap-2 w-full py-3 px-4 mb-6 rounded-xl bg-emerald-500 text-white font-medium shadow hover:bg-emerald-600 active:scale-[0.98] transition" aria-label="Ajouter un enfant">
            <span class="text-2xl">+</span>
            <span>Ajouter un enfant</span>
        </a>

        <?php if (empty($children)): ?>
            <p class="text-slate-500 text-center py-8">Aucun enfant enregistré. Cliquez sur « Ajouter un enfant » pour commencer.</p>
        <?php else: ?>
            <ul class="space-y-2">
                <?php foreach ($children as $child): ?>
                    <?php
                    $status = $child['last_status'] ?? null;
                    $statusLabel = $status === 'entree' ? 'Présent' : ($status === 'sortie' ? 'Sorti' : '—');
                    $statusClass = $status === 'entree' ? 'bg-green-100 text-green-800' : ($status === 'sortie' ? 'bg-slate-200 text-slate-600' : 'bg-amber-50 text-amber-800');
                    ?>
                    <li>
                        <a href="index.php?action=child&id=<?= (int) $child['id'] ?>" class="block rounded-xl bg-white p-4 shadow-sm border border-slate-200 hover:border-emerald-300 hover:shadow transition">
                            <div class="flex items-center justify-between gap-3">
                                <div class="min-w-0 flex-1">
                                    <span class="font-medium text-slate-800"><?= h($child['firstname'] . ' ' . $child['lastname']) ?></span>
                                </div>
                                <span class="shrink-0 px-2 py-1 rounded-lg text-xs font-medium <?= $statusClass ?>"><?= h($statusLabel) ?></span>
                            </div>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </main>
<?php require dirname(__DIR__) . '/partials/footer.php';
