<?php
function icon (string $name, string $class = 'w-4 h-4'): void {
    $icons = [
        'dashboard'    => [
            'path' => '<path fill-rule="evenodd" d="M4.857 3A1.857 1.857 0 0 0 3 4.857v4.286C3 10.169 3.831 11 4.857 11h4.286A1.857 1.857 0 0 0 11 9.143V4.857A1.857 1.857 0 0 0 9.143 3H4.857Zm10 0A1.857 1.857 0 0 0 13 4.857v4.286c0 1.026.831 1.857 1.857 1.857h4.286A1.857 1.857 0 0 0 21 9.143V4.857A1.857 1.857 0 0 0 19.143 3h-4.286Zm-10 10A1.857 1.857 0 0 0 3 14.857v4.286C3 20.169 3.831 21 4.857 21h4.286A1.857 1.857 0 0 0 11 19.143v-4.286A1.857 1.857 0 0 0 9.143 13H4.857Zm10 0A1.857 1.857 0 0 0 13 14.857v4.286c0 1.026.831 1.857 1.857 1.857h4.286A1.857 1.857 0 0 0 21 19.143v-4.286A1.857 1.857 0 0 0 19.143 13h-4.286Z" clip-rule="evenodd"/>',
            'vb' => '0 0 24 24',
            'fill' => 'currentColor'
        ],
        'penyakit'     => [
            'path' => '<path d="M10 3v3M10 14v3M3 10h3M14 10h3"/> <circle cx="10" cy="10" r="4"/>',
            'vb' => '0 0 20 20',
            'fill' => 'none'
        ],  
        'gejala'       => [
            'path' => '<path d="M4 6h12M4 10h8M4 14h10"/>',
            'vb' => '0 0 20 20',
            'fill' => 'none'
        ],
        'basis aturan' => [
            'path' => '<rect x="3" y="3" width="14" height="14" rx="3"/><path d="M7 10l2 2 4-4"/>',
            'vb' => '0 0 20 20',
            'fill' => 'none'
        ],
        'solusi'       => [
            'path' => '<path d="M10 3a7 7 0 100 14A7 7 0 0010 3zM10 7v4M10 13h.01"/>',
            'vb' => '0 0 20 20',
            'fill' => 'none'
        ],
        'pengguna'     => [
            'path' => '<circle cx="10" cy="8" r="3"/><path d="M4 17c0-3.3 2.7-6 6-6s6 2.7 6 6"/>',
            'vb' => '0 0 20 20',
            'fill' => 'none'
        ],
        'pengaturan'       => [
            'path' => '<circle cx="10" cy="10" r="3"/><path d="M10 2v2M10 16v2M2 10h2M16 10h2"/>',
            'vb' => '0 0 20 20',
            'fill' => 'none'
        ],
        'laporan'      => [
            'path' => '<path stroke="currentColor" stroke-linejoin="round" stroke-width="2" d="M16.444
                            18H19a1 1 0 0 0 1-1v-5a1 1 0 0 0-1-1H5a1 1 0 0 0-1 1v5a1 1 0 0 0 1 1h2.556M17 
                            11V5a1 1 0 0 0-1-1H8a1 1 0 0 0-1 1v6h10ZM7 15h10v4a1 1 0 0 1-1 1H8a1 1 0 0 1-1-1v-4Z"/>',
            'vb' => '0 0 24 24',
            'fill' => 'none'
        ],
        'logout'       => [
            'path' => '<path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 
                            12H8m12 0-4 4m4-4-4-4M9 4H7a3 3 0 0 0-3 3v10a3 3 0 0 0 3 3h2"/>',
            'vb' => '0 0 24 24',
            'fill' => 'none'
        ],
        'close'        => [
            'path' => '<path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>',
            'vb' => '0 0 24 24',
            'fill' => 'none'
        ],
        'menu'      => [
            'path' => '<path fill-rule="evenodd" d="M2 4.75A.75.75 0 012.75 4h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 4.75zm0 10.5a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75a.75.75 0 01-.75-.75zM2 10a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 10z" clip-rule="evenodd"/>',
            'vb' => '0 0 20 20',
            'fill' => 'none'
        ],
        'add'       => [
            'path'  => '<path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z"/>',
            'vb'    => '0 0 20 20',
            'fill'  => 'currentColor'
        ],
        'search'     => [
            'path'   => '<path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd"/>',
            'vb'    => '0 0 20 20',
            'fill'  => 'currentColor'
        ],
        'edit'      => [
            'path'   => '<path d="M3.5 5.75c0-.69.56-1.25 1.25-1.25H10A.75.75 0 0010 3H4.75A2.75 2.75 0 002 5.75v9.5A2.75 2.75 0 004.75 18h9.5A2.75 2.75 0 0017 15.25V10a.75.75 0 00-1.5 0v5.25c0 .69-.56 1.25-1.25 1.25h-9.5c-.69 0-1.25-.56-1.25-1.25v-9.5z"/>',
            'vb'    => '0 0 20 20',
            'fill'  => 'none'
        ]

    ];


    $path = $icons[$name] ?? '';
    if (empty($path)) return;
    
    $ic = $icons[$name];
    echo "<svg class=\"{$class} flex-shrink-0\" viewBox=\"{$ic['vb']}\"
               fill=\"{$ic['fill']}\" stroke=\"currentColor\"
               stroke-width=\"1.8\" stroke-linecap=\"round\"
               stroke-linejoin=\"round\">{$ic['path']}</svg>";
}

?>