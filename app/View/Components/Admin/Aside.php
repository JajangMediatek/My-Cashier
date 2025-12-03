<?php

namespace App\View\Components\Admin;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Aside extends Component
{
    /**
     * Create a new component instance.
     */
    public $routes;
    public function __construct()
    {
        $this->routes = [
    [
        "label" => "Dashboard",
        "icon" => "fas fa-laptop",
        "route_name" => "dashboard",
        "route_active" => "dashboard",
        "is_dropdown" => false
    ],
    [
        "label" => "Data Users",
        "icon" => "fas fa-users",
        "route_name" => "users.index",
        "route_active" => "users.*",
        "is_dropdown" => false,
        "admin_only" => true
    ],
    [
        "label" => "Master Data",
        "icon" => "fas fa-database",
        "route_active" => "master-data.*",
        "is_dropdown" => true,
        "dropdown" => [
            [
                "label" => "Kategori",
                "route_active" => "master-data.kategori.*",
                "route_name" => "master-data.kategori.index"
            ],
            [
                "label" => "Product",
                "route_active" => "master-data.product.*",
                "route_name" => "master-data.product.index"
            ],
        ]
    ],
    [
        "label" => "Penerimaan Barang",
        "icon" => "fas fa-truck-loading",
        "route_name" => "penerimaan-barang.index",
        "route_active" => "penerimaan-barang.*",
        "is_dropdown" => false
    ],
    [
        "label" => "Transaksi",
        "icon" => "fas fa-store",
        "route_name" => "pengeluaran-barang.index",
        "route_active" => "pengeluaran-barang.*",
        "is_dropdown" => false
    ],
    [
        "label" => "Pembuangan Barang",
        "icon" => "fas fa-dumpster",
        "route_name" => "pembuangan-barang.index",
        "route_active" => "pembuangan-barang.*",
        "is_dropdown" => false
    ],
    [
        "label" => "Laporan",
        "icon" => "fas fa-file-invoice",
        "route_active" => "laporan.*",
        "is_dropdown" => true,
        "dropdown" => [
            [
                "label" => "Penerimaan Barang",
                "route_active" => "laporan.penerimaan-barang.*",
                "route_name" => "laporan.penerimaan-barang.laporan"
            ],
            [
                "label" => "Transaksi",
                "route_active" => "laporan.pengeluaran-barang.*",
                "route_name" => "laporan.pengeluaran-barang.laporan"
            ],
            [
                "label" => "Pembuangan Barang",
                "route_active" => "laporan.pembuangan-barang.*",
                "route_name" => "laporan.pembuangan-barang.laporan"
            ],
        ]
    ],
];

    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.admin.aside');
    }
}
