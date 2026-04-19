@php
    $preset = \App\Models\Setting::get('branding_preset', 'default');
    $presets = [
        'default' => '#f97316',
        'green'   => '#22c55e',
        'blue'    => '#3b82f6',
        'red'     => '#ef4444',
        'purple'  => '#a855f7',
    ];

    $hex = ($preset === 'custom') 
        ? \App\Models\Setting::get('branding_custom_color', '#f97316') 
        : ($presets[$preset] ?? $presets['default']);

    $hexToHsl = function($hex) {
        $hex = str_replace('#', '', $hex);
        if(strlen($hex) == 3) $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
        $r = hexdec(substr($hex, 0, 2)) / 255;
        $g = hexdec(substr($hex, 2, 2)) / 255;
        $b = hexdec(substr($hex, 4, 2)) / 255;
        $max = max($r, $g, $b); $min = min($r, $g, $b);
        $h; $s; $l = ($max + $min) / 2;
        if($max == $min) { $h = $s = 0; } else {
            $d = $max - $min;
            $s = $l > 0.5 ? $d / (2 - $max - $min) : $d / ($max + $min);
            switch($max) {
                case $r: $h = ($g - $b) / $d + ($g < $b ? 6 : 0); break;
                case $g: $h = ($b - $r) / $d + 2; break;
                case $b: $h = ($r - $g) / $d + 4; break;
            }
            $h /= 6;
        }
        return [round($h * 360), round($s * 100), round($l * 100)];
    };

    $hexToRgb = function($hex) {
        $hex = str_replace('#', '', $hex);
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
        return "$r, $g, $b";
    };

    $hsl = $hexToHsl($hex);
    $h = $hsl[0];
    $s = $hsl[1];
    
    // Neutral Deep Black Palette (Professional & High Contrast)
    $p_rgb = $hexToRgb($hex);
    // Dynamic Background & Surfaces
    $bg_custom = \App\Models\Setting::get('branding_bg_color');
    $bg = $bg_custom ?: "#020202"; // Deep Coal Black
    
    // Sophisticated Dark Shades
    $s1 = $bg_custom ? 'rgba(255,255,255,0.02)' : "#080808"; 
    $s2 = $bg_custom ? 'rgba(255,255,255,0.04)' : "#0c0c0c";
    $s3 = $bg_custom ? 'rgba(255,255,255,0.06)' : "#111111";
    
    // If it's the professional black theme, use the hex values for maximum depth
    if(!$bg_custom) {
        $bg = "#030303"; // Richer black
        $s1 = "#080808"; // Layer 1
        $s2 = "#0e0e0e"; // Layer 2
        $s3 = "#141414"; // Layer 3
    }

    $border = "rgba(255, 255, 255, 0.05)";
    $border_soft = "rgba(255, 255, 255, 0.03)";
    
    $favicon = \App\Models\Setting::get('branding_favicon');
    $btnTextColor = \App\Models\Setting::get('branding_btn_text_color', '#ffffff');
    $badgeColor = \App\Models\Setting::get('branding_badge_color', $hex);
@endphp

@if($favicon)
    <link rel="icon" type="image/x-icon" href="{{ asset($favicon) }}">
@endif

<style>
:root {
    --primary:        {{ $hex }};
    --primary-glow:   rgba({{ $p_rgb }}, 0.35);
    --primary-soft:   rgba({{ $p_rgb }}, 0.12);
    --primary-faint:  rgba({{ $p_rgb }}, 0.06);

    --primary-light:  hsl({{ $h }}, {{ min(100, $s + 10) }}%, 60%);
    --primary-dark:   hsl({{ $h }}, {{ $s }}%, 40%);
    
    --bg:             {{ $bg }};
    --surface-1:      {{ $s1 }};
    --surface-2:      {{ $s2 }};
    --surface-3:      {{ $s3 }};
    
    --border:         {{ $border }};
    --border-soft:    {{ $border_soft }};

    /* Neutral Text Palette */
    --text-1:         #f5f5f5;
    --text-2:         #a1a1aa;
    --text-3:         #71717a;
}

body { background-color: var(--bg) !important; color: var(--text-1); }

/* Sidebar Immersion */
.sidebar { background-color: var(--surface-1) !important; border-right: 1px solid var(--border) !important; }
.topbar { background-color: var(--surface-1) !important; border-bottom: 1px solid var(--border) !important; }
.card, .stat-card { background-color: var(--surface-1) !important; border: 1px solid var(--border-soft) !important; }

/* Enhanced Sidebar Links */
.sidebar-link {
    padding: 12px var(--space-6) !important;
    margin: 4px var(--space-3) !important;
}
.sidebar-link:hover { background: var(--surface-2) !important; }
.sidebar-link.active {
    background: var(--primary-faint) !important;
    color: var(--primary) !important;
}
.sidebar-link.active .sidebar-icon { color: var(--primary) !important; }

/* Dashboard Icons Matization */
.stat-icon {
    background-color: var(--primary-soft) !important;
    color: var(--primary) !important;
    box-shadow: 0 0 15px var(--primary-faint);
}

/* Button Refinement */
.btn-primary {
    background: linear-gradient(135deg, var(--primary), var(--primary-dark)) !important;
    color: {{ $btnTextColor }} !important;
    box-shadow: 0 4px 12px var(--primary-glow);
    border: none;
    transition: all 0.2s ease-in-out !important;
}
.btn-primary:hover {
    background: linear-gradient(135deg, var(--primary-light), var(--primary)) !important;
    box-shadow: 0 8px 30px var(--primary-glow) !important;
    transform: translateY(-2px);
}

.badge-primary {
    background: var(--primary-soft);
    color: {{ $badgeColor }};
    border: 1px solid rgba({{ $p_rgb }}, 0.2);
}

.sidebar-brand-icon, .auth-logo-icon, .sidebar-user-avatar {
    background: linear-gradient(135deg, var(--primary), var(--primary-dark)) !important;
    box-shadow: 0 0 25px var(--primary-glow) !important;
}

.auth-page {
    background: radial-gradient(circle at center, #0a0a0a 0%, var(--bg) 100%);
    position: relative;
}
.auth-page::after {
    content: "";
    position: absolute;
    inset: 0;
    background: radial-gradient(circle at top right, var(--primary-faint), transparent 40%),
                radial-gradient(circle at bottom left, var(--primary-faint), transparent 40%);
    pointer-events: none;
}

.sidebar-brand, .sidebar-footer { border-color: var(--border) !important; }
.sidebar-submenu { border-left-color: var(--border) !important; }

/* Scrollbar */
::-webkit-scrollbar-thumb { background: var(--surface-3) !important; }
::-webkit-scrollbar-thumb:hover { background: var(--text-3) !important; }
</style>
