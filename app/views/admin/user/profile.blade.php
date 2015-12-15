@extends('layout')

@section('title')
Dashboard. User Profile
@endsection

@section('content')
       <button onclick="window.history.back();" type="button" class="btn btn-primary btn-sm">Back</button>
     <h3>Profile Data:</h3>
     <table class="table table-hover profile-table" >
         <tbody>
          <thead>
              <tr>
                 <th>Username</th>
                 <td>{{ $user['username']  }}</td>
             </tr>
             <tr>
                 <th>Registered</th>
                 <td>{{ $user['created_at']  }}</td>
             </tr>
         </thead>
         @foreach($sortedKeys as $item)
             @if ((is_string($user['profileData'][$item]) && !empty($user['profileData'][$item])) || (is_array($user['profileData'][$item]) && count($user['profileData'][$item]) > 1))
                 <tr>
                    <th>{{ $fieldLabels[$item] }} </th>
                    <td>@if (is_string($user['profileData'][$item]))
                            {{ $user['profileData'][$item] }}
                        @elseif (count($user['profileData'][$item]) > 1)
                            @foreach ($user['profileData'][$item] as $val)
                                <li>{{ $val }}</li>
                            @endforeach
                        @else
                          {{ current($user['profileData'][$item]) }}
                        @endif
                    </td>
                 </tr>
             @endif
         @endforeach

         </tbody>
     </table>
@endsection
