@props(['center' => false])
@php
  if($center === true){
    $defaults = [
      'class' => 'absolute left-1/2 top-1/2 transform -translate-x-1/2 -translate-y-1/2 text-lg font-bold'
    ];
  }else{
    $defaults = [
      'class' => 'text-xl font-bold'
    ];
  }
@endphp
<h2 {{ $attributes($defaults) }}>{{ $slot }}</h2>