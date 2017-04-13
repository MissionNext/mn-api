<!DOCTYPE html>
<html lang="en-US">
 <head>
  <meta charset="utf-8">
 </head>
 <body>
  <div>
   <p>Dear {{ $user['username'] }},</p>
   <p>Your MissionNext network status changed</p>
   <p>Current status is now:</p>
   @if ($user['is_active'])
    <p>Access Granted</p>
    <p>IF REGISTERED AS AN EXPLORENEXT AGENCY OR TEACHNEXT SCHOOL:</p>
    <p>We are pleased to inform you that your request for partnership with MissionNext has been approved!  You will soon have access to our online database of potential candidates to fill your open positions. </p>
    <p>Please log in to your account from https://new.missionnext.org/welcome/login-here/ (after choosing the appropriate service) with your username and password to make the activation payment. Once payment is received, you will receive instructions on how to set up your profile and post your jobs in order to make the most of your MissionNext partnership.</p>
    <p>- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -</p>
    <p>IF REGISTERED AS AN EXPLORENEXT AGENCY REP OR TEACHNEXT AGENCY TO SERVICE SCHOOLS: </p>
    <p>We are pleased to inform you that your MissionNext Rep Registration has been approved!  You will soon be able to affiliate with your mission agency and/or schools to view their posted jobs and view candidate profiles.   </p>
    <p>Please watch for an email from MissionNext with a coupon code that will allow you to activate your account without payment.  </p>
    <p>- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -</p>
    <p>If you have any questions, please contact us at headquarters@missionnext.org</p>
    <p>We look forward to partnering with you!</p>
    <p>MissionNext Partner Support Team</p>
    <p>Thank you,</p>
    <p>MissionNext Team</p>

   @else
    <p>Access Denied</p>
    <p>This could be because you signed up for the wrong website or wrong user type or have a duplicate record or you or your organization do not qualify for some reason.</p>
    <p>If you have any questions, please contact us at headquarters@missionnext.org  </p>
    <p>We look forward to partnering with you!  </p>
    <p>MissionNext Partner Support Team</p>
    <p>Thank you,</p>
    <p>MissionNext Team</p>
   @endif
  </div>
 </body>
</html>