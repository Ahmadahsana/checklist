<!-- Start Sidebar -->
<aside id="app-menu"
    class="hs-overlay fixed inset-y-0 start-0 z-60 hidden w-sidenav min-w-sidenav bg-primary-900 overflow-y-auto -translate-x-full transform transition-all duration-200 hs-overlay-open:translate-x-0 lg:bottom-0 lg:end-auto lg:z-30 lg:block lg:translate-x-0 rtl:translate-x-full rtl:hs-overlay-open:translate-x-0 rtl:lg:translate-x-0 print:hidden [--body-scroll:true] [--overlay-backdrop:true] lg:[--overlay-backdrop:false]">

    <div class="flex flex-col h-full">
        <!-- Sidenav Logo -->
        <div class="sticky top-0 flex h-topbar items-center justify-between px-6 ">
            <a href="/" class=" w-full flex justify-center">
                <img src="/images/logo-light.png" alt="logo" class="flex h-8">
            </a>
        </div>

        <div class="p-4 h-[calc(100%-theme('spacing.topbar'))] flex-grow" data-simplebar>
            <!-- Menu -->
            <ul class="admin-menu hs-accordion-group flex w-full flex-col gap-1">

                @can('user')
                    
                
                <li class="px-3 py-2 text-sm font-medium text-default-400">User</li>

                <li class="menu-item">
                    <a href="{{ route('user.dashboard')}}"
                        class="group flex items-center gap-x-3.5 rounded-md px-3 py-2 text-sm font-medium text-default-100 transition-all hover:bg-default-100/5">
                        <i class="material-symbols-rounded text-2xl">home</i>
                        <span class="menu-text"> Dashboard </span>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{ route('user-targets.index')}}"
                        class="group flex items-center gap-x-3.5 rounded-md px-3 py-2 text-sm font-medium text-default-100 transition-all hover:bg-default-100/5">
                        <i class="material-symbols-rounded text-2xl">check_box</i>
                        <span class="menu-text"> Amal Harian </span>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{ route('user-targets.personal-progress')}}"
                        class="group flex items-center gap-x-3.5 rounded-md px-3 py-2 text-sm font-medium text-default-100 transition-all hover:bg-default-100/5">
                        <i class="material-symbols-rounded text-2xl">apps</i>
                        <span class="menu-text"> Progress Personal </span>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="/user/payment"
                        class="group flex items-center gap-x-3.5 rounded-md px-3 py-2 text-sm font-medium text-default-100 transition-all hover:bg-default-100/5">
                        <i class="i-ph-clipboard-text-duotone text-2xl"></i>
                        <span class="menu-text"> Administrasi </span>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{ route('presensi.personal') }}"
                        class="group flex items-center gap-x-3.5 rounded-md px-3 py-2 text-sm font-medium text-default-100 transition-all hover:bg-default-100/5">
                        <i class="i-ph-clipboard-text-duotone text-2xl"></i>
                        <span class="menu-text"> Presensi Personal </span>
                    </a>
                </li>
                @endcan
                {{-- <li class="menu-item">
                    <a href="{{ route('profile.edit')}}"
                        class="group flex items-center gap-x-3.5 rounded-md px-3 py-2 text-sm font-medium text-default-100 transition-all hover:bg-default-100/5">
                        <i class="i-ph-clipboard-text-duotone text-2xl"></i>
                        <span class="menu-text"> Profile </span>
                    </a>
                </li> --}}
                
                {{-- <li class="menu-item">
                    <a href="{{ route('login')}}"
                        class="group flex items-center gap-x-3.5 rounded-md px-3 py-2 text-sm font-medium text-default-100 transition-all hover:bg-default-100/5">
                        <i class="i-ph-clipboard-text-duotone text-2xl"></i>
                        <span class="menu-text"> Login </span>
                    </a>
                </li> --}}

                @can('admin')
                    
                
                <li class="px-3 py-2 text-sm font-medium text-default-400">ِAdmin</li>

                <li class="menu-item">
                    <a href="{{ route('admin.dashboard')}}"
                        class="group flex items-center gap-x-3.5 rounded-md px-3 py-2 text-sm font-medium text-default-100 transition-all hover:bg-default-100/5">
                        <i class="material-symbols-rounded text-2xl">home</i>
                        <span class="menu-text"> Dashboard </span>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{ route('programs.index')}}"
                        class="group flex items-center gap-x-3.5 rounded-md px-3 py-2 text-sm font-medium text-default-100 transition-all hover:bg-default-100/5">
                        <i class="material-symbols-rounded text-2xl">check_box</i>
                        <span class="menu-text"> Programs </span>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{ route('admin.progress.index')}}"
                        class="group flex items-center gap-x-3.5 rounded-md px-3 py-2 text-sm font-medium text-default-100 transition-all hover:bg-default-100/5">
                        <i class="i-ph-clipboard-text-duotone text-2xl"></i>
                        <span class="menu-text"> Progress </span>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="/admin/progress/user"
                        class="group flex items-center gap-x-3.5 rounded-md px-3 py-2 text-sm font-medium text-default-100 transition-all hover:bg-default-100/5">
                        <i class="i-ph-clipboard-text-duotone text-2xl"></i>
                        <span class="menu-text"> Progress User </span>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{ route('users.index')}}"
                        class="group flex items-center gap-x-3.5 rounded-md px-3 py-2 text-sm font-medium text-default-100 transition-all hover:bg-default-100/5">
                        <i class="i-ph-clipboard-text-duotone text-2xl"></i>
                        <span class="menu-text"> Daftar User </span>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{ route('kos.index')}}"
                        class="group flex items-center gap-x-3.5 rounded-md px-3 py-2 text-sm font-medium text-default-100 transition-all hover:bg-default-100/5">
                        <i class="i-ph-clipboard-text-duotone text-2xl"></i>
                        <span class="menu-text"> Daftar Kos </span>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="/admin/manage-fees"
                        class="group flex items-center gap-x-3.5 rounded-md px-3 py-2 text-sm font-medium text-default-100 transition-all hover:bg-default-100/5">
                        <i class="i-ph-clipboard-text-duotone text-2xl"></i>
                        <span class="menu-text"> Kelola Administrasi </span>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="/admin/validasi_user"
                        class="group flex items-center gap-x-3.5 rounded-md px-3 py-2 text-sm font-medium text-default-100 transition-all hover:bg-default-100/5">
                        <i class="i-ph-clipboard-text-duotone text-2xl"></i>
                        <span class="menu-text"> Validasi User </span>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{ route('presensi.index') }}"
                        class="group flex items-center gap-x-3.5 rounded-md px-3 py-2 text-sm font-medium text-default-100 transition-all hover:bg-default-100/5">
                        <i class="i-ph-clipboard-text-duotone text-2xl"></i>
                        <span class="menu-text"> Presensi Kegiatan </span>
                    </a>
                </li>
                {{-- <li class="menu-item">
                    <a href="{{ route('login')}}"
                        class="group flex items-center gap-x-3.5 rounded-md px-3 py-2 text-sm font-medium text-default-100 transition-all hover:bg-default-100/5">
                        <i class="i-ph-clipboard-text-duotone text-2xl"></i>
                        <span class="menu-text"> Login </span>
                    </a>
                </li> --}}

                
                @endcan
            </ul>
        </div>

    </div>
</aside>
<!-- End Sidebar -->