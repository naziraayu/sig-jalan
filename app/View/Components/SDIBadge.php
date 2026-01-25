<?php

namespace App\View\Components;

use Illuminate\View\Component;

class SdiBadge extends Component
{
    public $category;
    public $bgColor;
    public $textColor;
    public $sdiValue;
    
    /**
     * Color mapping untuk kategori SDI
     * Sesuai dengan warna di Chart.js dashboard
     */
    private $colorMap = [
        'Baik' => [
            'bg' => 'rgba(72, 187, 120, 0.8)',
            'text' => '#fff'
        ],
        'Sedang' => [
            'bg' => 'rgba(251, 191, 36, 0.8)',
            'text' => '#fff'
        ],
        'Rusak Ringan' => [
            'bg' => 'rgba(255, 159, 64, 0.8)',
            'text' => '#fff'
        ],
        'Rusak Berat' => [
            'bg' => 'rgba(245, 101, 101, 0.8)',
            'text' => '#fff'
        ],
    ];
    
    /**
     * Create a new component instance.
     *
     * @param string $category Kategori kondisi jalan
     * @param float|null $sdiValue Nilai SDI (optional)
     * @return void
     */
    public function __construct($category, $sdiValue = null)
    {
        $this->category = $category;
        $this->sdiValue = $sdiValue;
        
        // Get colors based on category
        $colors = $this->colorMap[$category] ?? [
            'bg' => 'rgba(108, 117, 125, 0.8)', // Default gray untuk unknown category
            'text' => '#fff'
        ];
        
        $this->bgColor = $colors['bg'];
        $this->textColor = $colors['text'];
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.sdi-badge');
    }
}