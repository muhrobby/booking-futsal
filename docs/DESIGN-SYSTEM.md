# 🎨 Design System & Component Guidelines

## Komponenten Utama

### 1. Navigation Components

#### Navbar (Member/Guest)

**Location**: `resources/views/components/navbar.blade.php`

```blade
<nav class="sticky top-0 z-50 bg-white shadow-sm border-b border-gray-200">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center h-16">

      <!-- Logo -->
      <div class="flex-shrink-0">
        <a href="{{ route('home') }}" class="text-2xl font-bold text-blue-600">
          FutsalGO
        </a>
      </div>

      <!-- Menu Desktop -->
      <div class="hidden md:block">
        <div class="ml-10 flex items-baseline space-x-4">
          <a href="{{ route('home') }}" class="px-3 py-2 rounded-md text-sm font-medium hover:bg-gray-100">
            Home
          </a>
          <a href="{{ route('schedule.index') }}" class="px-3 py-2 rounded-md text-sm font-medium hover:bg-gray-100">
            Booking
          </a>
          @auth
            <a href="{{ route('bookings.my') }}" class="px-3 py-2 rounded-md text-sm font-medium hover:bg-gray-100">
              My Bookings
            </a>
          @endauth
        </div>
      </div>

      <!-- User Menu / Auth -->
      <div class="flex items-center space-x-4">
        @auth
          <div class="relative group">
            <button class="flex items-center space-x-2 text-gray-700 hover:text-blue-600">
              <span>{{ auth()->user()->name }}</span>
              <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
              </svg>
            </button>
            <!-- Dropdown menu -->
            <div class="hidden group-hover:block absolute right-0 w-48 bg-white rounded-lg shadow-lg">
              <a href="{{ route('profile') }}" class="block px-4 py-2 text-gray-800 hover:bg-gray-100">
                Profile
              </a>
              <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full text-left px-4 py-2 text-gray-800 hover:bg-gray-100">
                  Logout
                </button>
              </form>
            </div>
          </div>
        @else
          <a href="{{ route('login') }}" class="text-gray-700 hover:text-blue-600 font-medium">
            Login
          </a>
          <a href="{{ route('register') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
            Register
          </a>
        @endauth
      </div>
    </div>
  </div>
</nav>
```

---

#### Admin Sidebar

**Location**: `resources/views/components/admin/sidebar.blade.php`

```blade
<aside class="fixed left-0 top-0 h-screen w-64 bg-gray-900 text-white p-6 overflow-y-auto z-40">
  <!-- Logo -->
  <div class="mb-8">
    <a href="{{ route('home') }}" class="text-2xl font-bold text-blue-400">
      FutsalGO Admin
    </a>
  </div>

  <!-- Navigation Menu -->
  <nav class="space-y-2">
    <!-- Dashboard -->
    <a href="{{ route('admin.dashboard') }}"
       class="block px-4 py-3 rounded-lg {{ request()->routeIs('admin.dashboard') ? 'bg-blue-600' : 'hover:bg-gray-800' }}">
      <div class="flex items-center space-x-3">
        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
          <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4z"></path>
          <path fill-rule="evenodd" d="M3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zm11 0a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z" clip-rule="evenodd"></path>
        </svg>
        <span>Dashboard</span>
      </div>
    </a>

    <!-- Fields Management -->
    <a href="{{ route('admin.fields.index') }}"
       class="block px-4 py-3 rounded-lg {{ request()->routeIs('admin.fields.*') ? 'bg-blue-600' : 'hover:bg-gray-800' }}">
      <div class="flex items-center space-x-3">
        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path>
        </svg>
        <span>Fields</span>
      </div>
    </a>

    <!-- Bookings Management -->
    <a href="{{ route('admin.bookings.index') }}"
       class="block px-4 py-3 rounded-lg {{ request()->routeIs('admin.bookings.*') ? 'bg-blue-600' : 'hover:bg-gray-800' }}">
      <div class="flex items-center space-x-3">
        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
          <path d="M5 3a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2V5a2 2 0 00-2-2H5z"></path>
        </svg>
        <span>Bookings</span>
      </div>
    </a>

    <!-- Users Management (Future) -->
    <a href="{{ route('admin.users.index') }}"
       class="block px-4 py-3 rounded-lg hover:bg-gray-800">
      <div class="flex items-center space-x-3">
        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
          <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM9 14a6 6 0 00-6-6v6a6 6 0 006 6v-6z"></path>
        </svg>
        <span>Users</span>
      </div>
    </a>
  </nav>

  <!-- Divider -->
  <hr class="my-6 border-gray-700">

  <!-- Settings -->
  <nav class="space-y-2">
    <a href="{{ route('profile') }}" class="block px-4 py-3 rounded-lg hover:bg-gray-800">
      <div class="flex items-center space-x-3">
        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"></path>
        </svg>
        <span>Settings</span>
      </div>
    </a>
  </nav>
</aside>
```

---

### 2. Card Components

#### Booking Card (Member)

**Location**: `resources/views/bookings/components/booking-card.blade.php`

```blade
<div class="bg-white rounded-lg border border-gray-200 hover:shadow-lg transition p-6">
  <!-- Header -->
  <div class="flex justify-between items-start mb-4">
    <div>
      <h3 class="text-lg font-semibold text-gray-900">{{ $booking->field->name }}</h3>
      <p class="text-sm text-gray-500">Booking ID: #{{ $booking->id }}</p>
    </div>
    <!-- Status Badge -->
    <span @class([
      'px-3 py-1 rounded-full text-sm font-medium',
      'bg-green-100 text-green-800' => $booking->status === 'confirmed',
      'bg-yellow-100 text-yellow-800' => $booking->status === 'pending',
      'bg-red-100 text-red-800' => $booking->status === 'cancelled',
      'bg-gray-100 text-gray-800' => $booking->status === 'completed',
    ])>
      {{ ucfirst($booking->status) }}
    </span>
  </div>

  <!-- Booking Details -->
  <div class="space-y-2 mb-4 text-sm">
    <div class="flex justify-between">
      <span class="text-gray-600">Date:</span>
      <span class="font-medium text-gray-900">{{ $booking->date->format('d M Y') }}</span>
    </div>
    <div class="flex justify-between">
      <span class="text-gray-600">Time:</span>
      <span class="font-medium text-gray-900">{{ $booking->start_time }} - {{ $booking->end_time }}</span>
    </div>
    <div class="flex justify-between">
      <span class="text-gray-600">Duration:</span>
      <span class="font-medium text-gray-900">{{ $booking->duration }} jam</span>
    </div>
    <div class="flex justify-between border-t pt-2">
      <span class="text-gray-600">Total:</span>
      <span class="font-semibold text-gray-900">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</span>
    </div>
  </div>

  <!-- Actions -->
  <div class="flex gap-2 pt-4 border-t">
    <a href="{{ route('bookings.show', $booking) }}" class="flex-1 text-center px-3 py-2 rounded-lg bg-blue-600 text-white text-sm font-medium hover:bg-blue-700 transition">
      View Details
    </a>
    @if($booking->status === 'pending')
      <button class="flex-1 px-3 py-2 rounded-lg border border-red-600 text-red-600 text-sm font-medium hover:bg-red-50 transition">
        Cancel
      </button>
    @endif
  </div>
</div>
```

---

#### Stats Card

**Location**: `resources/views/components/stats-card.blade.php`

```blade
<div class="bg-white rounded-lg border border-gray-200 p-6">
  <div class="flex items-center justify-between">
    <div>
      <p class="text-sm font-medium text-gray-600">{{ $title }}</p>
      <p class="text-3xl font-bold text-gray-900 mt-2">{{ $value }}</p>
      @if(isset($change))
        <p class="text-xs text-gray-500 mt-1">
          <span class="{{ str_contains($change, '+') ? 'text-green-600' : 'text-red-600' }}">
            {{ $change }}
          </span>
          dari bulan lalu
        </p>
      @endif
    </div>
    @if(isset($icon))
      <div class="p-3 bg-{{ $color ?? 'blue' }}-100 rounded-lg">
        {{ $icon }}
      </div>
    @endif
  </div>
</div>
```

---

### 3. Form Components

#### Input Component

**Location**: `resources/views/components/form/input.blade.php`

```blade
<div class="mb-4">
  <label for="{{ $name }}" class="block text-sm font-medium text-gray-700 mb-2">
    {{ $label }}
    @if($required ?? false)
      <span class="text-red-600">*</span>
    @endif
  </label>
  <input
    type="{{ $type ?? 'text' }}"
    name="{{ $name }}"
    id="{{ $name }}"
    value="{{ old($name, $value ?? '') }}"
    placeholder="{{ $placeholder ?? '' }}"
    {{ $attributes->merge(['class' => 'w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent']) }}
  />
  @error($name)
    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
  @enderror
</div>
```

---

### 4. Table Component

#### Data Table

**Location**: `resources/views/components/data-table.blade.php`

```blade
<div class="overflow-x-auto">
  <table class="w-full border-collapse">
    <thead class="bg-gray-50 border-b border-gray-200">
      <tr>
        @foreach($columns as $column)
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">
            {{ $column['label'] }}
          </th>
        @endforeach
        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">
          Actions
        </th>
      </tr>
    </thead>
    <tbody class="divide-y divide-gray-200">
      @foreach($items as $item)
        <tr class="hover:bg-gray-50 transition">
          @foreach($columns as $column)
            <td class="px-6 py-4 text-sm text-gray-900">
              @if(isset($column['formatter']))
                {!! call_user_func($column['formatter'], $item[$column['key']] ?? null, $item) !!}
              @else
                {{ $item[$column['key']] ?? '-' }}
              @endif
            </td>
          @endforeach
          <td class="px-6 py-4 text-sm space-x-2 flex">
            @if(isset($actions))
              @foreach($actions as $action)
                {{ $action($item) }}
              @endforeach
            @endif
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>
</div>

@if($items->hasPages())
  <div class="mt-6">
    {{ $items->links() }}
  </div>
@endif
```

---

## Styling Guidelines

### Button States

```css
/* Primary Button */
.btn-primary {
    @apply px-4 py-2 bg-blue-600 text-white rounded-lg font-medium;
    @apply hover:bg-blue-700 active:bg-blue-800;
    @apply transition duration-200;
}

/* Secondary Button */
.btn-secondary {
    @apply px-4 py-2 bg-gray-200 text-gray-900 rounded-lg font-medium;
    @apply hover:bg-gray-300 active:bg-gray-400;
    @apply transition duration-200;
}

/* Danger Button */
.btn-danger {
    @apply px-4 py-2 bg-red-600 text-white rounded-lg font-medium;
    @apply hover:bg-red-700 active:bg-red-800;
    @apply transition duration-200;
}

/* Disabled State */
.btn:disabled {
    @apply opacity-50 cursor-not-allowed;
}
```

### Status Badges

```css
.badge-success {
    @apply bg-green-100 text-green-800;
}
.badge-warning {
    @apply bg-yellow-100 text-yellow-800;
}
.badge-danger {
    @apply bg-red-100 text-red-800;
}
.badge-info {
    @apply bg-blue-100 text-blue-800;
}
.badge-default {
    @apply bg-gray-100 text-gray-800;
}
```

### Responsive Breakpoints

```css
/* Mobile First */
@media (min-width: 640px) {
    /* sm */
}
@media (min-width: 768px) {
    /* md */
}
@media (min-width: 1024px) {
    /* lg */
}
@media (min-width: 1280px) {
    /* xl */
}
@media (min-width: 1536px) {
    /* 2xl */
}
```

---

## Animation Patterns

### Fade In

```css
.animate-fade-in {
    animation: fadeIn 0.3s ease-in;
}

@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}
```

### Slide In

```css
.animate-slide-in {
    animation: slideIn 0.3s ease-out;
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
```

---

**Last Updated**: 28 Oktober 2025
