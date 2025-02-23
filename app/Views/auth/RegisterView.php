<?php

use function App\Helpers\getParentGroupsByRegionId;
use function App\Helpers\getRegions;

?>
<main>
    <div class="container login">

        <div class="card register">
            <div class="card-header header-plain">
                <img class="mb-2 navbar-brand-logo" src="<?= base_url('/') ?>assets/img/banner_small.png"
                     alt="WaldorfConnect Logo">
                <h1 class="h2">Registrieren</h1>
            </div>
            <?php if (session('success')): ?>
                <?= form_open('register/resend', '', ['userId' => session('userId')]) ?>
                <div class="card-body">

                    <?php if (session('resend')): ?>

                        <?php if (session('resend') === 'success'): ?>
                            <div class="alert alert-success">
                                <b>Erneuter Versand erfolgreich!</b> Wir haben dir eine weitere E-Mail mit einem
                                Bestätigungslink an <?= session('email') ?> gesendet. Bitte klicke auf diesen Link,
                                um mit der Registrierung fortzufahren!
                            </div>
                        <?php else: ?>
                            <div class="alert alert-danger">
                                <b>Erneuter Versand fehlgeschlagen!</b> Wir haben versucht dir eine weitere E-Mail mit
                                Bestätigungslink an <?= session('email') ?> zu senden, aber etwas ist
                                schiefgegangen. Wende dich bitte an technik@waldorfconnect.de!
                            </div>
                        <?php endif; ?>

                    <?php else: ?>
                        <div class="alert alert-success">
                            <b>Registrierung erfolgreich!</b> Dein Account wurde angelegt. Wir haben dir nun eine <b>E-Mail
                                mit
                                einem Bestätigungslink</b> an <?= session('email') ?> gesendet. Bitte klicke auf diesen
                            Link, um mit
                            der Registrierung fortzufahren!<br>Dein Benutzername lautet:
                            <b><?= session('username') ?></b>.
                        </div>
                    <?php endif; ?>

                    <button class="btn btn-link text-dark" type="submit">Nach ein paar Minuten noch keine E-Mail
                        erhalten? Erneut anfordern!
                    </button>
                </div>
                <?= form_close(); ?>
            <?php else: ?>
                <?= form_open('register') ?>
                <div class="card-body">
                    <?php if ($error = session('error')): ?>
                        <div class="alert alert-danger">
                            <?= $error ?>
                        </div>
                    <?php endif; ?>

                    <div class="mb-3">
                        <label for="inputFirstName" class="sr-only">Vorname(n)</label>
                        <input class="form-control" id="inputFirstName" name="firstName" autocomplete="given-name"
                               placeholder="Vorname" value="<?= old('firstName') ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="inputLastName" class="sr-only">Nachname</label>
                        <input class="form-control" id="inputLastName" name="lastName" autocomplete="family-name"
                               placeholder="Nachname" value="<?= old('lastName') ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="inputEmail" class="sr-only">E-Mail</label>
                        <input type="email" class="form-control" id="inputEmail" name="email" autocomplete="email"
                               placeholder="E-Mail" value="<?= old('email') ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="inputPassword" class="sr-only">Passwort</label>
                        <input type="password" class="form-control" id="inputPassword" name="password"
                               autocomplete="new-password" placeholder="Passwort" required>
                    </div>

                    <div>
                        <label for="inputConfirmedPassword" class="sr-only">Passwort wiederholen</label>
                        <input type="password" class="form-control" id="inputConfirmedPassword"
                               name="confirmedPassword"
                               autocomplete="new-password" placeholder="Passwort wiederholen" required>
                    </div>

                </div>
                <div class="card-footer footer-plain">
                    <button class="btn btn-primary btn-block" type="submit">Registrieren</button>
                    <a class="btn btn-link text-dark"
                       href="<?= base_url('/login') ?>">Bereits registriert? Jetzt anmelden!</a>
                </div>
                <?= form_close(); ?>
            <?php endif; ?>
        </div>