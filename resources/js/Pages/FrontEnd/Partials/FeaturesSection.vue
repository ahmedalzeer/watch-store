<template>
    <section class="py-16 bg-white">
        <div class="container mx-auto px-4">
            <h2 class="text-2xl font-bold mb-10 text-gray-900 border-l-4 border-blue-600 pl-4">
                {{ $t('messages.why_choose_us') }}
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div
                    class="bg-gray-50 p-8 rounded-lg text-left hover:-translate-y-1 transition-transform duration-300 border border-gray-100">
                    <div
                        class="mb-4 w-12 h-12 flex items-center justify-center bg-white rounded-full shadow-sm text-blue-600">
                        <i class="fas fa-truck text-xl"></i>
                    </div>
                    <h3 class="font-bold text-lg mb-2">{{ $t('messages.fast_delivery') }}</h3>
                    <p class="text-sm text-gray-500 leading-relaxed">{{ $t('messages.fast_delivery_desc') }}</p>
                </div>

                <div
                    class="bg-gray-50 p-8 rounded-lg text-left hover:-translate-y-1 transition-transform duration-300 border border-gray-100">
                    <div
                        class="mb-4 w-12 h-12 flex items-center justify-center bg-white rounded-full shadow-sm text-green-600">
                        <i class="fas fa-headset text-xl"></i>
                    </div>
                    <h3 class="font-bold text-lg mb-2">{{ $t('messages.customer_support') }}</h3>
                    <p class="text-sm text-gray-500 leading-relaxed">{{ $t('messages.customer_support_desc') }}</p>
                </div>

                <div
                    class="bg-gray-50 p-8 rounded-lg text-left hover:-translate-y-1 transition-transform duration-300 border border-gray-100">
                    <div
                        class="mb-4 w-12 h-12 flex items-center justify-center bg-white rounded-full shadow-sm text-purple-600">
                        <i class="fas fa-globe text-xl"></i>
                    </div>
                    <h3 class="font-bold text-lg mb-2">{{ $t('messages.trusted_worldwide') }}</h3>
                    <p class="text-sm text-gray-500 leading-relaxed">{{ $t('messages.trusted_worldwide_desc') }}</p>
                </div>

                <div
                    class="bg-gray-50 p-8 rounded-lg text-left hover:-translate-y-1 transition-transform duration-300 border border-gray-100">
                    <div
                        class="mb-4 w-12 h-12 flex items-center justify-center bg-white rounded-full shadow-sm text-orange-600">
                        <i class="fas fa-award text-xl"></i>
                    </div>
                    <h3 class="font-bold text-lg mb-2">{{ $t('messages.years_services') }}</h3>
                    <p class="text-sm text-gray-500 leading-relaxed">{{ $t('messages.years_services_desc') }}</p>
                </div>
            </div>
        </div>
    </section>
</template>
