<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @php
        $preset = \App\Models\Setting::get('branding_preset', 'default');
        $presets = [
            'default' => ['main' => '#f97316'],
            'green'   => ['main' => '#22c55e'],
            'blue'    => ['main' => '#3b82f6'],
            'red'     => ['main' => '#ef4444'],
            'purple'  => ['main' => '#a855f7'],
        ];
        $primary = ($preset === 'custom') ? \App\Models\Setting::get('branding_custom_color', '#f97316') : ($presets[$preset]['main'] ?? $presets['default']['main']);
        $logo = \App\Models\Setting::get('branding_logo');
    @endphp
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f7f6; margin: 0; padding: 0; color: #333; }
        .wrapper { width: 100%; table-layout: fixed; background-color: #f4f7f6; padding-bottom: 40px; }
        .main { background-color: #ffffff; margin: 0 auto; width: 100%; max-width: 600px; border-spacing: 0; font-family: sans-serif; color: #4a4a4a; }
        .header { background-color: #ffffff; padding: 40px 0; text-align: center; border-bottom: 1px solid #eeeeee; }
        .content { padding: 40px; line-height: 1.6; }
        .footer { padding: 20px; text-align: center; font-size: 12px; color: #999999; }
        .btn { display: inline-block; padding: 12px 30px; background-color: {{ $primary }}; color: #ffffff !important; text-decoration: none; border-radius: 6px; font-weight: bold; margin-top: 20px; }
        h1, h2, h3 { color: {{ $primary }}; margin-top: 0; }
        .logo-img { height: 50px; max-width: 200px; object-fit: contain; }
        hr { border: 0; border-top: 1px solid #eeeeee; margin: 30px 0; }
    </style>
</head>
<body>
    <div class="wrapper">
        <table class="main" width="100%">
            <tr>
                <td class="header">
                    @if($logo)
                        <img src="{{ asset($logo) }}" alt="Logo" class="logo-img">
                    @else
                        <h2 style="margin:0; color: {{ $primary }}">MembersArea</h2>
                    @endif
                </td>
            </tr>
            <tr>
                <td class="content">
                    {!! $content ?? '' !!}
                </td>
            </tr>
            <tr>
                <td class="footer">
                    &copy; {{ date('Y') }} {{ \App\Models\Setting::get('mail_from_name', 'MembersArea') }}. Todos os direitos reservados.
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
