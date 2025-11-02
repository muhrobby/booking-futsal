<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'phone' => ['required', 'string', 'max:20'],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        event(new Registered($user = User::create($validated)));

        Auth::login($user);

        $this->redirect(route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div>
    <form wire:submit="register" class="space-y-4 sm:space-y-5">
        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Full Name')" class="text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2 block" />
            <x-text-input 
                wire:model="name" 
                id="name" 
                class="block w-full px-3 sm:px-4 py-2 border border-gray-300 rounded-lg text-xs sm:text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                type="text" 
                name="name" 
                required 
                autofocus 
                autocomplete="name" 
            />
            <x-input-error :messages="$errors->get('name')" class="mt-1 text-xs" />
        </div>

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email Address')" class="text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2 block" />
            <x-text-input 
                wire:model="email" 
                id="email" 
                class="block w-full px-3 sm:px-4 py-2 border border-gray-300 rounded-lg text-xs sm:text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                type="email" 
                name="email" 
                required 
                autocomplete="username" 
            />
            <x-input-error :messages="$errors->get('email')" class="mt-1 text-xs" />
        </div>

        <!-- Phone Number -->
        <div>
            <x-input-label for="phone" :value="__('Phone Number')" class="text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2 block" />
            <x-text-input 
                wire:model="phone" 
                id="phone" 
                class="block w-full px-3 sm:px-4 py-2 border border-gray-300 rounded-lg text-xs sm:text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                type="text" 
                name="phone" 
                placeholder="08xxxxxxxxxx"
                required 
                autocomplete="tel" 
            />
            <x-input-error :messages="$errors->get('phone')" class="mt-1 text-xs" />
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Password')" class="text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2 block" />
            <x-text-input 
                wire:model="password" 
                id="password" 
                class="block w-full px-3 sm:px-4 py-2 border border-gray-300 rounded-lg text-xs sm:text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                type="password"
                name="password"
                required 
                autocomplete="new-password" 
            />
            <x-input-error :messages="$errors->get('password')" class="mt-1 text-xs" />
            <p class="text-xs text-gray-500 mt-1">Minimal 8 karakter, gunakan huruf, angka, dan simbol</p>
        </div>

        <!-- Confirm Password -->
        <div>
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" class="text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2 block" />
            <x-text-input 
                wire:model="password_confirmation" 
                id="password_confirmation" 
                class="block w-full px-3 sm:px-4 py-2 border border-gray-300 rounded-lg text-xs sm:text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                type="password"
                name="password_confirmation" 
                required 
                autocomplete="new-password" 
            />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1 text-xs" />
        </div>

        <!-- Terms & Conditions -->
        <div class="flex items-start">
            <input 
                type="checkbox" 
                id="terms" 
                class="h-4 w-4 mt-0.5 rounded border-gray-300 text-blue-600 focus:ring-blue-500 cursor-pointer" 
                required
            >
            <label for="terms" class="ms-2 text-xs sm:text-sm text-gray-600 cursor-pointer">
                Saya setuju dengan 
                <a href="#" class="text-blue-600 hover:text-blue-700 font-semibold">Syarat & Ketentuan</a>
            </label>
        </div>

        <!-- Submit Button -->
        <x-primary-button class="w-full py-2 sm:py-3 text-xs sm:text-sm justify-center rounded-lg">
            {{ __('Create Account') }}
        </x-primary-button>
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

    <!-- Login Link -->
    <div class="text-center">
        <p class="text-xs sm:text-sm text-gray-600">
            Sudah punya akun?
            <a href="{{ route('login') }}" class="font-semibold text-blue-600 hover:text-blue-700 transition" wire:navigate>
                Login di sini
            </a>
        </p>
    </div>
</div>
