<x-layout-form>
  @section('title', 'Verify Email')
  <div class="min-h-screen flex items-center justify-center bg-gray-100 px-4">
    <div class="bg-white p-8 rounded-xl shadow-md w-full max-w-md">
      <h2 class="text-xl font-bold text-center mb-4">Verify Your Email</h2>

      @if (session('resent'))
        <div class="bg-green-100 border border-green-400 text-green-700 p-3 rounded mb-4 text-sm">
          A new verification link has been sent to your email address.
        </div>
      @endif

      <p class="text-sm text-gray-600 mb-6 text-center">
        Before proceeding, please check your email for a verification link.
        If you didn't receive the email, you can request another.
      </p>

      <form method="POST" action="{{ route('verification.resend') }}">
        @csrf
        <button type="submit"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded">
          Resend Verification Email
        </button>
      </form>
    </div>
  </div>
</x-layout-form>

