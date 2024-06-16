<div>
    <button {{ $attributes->merge(['type' => 'submit', 'class' => 'flex space-x-2 items-center justify-center bg-red-600 hover:bg-red-700 rounded-sm px-6 py-3 text-gray-100 hover:shadow-xl transition duration-150']) }}>
        {{ $slot }}
    </button>
</div>