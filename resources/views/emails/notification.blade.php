@php
    $company = \App\Models\CompanySetting::current();
    $brandColor = $company->accentHex();
@endphp
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <title>{{ $title }}</title>
</head>
<body style="margin:0; padding:32px 12px; background:#f5f6fa; color:#263238; font-family:Arial,Helvetica,sans-serif;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-collapse:collapse;">
        <tr>
            <td align="center">
                <table role="presentation" width="600" cellspacing="0" cellpadding="0" style="width:100%; max-width:600px; border-collapse:separate; border-spacing:0; overflow:hidden; border-radius:18px; background:#ffffff; box-shadow:0 12px 36px rgba(15,23,42,.10);">
                    <tr>
                        <td style="padding:26px 32px; background:{{ $brandColor }};">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-collapse:collapse;">
                                <tr>
                                    <td width="58" valign="middle">
                                        @if ($company->logoUrl())
                                            <img src="{{ $company->logoUrl() }}" width="48" height="48" alt="{{ $company->trade_name }}" style="display:block; width:48px; height:48px; border:0; border-radius:12px; background:#ffffff; object-fit:contain;">
                                        @else
                                            <div style="width:48px; height:48px; border-radius:12px; background:#ffffff; color:{{ $brandColor }}; font-size:17px; font-weight:700; line-height:48px; text-align:center;">
                                                {{ $company->initials() }}
                                            </div>
                                        @endif
                                    </td>
                                    <td valign="middle" style="padding-left:12px; color:#ffffff;">
                                        <div style="font-size:17px; font-weight:700; line-height:1.3;">
                                            {{ $company->trade_name }}
                                        </div>
                                        @if ($company->slogan)
                                            <div style="padding-top:4px; color:rgba(255,255,255,.88); font-size:12px;">
                                                {{ $company->slogan }}
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:34px 36px 20px;">
                            <div style="margin-bottom:10px; color:{{ $brandColor }}; font-size:11px; font-weight:700; letter-spacing:1.2px; text-transform:uppercase;">
                                Notificación del ERP
                            </div>
                            <h1 style="margin:0 0 18px; color:#172033; font-size:25px; line-height:1.3;">
                                {{ $title }}
                            </h1>
                            <p style="margin:0 0 12px; color:#263238; font-size:16px; font-weight:700;">
                                {{ $greeting }}
                            </p>
                            <p style="margin:0; color:#596579; font-size:15px; line-height:1.7;">
                                {{ $bodyText }}
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:8px 36px 26px;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-collapse:collapse; border:1px solid #e8ebf1; border-radius:12px;">
                                @foreach ($details as $detail)
                                    <tr>
                                        <td style="padding:13px 15px; border-bottom:1px solid #edf0f4; color:#687386; font-size:13px;">
                                            {{ $detail['label'] }}
                                        </td>
                                        <td align="right" style="padding:13px 15px; border-bottom:1px solid #edf0f4; color:#172033; font-size:13px; font-weight:700;">
                                            {{ $detail['value'] }}
                                        </td>
                                    </tr>
                                @endforeach
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td align="center" style="padding:0 36px 34px;">
                            <a href="{{ $actionUrl }}" style="display:inline-block; padding:14px 24px; border-radius:10px; background:{{ $brandColor }}; color:#ffffff; font-size:14px; font-weight:700; text-decoration:none;">
                                {{ $actionText }}
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td align="center" style="padding:20px 30px; border-top:1px solid #edf0f4; background:#fafbfc; color:#7b8494; font-size:12px; line-height:1.7;">
                            <div style="color:#465164; font-weight:700;">{{ $company->trade_name }}</div>
                            @if ($company->address || $company->city)
                                <div>{{ collect([$company->address, $company->city])->filter()->join(', ') }}</div>
                            @endif
                            @if ($company->phone || $company->email)
                                <div>{{ collect([$company->phone, $company->email])->filter()->join(' · ') }}</div>
                            @endif
                            <div style="padding-top:8px;">Mensaje automático del sistema de gestión empresarial.</div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
