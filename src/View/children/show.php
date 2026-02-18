<?php
$pageTitle = 'Garderie — ' . h($child['firstname'] . ' ' . $child['lastname']);
require dirname(__DIR__) . '/partials/header.php';
?>
    <header class="bg-emerald-600 text-white shadow">
        <div class="max-w-2xl mx-auto px-4 py-4 flex justify-between items-start">
            <div>
                <a href="index.php" class="text-emerald-100 text-sm hover:text-white">&larr; Liste des enfants</a>
                <h1 class="text-xl font-semibold mt-1"><?= h($child['firstname'] . ' ' . $child['lastname']) ?></h1>
            </div>
            <a href="index.php?action=logout" class="text-emerald-100 text-sm hover:text-white whitespace-nowrap">Déconnexion</a>
        </div>
    </header>

    <main class="max-w-2xl mx-auto px-4 py-6 space-y-6">
        <section class="rounded-xl bg-white p-4 shadow-sm border border-slate-200">
            <h2 class="font-medium text-slate-800 mb-3">Fiche</h2>
            <p class="text-slate-600">Créée le <?= h(format_datetime($child['created_at'])) ?></p>
            <?php if (!empty($contacts)): ?>
                <h3 class="font-medium text-slate-700 mt-4 mb-2">Contacts</h3>
                <ul class="space-y-2">
                    <?php foreach ($contacts as $c): ?>
                        <li class="text-sm text-slate-600">
                            <?= h($c['firstname'] . ' ' . $c['lastname']) ?>
                            — <a href="tel:<?= h(preg_replace('/\s+/', '', $c['phone'])) ?>" class="text-emerald-600 hover:underline"><?= h($c['phone']) ?></a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
            <form action="index.php" method="post" class="mt-4 pt-4 border-t border-slate-200" onsubmit="return confirm('Supprimer définitivement cette fiche enfant ?');">
                <input type="hidden" name="action" value="child_delete">
                <input type="hidden" name="id" value="<?= (int) $child['id'] ?>">
                <button type="submit" class="px-4 py-2 rounded-lg border border-red-200 text-red-600 text-sm font-medium hover:bg-red-50">Supprimer la fiche</button>
            </form>
        </section>

        <section class="rounded-xl bg-white p-4 shadow-sm border border-slate-200">
            <h2 class="font-medium text-slate-800 mb-3">Présence</h2>
            <p class="text-sm text-slate-600 mb-4">
                <?php if ($currentStatus === 'entree'): ?>L'enfant est actuellement <strong class="text-green-700">présent</strong>.<?php endif; ?>
                <?php if ($currentStatus === 'sortie' || $currentStatus === null): ?>L'enfant n'est pas en garderie.<?php endif; ?>
            </p>
            <div class="flex gap-3">
                <?php if ($canEntree): ?>
                    <form action="index.php" method="post" class="flex-1">
                        <input type="hidden" name="action" value="log">
                        <input type="hidden" name="child_id" value="<?= (int) $child['id'] ?>">
                        <input type="hidden" name="type" value="entree">
                        <button type="submit" class="w-full py-3 px-4 rounded-xl bg-green-500 text-white font-medium shadow hover:bg-green-600 transition">Entrée</button>
                    </form>
                <?php endif; ?>
                <?php if ($canSortie): ?>
                    <form action="index.php" method="post" class="flex-1">
                        <input type="hidden" name="action" value="log">
                        <input type="hidden" name="child_id" value="<?= (int) $child['id'] ?>">
                        <input type="hidden" name="type" value="sortie">
                        <button type="submit" class="w-full py-3 px-4 rounded-xl bg-amber-500 text-white font-medium shadow hover:bg-amber-600 transition">Sortie</button>
                    </form>
                <?php endif; ?>
            </div>
        </section>

        <section class="rounded-xl bg-white p-4 shadow-sm border border-slate-200">
            <h2 class="font-medium text-slate-800 mb-3">Historique entrées / sorties</h2>
            <?php if (empty($logbook)): ?>
                <p class="text-slate-500 text-sm">Aucun enregistrement pour le moment.</p>
            <?php else: ?>
                <ul class="space-y-2">
                    <?php foreach ($logbook as $entry): ?>
                        <li class="flex items-center gap-3 py-2 border-b border-slate-100 last:border-0">
                            <span class="shrink-0 w-16 px-2 py-0.5 rounded text-xs font-medium <?= $entry['type'] === 'entree' ? 'bg-green-100 text-green-800' : 'bg-amber-100 text-amber-800' ?>">
                                <?= $entry['type'] === 'entree' ? 'Entrée' : 'Sortie' ?>
                            </span>
                            <span class="text-slate-600 text-sm"><?= h(format_datetime($entry['created_at'])) ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </section>
    </main>
<?php require dirname(__DIR__) . '/partials/footer.php';
