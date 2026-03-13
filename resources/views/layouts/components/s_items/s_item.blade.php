<li class="nav-item">
  <a href="{{ $s_item_url }}" class="nav-link">
    <i class="{{ $s_item_icon }}"></i>
    <p>{{ $s_item_text }}
    	@if(isset($ctr_id))
		    <span id="{{ $ctr_id }}" style="background-color: #212529;color:#ffffff" class="badge right"></span>
	    @endif
    </p>
  </a>
</li>