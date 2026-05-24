<footer class="px-6 py-10 lg:px-12 bg-linear-to-br from-blue-50 to-blue-100 text-gray-800 border-t border-blue-200">
    <div class="max-w-7xl mx-auto">
        <div class="grid gap-10 mb-10 sm:grid-cols-2 lg:grid-cols-4">

            <!-- Logo and Description -->
            <div class="sm:col-span-2">
                <a href="{{ route('home') }}" class="inline-flex items-center">
                    <img src="{{ asset('images/Emblem_of_Sri_Lanka.svg') }}" alt="logo" class="h-12 w-auto p-1">
                    <span class="ml-3 text-xl font-semibold text-blue-900 tracking-wide hidden sm:inline">
                        <span class="text-blue-500">E</span>ducation <span class="text-blue-500">M</span>anagement <span class="text-blue-500">I</span>nformation <span class="text-blue-500">S</span>ystem
                    </span>


                    <span class="ml-3 text-xl font-semibold text-blue-900 tracking-wide block lg:hidden">
                        EMIS
                    </span>
                </a>
                <p class="mt-6 text-sm leading-relaxed text-gray-700 max-w-md">
                    Empowering the nation through accessible, inclusive, and high-quality education.
                    Our mission is to foster lifelong learning and innovation for all learners.
                </p>
            </div>

            <!-- Quick Links -->
            <div class="flex flex-col gap-2 text-sm">
                <p class="text-base font-semibold text-blue-900 tracking-wide">Quick Links</p>
                <a href="https://www.moe.gov.lk/" target="_blank" class="hover:text-blue-700 transition-colors">Ministry of Education</a>
                <a href="https://www.doenets.lk/" target="_blank" class="hover:text-blue-700 transition-colors">Division of Examinations</a>
                <a href="https://www.nie.ac.lk/" target="_blank" class="hover:text-blue-700 transition-colors">National Institute of Education</a>
                <a href="http://www.edupub.gov.lk/" target="_blank" class="hover:text-blue-700 transition-colors">Educational Publications Department</a>
            </div>

            <!-- Contact and Social -->
            <div class="space-y-4">
                <p class="text-base font-semibold text-blue-900 tracking-wide">Contact Us</p>
                <div class="text-sm space-y-2">
                    <p><span class="font-medium">Address:</span> Ministry of Education, Sri Lanka</p>
                    <p><span class="font-medium">Email:</span>
                        <a href="mailto:info@moe.gov.lk" class="hover:text-blue-700 transition-colors">
                            info@moe.gov.lk
                        </a>
                    </p>
                    <p><span class="font-medium">Hotline:</span> +94 112 785 141</p>
                </div>

            </div>
        </div>

        <!-- Footer Bottom -->
        <div class="flex flex-col-reverse items-center justify-between gap-4 pt-6 border-t border-blue-200 sm:flex-row">
            <p class="text-sm text-gray-600">
                &copy; {{ now()->year }} National Education System. All Rights Reserved.
            </p>
            <ul class="flex flex-wrap items-center gap-4 text-sm text-gray-600">
                <li><a href="{{ route('privacy-policy') }}" class="hover:text-blue-700 transition-colors">Privacy Policy</a></li>
                <li><a href="{{ route('terms-of-use') }}" class="hover:text-blue-700 transition-colors">Terms of Use</a></li>
                <li><a href="{{ route('accessibility') }}" class="hover:text-blue-700 transition-colors">Accessibility</a></li>
            </ul>
        </div>
    </div>
</footer>
