<!DOCTYPE html>
<html lang="en">
   <head>
      <meta charset="UTF-8" />
      <meta http-equiv="X-UA-Compatible" content="IE=edge" />
      <meta name="viewport" content="width=device-width, initial-scale=1.0" />
      <title>TCD Login</title>
      <link rel="stylesheet" href="public/valentines/assets/style.css" />
   </head>
   <body>
      <section>
         <div class="container">
            <div class="hero">
               <p class="year">2024</p>
               <p class="event-title">Happy Valentines Day</p>
            </div>
            <div class="login-wrapper">
               <div class="login-form">
                  <p class="login-title">TCD</p>
                  <form action="#">
                     <a id="gauth" class="gmailSignin" style="cursor: pointer;"
                        ><i class="fa-brands fa-google"></i>&nbsp;Sign in Using
                        ICS Gmail Account</a
                     >
                  </form>
               </div>
            </div>
         </div>
         <div class="hearts">
            <img src="/public/valentines/assets/images/hearts.svg" alt="hearts" />
         </div>
         <div class="confetti">
            <img src="/public/valentines/assets/images/confetti.svg" alt="confetti" />
         </div>
      </section>
      <script
         src="https://kit.fontawesome.com/d1cc49e838.js"
         crossorigin="anonymous"
      ></script>
	  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script> 
<script src="https://kit.fontawesome.com/d1cc49e838.js" crossorigin="anonymous"></script>
      <script type="text/javascript">
if('<?php echo Session::has('message') ?>') {

message = '<?php echo Session::get('message') ?>';
type = '<?php echo Session::get('status') ?>';

Swal.fire({
  type: type,
  title: message,
  width: '370px',
  showConfirmButton: false,
  timer: 2000
})
}

$('#btnTCD').on('click', function() {
Swal.fire({
  title: 'TCD Portal',
  width: '360px',
  showConfirmButton: false,
  timer: 2000
})
})

$('#gauth').on('click', function() {

// $('.preloader-round').removeAttr('hidden', '');
$('.preloader-round').removeAttr('hidden', '');
// window.location.href = '{{ route('googleRedirect') }}';
window.location.href = 'auth/google';
})

/** add active class and stay opened when selected */
var url = window.location;

// for sidebar menu entirely but not cover treeview
$('ul.nav a').filter(function() {
 return this.href == url;
}).addClass('active');

$('ul.nav-treeview a').filter(function() {
return this.href == url;
}).parentsUntil(".nav-sidebar > .nav-treeview").addClass('menu-open').prev('a').addClass('active');

$('.setAcc').on('click', function() {
Swal.fire({
title: 'Configuring your account. . . please wait.',
width: '400px',
showConfirmButton: false
})
})
</script>
   </body>
</html>
