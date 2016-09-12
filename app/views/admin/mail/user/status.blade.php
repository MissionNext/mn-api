
Dear {{ ucfirst($user['username']) }},
Your global MissionNext network status changed
Current status: {{{ $user['is_active'] ? 'Access Granted.' : 'Access Denied.'  }}}
Thank you,
MissionNext Team