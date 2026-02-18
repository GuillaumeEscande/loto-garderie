<?php
$pageTitle = 'Garderie — Inscription enfant';
$formAction = $formAction ?? 'index.php';
$backUrl = $backUrl ?? 'index.php';
$backLabel = $backLabel ?? 'Retour';
$isPublic = $isPublic ?? false;
$created = isset($_GET['created']) && $_GET['created'] === '1';
require dirname(__DIR__) . '/partials/header.php';
?>
    <header class="bg-emerald-600 text-white shadow">
        <div class="max-w-2xl mx-auto px-4 py-4 flex justify-between items-start">
            <div>
                <a href="<?= h($backUrl) ?>" class="text-emerald-100 text-sm hover:text-white">&larr; <?= h($backLabel) ?></a>
                <h1 class="text-xl font-semibold mt-1"><?= $isPublic ? 'Inscription garderie' : 'Nouvel enfant' ?></h1>
            </div>
            <?php if (!$isPublic && function_exists('is_authenticated') && is_authenticated()): ?>
                <a href="index.php?action=logout" class="text-emerald-100 text-sm hover:text-white whitespace-nowrap">Déconnexion</a>
            <?php endif; ?>
        </div>
    </header>

    <main class="max-w-2xl mx-auto px-4 py-6">
        <?php if ($created): ?>
            <p class="mb-6 rounded-xl bg-green-50 border border-green-200 text-green-800 px-4 py-3 text-sm">Fiche enregistrée. Vous pouvez en ajouter une autre ci-dessous.</p>
        <?php endif; ?>
        <form action="<?= h($formAction) ?>" method="post" class="space-y-6">
            <?php if (!$isPublic): ?>
                <input type="hidden" name="action" value="child_create">
            <?php endif; ?>
            <section class="rounded-xl bg-white p-4 shadow-sm border border-slate-200">
                <h2 class="font-medium text-slate-800 mb-4">Identité de l'enfant</h2>
                <div class="space-y-4">
                    <div>
                        <label for="firstname" class="block text-sm font-medium text-slate-600 mb-1">Prénom</label>
                        <input type="text" id="firstname" name="firstname" required maxlength="<?= (int) CHILD_FIRSTNAME_MAX ?>" class="input" placeholder="Prénom">
                    </div>
                    <div>
                        <label for="lastname" class="block text-sm font-medium text-slate-600 mb-1">Nom</label>
                        <input type="text" id="lastname" name="lastname" required maxlength="<?= (int) CHILD_LASTNAME_MAX ?>" class="input" placeholder="Nom">
                    </div>
                </div>
            </section>

            <section class="rounded-xl bg-white p-4 shadow-sm border border-slate-200">
                <h2 class="font-medium text-slate-800 mb-2">Contacts en cas de besoin</h2>
                <p class="text-sm text-slate-500 mb-4">Nom, prénom et numéro de téléphone à contacter.</p>
                <div id="contacts-container" class="space-y-4">
                    <div class="contact-row flex flex-wrap gap-3 items-end">
                        <div class="flex-1 min-w-[120px]">
                            <label class="block text-xs text-slate-500 mb-1">Nom</label>
                            <input type="text" name="contact_lastname[]" maxlength="<?= (int) CONTACT_LASTNAME_MAX ?>" class="input" placeholder="Nom">
                        </div>
                        <div class="flex-1 min-w-[120px]">
                            <label class="block text-xs text-slate-500 mb-1">Prénom</label>
                            <input type="text" name="contact_firstname[]" maxlength="<?= (int) CONTACT_FIRSTNAME_MAX ?>" class="input" placeholder="Prénom">
                        </div>
                        <div class="flex-1 min-w-[140px]">
                            <label class="block text-xs text-slate-500 mb-1">Téléphone</label>
                            <input type="tel" name="contact_phone[]" maxlength="<?= (int) CONTACT_PHONE_MAX ?>" class="input" placeholder="06 12 34 56 78">
                        </div>
                        <button type="button" class="remove-contact px-3 py-2 rounded-lg border border-slate-300 text-slate-600 text-sm hover:bg-slate-50" aria-label="Supprimer ce contact">Supprimer</button>
                    </div>
                </div>
                <button type="button" id="add-contact" class="mt-3 text-sm text-emerald-600 font-medium hover:text-emerald-700">+ Ajouter un contact</button>
            </section>

            <div class="flex gap-3">
                <a href="<?= h($backUrl) ?>" class="flex-1 py-3 px-4 rounded-xl border border-slate-300 text-slate-700 text-center font-medium hover:bg-slate-50 transition"><?= h($backLabel) ?></a>
                <button type="submit" class="flex-1 py-3 px-4 rounded-xl bg-emerald-500 text-white font-medium shadow hover:bg-emerald-600 transition">Enregistrer</button>
            </div>
        </form>
        <?php if ($isPublic): ?>
            <p class="mt-6 text-center text-sm text-slate-500">
                <a href="index.php" class="text-emerald-600 hover:underline">Espace organisateur</a>
            </p>
        <?php endif; ?>
    </main>

    <script>
        document.getElementById('add-contact').addEventListener('click', function() {
            var tpl = document.querySelector('.contact-row').cloneNode(true);
            tpl.querySelectorAll('input').forEach(function(inp) { inp.value = ''; });
            document.getElementById('contacts-container').appendChild(tpl);
        });
        document.getElementById('contacts-container').addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-contact')) {
                var rows = document.querySelectorAll('.contact-row');
                if (rows.length > 1) e.target.closest('.contact-row').remove();
            }
        });
    </script>
<?php require dirname(__DIR__) . '/partials/footer.php';
