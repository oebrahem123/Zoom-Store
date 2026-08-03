@props([
    'user' => null,
    'src' => null,
    'class' => '',
    'alt' => '',
    'size' => null,
    'width' => null,
    'height' => null,
    'style' => '',
])

@php
    $defaultSrc = asset('assets/frontend/images/user.png');
    $avatarSrc = $src ?? ($user?->avatar ?: $defaultSrc);
    $sizeStyle = $size ? "width: {$size}; height: {$size};" : '';
    $dimStyle = $width ? "width: {$width};" : '';
    $dimStyle .= $height ? " height: {$height};" : '';
@endphp

<img src="{{ $avatarSrc }}"
     alt="{{ $alt }}"
     class="{{ $class }}"
     style="{{ $sizeStyle }} {{ $dimStyle }} {{ $style }}"
     onerror="this.onerror=null; this.src='{{ $defaultSrc }}';">
