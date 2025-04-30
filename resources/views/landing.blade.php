<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Rumah Prestasi Mahasiswa </title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="A fully featured admin theme which can be used to build CRM, CMS, etc." name="description">
    <meta content="MyraStudio" name="author">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- App favicon -->
    <link rel="shortcut icon" href="/images/favicon.ico">
    @vite(['resources/css/icons.css', 'resources/css/app.css',])
    @vite(['resources/js/app.js'])

    </head>

<body>

    {{-- <div class="wrapper">

        @include('layouts.shared/sidenav')

        <div class="page-content">

            @include('layouts.shared/topbar')

            <main>
                <!-- Start Content-->
                @yield('content')
            </main>

            @include('layouts.shared/footer')

        </div>

    </div>

    </div>

    @include('layouts.shared/footer-scripts') --}}

    <div class="bg-white">
        <header>
          <div class="relative bg-white">
            <div class="mx-auto flex max-w-7xl items-center justify-between p-6 md:justify-start md:space-x-10 lg:px-8">
              <div class="flex justify-start lg:w-0 lg:flex-1">
                <a href="#">
                  <span class="sr-only">SIIMAN</span>
                  <img class="h-8 w-auto sm:h-10" src="/images/logo-dark.png" alt="">
                </a>
              </div>
              <div class="-my-2 -mr-2 md:hidden">
                {{-- <button type="button" class="inline-flex items-center justify-center rounded-md bg-white p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500" aria-expanded="false">
                  <span class="sr-only">Open menu</span>
                  <!-- Heroicon name: outline/bars-3 -->
                  <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                  </svg>
                </button> --}}
                <button id="menu-button" type="button" class="md:hidden inline-flex items-center justify-center rounded-md bg-white p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500">
                    <span class="sr-only">Open menu</span>
                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16m-7 6h7" />
                    </svg>
                </button>
              </div>

              <nav class="hidden space-x-10 md:flex">
                <div class="relative">
                  <!-- Tombol Dropdown -->
                  {{-- <button id="dropdown-button" type="button" class="text-gray-500 group inline-flex items-center rounded-md bg-white text-base font-medium hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    <span>Solutions</span>
                    <svg class="text-gray-400 ml-2 h-5 w-5 group-hover:text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                      <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                    </svg>
                  </button> --}}
              
                  <!-- Dropdown Menu -->
                  <div id="dropdown-menu" class="hidden absolute z-10 -ml-4 mt-3 w-screen max-w-md transform lg:left-1/2 lg:ml-0 lg:max-w-2xl lg:-translate-x-1/2 bg-white shadow-lg ring-1 ring-black ring-opacity-5 rounded-lg">
                    <div class="relative grid gap-6 bg-white px-5 py-6 sm:gap-8 sm:p-8 lg:grid-cols-2">
                      <a href="#" class="-m-3 flex items-start rounded-lg p-3 hover:bg-gray-50">
                        <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-md bg-gradient-to-r from-purple-600 to-indigo-600 text-white">
                          <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 13.5h3.86a2.25 2.25 0 012.012 1.244l.256.512a2.25 2.25 0 002.013 1.244h3.218a2.25 2.25 0 002.013-1.244l.256-.512a2.25 2.25 0 012.013-1.244h3.859m-19.5.338V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18v-4.162c0-.224-.034-.447-.1-.661L19.24 5.338a2.25 2.25 0 00-2.15-1.588H6.911a2.25 2.25 0 00-2.15 1.588L2.35 13.177a2.25 2.25 0 00-.1.661z" />
                          </svg>
                        </div>
                        <div class="ml-4">
                          <p class="text-base font-medium text-gray-900">Inbox</p>
                          <p class="mt-1 text-sm text-gray-500">Get a better understanding of where your traffic is coming from.</p>
                        </div>
                      </a>
                    </div>
                  </div>
                </div>
              
                <a href="#" class="text-base font-medium text-gray-500 hover:text-gray-900">Home</a>
                <a href="#agenda" class="text-base font-medium text-gray-500 hover:text-gray-900">Agenda</a>
                <a href="#about" class="text-base font-medium text-gray-500 hover:text-gray-900">About</a>
                <a href="#program" class="text-base font-medium text-gray-500 hover:text-gray-900">Program</a>
              </nav>

              <div class="hidden items-center justify-end md:flex md:flex-1 lg:w-0">
                {{-- <a href="#" class="whitespace-nowrap text-base font-medium text-gray-500 hover:text-gray-900">Sign in</a> --}}
                <a href="/login" class="ml-8 inline-flex items-center justify-center whitespace-nowrap rounded-md border border-transparent bg-gradient-to-r from-purple-600 to-indigo-600 bg-origin-border px-4 py-2 text-base font-medium text-white shadow-sm hover:from-purple-700 hover:to-indigo-700">Login</a>
              </div>
            </div>
      
            <!--
              Mobile menu, show/hide based on mobile menu state.
      
              Entering: "duration-200 ease-out"
                From: "opacity-0 scale-95"
                To: "opacity-100 scale-100"
              Leaving: "duration-100 ease-in"
                From: "opacity-100 scale-100"
                To: "opacity-0 scale-95"
            -->
            <!-- Mobile Menu Button -->
{{-- <button id="menu-button" type="button" class="md:hidden inline-flex items-center justify-center rounded-md bg-white p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500">
    <span class="sr-only">Open menu</span>
    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16m-7 6h7" />
    </svg>
  </button> --}}
  
  <!-- Mobile menu -->
  <div id="mobile-menu" class="hidden absolute inset-x-0 top-0 z-30 origin-top-right transform p-2 transition md:hidden">
    <div class="divide-y-2 divide-gray-50 rounded-lg bg-white shadow-lg ring-1 ring-black ring-opacity-5">
      <div class="px-5 pt-5 pb-6">
        <div class="flex items-center justify-between">
          <div>
            <img class="h-8 w-auto" src="/images/logo-dark.png" alt="Your Company">
          </div>
          <div class="-mr-2">
            <!-- Close Menu Button -->
            <button id="close-button" type="button" class="inline-flex items-center justify-center rounded-md bg-white p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500">
              <span class="sr-only">Close menu</span>
              <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
        </div>
        <div class="mt-6">
          <nav class="grid grid-cols-1 gap-7">
            <a href="/login" class="-m-3 flex items-center rounded-lg p-3 hover:bg-gray-50">
              <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-md bg-gradient-to-r from-purple-600 to-indigo-600 text-white">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75" />
                  </svg>
                  
              </div>
              <div class="ml-4 text-base font-medium text-gray-900">Login</div>
            </a>
          </nav>
        </div>
      </div>
    </div>
  </div>
          </div>
        </header>
      
        <main>
          <!-- Hero section -->
          <div class="relative">
            <div class="absolute inset-x-0 bottom-0 h-1/2 bg-gray-100"></div>
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
              <div class="relative shadow-xl overflow-hidden rounded-2xl">
                <div class="absolute inset-0">
                  <img class="h-full w-full object-cover" src="https://images.unsplash.com/photo-1605146769289-440113cc3d00?q=80&w=2070&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D=rb-1.2.1&auto=format&fit=crop&w=2830&q=80&sat=-100" alt="People working on laptops">
                  <div class="absolute inset-0 bg-gradient-to-r from-yellow-500/50 to-yellow-800/50 mix-blend-multiply"></div>
                </div>
                <div class="relative flex py-16 px-6 sm:py-24 lg:py-32 lg:px-8">
                  <div class="lg:w-1/2">
                    <h1 class="text-left text-4xl font-bold tracking-tight sm:text-5xl lg:text-6xl">
                      <span class="block text-white">Siiman</span>
                      <span class="block text-white">Rumah Prestasi Mahasiswa</span>
                    </h1>
                    
                    <div class="mx-auto mt-10 max-w-sm sm:flex sm:max-w-none sm:justify-center rounded-3xl overflow-hidden">
                      <img src="/images/blocks.png" alt="" class="w-full h-auto object-cover">
                    </div>
                  </div>
                  <div class="hidden lg:block absolute top-0 right-0 px-15 lg:w-1/2 h-full ">
                    <div class="bg-white/50 w-full h-full flex justify-center ">
                      <img class="h-full" src="/images/rpm kecil.png" class="" alt="">
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
      
          <!-- Logo Cloud -->
          {{-- <div class="bg-gray-100">
            <div class="mx-auto max-w-7xl py-16 px-6 lg:px-8">
              <p class="text-center text-base font-semibold text-gray-500">Trusted by over 5 very average small businesses</p>
              <div class="mt-6 grid grid-cols-2 gap-8 md:grid-cols-6 lg:grid-cols-5">
                <div class="col-span-1 flex justify-center md:col-span-2 lg:col-span-1">
                  <img class="h-12" src="https://tailwindui.com/img/logos/tuple-logo-gray-400.svg" alt="Tuple">
                </div>
                <div class="col-span-1 flex justify-center md:col-span-2 lg:col-span-1">
                  <img class="h-12" src="https://tailwindui.com/img/logos/mirage-logo-gray-400.svg" alt="Mirage">
                </div>
                <div class="col-span-1 flex justify-center md:col-span-2 lg:col-span-1">
                  <img class="h-12" src="https://tailwindui.com/img/logos/statickit-logo-gray-400.svg" alt="StaticKit">
                </div>
                <div class="col-span-1 flex justify-center md:col-span-2 md:col-start-2 lg:col-span-1">
                  <img class="h-12" src="https://tailwindui.com/img/logos/transistor-logo-gray-400.svg" alt="Transistor">
                </div>
                <div class="col-span-2 flex justify-center md:col-span-2 md:col-start-4 lg:col-span-1">
                  <img class="h-12" src="https://tailwindui.com/img/logos/workcation-logo-gray-400.svg" alt="Workcation">
                </div>
              </div>
            </div>
          </div> --}}
      
          <!-- Alternating Feature Sections -->
          <div class="relative overflow-hidden pt-8 pb-9 lg:pt-16 lg:pb-32" id="agenda">
            <div class="bg-white">
              <div class="mx-auto max-w-4xl py-8 px-6 sm:py-24 lg:flex lg:max-w-7xl lg:items-center lg:justify-between lg:px-8">
                <h2 class="text-2xl lg:text-4xl font-bold tracking-tight text-gray-900 sm:text-4xl">
                  <span class="block">Agenda pekan ini</span>
                  <span class="-mb-1 lg:mt-3 block bg-gradient-to-r from-purple-600 to-indigo-600 bg-clip-text pb-1 text-transparent">" Sehari bersama Al Quran "</span>
                </h2>
                <div class="mt-6 space-y-4 sm:flex sm:space-y-0 sm:space-x-5">
                  <a href="#" class="flex items-center justify-center rounded-md border border-transparent bg-gradient-to-r from-purple-600 to-indigo-600 bg-origin-border px-4 py-3 text-base font-medium text-white shadow-sm hover:from-purple-700 hover:to-indigo-700">Join Sekarang</a>
                  {{-- <a href="#" class="flex items-center justify-center rounded-md border border-transparent bg-indigo-50 px-4 py-3 text-base font-medium text-indigo-800 shadow-sm hover:bg-indigo-100">Get started</a> --}}
                </div>
              </div>
            </div>
            <div aria-hidden="true" class="absolute inset-x-0 top-0 h-48 bg-gradient-to-b from-gray-100"></div>
            <div class="relative">
              <div class="lg:mx-auto lg:grid lg:max-w-7xl lg:grid-flow-col-dense lg:grid-cols-2 lg:gap-24 lg:px-8">
                <div class="mt-3 lg:mt-0">
                  <div class=" flex justify-center lg:relative lg:m-0 lg:h-full lg:px-0">
                    <img class="w-64 rounded-xl shadow-xl ring-1 ring-black ring-opacity-5 lg:absolute lg:left-0 lg:h-full lg:w-auto lg:max-w-none" src="/images/rpm_agenda.jpg" alt="Inbox user interface">
                  </div>
                </div>
                <div class="mx-auto max-w-xl px-6 lg:mx-0 lg:max-w-none lg:py-16 lg:px-0 mt-8 lg:mt-0">
                  <p class=" text-justify">
                    

                    🪄🎀 SEHARI BERSAMA QURAN🎀🪄

                    🗓 Sabtu, 15 Maret 2025
                    ⏰ 08.00- selesai
                    📍Masjid Ayodya, Sekaran
                    https://maps.app.goo.gl/3crxhLK72i3jmRELA

                    Special Kajian Bareng:
                    🎀Ustadzah Ani Rahmawati Dewi, S.Pd

                    Rangkaian kegiatan :
                    1. Halaqoh Tilawah
                    2. Halaqoh Tahsin
                    3. Kajian Muslimah
                    4. Ifthar Bersama

                    Apa yang perlu dibawa?
                    1. Niat karena Allah taala
                    2. Al Quran 
                    3. Alat tulis
                    4. Hati bahagiaaa
                    5. Rasa Semangatt

                    SEMUA ANGGOTA RPM WAJIB MENGIKUTI KEGIATAN INI (konfirmasi kehadiran lewat PJ kost)💗🌷

                    📍 TERBUKA UNTUK UMUM (Muslimah Only)😚🫱🏻‍🫲🏻

                    Daftar di sini : 
                    https://forms.gle/98fJUEECDfa13Nci7

                    CP : +62 838-3624-6830 (Hanin)

                    Jangan lupaa datang yaa, diharapkan datang tepat waktu🙂‍↕🎈

                    Wassalamu'alaikum warahmatullahi wabarakatuh.

                    #RPTM 
                    #BERPRESTASIDANBERKARAKTER 
                    #SEHARIBERSAMAQURAN 
                    #RUMAHPRESTASIDANTAHFIDZMAHASISWA

                  </p>
                </div>
                
              </div>
            </div>
            {{-- <div class="mt-24">
              <div class="lg:mx-auto lg:grid lg:max-w-7xl lg:grid-flow-col-dense lg:grid-cols-2 lg:gap-24 lg:px-8">
                <div class="mx-auto max-w-xl px-6 lg:col-start-2 lg:mx-0 lg:max-w-none lg:py-32 lg:px-0">
                  <div>
                    <div>
                      <span class="flex h-12 w-12 items-center justify-center rounded-md bg-gradient-to-r from-purple-600 to-indigo-600">
                        <!-- Heroicon name: outline/sparkles -->
                        <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 00-2.456 2.456zM16.894 20.567L16.5 21.75l-.394-1.183a2.25 2.25 0 00-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 001.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 001.423 1.423l1.183.394-1.183.394a2.25 2.25 0 00-1.423 1.423z" />
                        </svg>
                      </span>
                    </div>
                    <div class="mt-6">
                      <h2 class="text-3xl font-bold tracking-tight text-gray-900">Better understand your customers</h2>
                      <p class="mt-4 text-lg text-gray-500">Semper curabitur ullamcorper posuere nunc sed. Ornare iaculis bibendum malesuada faucibus lacinia porttitor. Pulvinar laoreet sagittis viverra duis. In venenatis sem arcu pretium pharetra at. Lectus viverra dui tellus ornare pharetra.</p>
                      <div class="mt-6">
                        <a href="#" class="inline-flex rounded-md border border-transparent bg-gradient-to-r from-purple-600 to-indigo-600 bg-origin-border px-4 py-2 text-base font-medium text-white shadow-sm hover:from-purple-700 hover:to-indigo-700">Get started</a>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="mt-12 sm:mt-16 lg:col-start-1 lg:mt-0">
                  <div class="-ml-48 pr-6 md:-ml-16 lg:relative lg:m-0 lg:h-full lg:px-0">
                    <img class="w-full rounded-xl shadow-xl ring-1 ring-black ring-opacity-5 lg:absolute lg:right-0 lg:h-full lg:w-auto lg:max-w-none" src="https://tailwindui.com/img/component-images/inbox-app-screenshot-2.jpg" alt="Customer profile user interface">
                  </div>
                </div>
              </div>
            </div> --}}
          </div>
      
          <!-- Gradient Feature Section -->
          <div class="bg-gradient-to-r from-purple-800 to-indigo-700 rounded-t-2xl" id="about">
            <div class="mx-auto max-w-4xl py-16 px-6 sm:pt-20 sm:pb-24 lg:max-w-7xl lg:px-8 lg:pt-24">
              <h2 class="text-3xl font-bold tracking-tight text-white">Selayang pandang </h2>
              <p class="mt-4 max-w-3xl text-lg text-purple-200">
                Rumah Prestasi dan Tahfidz UNNES merupakan hunian mahasiswi khusus muslimah yang berorientasi mewujudkan insan cendekia yang berdaya guna tinggi di lingkungan luas. Tidak hanya membentuk mahasiswi yang berprestasi, melainkan melahirkan mahasiswi muslim yang berkarakter dengan didukung lingkungan yang nyaman dan kondusif serta program aktualisasi diri yang dapat mengembangkan potensi yang ada dalam diri mahasiswi.
              </p>
              <div class="mt-12 ">
                <div class="rounded-2xl overflow-hidden">
                  <img src="/images/blok1.jpg" alt="">
                </div>
              </div>
            </div>
          </div>
      
          <!-- Stats section -->
          <div class="relative bg-gray-900 rounded-b-2xl overflow-hidden">
            <div class="absolute inset-x-0 bottom-0 h-80 xl:top-0 xl:h-full">
              <div class="h-full w-full xl:grid xl:grid-cols-2">
                <div class="h-full xl:relative xl:col-start-2">
                  <img class="h-full w-full object-cover opacity-25 xl:absolute xl:inset-0" src="/images/visimisi.webp" alt="People working on laptops">
                  <div aria-hidden="true" class="absolute inset-x-0 top-0 h-32 bg-gradient-to-b from-gray-900 xl:inset-y-0 xl:left-0 xl:h-full xl:w-32 xl:bg-gradient-to-r"></div>
                </div>
              </div>
            </div>
            <div class="mx-auto max-w-4xl px-6 lg:max-w-7xl lg:px-8 xl:grid xl:grid-flow-col-dense xl:grid-cols-2 xl:gap-x-8">
              <div class="relative pt-12 pb-64 sm:pt-24 sm:pb-64 xl:col-start-1 xl:pb-24">
                <p class="mt-5 text-3xl font-bold tracking-tight text-white">Visi</p>
                <p class="mt-3 text-lg text-gray-300">
                  Melahirkan mahasiswi yang berprestasi dan berkarakter di lingkungan civitas akademika dan lingkungan luas
                </p>
                
                
                <p class="mt-5 text-3xl font-bold tracking-tight text-white">Misi</p>
                <p class="mt-3 text-lg text-gray-300">
                  1.	Memberikan pendidikan karakter kepada mahasiswa Rumah Prestasi dan Tahfidz UNNES <br>
                  2.	Menerapkan nilai-nilai karakter muslim dalam kehidupan sehari-hari <br>
                  3.	Memberdayakan potensi mahasiswi dalam rangka optimalisasi prestasi <br>
                  4.	Aktif berkontribusi dalam kehidupan bermasyarakat, berbangsa dan bernegara. <br>

                </p>
              </div>
            </div>
          </div>
      
          <!-- CTA Section -->
          <div class="bg-white" id="program">
            <div class="mx-auto max-w-4xl py-16 px-6 sm:py-24 lg:flex lg:max-w-7xl lg:items-center lg:justify-between lg:px-8">
              <h2 class="text-4xl font-bold tracking-tight text-gray-900 sm:text-4xl">
                <span class="block">
                  Program wajib
                </span>
                <span class="-mb-1 block bg-gradient-to-r from-purple-600 to-indigo-600 bg-clip-text pb-1 text-transparent">Character building class</span>
              </h2>
              <div class="mt-6 space-y-4 sm:flex sm:space-y-0 sm:space-x-5 rounded-xl overflow-hidden">
                <img src="/images/build.png" class="w-96" alt="">
              </div>
            </div>
          </div>
        </main>
      
        <footer class="bg-gray-50" aria-labelledby="footer-heading">
          <h2 id="footer-heading" class="sr-only">Footer</h2>
          <div class="mx-auto max-w-7xl px-6 pt-16 pb-8 lg:px-8 lg:pt-24">
            <div class="xl:grid xl:grid-cols-3 xl:gap-8">
              
              <div class="mt-12 xl:mt-0">
                <h3 class="text-base font-medium text-gray-900">Subscribe to our newsletter</h3>
                <p class="mt-4 text-base text-gray-500">The latest news, articles, and resources, sent to your inbox weekly.</p>
                <form class="mt-4 sm:flex sm:max-w-md">
                  <label for="email-address" class="sr-only">Email address</label>
                  <input type="email" name="email-address" id="email-address" autocomplete="email" required class="w-full min-w-0 appearance-none rounded-md border border-gray-300 bg-white py-2 px-4 text-base text-gray-900 placeholder-gray-500 shadow-sm focus:border-indigo-500 focus:placeholder-gray-400 focus:outline-none focus:ring-indigo-500" placeholder="Enter your email">
                  <div class="mt-3 rounded-md sm:mt-0 sm:ml-3 sm:flex-shrink-0">
                    <button type="submit" class="flex w-full items-center justify-center rounded-md border border-transparent bg-gradient-to-r from-purple-600 to-indigo-600 bg-origin-border px-4 py-3 text-base font-medium text-white shadow-sm hover:from-purple-700 hover:to-indigo-700">Subscribe</button>
                  </div>
                </form>
              </div>
            </div>
            <div class="mt-12 border-t border-gray-200 pt-8 md:flex md:items-center md:justify-between lg:mt-16">
              <div class="flex space-x-6 md:order-2">
                <a href="#" class="text-gray-400 hover:text-gray-500">
                  <span class="sr-only">Facebook</span>
                  <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd" />
                  </svg>
                </a>
      
                <a href="#" class="text-gray-400 hover:text-gray-500">
                  <span class="sr-only">Instagram</span>
                  <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path fill-rule="evenodd" d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z" clip-rule="evenodd" />
                  </svg>
                </a>
      
                <a href="#" class="text-gray-400 hover:text-gray-500">
                  <span class="sr-only">Twitter</span>
                  <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84" />
                  </svg>
                </a>
      
                <a href="#" class="text-gray-400 hover:text-gray-500">
                  <span class="sr-only">GitHub</span>
                  <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path fill-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z" clip-rule="evenodd" />
                  </svg>
                </a>
      
                <a href="#" class="text-gray-400 hover:text-gray-500">
                  <span class="sr-only">Dribbble</span>
                  <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path fill-rule="evenodd" d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10c5.51 0 10-4.48 10-10S17.51 2 12 2zm6.605 4.61a8.502 8.502 0 011.93 5.314c-.281-.054-3.101-.629-5.943-.271-.065-.141-.12-.293-.184-.445a25.416 25.416 0 00-.564-1.236c3.145-1.28 4.577-3.124 4.761-3.362zM12 3.475c2.17 0 4.154.813 5.662 2.148-.152.216-1.443 1.941-4.48 3.08-1.399-2.57-2.95-4.675-3.189-5A8.687 8.687 0 0112 3.475zm-3.633.803a53.896 53.896 0 013.167 4.935c-3.992 1.063-7.517 1.04-7.896 1.04a8.581 8.581 0 014.729-5.975zM3.453 12.01v-.26c.37.01 4.512.065 8.775-1.215.25.477.477.965.694 1.453-.109.033-.228.065-.336.098-4.404 1.42-6.747 5.303-6.942 5.629a8.522 8.522 0 01-2.19-5.705zM12 20.547a8.482 8.482 0 01-5.239-1.8c.152-.315 1.888-3.656 6.703-5.337.022-.01.033-.01.054-.022a35.318 35.318 0 011.823 6.475 8.4 8.4 0 01-3.341.684zm4.761-1.465c-.086-.52-.542-3.015-1.659-6.084 2.679-.423 5.022.271 5.314.369a8.468 8.468 0 01-3.655 5.715z" clip-rule="evenodd" />
                  </svg>
                </a>
              </div>
              <p class="mt-8 text-base text-gray-400 md:order-1 md:mt-0">&copy; 2025 Siiman Rumah Prestasi Mahasiswa, Inc. All rights reserved.</p>
            </div>
          </div>
        </footer>
      </div>

    @vite(['resources/js/app.js'])

    <script>
        document.addEventListener("DOMContentLoaded", function () {
          const dropdownButton = document.getElementById("dropdown-button");
          const dropdownMenu = document.getElementById("dropdown-menu");
      
          dropdownButton.addEventListener("click", function () {
            dropdownMenu.classList.toggle("hidden");
          });
      
          // Menutup dropdown jika klik di luar
          document.addEventListener("click", function (event) {
            if (!dropdownButton.contains(event.target) && !dropdownMenu.contains(event.target)) {
              dropdownMenu.classList.add("hidden");
            }
          });
        });
      </script>
      <script>
        document.addEventListener("DOMContentLoaded", function () {
          const menuButton = document.getElementById("menu-button");
          const closeButton = document.getElementById("close-button");
          const mobileMenu = document.getElementById("mobile-menu");
      
          // Menampilkan menu saat tombol dibuka
          menuButton.addEventListener("click", function () {
            mobileMenu.classList.toggle("hidden");
          });
      
          // Menyembunyikan menu saat tombol ditutup
          closeButton.addEventListener("click", function () {
            mobileMenu.classList.add("hidden");
          });
      
          // Menutup menu jika klik di luar elemen menu
          document.addEventListener("click", function (event) {
            if (!mobileMenu.contains(event.target) && !menuButton.contains(event.target)) {
              mobileMenu.classList.add("hidden");
            }
          });
        });
      </script>
</body>

</html>