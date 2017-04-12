
Dear {{ $user['username'] }},
<br/><br/>
Your MissionNext network status changed
<br/><br/>
Current status is now:
<br/><br/>
@if ($user['is_active'])
Access Granted
<br/><br/>
IF REGISTERED AS AN EXPLORENEXT AGENCY OR TEACHNEXT SCHOOL: 
<br><br> 
We are pleased to inform you that your request for partnership with MissionNext has been approved!  You will soon have access to our online database of potential candidates to fill your open positions. 
<br/><br/>
Please log in to your account from https://new.missionnext.org/welcome/login-here/ (after choosing the appropriate service) with your username and password to make the activation payment. Once payment is received, you will receive instructions on how to set up your profile and post your jobs in order to make the most of your MissionNext partnership. 
<br/><br/>
 - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 
<br/>
IF REGISTERED AS AN EXPLORENEXT AGENCY REP OR TEACHNEXT AGENCY TO SERVICE SCHOOLS:  
<br/><br/>
We are pleased to inform you that your MissionNext Rep Registration has been approved!  You will soon be able to affiliate with your mission agency and/or schools to view their posted jobs and view candidate profiles.    
<br/><br/>
Please watch for an email from MissionNext with a coupon code that will allow you to activate your account without payment.  
<br/><br/>
 - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 
<br/>
If you have any questions, please contact us at headquarters@missionnext.org 
<br/><br/>
We look forward to partnering with you! 
<br/><br/>  
MissionNext Partner Support Team <br/>
Thank you, <br/>
MissionNext Team <br/><br/>

@else
Access Denied
<br><br>  
This could be because you signed up for the wrong website or wrong user type or have a duplicate record or you or your organization do not qualify for some reason.
<br><br> 
If you have any questions, please contact us at headquarters@missionnext.org   
<br><br> 
We look forward to partnering with you!   
<br><br> 
MissionNext Partner Support Team <br> 
Thank you, <br> 
MissionNext Team <br><br> 
@endif