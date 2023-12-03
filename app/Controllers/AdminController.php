<?php

namespace App\Controllers;

use App\Entities\UserRole;
use App\Entities\UserStatus;
use CodeIgniter\HTTP\RedirectResponse;
use Exception;
use function App\Helpers\saveImage;
use function App\Helpers\createOrganisation;
use function App\Helpers\createImageValidationRule;
use function App\Helpers\createRegion;
use function App\Helpers\createSchool;
use function App\Helpers\deleteOrganisation;
use function App\Helpers\deleteRegion;
use function App\Helpers\deleteSchool;
use function App\Helpers\deleteUser;
use function App\Helpers\getCurrentUser;
use function App\Helpers\getOrganisationById;
use function App\Helpers\getRegionById;
use function App\Helpers\getSchoolById;
use function App\Helpers\getUserById;
use function App\Helpers\getUserByUsernameAndPassword;
use function App\Helpers\hashSSHA;
use function App\Helpers\login;
use function App\Helpers\logout;
use function App\Helpers\saveOrganisation;
use function App\Helpers\saveRegion;
use function App\Helpers\saveSchool;
use function App\Helpers\saveUser;
use function App\Helpers\queueMail;

class AdminController extends BaseController
{
    public function index(): string
    {
        return $this->render('admin/IndexView');
    }

    public function debug(): string
    {
        return $this->render('admin/DebugView');
    }

    public function accept(): string
    {
        return $this->render('admin/AcceptView');
    }

    public function acceptUser(): RedirectResponse
    {
        $userId = $this->request->getPost('id');
        $user = getUserById($userId);

        // User awaiting acceptance
        if ($user->getStatus() != UserStatus::PENDING_ACCEPT) {
            return redirect('admin/users')->with('error', 'Dieser Nutzer wurde bereits akzeptiert.');
        }

        $user->setStatus(UserStatus::OK);
        try {
            saveUser($user);
            queueMail($user->getId(), 'Konto freigegeben', view('mail/AccountAccepted', ['user' => $user]));
        } catch (Exception $e) {
            return redirect('admin/users')->with('error', 'Fehler beim Speichern: ' . $e->getMessage());
        }

        return redirect('admin/users')->with('success', $user->getName() . ' erfolgreich freigegeben!');
    }

    public function denyUser(): RedirectResponse
    {
        $userId = $this->request->getPost('id');
        $user = getUserById($userId);

        // User awaiting acceptance
        if ($user->getStatus() != UserStatus::PENDING_ACCEPT) {
            return redirect('admin/users')->with('error', 'Dieser Nutzer wurde bereits abgelehnt.');
        }

        $user->setStatus(UserStatus::DENIED);
        try {
            saveUser($user);
            queueMail($user->getId(), 'Kontoerstellung abgelehnt', view('mail/AccountDenied', ['user' => $user]));
        } catch (Exception $e) {
            return redirect('admin/users')->with('error', 'Fehler beim Speichern: ' . $e->getMessage());
        }

        return redirect('admin/users')->with('success', $user->getName() . ' erfolgreich abgelehnt!');
    }

    public function users(): string
    {
        return $this->render('admin/user/UsersView');
    }

    public function editUser(int $userId): string|RedirectResponse
    {
        $self = getCurrentUser();
        $user = getUserById($userId);

        if (!$user) {
            return redirect('admin/users')->with('error', 'Unbekannter Benutzer.');
        }

        if (!$self->mayManage($user)) {
            return redirect('admin/users')->with('error', 'Du darfst diesen Benutzer nicht bearbeiten.');
        }

        return $this->render('admin/user/UserEditView', ['user' => $user]);
    }

    public function handleEditUser(): RedirectResponse
    {
        $self = getCurrentUser();
        $userId = $this->request->getPost('id');
        $user = getUserById($userId);

        if (!$user) {
            return redirect('admin/users')->with('error', 'Unbekannter Benutzer.');
        }

        if (!$self->mayManage($user)) {
            return redirect('admin/users')->with('error', 'Du darfst diesen Benutzer nicht bearbeiten.');
        }

        $name = $this->request->getPost('name');
        $email = $this->request->getPost('email');
        $status = $this->request->getPost('status');
        $password = $this->request->getPost('password');
        $confirmedPassword = $this->request->getPost('confirmedPassword');

        $user->setName($name);
        $user->setEmail($email);

        if ($self->isGlobalAdmin()) {
            $user->setStatus(UserStatus::from($status));
        }

        // Check if user wants to change password
        if (strlen($password) > 0) {
            // Ensure matching
            if ($password != $confirmedPassword) {
                return redirect()->to('admin/user/edit/' . $userId)->with('error', 'Passwörter stimmen nicht überein.');
            }

            $user->setPassword(hashSSHA($password));
        }

        try {
            saveUser($user);
            return redirect('admin/users')->with('success', 'Benutzer bearbeitet.');
        } catch (Exception $e) {
            return redirect('admin/users')->with('error', 'Fehler beim Speichern: ' . $e->getMessage());
        }
    }

    public function handleDeleteUser(): RedirectResponse
    {
        $self = getCurrentUser();
        $userId = $this->request->getPost('id');
        $user = getUserById($userId);

        if (!$user) {
            return redirect('admin/users')->with('error', 'Unbekannter Benutzer.');
        }

        if (!$self->mayManage($user)) {
            return redirect('admin/users')->with('error', 'Du darfst diesen Benutzer nicht löschen.');
        }

        try {
            deleteUser($userId);
            return redirect('admin/users')->with('success', 'Benutzer gelöscht.');
        } catch (Exception $e) {
            return redirect('admin/users')->with('error', 'Fehler beim Löschen: ' . $e->getMessage());
        }
    }

    public function organisations(): string
    {
        return $this->render('admin/organisation/OrganisationsView');
    }

    public function createOrganisation(): string
    {
        return $this->render('admin/organisation/OrganisationCreateView');
    }

    public function handleCreateOrganisation(): RedirectResponse
    {
        $self = getCurrentUser();

        $name = $this->request->getPost('name');
        $websiteUrl = $this->request->getPost('websiteUrl');
        $regionId = $this->request->getPost('region');
        $region = getRegionById($regionId);

        if (!$region) {
            return redirect('admin/organisations')->with('error', 'Unbekannte Region.');
        }

        if (!$region->isManageableBy($self)) {
            return redirect('admin/organisations')->with('error', 'Du darfst in dieser Region keine Organisationen verwalten.');
        }

        $organisation = createOrganisation($name, $websiteUrl, $regionId);

        try {
            $id = saveOrganisation($organisation);

            // 1. Prevent a logo/image from being uploaded that is not image or bigger than 1/2MB
            if (!$this->validate(createImageValidationRule('logo', 1000, true))) {
                return redirect('admin/organisations')->with('error', $this->validator->getErrors());
            }
            if (!$this->validate(createImageValidationRule('image'))) {
                return redirect('admin/organisations')->with('error', $this->validator->getErrors());
            }

            $logoFile = $this->request->getFile('logo');
            $imageFile = $this->request->getFile('image');

            // 2. If a logo/image was uploaded save it | Logos may be SVGs, all other formats are converted to WEBP
            if ($logoFile->isValid()) {
                saveImage($logoFile, ROOTPATH . 'public/assets/img/group/' . $id, 'logo');
            }
            if ($imageFile->isValid()) {
                saveImage($imageFile, ROOTPATH . 'public/assets/img/group/' . $id, 'image');
            }

            return redirect('admin/organisations')->with('success', 'Gruppe erstellt.');
        } catch (Exception $e) {
            return redirect('admin/organisations')->with('error', 'Fehler beim Speichern: ' . $e->getMessage());
        }
    }

    public function handleDeleteOrganisation(): RedirectResponse
    {
        $self = getCurrentUser();
        $organisationId = $this->request->getPost('id');
        $organisation = getOrganisationById($organisationId);

        if (!$organisation) {
            return redirect('admin/organisations')->with('error', 'Unbekannte Organisation.');
        }

        if (!$organisation->isManageableBy($self)) {
            return redirect('admin/organisations')->with('error', 'Du darfst diese Organisation nicht löschen.');
        }

        try {
            deleteOrganisation($organisationId);
            $imagesFolder = ROOTPATH . 'public/assets/img/group/' . $organisationId;
            if (is_dir($imagesFolder)) {
                delete_files($imagesFolder, true, false, true);
                rmdir($imagesFolder);
            }
            return redirect('admin/organisations')->with('success', 'Organisation gelöscht.');
        } catch (Exception $e) {
            return redirect('admin/organisations')->with('error', 'Fehler beim Löschen: ' . $e->getMessage());
        }
    }

    public function editGroup(int $organisationId): RedirectResponse|string
    {
        $self = getCurrentUser();
        $organisation = getOrganisationById($organisationId);
        if (!$organisation) {
            return redirect('admin/organisations')->with('error', 'Unbekannte Organisation.');
        }

        if (!$organisation->isManageableBy($self)) {
            return redirect('admin/organisations')->with('error', 'Du darfst diese Organisation nicht bearbeiten.');
        }

        return $this->render('admin/organisation/OrganisationEditView', ['organisation' => $organisation]);
    }

    public function handleEditOrganisation(): RedirectResponse
    {
        $self = getCurrentUser();
        $organisationId = $this->request->getPost('id');
        $organisation = getOrganisationById($organisationId);

        if (!$organisation) {
            return redirect('admin/organisations')->with('error', 'Unbekannte Organisation.');
        }

        if (!$organisation->isManageableBy($self)) {
            return redirect('admin/organisations')->with('error', 'Du darfst diese Organisation nicht bearbeiten.');
        }

        $name = $this->request->getPost('name');
        $websiteUrl = $this->request->getPost('websiteUrl');
        $regionId = $this->request->getPost('region');

        $organisation->setName($name);
        $organisation->setWebsiteUrl($websiteUrl);
        $organisation->setRegionId($regionId);

        // 1. Prevent a logo/image from being uploaded that is not image or bigger than 1/2MB
        if (!$this->validate(createImageValidationRule('logo', 1000, true))) {
            return redirect('admin/organisations')->with('error', $this->validator->getErrors());
        }
        if (!$this->validate(createImageValidationRule('image'))) {
            return redirect('admin/organisations')->with('error', $this->validator->getErrors());
        }

        $logoFile = $this->request->getFile('logo');
        $imageFile = $this->request->getFile('image');

        // 2. If a logo/image was uploaded save it | Logos may be SVGs, all other formats are converted to WEBP
        if ($logoFile->isValid()) {
            saveImage($logoFile, ROOTPATH . 'public/assets/img/group/' . $organisationId, 'logo');
        }
        if ($imageFile->isValid()) {
            saveImage($imageFile, ROOTPATH . 'public/assets/img/group/' . $organisationId, 'image');
        }

        try {
            saveOrganisation($organisation);
            return redirect('admin/organisations')->with('success', 'Organisation bearbeitet.');
        } catch (Exception $e) {
            return redirect('admin/organisations')->with('error', 'Fehler beim Speichern: ' . $e->getMessage());
        }
    }

    public function regions(): string
    {
        return $this->render('admin/region/RegionsView');
    }

    public function createRegion(): string
    {
        return $this->render('admin/region/RegionCreateView');
    }

    public function handleCreateRegion(): RedirectResponse
    {
        $name = $this->request->getPost('name');
        $isoCode = $this->request->getPost('iso');
        $region = createRegion($name, $isoCode);

        try {
            saveRegion($region);
            return redirect('admin/regions')->with('success', 'Region erstellt.');
        } catch (Exception $e) {
            return redirect('admin/regions')->with('error', 'Fehler beim Speichern: ' . $e->getMessage());
        }
    }

    public function handleDeleteRegion(): RedirectResponse
    {
        $regionId = $this->request->getPost('id');
        $region = getRegionById($regionId);

        if (!$region) {
            return redirect('admin/regions')->with('error', 'Unbekannte Region.');
        }

        try {
            deleteRegion($regionId);
            return redirect('admin/regions')->with('success', 'Region gelöscht.');
        } catch (Exception $e) {
            return redirect('admin/regions')->with('error', 'Fehler beim Löschen: ' . $e->getMessage());
        }
    }

    public function editRegion(int $regionId): RedirectResponse|string
    {
        $region = getRegionById($regionId);
        if (!$region) {
            return redirect('admin/regions')->with('error', 'Unbekannte Region.');
        }

        return $this->render('admin/region/RegionEditView', ['region' => $region]);
    }

    public function handleEditRegion(): RedirectResponse
    {
        $regionId = $this->request->getPost('id');
        $region = getRegionById($regionId);

        if (!$region) {
            return redirect('admin/regions')->with('error', 'Unbekannte Region.');
        }

        $name = $this->request->getPost('name');
        $iso = $this->request->getPost('iso');

        $region->setName($name);
        $region->setIsoCode($iso);

        try {
            saveRegion($region);
            return redirect('admin/regions')->with('success', 'Region bearbeitet.');
        } catch (Exception $e) {
            return redirect('admin/regions')->with('error', 'Fehler beim Speichern: ' . $e->getMessage());
        }
    }
}
