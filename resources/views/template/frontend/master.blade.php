<!DOCTYPE html>

<!--
 // WEBSITE: https://themefisher.com
 // TWITTER: https://twitter.com/themefisher
 // FACEBOOK: https://www.facebook.com/themefisher
 // GITHUB: https://github.com/themefisher/
-->

<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title', 'Goku')</title>
@include('template.frontend.css')
@livewireStyles
</head>
<body>
@include('template.frontend.header')
@yield('content')
@include('template.frontend.footer')
@include('template.frontend.js')
@livewireScripts
</body>
