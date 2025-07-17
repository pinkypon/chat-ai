<x-layout-form>
  @section('title', 'Register')

  <div class="w-full max-w-sm sm:max-w-md">
    <!-- Logo + Name -->
    <a href="/">
      <div class="flex justify-center items-center gap-2 mb-6">
        <x-page-heading>ChatAI</x-page-heading>
        <x-logo :large="true"></x-logo>
      </div>
    </a>

    <!-- Register Card -->
    <div class="bg-white p-8 rounded-xl shadow-md w-full">
      <p class="text-center text-sm text-gray-500 mb-4">Create your account</p>

      <form method="POST" action="{{ route('register') }}" x-data="{ password: '' }">
        @csrf

        <x-forms.input
          label="Name"
          name="name"
          placeholder="John Doe"
          autocomplete="name"
        />

        <x-forms.input
          label="Email"
          name="email"
          placeholder="you@example.com"
          autocomplete="email"
        />

        <!-- Password Input with Alpine Model -->
        <x-forms.input
          label="Password"
          name="password"
          type="password"
          placeholder="••••••••"
          autocomplete="new-password"
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

        <x-forms.input
          label="Confirm Password"
          name="password_confirmation"
          type="password"
          placeholder="••••••••"
          autocomplete="new-password"
        />

        <button
          type="submit"
          class="bg-blue-600 text-white px-6 py-3 rounded w-full hover:bg-blue-700 mt-4"
        >
          Register
        </button>
      </form>

      <p class="text-sm mt-4 text-center">
        Already have an account?
        <a href="/login" class="text-blue-600 hover:underline">Sign in</a>
      </p>
    </div>
  </div>
</x-layout-form>
