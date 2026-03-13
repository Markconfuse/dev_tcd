<head>
  <meta charset="utf-8">
  <style>
    .btn {
      background-color: #61615c !important; 
      border-color: #272727 !important; 
      border-radius: 3px !important;
      -webkit-box-shadow: none !important;
      box-shadow: none !important;
      border: 1px solid transparent !important;
      display: inline-block !important;
      padding: 6px 12px !important;
      margin-bottom: 0 !important;
      font-size: 14px !important;
      font-weight: 400 !important;
      line-height: 1.42857143 !important;
      text-align: center !important;
      white-space: nowrap !important;
      vertical-align: middle !important;
      -ms-touch-action: manipulation !important;
      touch-action: manipulation !important;
      cursor: pointer !important;
      -webkit-user-select: none !important;
      -moz-user-select: none !important;
      -ms-user-select: none !important;
      user-select: none !important;
      background-image: none !important;
      border: 1px solid transparent !important;
    }
    .btn:hover {
       border: 1px solid #61615c!important;
       text-shadow: #1e4158 0 1px 0!important;
       background: #ff0075!important;
       background: -webkit-linear-gradient(top, #3a3a3a, #616060)!important;
       background: -moz-linear-gradient(top, #3a3a3a, #616060)!important;
       background: -ms-linear-gradient(top, #3a3a3a, #616060)!important;
       background: -o-linear-gradient(top, #3a3a3a, #616060)!important;
       background-image: -ms-linear-gradient(top, #3a3a3a 0%, #616060 100%)!important;
       color: #fff;
    }
  </style>
</head>
<body style="background-color:#bfbfbf">
  <div style="margin-left:12%;display: inline-block; width:90%; margin-top:20px">
    <div style="background-color:white;padding:20px;width:80%;overflow-x:auto;">
      <div align="center">
      <h2 style="font-family:'Russo One';margin-top: 0;margin-bottom:0;font-size:24px" id="headertitle">Request Notification</h2>
      <hr style="margin-top: 0;margin-bottom:0;">
      <h2 style="font-family:'Orbitron';margin-top: 0;margin-bottom:0;font-size:18px">TCD Portal</h2>
      </div>
      <h3 style="font-family:'Dosis'">{!! $email_content !!}</h3>
      <a href="{{ $email_link }}" class="btn col-md-4" style="text-decoration:none;color:white;background-color:#61615c;padding:5px 5px 5px 5px;" align="center">View More Information</a><br>
      <hr style="margin-top: 10px;margin-bottom:0;">
      <center>
        <h5 style="margin-top: 0;margin-bottom:0;">** This is a system generated message. DO NOT REPLY TO THIS EMAIL. **</h5>
        <h6 style="margin-top: 0;margin-bottom:0;float:right">APPSDEV</h6>
      </center>
    </div>
    <br><br>
  </div>
</body>