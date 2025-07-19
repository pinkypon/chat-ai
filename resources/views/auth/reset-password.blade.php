<x-layout-form>
  @section('title', 'Reset Password')

  <div class="w-full max-w-sm sm:max-w-md">
    <!-- Logo + Name -->
    <div class="flex justify-center items-center gap-2 mb-6">
      <a href="/">
        <div class="flex justify-center items-center gap-2 mb-6">
          <x-page-heading>ChatAI</x-page-heading>
          <x-logo :large="true"></x-logo>
        </div>
      </a>
    </div>

    <!-- Reset Password Card -->
    <div class="bg-white p-8 rounded-xl shadow-md w-full">
      <h1 class="text-center text-lg font-semibold mb-4">Reset Password</h1>

      <form method="POST" action="{{ route('password.update') }}" x-data="{ password: '' }">
        @csrf

        <!-- Reset Token -->
        <input type="hidden" name="token" value="{{ $token }}">

        <!-- Email -->
        <x-forms.input
          label="Email"
          name="email"
          type="email"
          placeholder="you@example.com"
          autocomplete="email"
          required
        />

        <!-- New Password with Live Checklist -->
        <x-forms.input
          label="New Password"
          name="password"
          type="password"
          placeholder="••••••••"
          autocomplete="new-password"
          required
          x-model="password"
        />

        <!-- Live Password Checklist -->
        <div class="text-sm text-gray-600 space-y-1 mt-2 ml-1">
          <p :class="password.length >= 8 ? 'text-green-600' : 'text-gray-500'">
            • Minimum 8 characters
          </p>
          <p :class="/[A-Z]/.test(password) && /[a-z]/.test(password) ? 'text-green-600' : 'text-gray-500'">
            • Upper & lowercase letters
          </p>
          <p :class="/[0-9]/.test(password) ? 'text-green-600' : 'text-gray-500'">
            • At least one number
          </p>
          <p :class="/[^A-Za-z0-9]/.test(password) ? 'text-green-600' : 'text-gray-500'">
            • At least one symbol
          </p>
        </div>

        <!-- Confirm Password -->
        <x-forms.input
          label="Confirm Password"
          name="password_confirmation"
          type="password"
          placeholder="••••••••"
          autocomplete="new-password"
          required
        />

        <!-- Submit Button -->
        <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded w-full hover:bg-blue-700 mt-4">
          Reset Password
        </button>
      </form>

      <!-- Back to Login -->
      <p class="text-sm mt-6 text-center">
        <a href="{{ route('login') }}" class="text-blue-600 hover:underline">Back to Login</a>
      </p>
    </div>
  </div>
</x-layout-form>
