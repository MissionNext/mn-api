@extends('layout')

@section('title')
Dashboard. User Profile
@endsection

@section('content')
       <button onclick="window.history.back();" type="button" class="btn btn-primary btn-sm">Back</button>
     <h3>Profile Data:</h3>
     <table class="table table-hover" >
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
         @foreach($user['profileData'] as $field => $value)
             @if ((is_string($value) && !empty($value)) || (is_array($value) && count($value) > 1))
                 <tr>
                    <th>{{ ucfirst(str_replace("_", " ", $field)) }} </th>
                    <td>@if (is_string($value))
                            {{ $value  }}
                        @elseif (count($value) > 1)
                            @foreach ($value as $val)
                                <li>{{ $val }}</li>
                            @endforeach
                        @else
                          {{ current($value) }}
                        @endif
                    </td>
                 </tr>
             @endif
         @endforeach

         </tbody>
     </table>
@endsection
