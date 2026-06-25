<?php

namespace App\Controllers;

class Page extends BaseController
{
	public function about()
	{
		echo view('about', [
            'title' => 'RuangCerita | About',
            'heroTitle' => 'RuangCerita dibuat buat orang yang suka nulis',
        ]);
	}
    
	public function contact()
	{
		echo view('contact', [
            'title' => 'RuangCerita | Contact',
            'heroTitle' => 'Kalau mau ngobrol, kami selalu open',
        ]);
	}
    
	public function faqs()
	{
		echo view('faqs', [
            'title' => 'RuangCerita | FAQ',
            'heroTitle' => 'Pertanyaan yang paling sering ditanyain',
        ]);
	}
}
