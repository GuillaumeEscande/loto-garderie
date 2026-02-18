<?php
$pageTitle = 'Garderie — Connexion';
$loginError = $loginError ?? false;
require dirname(__DIR__) . '/partials/header.php';
?>
    <header class="bg-emerald-600 text-white shadow">
        <div class="max-w-2xl mx-auto px-4 py-4">
            <h1 class="text-xl font-semibold">Garderie</h1>
            <p class="text-emerald-100 text-sm">Espace organisateur</p>
        </div>
    </header>

    <main class="max-w-2xl mx-auto px-4 py-8">
        <section class="rounded-xl bg-white p-6 shadow-sm border border-slate-200">
            <h2 class="font-medium text-slate-800 mb-4">Connexion</h2>
            <?php if ($loginError): ?>
                <p class="mb-4 text-red-600 text-sm">Secret incorrect.</p>
            <?php endif; ?>
            <form action="index.php" method="post" class="space-y-4">
                <input type="hidden" name="action" value="login_check">
                <div>
                    <label for="secret" class="block text-sm font-medium text-slate-600 mb-1">Secret</label>
                    <input type="password" id="secret" name="secret" required maxlength="<?= (int) ADMIN_SECRET_MAX ?>" autocomplete="current-password" class="input" placeholder="Mot de passe d'accès">
                </div>
                <button type="submit" class="w-full py-3 px-4 rounded-xl bg-emerald-500 text-white font-medium shadow hover:bg-emerald-600 transition">Accéder à l'application</button>
            </form>
            <p class="mt-4 text-sm text-slate-500">
                <a href="inscription.php" class="text-emerald-600 hover:underline">Inscription d'un enfant (accès public)</a>
            </p>
        </section>
    </main>
<?php require dirname(__DIR__) . '/partials/footer.php';
