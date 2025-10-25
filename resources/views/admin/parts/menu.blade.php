 <li class="menu-item">
     <a href="{{route('dashboard')}}" class="menu-link">
         <i class="menu-icon tf-icons bx bx-home-circle"></i>
         <div data-i18n="Dashboard">Dashboard</div>
     </a>
 </li>

 <li class="menu-item {{ request()->routeIs('form.*') ? 'open' : '' }}">
     <a href="javascript:void(0);" class="menu-link menu-toggle">
         <i class="menu-icon tf-icons bx bx-file-detail"></i>
         <div data-i18n="Master Data">Form Masuk</div>
     </a>

     <ul class="menu-sub">
         <li class="menu-item {{ request()->routeIs('form.data') ? 'active' : '' }}">
             <a href="{{ route('form.data') }}" class="menu-link">
                 <div data-i18n="Form Data">Form Data Valid</div>
             </a>
         </li>
         <li class="menu-item {{ request()->routeIs('form.permasalahan') ? 'active' : '' }}">
             <a href="{{ route('form.permasalahan') }}" class="menu-link">
                 <div data-i18n="Permasalahan">Permasalahan</div>
             </a>
         </li>
         <li class="menu-item {{ request()->routeIs('form.progres') ? 'active' : '' }}">
             <a href="{{ route('form.progres') }}" class="menu-link">
                 <div data-i18n="Form Progres">Form Progres</div>
             </a>
         </li>

     </ul>
 </li>

 <li class="menu-item {{ request()->routeIs('master.*') ? 'open' : '' }}">
     <a href="javascript:void(0);" class="menu-link menu-toggle">
         <i class="menu-icon tf-icons bx bx-layout"></i>
         <div data-i18n="Master Data">Master Data</div>
     </a>

     <ul class="menu-sub">
         <li class="menu-item {{ request()->routeIs('master.di') ? 'active' : '' }}">
             <a href="{{ route('master.di') }}" class="menu-link">
                 <div data-i18n="Daerah Irigasi">Daerah Irigasi</div>
             </a>
         </li>
         <li class="menu-item {{ request()->routeIs('master.saluran') ? 'active' : '' }}">
             <a href="{{ route('master.saluran') }}" class="menu-link">
                 <div data-i18n="Saluran">Saluran</div>
             </a>
         </li>
         <li class="menu-item {{ request()->routeIs('master.p3a') ? 'active' : '' }}">
             <a href="{{ route('master.p3a') }}" class="menu-link">
                 <div data-i18n="Saluran">P3A</div>
             </a>
         </li>
         <li class="menu-item {{ request()->routeIs('master.import.form') ? 'active' : '' }}">
             <a href="{{ route('master.import.form') }}" class="menu-link">
                 <div data-i18n="Saluran">Import</div>
             </a>
         </li>

     </ul>
 </li>

 <li class="menu-item {{ request()->routeIs('admin.*') ? 'open' : '' }}">
     <a href="javascript:void(0);" class="menu-link menu-toggle">
         <i class="menu-icon tf-icons bx bx-apps-alt"></i>
         <div data-i18n="Master Data">Petugas</div>
     </a>

     <ul class="menu-sub">
         <li class="menu-item {{ request()->routeIs('admin.juru') ? 'active' : '' }}">
             <a href="{{ route('admin.juru') }}" class="menu-link">
                 <div data-i18n="Juru">Juru</div>
             </a>
         </li>
         <li class="menu-item {{ request()->routeIs('admin.pengamat') ? 'active' : '' }}">
             <a href="{{ route('admin.pengamat') }}" class="menu-link">
                 <div data-i18n="Pengamat">Pengamat</div>
             </a>
         </li>
         <li class="menu-item {{ request()->routeIs('admin.upi') ? 'active' : '' }}">
             <a href="{{ route('admin.upi') }}" class="menu-link">
                 <div data-i18n="Upi">UPI</div>
             </a>
         </li>
     </ul>
 </li>