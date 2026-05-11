@php
    $isShowPage = request()->routeIs([
        'admin.companies.show',
        'admin.workers.show',
        'admin.lawyers.show',
    ]);

    $hasToast = ! $isShowPage && (session('toast_success') || session('toast_error'));
@endphp

@if($hasToast)
    @php
        $isSuccess = session('toast_success') ? true : false;
        $message = session('toast_success') ?? session('toast_error');
    @endphp

    <div
        id="toast-message"
        class="fixed top-6 left-1/2 z-[9999] flex w-[90%] max-w-md -translate-x-1/2 items-center gap-3 rounded-2xl border px-5 py-4 shadow-2xl transition-all duration-300
        {{ $isSuccess
            ? 'border-green-300 bg-green-50 text-green-800 shadow-green-100'
            : 'border-red-300 bg-red-50 text-red-800 shadow-red-100'
        }}"
    >
        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full
            {{ $isSuccess ? 'bg-green-600 text-white' : 'bg-red-600 text-white' }}"
        >
            {{ $isSuccess ? '✓' : '!' }}
        </div>

        <div class="flex-1">
            <p class="text-sm font-bold">
                {{ $message }}
            </p>
        </div>

        <button
            type="button"
            onclick="closeToast()"
            class="text-lg font-bold {{ $isSuccess ? 'text-green-700' : 'text-red-700' }}"
        >
            ×
        </button>
    </div>

    <script>
        function closeToast() {
            const toast = document.getElementById('toast-message');

            if (!toast) return;

            toast.style.opacity = '0';
            toast.style.transform = 'translate(-50%, -20px)';

            setTimeout(() => {
                toast.remove();
            }, 300);
        }

        setTimeout(() => {
            closeToast();
        }, 3500);
    </script>
@endif
