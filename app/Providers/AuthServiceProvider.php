<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Notifications\Messages\MailMessage;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        ResetPassword::toMailUsing(function ($notifiable, string $token) {
            $url = url(route('password.reset', [
                'token' => $token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ], false));

            $expire = config('auth.passwords.' . config('auth.defaults.passwords') . '.expire', 60);

            return (new MailMessage)
                ->subject('Reset Password Akun SIMOTB')
                ->greeting('Halo, ' . $notifiable->name . '!')
                ->line('Kami menerima permintaan untuk mereset password akun Anda di SIMOTB - Toko Bangunan Sumber Alam Jaya.')
                ->line('Silakan klik tombol di bawah ini untuk membuat password baru.')
                ->action('Reset Password', $url)
                ->line("Tautan reset password ini akan kedaluwarsa dalam {$expire} menit.")
                ->line('Jika Anda tidak meminta reset password, abaikan email ini. Password Anda tetap aman dan tidak ada perubahan apa pun pada akun Anda.')
                ->salutation('Hormat kami, Tim SIMOTB');
        });
    }
}
