<x-layout-form>
  <div class="w-full max-w-sm sm:max-w-md">
    <!-- Logo + Name -->
    <div class="flex justify-center items-center gap-2 mb-6">
      <x-page-heading>ChatAI</x-page-heading>
      <x-logo :large="true" />
    </div>

    <!-- Reset Password Card -->
    <div class="bg-white p-8 rounded-xl shadow-md w-full">
      <h1 class="text-center text-lg font-semibold mb-4">Reset Password</h1>

      <form method="POST" action="{{ route('password.update') }}">
        @csrf

        <input type="hidden" name="token" value="{{ $token }}">

        <x-forms.input
          label="Email"
          name="email"
          type="email"
          placeholder="you@example.com"
          autocomplete="email"
          required
        />

        <x-forms.input
          label="New Password"
          name="password"
          type="password"
          placeholder="••••••••"
          autocomplete="new-password"
          required
        />

        <x-forms.input
          label="Confirm Password"
          name="password_confirmation"
          type="password"
          placeholder="••••••••"
          autocomplete="new-password"
          required
        />

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
