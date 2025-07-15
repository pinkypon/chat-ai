@props(['large' => false ])
@php
  if($large === true){
    $defaults = [
      'class' => 'w-12 h-12'
    ];
  }else{
    $defaults = [
      'class' => 'w-8 h-8'
    ];
  }
@endphp
<img {{ $attributes($defaults) }}src="{{ asset('images/test.png') }}">