<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/">Startseite</a></li>
        <li class="breadcrumb-item active" aria-current="page">
            Administration
        </li>
    </ol>
</nav>

<h1 class="header">Administration</h1>

<?php

use App\Entities\UserRole;
use function App\Helpers\getCurrentUser;
use function App\Helpers\getManageableUsers;
use function App\Helpers\getUsers;

$user = getCurrentUser() ?>

<div class="row row-cols-1 row-cols-md-2 row-cols-lg-3">
    <div class="col-md-4">
        <div class="card mb-3">
            <div class="card-header text-center">Deine Rolle</div>
            <div class="card-body text-center">
                <span class="card-title display-6"><?= $user->getRole()->displayName() ?></span>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card mb-3">
            <div class="card-header text-center">Nutzer gesamt</div>
            <div class="card-body text-center">
                <span class="card-title display-2"><?= count(getUsers()) ?></span>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card mb-3">
            <div class="card-header text-center">Nutzer im Zuständigkeitsbereich</div>
            <div class="card-body text-center">
                <span class="card-title display-2"><?= count(getManageableUsers()) ?></span>
            </div>
        </div>
    </div>
</div>

<hr>

<div class="row">
    <div class="d-grid gap-2">
        <a href="<?= base_url('admin/users') ?>" class="btn btn-primary btn-lg">
            <i class="fas fa-users fa-2x mb-2"></i><br>
            Benutzer
        </a>
        <a href="<?= base_url('admin/schools') ?>" class="btn btn-primary btn-lg">
            <i class="fas fa-school fa-2x mb-2"></i><br>
            Schulen
        </a>
        <a href="<?= base_url('admin/regions') ?>"
           class="btn btn-primary btn-lg <?= $user->getRole() != UserRole::REGION_ADMIN && $user->getRole() != UserRole::GLOBAL_ADMIN ? "disabled" : "" ?>">
            <i class="fas fa-landmark fa-2x mb-2"></i><br>
            Regionen
        </a>
        <a href="<?= base_url('admin/groups') ?>"
           class="btn btn-primary btn-lg <?= $user->getRole() != UserRole::REGION_ADMIN && $user->getRole() != UserRole::GLOBAL_ADMIN ? "disabled" : "" ?>">
            <i class="fas fa-people-group fa-2x mb-2"></i><br>
            Gruppen
        </a>
    </div>
</div>
