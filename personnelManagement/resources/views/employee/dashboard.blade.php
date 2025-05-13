@extends('layouts.employeeHome')

@section('content')
    <section class="w-full">
         <!-- Greeting -->
  <h1 class="text-xl font-semibold mb-4" style="color: #BD6F22;">
  Welcome back, {{ Auth::user()->first_name }}!
</h1>
<hr>

       
    </section>
@endsection
