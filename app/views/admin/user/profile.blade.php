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
         @forelse($user['profileData'] as $field => $value)
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
         @empty
             <p>No profile data</p>
         @endforelse
         </tr>

         </tbody>
     </table>
@endsection
