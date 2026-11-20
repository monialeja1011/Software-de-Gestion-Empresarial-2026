@php
    $accent = $branding->accentHex();
    $logo = $branding->logoDataUri();
    $contact = collect([
        $branding->phone,
        $branding->email,
        $branding->website,
    ])->filter()->implode(' · ');
@endphp

<table width="100%" cellpadding="0" cellspacing="0"
       style="margin-bottom: 14px; border-bottom: 2px solid {{ $accent }};">
    <tr>
        <td width="70" style="padding: 0 12px 12px 0; vertical-align: top;">
            @if ($logo)
                <img src="{{ $logo }}" alt="Logo de {{ $branding->trade_name }}"
                     width="58" height="58" style="object-fit: contain;">
            @else
                <table width="58" height="58" cellpadding="0" cellspacing="0"
                       style="background-color: {{ $accent }}; color: #ffffff; text-align: center;">
                    <tr>
                        <td style="font-size: 18px; font-weight: bold;">
                            {{ $branding->initials() }}
                        </td>
                    </tr>
                </table>
            @endif
        </td>
        <td style="padding-bottom: 12px; vertical-align: top; font-family: DejaVu Sans, sans-serif;">
            <div style="color: {{ $accent }}; font-size: 15px; font-weight: bold;">
                {{ $branding->trade_name }}
            </div>

            @if ($branding->legal_name)
                <div style="margin-top: 3px; font-size: 10px;">
                    {{ $branding->legal_name }}
                </div>
            @endif

            @if ($branding->nit)
                <div style="margin-top: 3px; font-size: 10px;">
                    NIT: {{ $branding->nit }}
                </div>
            @endif

            @if ($branding->address || $branding->city)
                <div style="margin-top: 3px; font-size: 10px;">
                    {{ collect([$branding->address, $branding->city])->filter()->implode(', ') }}
                </div>
            @endif

            @if ($contact)
                <div style="margin-top: 3px; font-size: 10px;">
                    {{ $contact }}
                </div>
            @endif
        </td>
    </tr>
</table>