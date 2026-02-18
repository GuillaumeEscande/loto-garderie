<?php
$pageTitle = 'Garderie — Liste des enfants';
$filter = $filter ?? 'all';
$search = $search ?? '';
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
        <a href="index.php?action=child_new" class="flex items-center justify-center gap-2 w-full py-3 px-4 mb-4 rounded-xl bg-emerald-500 text-white font-medium shadow hover:bg-emerald-600 active:scale-[0.98] transition" aria-label="Ajouter un enfant">
            <span class="text-2xl">+</span>
            <span>Ajouter un enfant</span>
        </a>

        <?php
        $searchParam = $search !== '' ? '&search=' . urlencode($search) : '';
        ?>
        <div class="mb-4 rounded-xl bg-white p-3 shadow-sm border border-slate-200">
            <p class="text-xs font-medium text-slate-500 uppercase tracking-wide mb-2">Statut</p>
            <div class="grid grid-cols-3 gap-2" role="group" aria-label="Filtrer par statut">
                <a href="index.php?action=list&amp;filter=all<?= h($searchParam) ?>" class="filter-btn flex items-center justify-center min-h-[44px] px-3 py-2.5 rounded-xl text-sm font-medium transition <?= $filter === 'all' ? 'filter-btn-active' : 'filter-btn-inactive' ?>">
                    Tous
                </a>
                <a href="index.php?action=list&amp;filter=present<?= h($searchParam) ?>" class="filter-btn flex items-center justify-center min-h-[44px] px-3 py-2.5 rounded-xl text-sm font-medium transition <?= $filter === 'present' ? 'filter-btn-active filter-btn-present' : 'filter-btn-inactive' ?>">
                    Présents
                </a>
                <a href="index.php?action=list&amp;filter=absent<?= h($searchParam) ?>" class="filter-btn flex items-center justify-center min-h-[44px] px-3 py-2.5 rounded-xl text-sm font-medium transition <?= $filter === 'absent' ? 'filter-btn-active filter-btn-absent' : 'filter-btn-inactive' ?>">
                    Absents
                </a>
            </div>
            <form method="get" action="index.php" class="mt-3">
                <input type="hidden" name="action" value="list">
                <input type="hidden" name="filter" value="<?= h($filter) ?>">
                <label for="list-search" class="sr-only">Rechercher un nom</label>
                <div class="flex gap-2">
                    <input type="search" id="list-search" name="search" value="<?= h($search) ?>" maxlength="100" class="input flex-1 min-h-[44px]" placeholder="Prénom ou nom…" autocomplete="off">
                    <button type="submit" class="shrink-0 min-h-[44px] min-w-[44px] rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center hover:bg-slate-200 active:scale-95 transition" aria-label="Rechercher">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </button>
                </div>
            </form>
        </div>

        <?php if (empty($children)): ?>
            <p class="text-slate-500 text-center py-8"><?= $filter !== 'all' || $search !== '' ? 'Aucun enfant ne correspond aux critères.' : 'Aucun enfant enregistré. Cliquez sur « Ajouter un enfant » pour commencer.' ?></p>
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
