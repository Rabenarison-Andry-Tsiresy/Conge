<?php

namespace App\Controllers;

use App\Models\UserModel;

class AuthController extends BaseController
{
    public function index()
    {
        $user = $this->currentUser();
        if ($user) {
            return $this->redirectByRole($user['role']);
        }

        return redirect()->to('/login');
    }

    public function login(): string
    {
        return view('auth/login', [
            'error' => $this->session->getFlashdata('error'),
            'email' => old('email'),
        ]);
    }

    public function attempt()
    {
        $email = trim((string) $this->request->getPost('email'));
        $password = (string) $this->request->getPost('password');

        if ($email === '' || $password === '') {
            $this->session->setFlashdata('error', 'Veuillez saisir votre email et mot de passe.');
            return redirect()->back()->withInput();
        }

        $users = new UserModel();
        $user = $users->findByEmail($email);

        if (!$user || !$users->verifyPassword($user['password'], $password) || (int) $user['actif'] !== 1) {
            $this->session->setFlashdata('error', 'Identifiants incorrects. Veuillez reessayer.');
            return redirect()->back()->withInput();
        }

        $this->session->set('auth_user', [
            'id' => (int) $user['id'],
            'nom' => $user['nom'],
            'prenom' => $user['prenom'],
            'email' => $user['email'],
            'role' => $user['role'],
            'departement_id' => $user['departement_id'],
        ]);

        return $this->redirectByRole($user['role']);
    }

    public function logout()
    {
        $this->session->destroy();

        return redirect()->to('/login');
    }

    private function redirectByRole(string $role)
    {
        $role = strtolower($role);
        if ($role === 'admin') {
            return redirect()->to('/admin');
        }
        if ($role === 'rh' || $role === 'responsable rh') {
            return redirect()->to('/rh');
        }

        return redirect()->to('/employe');
    }
}
