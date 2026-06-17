<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class ForgotPassword extends Component
{
    use LivewireAlert;

    public $email = '';
    public $newPassword = null;
    public $success = false;

    public function resetPassword()
    {
        $this->validate([
            'email' => 'required|email|exists:users,email',
        ], [
            'email.exists' => 'Email tidak ditemukan.',
        ]);

        $user = User::where('email', $this->email)->first();

        $newPassword = Str::random(8);

        $user->update([
            'password' => bcrypt($newPassword),
        ]);

        $appName = config('app.name');

        Mail::raw(
            "Halo {$user->name},\n\n" .
            "Password anda telah direset. Berikut password baru anda:\n\n" .
            "Password: {$newPassword}\n\n" .
            "Segera login dan ganti password anda setelah masuk.\n\n" .
            "Salam,\n{$appName}",
            function ($message) use ($user, $appName) {
                $message->to($user->email)
                    ->subject("[{$appName}] Reset Password");
            }
        );

        $this->newPassword = $newPassword;
        $this->success = true;
    }

    public function render()
    {
        return view('livewire.auth.forgot-password')->layout('layouts.default', ['title' => 'Lupa Password']);
    }
}
