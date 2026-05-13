<footer class="bg-gray-50 border-t border-gray-200 mt-12">
    <div class="mx-auto px-4 py-8">
        <div class="grid grid-cols-1 md:grid-cols-{{ $themeSettings['layout']['footer_columns'] ?? 4 }} gap-8">
            <div>
                <h3 class="font-bold mb-3" style="font-family: {{ $themeSettings['fonts']['heading'] ?? 'Inter, sans-serif' }}">
                    About
                </h3>
                <p class="text-sm text-gray-600">
                    {{ $siteDescription ?? 'A powerful CMS portal built with Laravel.' }}
                </p>
            </div>
            <div>
                <h3 class="font-bold mb-3">Quick Links</h3>
                <ul class="space-y-2 text-sm text-gray-600">
                    <li><a href="{{ route('public.home') }}" class="hover:text-blue-600">Home</a></li>
                    <li><a href="{{ route('public.blog') }}" class="hover:text-blue-600">Blog</a></li>
                </ul>
            </div>
            <div>
                <h3 class="font-bold mb-3">Contact</h3>
                <ul class="space-y-2 text-sm text-gray-600">
                    <li><i class="fas fa-envelope mr-2"></i> info@example.com</li>
                    <li><i class="fas fa-phone mr-2"></i> +92 300 1234567</li>
                </ul>
            </div>
            <div>
                <h3 class="font-bold mb-3">Follow Us</h3>
                <div class="flex gap-3 text-xl text-gray-500">
                    <a href="#" class="hover:text-blue-600"><i class="fab fa-facebook"></i></a>
                    <a href="#" class="hover:text-blue-400"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="hover:text-pink-600"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="hover:text-blue-700"><i class="fab fa-linkedin"></i></a>
                </div>
            </div>
        </div>
        <div class="border-t border-gray-200 mt-8 pt-4 text-center text-sm text-gray-500">
            &copy; {{ date('Y') }} {{ $siteName ?? 'CMS Portal' }}. All rights reserved.
        </div>
    </div>
</footer>