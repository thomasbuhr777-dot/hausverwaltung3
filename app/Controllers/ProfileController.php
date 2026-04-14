<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class ProfileController extends BaseController
{
    public function edit()
    {
        return view('profile/edit', [
            'user'       => auth()->user(),
            'forceReset' => session()->get('force_password_reset')
        ]);
    }

    public function update()
    {
        $user      = auth()->user();
        $userModel = model(config('Auth')->userProvider);

        $password        = $this->request->getPost('password');
        $passwordConfirm = $this->request->getPost('password_confirm');
        $currentPassword = $this->request->getPost('current_password');

        // 🔒 Passwort-Zwang nach Magic-Link
        if (session()->get('force_password_reset') && empty($password)) {
            return redirect()->back()
                ->with('error', 'Sie müssen ein neues Passwort setzen.');
        }

        // Normale Profilfelder
        $data = $this->request->getPost([
            'anrede',
            'vorname',
            'nachname',
            'mobile',
        ]);

        $userModel->update($user->id, $data);

        // Passwort ändern
  if (!empty($password)) {

    if ($password !== $passwordConfirm) {
        return redirect()->back()
            ->withInput()
            ->with('error', 'Passwörter stimmen nicht überein.');
    }

    if (strlen($password) < 8) {
        return redirect()->back()
            ->withInput()
            ->with('error', 'Passwort muss mindestens 8 Zeichen lang sein.');
    }

    // 🔐 Nur prüfen, wenn KEIN Magic-Link-Reset
    if (!session()->get('force_password_reset')) {

        if (!service('passwords')->verify($currentPassword, $user->password_hash)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Aktuelles Passwort ist falsch.');
        }
    }

        $user->password = $password;
        $userModel->save($user);

        session()->remove('force_password_reset');
}

    return redirect()->to('/profile')->with('message', 'Profil aktualisiert.');
    }
}
