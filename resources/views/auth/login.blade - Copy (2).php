<!DOCTYPE html>
<html lang="en">
   <head>
      <meta charset="UTF-8" />
      <meta http-equiv="X-UA-Compatible" content="IE=edge" />
      <meta name="viewport" content="width=device-width, initial-scale=1.0" />
      <title>TCD | Login-page</title>
      <link rel="stylesheet" href="public/tcd_christmas/assets/style.css" />
   </head>
   <body>
      <section>
         <!-- <img
            src="./assets/images/candy_overlay.png"
            alt="candy-overlay"
            class="candy-overlay"
         /> -->
         <div class="overlay"></div>
         <div class="snowflakes" aria-hidden="true">
            <div class="snowflake">
               ❅
            </div>
            <div class="snowflake">
               ❅
            </div>
            <div class="snowflake">
               ❆
            </div>
            <div class="snowflake">
               ❄
            </div>
            <div class="snowflake">
               ❅
            </div>
            <div class="snowflake">
               ❆
            </div>
            <div class="snowflake">
               ❄
            </div>
            <div class="snowflake">
               ❅
            </div>
            <div class="snowflake">
               ❆
            </div>
            <div class="snowflake">
               ❄
            </div>
         </div>
         <div class="path"></div>
         <img
            src="public/tcd_christmas/assets/images/building.png"
            alt="building"
            class="__building"
         />
         <img
            src="public/tcd_christmas/assets/images/snowman.svg"
            alt="snowman"
            class="__snowman"
         />
         <div class="container">
            <div class="candy-quote">
               <!-- <img
                  src="./assets/images/candy-stick.png"
                  alt="candy-stick"
                  class="candy-stick"
                  width="100"
               /> -->
               <p class="quote-container">
                  "Wishing all the hardworking and dedicated workers a joyous
                  Christmas filled with the warmth of gratitude, the spirit of
                  camaraderie, and the well-deserved rest that comes with the
                  season. Your efforts make the holidays brighter for everyone.
                  Cheers to a festive season of appreciation and joy!"
               </p>
            </div>
            <div class="introduction">
               <div class="theme">Merry Christmas</div>
               <div class="greeting">
                  <img
                     src="public/tcd_christmas/assets/images/ics.png"
                     alt=""
                     width="50"
                     style="border-radius: 5px;"
                  />&nbsp; Integrated Computer Systems
               </div>
            </div>
            <div class="login-card" style="width: 34em;">
               <img src="public/tcd_christmas/assets/images/deco.png" alt="deco" class="deco" />
               <div class="login-card-title">TCD</div>
               <div class="login-card-body">
             
              <button id="gauth" class="ics-button" style="width: 30em; height: 8em; background-color: #C3272B;">
              <p style="font-size: 25px;"><i class="fa-brands fa-google"></i>
                        Sign in using ICS Gmail&nbsp;
                        <i class="fa-solid fa-arrow-right"></i></p>
                     </button>
                     <!-- <label for="email">Email</label>
                     <div class="email-holder" id="email-holder">
                        <input
                           type="email"
                           class="email"
                           name="email"
                           id="email"
                        />
                        <i class="fa-solid fa-envelope"></i>
                        <div class="email-liner" id="email-liner"></div>
                     </div>
                     <label for="password">Password</label>
                     <div class="password-holder" id="password-holder">
                        <input
                           type="password"
                           class="password"
                           name="password"
                           id="password"
                        />
                        <i class="fa-solid fa-lock"></i>
                        <div class="password-liner" id="password-liner"></div>
                     </div>
                     <div>
                        <div class="form-check">
                           <label class="form-check-label">
                              <input
                                 type="checkbox"
                                 class="form-check-input"
                                 name=""
                                 id=""
                                 value="checkedValue"
                                 checked
                              />
                              Remember me
                           </label>
                        </div>
                     </div>
                     <button class="ics-button">Sign-in</button> -->
                  
               </div>
            </div>
         </div>
      </section>

      <script>
         const email_holder = document.getElementById("email-holder");
         const email_liner = document.getElementById("email-liner");
         const password_holder = document.getElementById("password-holder");
         const password_liner = document.getElementById("password-liner");

         email_holder.addEventListener("click", function () {
            email_liner.style.width = "70%";
         });
         password_holder.addEventListener("click", function () {
            password_liner.style.width = "70%";
         });

      </script>
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
