<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form wire:submit="login" class="space-y-4 sm:space-y-5">
        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email Address')" class="text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2 block" />
            <x-text-input 
                wire:model="form.email" 
                id="email" 
                class="block w-full px-3 sm:px-4 py-2 border border-gray-300 rounded-lg text-xs sm:text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                type="email" 
                name="email" 
                required 
                autofocus 
                autocomplete="username" 
            />
            <x-input-error :messages="$errors->get('form.email')" class="mt-1 text-xs" />
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Password')" class="text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2 block" />
            <x-text-input 
                wire:model="form.password" 
                id="password" 
                class="block w-full px-3 sm:px-4 py-2 border border-gray-300 rounded-lg text-xs sm:text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                type="password"
                name="password"
                required 
                autocomplete="current-password" 
            />
            <x-input-error :messages="$errors->get('form.password')" class="mt-1 text-xs" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center">
            <input 
                wire:model="form.remember" 
                id="remember" 
                type="checkbox" 
                class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 cursor-pointer" 
                name="remember"
            >
            <label for="remember" class="ms-2 text-xs sm:text-sm text-gray-600 cursor-pointer">
                {{ __('Remember me') }}
            </label>
        </div>

        <!-- Submit & Forgot Password -->
        <div class="flex flex-col gap-3 sm:gap-4 pt-2 sm:pt-3">
            <x-primary-button class="w-full py-2 sm:py-3 text-xs sm:text-sm justify-center rounded-lg">
                {{ __('Sign In') }}
            </x-primary-button>

            @if (Route::has('password.request'))
                <a class="text-center underline text-xs sm:text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition" href="{{ route('password.request') }}" wire:navigate>
                    {{ __('Forgot your password?') }}
                </a>
            @endif
        </div>
    </form>

    <!-- Divider -->
    <div class="relative my-6 sm:my-8">
        <div class="absolute inset-0 flex items-center">
            <div class="w-full border-t border-gray-300"></div>
        </div>
        <div class="relative flex justify-center text-xs sm:text-sm">
            <span class="px-2 bg-white text-gray-500">atau</span>
        </div>
    </div>

    <!-- Register Link -->
    <div class="text-center">
        <p class="text-xs sm:text-sm text-gray-600">
            Belum punya akun?
            <a href="{{ route('register') }}" class="font-semibold text-blue-600 hover:text-blue-700 transition" wire:navigate>
                Buat akun baru
            </a>
        </p>
    </div>
</div>
