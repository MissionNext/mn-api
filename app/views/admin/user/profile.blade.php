@extends('layout')

@section('title')
Dashboard. User Profile
@endsection

@section('content')
     <h3>Profile Data:</h3>
     <table class="table table-hover" >
         <tbody>
         @forelse($user['profileData'] as $field => $value)
         <tr>
            <th>{{ $field }} </th>
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
