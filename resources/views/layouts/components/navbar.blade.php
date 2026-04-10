  <nav class="main-header navbar navbar-expand {{ config('appsdev_conf.skin_nav') }}">

    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#"><i class="fa fa-bars"></i></a>
      </li>
    </ul>

    <form action="{{ url('search-ticket') }}" method="post" accept-charset="utf-8" class="form-inline ml-3">
      @csrf
      <div class="input-group input-group-sm">
        <input class="form-control form-control-navbar" name="refNo" type="search" placeholder="Search via Reference#" autocomplete="off" required>
        <div class="input-group-append">
          <button class="btn btn-navbar" type="submit">
            <i class="fas fa-search"></i>
          </button>
        </div>
      </div>
    </form>


    <ul class="navbar-nav ml-auto">


      <!-- Temporary Buttons for View -->

      <div class="text-center">

{{--         @if(Session('userData')->account_id == 57610 || Session('userData')->account_id == 56857)
          @if(Session('userData')->role_id !== '3')
            <a href="{{ route('setAcc',['tid' => '3']) }}" class="btn btn-primary btn-sm btn-flat setAcc">
              <i class="fas fa-user-tie"></i> Set as Engineer
            </a>
          @endif

          @if(Session('userData')->role_id !== '4')
              <a href="{{ route('setAcc',['tid' => '4']) }}" class="btn btn-success btn-sm btn-flat setAcc">
                <i class="far fa-user"></i> Set as Requestor
              </a>
          @endif

          @if(Session('userData')->role_id !== '2')
            <a href="{{ route('setAcc',['tid' => '2']) }}" class="btn btn-danger btn-sm btn-flat setAcc">
              <i class="fas fa-user-nurse"></i> Set as Admin
            </a>
          @endif
        @endif --}}
        
      </div>

      <!-- End of temporary Buttons for View -->


{{--       <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#" aria-expanded="true">
          <i class="far fa-bell"></i>
          <span class="badge badge-warning navbar-badge" id="spnBadgeCounter"></span>
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right" style="max-height:233px;overflow-y:scroll">
          @include('layouts.components.preloader-ring')

          <span class="dropdown-item dropdown-header" id="spnHeaderCounter"></span>
          <div class="dropdown-divider"></div>
          <div id="dvNotif">
          </div>
          <a href="#" class="dropdown-item dropdown-footer">See All Notifications</a>
        </div>
      </li> --}}

{{--       <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#" aria-expanded="true">
          <i class="far fa-bell"></i>
          <span class="badge badge-warning navbar-badge" id="spnBadgeCounter">0</span>
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right" style="max-height:233px;overflow-y:scroll">
          @include('layouts.components.preloader-ring')

          <span class="dropdown-item dropdown-header" id="spnHeaderCounter"></span>
          <div class="dropdown-divider"></div>
          <div id="dvNotif">
          </div>
          <a href="#" class="dropdown-item dropdown-footer" disabled>Temporarily unavailable</a>
        </div>
      </li> --}}

      <div>
        <a href="#" class="btn btn-info btn-sm btn-flat mr-2" data-toggle="modal" data-target="#settingsModal">
          <i class="fa fa-fw fa-cog"></i> Settings
        </a>
      </div>

      <div>
        <a href="https://proport.ics.com.ph/google/quickie?em={{ base64_encode(Session::get('userData')->Email) }}"  target="_blank" class="btn bg-pink btn-sm btn-flat mr-2">
          <i class="fa fa-fw fa-ticket"></i> ProPort
        </a>
      </div>

      <div>
        <a href="#" class="btn btn-logout btn-sm btn-flat" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
          <i class="fa fa-fw fa-power-off"></i> Logout
        </a>
        <form id="logout-form" action="{{ url(config('appsdev_conf.logout_url', 'logout')) }}" method="POST" style="display: none;">
          @if(config('appsdev_conf.logout_method'))
            {{ method_field(config('appsdev_conf.logout_method')) }}
          @endif
          {{ csrf_field() }}
        </form>
      </div>
    </ul>

  </nav>

@include('layouts.components.settings_modal')