<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {
        return view('home', [
            'title' => 'RuangCerita | Home',
            'heroTitle' => 'RuangCerita',
            'heroSubtitle' => 'Tempat nulis yang sederhana, fokus, dan enak dipakai harian.',
        ]);
    }
}
