<x-layout-form>
  @section('title', 'Register')
  <div class="w-full max-w-sm sm:max-w-md">
    <!-- Logo + Name -->
    <div class="flex justify-center items-center gap-2 mb-6">
      <x-page-heading>ChatAI</x-page-heading>
      <x-logo :large="true"></x-logo>
    </div>

    <!-- Register Card -->
    <div class="bg-white p-8 rounded-xl shadow-md w-full">
      <p class="text-center text-sm text-gray-500 mb-4">Create your account</p>
      <form method="POST" action="{{ route('register') }}">
        @csrf
          <x-forms.input label="Name" name="name" placeholder="John Doe" autocomplete="name" />
          <x-forms.input label="Email" name="email" placeholder="you@example.com" autocomplete="email" />
          <x-forms.input label="Password" name="password" type="password" placeholder="••••••••" autocomplete="new-password" />
          <x-forms.input label="Confirm Password" name="password_confirmation" type="password" placeholder="••••••••" autocomplete="new-password" />
        <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded w-full hover:bg-blue-700">Register</button>
      </form>

      <p class="text-sm mt-4 text-center">
        Already have an account?
        <a href="/login" class="text-blue-600 hover:underline">Sign in</a>
      </p>
    </div>
  </div>
</x-layout-form>