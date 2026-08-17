<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class AppLayout extends Component
{
    /**
     * AppLayout menerima prop 'title' yang akan ditampilkan
     * di <title> tag dan header halaman.
     *
     * Cara pakai: <x-app-layout title="Daftar Tiket">
     */
    public function __construct(public string $title = 'Dashboard')
    {
        //
    }

    public function render(): View
    {
        return view('layouts.app');
    }
}
