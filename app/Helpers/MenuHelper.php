<?php

namespace App\Helpers;

class MenuHelper
{
    public static function getMenuGroups()
    {
        $isAdminOrOwner = auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isOwner());
        $isAdminOrKaryawan = auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isKaryawan());
        $isAdmin = auth()->check() && (auth()->user()->isAdmin());

        $groups = [];

        // 1. Menu Utama / Dashboard
        $groups[] = [
            'title' => 'Menu Utama',
            'items' => [
                [
                    'name' => 'Dashboard',
                    'icon' => 'dashboard',
                    'path' => '/dashboard',
                ],
            ],
        ];

        // 2. Stok Rak (Semua role)
        $groups[] = [
            'title' => 'Stok Rak',
            'items' => [
                [
                    'name' => 'Stok Display Rak Obat',
                    'icon' => 'shop',
                    'path' => '/display-rak',
                ],
            ],
        ];

        // 3. Kasir / Penjualan
        $kasirItems = [];
        if (!auth()->user()->isOwner()) {
            $kasirItems[] = [
                'name' => 'Kasir',
                'icon' => 'transaction',
                'path' => '/kasir',
            ];
        }
        $kasirItems[] = [
            'name' => 'Riwayat Penjualan',
            'icon' => 'receipt',
            'path' => '/riwayat-penjualan',
        ];

        $groups[] = [
            'title' => auth()->user()->isOwner() ? 'Penjualan' : 'Kasir',
            'items' => $kasirItems,
        ];

        // 4. Data Master
        $masterItems = [
            [
                'name' => 'Data Obat',
                'icon' => 'medicine',
                'path' => '/obat',
            ],
        ];

        $masterItems[] = [
            'name' => 'Data Supplier',
            'icon' => 'supplier',
            'path' => '/supplier',
        ];

        $groups[] = [
            'title' => 'Data Master',
            'items' => $masterItems,
        ];

        // 5. Gudang (FEFO)
        if($isAdminOrKaryawan) {
            $groups[] = [
                'title' => 'Gudang (FEFO)',
                'items' => [
                    [
                        'name' => 'Stok Gudang',
                        'icon' => 'building-warehouse',
                        'path' => '/stok-gudang',
                    ],
                    [
                        'name' => 'Obat Masuk',
                        'icon' => 'inbox-in',
                        'path' => '/obat-masuk',
                    ],
                    [
                        'name' => 'Transfer ke Rak',
                        'icon' => 'inbox-right',
                        'path' => '/transfer-rak',
                    ],
                ],
            ];
        }else{
            $groups[] = [
                'title' => 'Gudang (FEFO)',
                'items' => [
                    [
                        'name' => 'Stok Gudang',
                        'icon' => 'building-warehouse',
                        'path' => '/stok-gudang',
                    ],
                ],
            ];
        }

        // 6. Pemesanan (ROP)
        if ($isAdminOrOwner) {
            $groups[] = [
                'title' => 'Pemesanan (ROP)',
                'items' => [
                    [
                        'name' => 'Data Pesanan',
                        'icon' => 'cart',
                        'path' => '/pesanan',
                    ],
                ],
            ];
        }

        // 7. Laporan
        if ($isAdminOrOwner) {
            $groups[] = [
                'title' => 'Laporan',
                'items' => [
                    [
                        'name' => 'Laporan Obat Masuk',
                        'icon' => 'report-in',
                        'path' => '/laporan/obat-masuk',
                    ],
                    [
                        'name' => 'Laporan Stok Obat',
                        'icon' => 'report-in',
                        'path' => '/laporan/stok-obat',
                    ],
                    [
                        'name' => 'Laporan Penjualan',
                        'icon' => 'report-out',
                        'path' => '/laporan/penjualan',
                    ],
                ],
            ];
        }

        // 8. Pengaturan
        if ($isAdmin) {
            $groups[] = [
                'title' => 'Pengaturan',
                'items' => [
                    [
                        'name' => 'Kelola Pengguna',
                        'icon' => 'users',
                        'path' => '/pengguna',
                    ],
                ],
            ];
        }

        return $groups;
    }

    public static function isActive($path)
    {
        return request()->is(ltrim($path, '/') . '*');
    }

    public static function getIconSvg($iconName)
    {
        $icons = [
            'dashboard'   => '<i class="ti ti-layout-dashboard text-lg"></i>',
            'database'    => '<i class="ti ti-database text-lg"></i>',
            'medicine'    => '<i class="ti ti-pill text-lg"></i>',
            'supplier'    => '<i class="ti ti-truck-delivery text-lg"></i>',
            'transaction' => '<i class="ti ti-arrows-left-right text-lg"></i>',
            'inbox-in'    => '<i class="ti ti-circle-arrow-down text-lg"></i>',
            'inbox-right'   => '<i class="ti ti-circle-arrow-right text-lg"></i>',
            'cart'        => '<i class="ti ti-shopping-cart text-lg"></i>',
            'report'      => '<i class="ti ti-file-analytics text-lg"></i>',
            'report-in'   => '<i class="ti ti-file-import text-lg"></i>',
            'report-out'  => '<i class="ti ti-file-export text-lg"></i>',
            'settings'    => '<i class="ti ti-settings text-lg"></i>',
            'users'       => '<i class="ti ti-users text-lg"></i>',
            'receipt'     => '<i class="ti ti-receipt text-lg"></i>',
            'shop'        => '<i class="ti ti-shopping-bag"></i>',
            'building-warehouse' => '<i class="ti ti-building-warehouse"></i>',
        ];

        return $icons[$iconName] ?? '<i class="ti ti-circle text-lg"></i>';
    }
}
