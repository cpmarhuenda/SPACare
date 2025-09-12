@props(['logoBase64'])

<tr>
    <td class="header">
        <a href="{{ config('app.url') }}" style="display: inline-block;">
            @if(!empty($logoBase64))
                <img src="{{ $logoBase64 }}" alt="{{ config('app.name') }}" style="height: 80px;">
            @else
                <h1>{{ config('app.name') }}</h1>
            @endif
        </a>
    </td>
</tr>
