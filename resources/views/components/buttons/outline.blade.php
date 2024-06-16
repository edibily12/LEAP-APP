<div>
    <button {{ $attributes->merge(['type' => 'submit', 'class' => 'relative flex items-center justify-center border border-purple-500 text-purple-500 rounded-sm px-6 py-3 hover:shadow-xl transition duration-150']) }}>
        {{ $slot }}
    </button>
</div>