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
                            @if (0 === strpos($user['profileData'][$item], $uploadFieldPrefix))
                                <a href="/uploads/{{ $user['profileData'][$item] }}" target="_blank">
                                    {{ $user['profileData'][$item] }}
                                </a>
                            @else
                                {{ $user['profileData'][$item] }}
                            @endif
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
