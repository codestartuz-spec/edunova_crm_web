{{-- v14: O'quvchi kabineti menyusi (faqat o'quvchi roli uchun). --}}
<x-nav-link :href="route('student.home')" icon="home" :active="request()->routeIs('student.home')">Bosh sahifa</x-nav-link>
<x-nav-link :href="route('student.groups')" icon="users" :active="request()->routeIs('student.groups')">Guruhlarim</x-nav-link>
<x-nav-link :href="route('student.payments')" icon="banknote" :active="request()->routeIs('student.payments')">To'lovlar va balans</x-nav-link>
<x-nav-link :href="route('student.attendance')" icon="calendar" :active="request()->routeIs('student.attendance')">Davomad</x-nav-link>
<x-nav-link :href="route('student.courses')" icon="book" :active="request()->routeIs('student.courses', 'student.courses.*', 'student.tests.*')">Kurs materiallari</x-nav-link>
<x-nav-link :href="route('tutorials.index')" icon="play" :active="request()->routeIs('tutorials.index', 'tutorials.show')">Video darslar</x-nav-link>
