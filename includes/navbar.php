<script src="https://cdn.tailwindcss.com"></script>
<nav class="bg-gray-800 p-4">
    <div class="container mx-auto flex justify-between items-center">
        <!-- Dynamic link based on user role -->
        <a href="
            <?php 
                if (isset($_SESSION['role'])) {
                    if ($_SESSION['role'] === 'admin') {
                        echo '../admin/manage_events.php';
                    } elseif ($_SESSION['role'] === 'user') {
                        echo '../user/view_events.php';
                    }
                } else {
                    echo '#'; // Default link when no session
                }
            ?>" 
            class="text-white font-bold text-xl"
        >
            My Website
        </a>

        <div class="block lg:hidden">
            <button id="menu-toggle" class="text-white focus:outline-none">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path>
                </svg>
            </button>
        </div>

        <ul id="menu" class="hidden lg:flex space-x-6 text-white">
            <?php if (isset($_SESSION['role'])): ?>
                <?php if ($_SESSION['role'] === 'admin'): ?>
                    <li><a href="../admin/manage_events.php" class="hover:text-gray-400">Manage Events</a></li>
                    <li><a href="../admin/add_event.php" class="hover:text-gray-400">Add Event</a></li>
                    <li><a href="../admin/view_users.php" class="hover:text-gray-400">View Users</a></li>
                    <li><a href="../admin/view_registrations.php" class="hover:text-gray-400">View Registrations</a></li>
                    <li><a href="../admin/view_profile.php" class="hover:text-gray-400">Profile</a></li>
                <?php elseif ($_SESSION['role'] === 'user'): ?>
                    <li><a href="../user/view_events.php" class="hover:text-gray-400">Home</a></li>
                    <li><a href="../user/registered_event.php" class="hover:text-gray-400">Registered Event</a></li>
                    <li><a href="../user/view_profile.php" class="hover:text-gray-400">Profile</a></li>
                <?php endif; ?>
                <li><a href="../logout.php" class="hover:text-gray-400">Logout</a></li>
            <?php else: ?>
                <li><a href="../login.php" class="hover:text-gray-400">Login</a></li>
                <li><a href="../register.php" class="hover:text-gray-400">Register</a></li>
            <?php endif; ?>
        </ul>
    </div>

    <ul id="mobile-menu" class="lg:hidden hidden space-y-4 mt-4 text-white text-center rounded-lg border border-solid p-5 border-white border-1">
        <?php if (isset($_SESSION['role'])): ?>
            <?php if ($_SESSION['role'] === 'admin'): ?>
                <li><a href="../admin/manage_events.php" class="hover:text-gray-400">Manage Events</a></li>
                <li><a href="../admin/add_event.php" class="hover:text-gray-400">Add Event</a></li>
                <li><a href="../admin/view_users.php" class="hover:text-gray-400">View Users</a></li>
                <li><a href="../admin/view_registrations.php" class="hover:text-gray-400">View Registrations</a></li>
                <li><a href="../admin/view_profile.php" class="hover:text-gray-400">Profile</a></li>
            <?php elseif ($_SESSION['role'] === 'user'): ?>
                <li><a href="../user/view_events.php" class="hover:text-gray-400">Home</a></li>
                <li><a href="../user/registered_event.php" class="hover:text-gray-400">Registered Event</a></li>
                <li><a href="../user/view_profile.php" class="hover:text-gray-400">Profile</a></li>
            <?php endif; ?>
            <li><a href="../logout.php" class="hover:text-gray-400">Logout</a></li>
        <?php else: ?>
            <li><a href="../login.php" class="hover:text-gray-400">Login</a></li>
            <li><a href="../register.php" class="hover:text-gray-400">Register</a></li>
        <?php endif; ?>
    </ul>
</nav>

<script>
    const menuToggle = document.getElementById('menu-toggle');
    const mobileMenu = document.getElementById('mobile-menu');
    menuToggle.addEventListener('click', () => {
        mobileMenu.classList.toggle('hidden');
    });
</script>
