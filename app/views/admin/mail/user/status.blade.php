<body>

<p>Dear {{ ucfirst($user['username']) }},</p>
<p>Your global MissionNext network status changed</p>
<p>Current status: {{{ $user['is_active'] ? 'Access Granted.' : 'Access Denied.'  }}}</p>
<p>Thank you,</p>
MissionNext Team

</body>
