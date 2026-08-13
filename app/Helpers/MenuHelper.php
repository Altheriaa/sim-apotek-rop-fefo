<?php

namespace App\Helpers;

class MenuHelper
{
    public static function getMainNavItems()
    {
        return [
            [
                'icon' => 'dashboard',
                'name' => 'Dashboard',
                'path' => '/dashboard',
            ],
            [
                'name' => 'Master Data',
                'icon' => 'database',
                'subItems' => [
                    ['name' => 'Data Obat', 'path' => '/obat'],
                    ['name' => 'Data Supplier', 'path' => '/supplier'],
                ],
            ],
            [
                'name' => 'Transaksi (FEFO)',
                'icon' => 'transaction',
                'subItems' => [
                    ['name' => 'Obat Masuk', 'path' => '/obat-masuk'],
                    ['name' => 'Obat Keluar', 'path' => '/obat-keluar'],
                ],
            ],
            [
                'name' => 'Pemesanan (ROP)',
                'icon' => 'cart',
                'path' => '/pesanan',
            ],
            [
                'name' => 'Laporan',
                'icon' => 'report',
                'subItems' => [
                    ['name' => 'Laporan Obat Masuk', 'path' => '/laporan/obat-masuk'],
                    ['name' => 'Laporan Obat Keluar', 'path' => '/laporan/obat-keluar'],
                ],
            ],
            [
                'name' => 'Pengaturan',
                'icon' => 'settings',
                'subItems' => [
                    ['name' => 'Kelola Pengguna', 'path' => '/pengguna'],
                ],
            ],
        ];
    }

    public static function getMenuGroups()
    {
        return [
            [
                'title' => 'Menu Utama',
                'items' => self::getMainNavItems()
            ]
        ];
    }

    public static function isActive($path)
    {
        return request()->is(ltrim($path, '/').'*'); // Allow matching child paths
    }

    public static function getIconSvg($iconName)
    {
        $icons = [
            'dashboard'   => '<i class="ti ti-layout-dashboard text-lg"></i>',
            'database'    => '<i class="ti ti-database text-lg"></i>',
            'transaction' => '<i class="ti ti-arrows-left-right text-lg"></i>',
            'cart'        => '<i class="ti ti-shopping-cart text-lg"></i>',
            'report'      => '<i class="ti ti-file-analytics text-lg"></i>',
            'settings'    => '<i class="ti ti-settings text-lg"></i>',
        ];

        return $icons[$iconName] ?? '<i class="ti ti-circle text-lg"></i>';
    }
}
