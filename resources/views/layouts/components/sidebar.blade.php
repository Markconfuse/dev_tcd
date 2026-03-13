<aside class="main-sidebar elevation-4 {{ config('appsdev_conf.skin_sidebar') }}">
  <!-- Brand Logo -->
  <a href="{{route('compose-request')}}" class="brand-link {{ config('appsdev_conf.skin_logo') }}">
    <img src="{{ asset('/img/assets/tcd-icon_trns_v2.ico') }}" alt="Icecream Online by IT Appsdev" class="brand-image img-circle elevation-3" style="opacity: .8">
    <span class="brand-text font-weight-light">{{ config('appsdev_conf.logo') }}</span>
  </a>
  <!-- Sidebar -->
  <div class="sidebar">
    <!-- Sidebar user panel (optional) -->

    <div class="user-panel mt-3 pb-3 mb-3 d-flex">
      <div class="image">
        @auth
          <img src="{{ Session::get('userData')->GAvatar }}" class="img-circle elevation-2" alt="User Image">
        @endauth
      </div>
      <div class="info">
        <a href="#" class="d-block">{{ auth()->user()->first_name }} </a>
      </div>
    </div>

    <!-- Sidebar Menu -->
    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          @include('layouts.components.s_items.s_item', ['s_item_url' => route('dashboard'), 's_item_icon' => 'nav-icon far fa-tachometer-alt', 's_item_text' => 'Dashboard' ])

          @if(Session('userData')->role_name == 'super_user')
            @include('layouts.components.s_items.s_item', ['s_item_url' => url('user-logs'), 's_item_icon' => 'nav-icon fas fa-cabinet-filing', 's_item_text' => 'User Logs' ])
          @endif

          @include('layouts.components.s_items.s_item', ['s_item_url' => route('compose-request'), 's_item_icon' => 'nav-icon fas fa-pen-square', 's_item_text' => 'Compose Request' ])

          @include('layouts.components.s_items.s_item', ['s_item_url' => route('status-request',['status' => 'all']), 's_item_icon' => 'nav-icon fas fa-globe-europe', 's_item_text' => 'All Request', 'ctr_id' => 'allctr'])

          @if(Session('userData')->role_name == 'engineer' && Session('userData')->account_id != 57610)
            @include('layouts.components.s_items.s_item', ['s_item_url' => route('status-request',['status' => 'pending']), 's_item_icon' => 'nav-icon fas fa-file-import', 's_item_text' => 'Pending Request', 'ctr_id' => 'pendingctr'])
          @endif

          @if(Session('userData')->role_name !== 'engineer' || Session('userData')->account_id == 57610 || Session('userData')->account_id == 57615)
            @include('layouts.components.s_items.s_item', ['s_item_url' => route('status-request',['status' => 'unassigned']), 's_item_icon' => 'nav-icon fas fa-user-times', 's_item_text' => 'Unassigned Request', 'ctr_id' => 'unassignedctr'])

            @include('layouts.components.s_items.s_item', ['s_item_url' => route('status-request',['status' => 'assigned']), 's_item_icon' => 'nav-icon fas fa-file-import', 's_item_text' => 'Assigned Request', 'ctr_id' => 'assignedctr'])
          @endif


          @include('layouts.components.s_items.s_item', ['s_item_url' => route('status-request',['status' => 'answered']), 's_item_icon' => 'nav-icon fas fa-file-contract', 's_item_text' => 'Answered Request', 'ctr_id' => 'answeredctr'])

          @include('layouts.components.s_items.s_item', ['s_item_url' => route('status-request',['status' => 'closed']), 's_item_icon' => 'nav-icon fas fa-ban', 's_item_text' => 'Closed Request', 'ctr_id' => 'closedctr'])

          @if(Session('userData')->role_name == 'engineer' || Session('userData')->role_name == 'admin' || Session('userData')->role_name == 'super_user')
            @include('layouts.components.s_items.s_item', ['s_item_url' => route('status-request',['status' => 'reassigned']), 's_item_icon' => 'nav-icon fas fa-people-carry', 's_item_text' => 'Reassigned Request', 'ctr_id' => 'reassignedctr'])
          @endif

          @if(Session('userData')->account_id == 57610 || Session('userData')->account_id == 57615 || Session('userData')->role_name == 'admin' || Session('userData')->role_name == 'super_user')
            @include('layouts.components.s_items.s_item', ['s_item_url' => route('status-request',['status' => 'cebu']), 's_item_icon' => 'nav-icon fas fa-plane-departure', 's_item_text' => 'Cebu Request', 'ctr_id' => 'cebuctr'])
          @endif
		    
    		  @if(in_array(Session('userData')->account_id, array(57610,57615,926,205,856,10904,387,310,387,947,1507,317)) || 
            Session('userData')->role_name == 'admin' || 
            Session('userData')->role_name == 'super_user' ||
            Session('userData')->role_name == 'engineer')
            @include('layouts.components.s_items.s_item', ['s_item_url' => route('escalated-request',['status' => 'escalated']), 's_item_icon' => 'nav-icon fas fa-exclamation', 's_item_text' => 'Escalated Request', 'ctr_id' => 'escalatedctr'])
          @endif
	
		  @if(Session('userData')->getOriginal('role_name') == 'admin' || Session('userData')->role_name == 'super_user')
			@include('layouts.components.s_items.s_item', ['s_item_url' => route('tcd-reports'), 's_item_icon' => 'nav-icon fas fa-database', 's_item_text' => 'Reports' ])
	      @endif
		  @if(Session('userData')->account_id == 57751 || Session('userData')->account_id == 56395 || Session('userData')->account_id == 57732 || Session('userData')->role_name == 'admin' || Session('userData')->role_name == 'super_user')
                    @include('layouts.components.s_items.s_item', [
                        's_item_url' => route('status-request', ['status' => 'unread']),
                        's_item_icon' => 'nav-icon fas fa-folder',
                        's_item_text' => 'Unread Request',
                        'ctr_id' => 'unreadctr',
                    ])
                    
                    @include('layouts.components.s_items.s_item', [
                        's_item_url' => route('status-request', ['status' => 'unanswered']),
                        's_item_icon' => 'nav-icon fas fa-folder-open',
                        's_item_text' => 'Unanswered Request',
                        'ctr_id' => 'unansweredctr',
                    ])
                    @if (Session('userData')->role_name == 'admin')
                    @include('layouts.components.s_items.s_item', [
                        's_item_url' => route('status-request', ['status' => 'counter']),
                        's_item_icon' => 'nav-icon fas fa-cogs',
                        's_item_text' => 'Tickets Counter',
                        'ctr_id' => 'counterctr',
                    ])
                    @endif
                    @include('layouts.components.s_items.s_item', [
                        's_item_url' => route('status-request', ['status' => 'engineers_ticket_monitoring']),
                        's_item_icon' => 'nav-icon fas fa-tools',
                        's_item_text' => 'Engrs Ticket Monitoring',
                        'ctr_id' => 'tscounterctr',
                    ])
                @endif

      </ul>
    </nav>

    <!-- /.sidebar-menu -->
  </div>
  <!-- /.sidebar -->
</aside>


