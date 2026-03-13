function refreshNotif(countURL, unreadURL) {
  notif = "<span class='dropdown-item dropdown-header markReadNav'>Mark all as read</span>";
  $('#dvNotif').html('');
  $('.preloader-round').removeAttr('hidden', '');

  $.get(countURL, function(data) {}).done(function(data) {
     $('#spnBadgeCounter').text(data['count']);
      if(data['count'] > 0) {
      $('#spnHeaderCounter').text(data['count']+' Notifications');
    } else {
      $('#spnHeaderCounter').text(data['count']+' Notification');
    }
  }).fail(function(data) {
    refreshNotif();
  })

  $.get(unreadURL, function(data) {}).done(function(data) {
    $.each(data.data, function(key, val) {
        notif += "<div class='dropdown-divider'></div>";
        notif += "<a id="+val['ticket_id']+" class='dropdown-item list-notif pointer'>";
        notif += "<div class='media'>";
        notif += "<img src='"+val['CGAvatar']+"' alt='User Avatar' class='img-size-50 img-circle mr-3'>";
        notif += "<div class='media-body'><h3 class='dropdown-item-title'>"+val['CName'];
        notif += "<span class='float-right text-sm text-muted'><i class='far fa-circle'></i></span></h3>";
        notif += "<p class='text-sm'>"+val['notification']+"</p>";
        notif += "<p class='text-sm text-muted'><i class='far fa-clock mr-1'></i>"+$.timeago(val['created_date'])+"</p>";
        notif += "</div></div></a>";
        notif += "<div class='dropdown-divider'></div>";
        $('#dvNotif').html(notif);
    })
    $('.preloader-round').attr('hidden', 'hidden');
  }).fail(function(data) {
    refreshNotif();
  })
}