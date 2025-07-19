<x-layout-form>
  @section('title', 'Forgot Password')
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

    <!-- Forgot Password Card -->
    <div class="bg-white p-8 rounded-xl shadow-md w-full">
      <h1 class="text-center text-lg font-semibold mb-4">Forgot Password</h1>
      <p class="text-sm text-gray-500 text-center mb-6">
        Enter your email address and we’ll send you a password reset link.
      </p>

      @if (session('status'))
        <div class="mb-4 text-sm text-green-600 text-center">
          {{ session('status') }}
        </div>
      @endif

      <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <x-forms.input
          label="Email"
          name="email"
          type="email"
          placeholder="you@example.com"
          autocomplete="email"
          required
        />

        <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded w-full hover:bg-blue-700 mt-4">
          Send Reset Link
        </button>
      </form>

      <!-- Back to Login -->
      <p class="text-sm mt-6 text-center">
        <a href="{{ route('login') }}" class="text-blue-600 hover:underline">Back to Login</a>
      </p>
    </div>
  </div>
</x-layout-form>
