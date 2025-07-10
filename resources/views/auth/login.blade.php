<x-layout-form>
  @section('title', 'Sign In - ChatAI')
  <div class="w-full max-w-sm sm:max-w-md">
    <!-- Logo + Name -->
    <div class="flex justify-center items-center gap-2 mb-6">
      <x-page-heading>ChatAI</x-page-heading>
      <x-logo :large="true"></x-logo>
    </div>

    <!-- Sign In Card -->
    <div class="bg-white p-8 rounded-xl shadow-md w-full">
      <p class="text-center text-sm text-gray-500 mb-4">Sign in to your account</p>
      <form method="POST" action="{{ route('login') }}">
        @csrf
        <x-forms.input label="Email" name="email" placeholder="you@example.com" autocomplete="email" />

        <x-forms.input label="Password" name="password" placeholder="••••••••" type="password" />

        <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded w-full hover:bg-blue-700">
          Sign In
        </button>
      </form>

      <!-- Divider -->
      <div class="flex items-center my-6">
        <hr class="flex-grow border-gray-300">
        <span class="mx-4 text-sm text-gray-400">or</span>
        <hr class="flex-grow border-gray-300">
      </div>

      <!-- Google Login Button -->
      <a href="{{ route('google.redirect') }}"
        class="flex items-center justify-center gap-2 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 font-medium py-3 px-6 rounded-lg w-full shadow-sm transition">
        <img src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg"
            alt="Google" class="w-5 h-5">
        <span>Continue with Google</span>
      </a>

      <!-- Forgot password -->
      <p class="text-sm mt-4 text-center text-gray-600">
        <a href="{{ route('password.request') }}" class="hover:underline">Forgot your password?</a>
      </p>

      <!-- Register -->
      <p class="text-sm mt-4 text-center">
        Don't have an account?
        <a href="{{ route('register') }}" class="text-blue-600 hover:underline">Register</a>
      </p>
    </div>
  </div>
</x-layout-form>
