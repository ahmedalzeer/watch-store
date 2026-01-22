<template>
    <footer class="bg-gray-50 pt-16 border-t border-gray-200">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-12">
                <!-- Col 1: Logo -->
                <div>
                    <img src="https://i.ibb.co/dHx2ZR3/velstore.png" alt="Velstore" class="w-24 mb-6">
                    <p class="text-gray-500 text-sm leading-relaxed">
                        Your one-stop destination for premium products. Quality meets style in every purchase.
                    </p>
                </div>

                <!-- Col 2: Account -->
                <div>
                    <h5 class="font-bold text-gray-900 mb-4">Account</h5>
                    <ul class="space-y-2 text-sm text-gray-500">
                        <li><a href="#" class="hover:text-black transition-colors">My Account</a></li>
                        <li><a href="#" class="hover:text-black transition-colors">Wishlist</a></li>
                        <li><a href="#" class="hover:text-black transition-colors">Order History</a></li>
                    </ul>
                </div>

                <!-- Col 3: Pages -->
                <div>
                    <h5 class="font-bold text-gray-900 mb-4">Pages</h5>
                    <ul class="space-y-2 text-sm text-gray-500">
                        <li><a href="#" class="hover:text-black transition-colors">Privacy Policy</a></li>
                        <li><a href="#" class="hover:text-black transition-colors">Terms of Service</a></li>
                        <li><a href="#" class="hover:text-black transition-colors">Shipping Info</a></li>
                    </ul>
                </div>

                <!-- Col 4: Social -->
                <div>
                    <h5 class="font-bold text-gray-900 mb-4">Follow Us</h5>
                    <div class="flex items-center gap-4">
                        <a href="#"
                            class="w-10 h-10 rounded-full bg-white shadow-sm flex items-center justify-center text-gray-600 hover:bg-blue-600 hover:text-white transition-all">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#"
                            class="w-10 h-10 rounded-full bg-white shadow-sm flex items-center justify-center text-gray-600 hover:bg-sky-500 hover:text-white transition-all">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#"
                            class="w-10 h-10 rounded-full bg-white shadow-sm flex items-center justify-center text-gray-600 hover:bg-pink-600 hover:text-white transition-all">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#"
                            class="w-10 h-10 rounded-full bg-white shadow-sm flex items-center justify-center text-gray-600 hover:bg-blue-800 hover:text-white transition-all">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom Strip -->
        <div class="bg-black text-white/70 py-4 text-xs">
            <div class="container mx-auto px-4 flex flex-col md:flex-row justify-between items-center gap-2">
                <span>© 2025 Velstore Demo. All Rights Reserved.</span>
                <span>Powered by Velstore Labs</span>
            </div>
        </div>
    </footer>
</template>
