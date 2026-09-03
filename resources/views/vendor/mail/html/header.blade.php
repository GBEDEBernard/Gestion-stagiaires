@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: block;">
    @if (trim($slot) && trim($slot) !== config('app.name'))
        {{ $slot }}
    @else
        <img src="{{ asset('images/TFGLOGO1.png') }}"
             alt="TECHNOLOGY FOREVER GROUP"
             class="logo"
             style="display: block; margin: 0 auto; max-width: 140px; height: auto; border-radius: 12px;">
    @endif
</a>
</td>
</tr>