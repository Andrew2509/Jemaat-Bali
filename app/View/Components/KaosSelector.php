<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\ComponentAttributeBag;

class KaosSelector extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public iterable $designs,
        public ?int $selectedId = null,
        public array $draft = [],
        public string $mode = 'kaos_saja', // 'kaos_saja' | 'kaos_donasi'
        public string $formAction = '',
        public bool $showSidebar = true,
        public ?string $sizeGuideRoute = null,
    ) {
        // Normalisasi draft: pastikan struktur [design_id][gender][size] = qty
        $this->draft = $this->normalizeDraft($draft);
    }

    /**
     * Normalisasi draft ke struktur: [design_id][gender][size] = qty
     */
    private function normalizeDraft(array $draft): array
    {
        $normalized = [];
        
        // Jika draft adalah items[] format lama (checkout lama)
        if (isset($draft['items']) && is_array($draft['items'])) {
            foreach ($draft['items'] as $item) {
                $id = $item['design_id'] ?? null;
                $gender = $item['gender'] ?? 'pria';
                $size = $item['ukuran'] ?? '';
                $qty = (int)($item['jumlah'] ?? 1);
                if ($id && $size) {
                    $normalized[$id][$gender][$size] = ($normalized[$id][$gender][$size] ?? 0) + $qty;
                }
            }
        }
        
        // Format baru: [design_id][gender][size] = qty
        if (isset($draft['draft']) && is_array($draft['draft'])) {
            foreach ($draft['draft'] as $id => $genders) {
                foreach ($genders as $gender => $sizes) {
                    foreach ($sizes as $size => $qty) {
                        $normalized[$id][$gender][$size] = ($normalized[$id][$gender][$size] ?? 0) + (int)$qty;
                    }
                }
            }
        }
        
        // Format lama checkout: ukuran + jumlah tunggal
        if (isset($draft['ukuran']) && $draft['ukuran'] && isset($draft['design_id'])) {
            $id = $draft['design_id'];
            $gender = $draft['gender'] ?? 'pria';
            $size = $draft['ukuran'];
            $qty = (int)($draft['jumlah'] ?? 1);
            $normalized[$id][$gender][$size] = ($normalized[$id][$gender][$size] ?? 0) + $qty;
        }
        
        return $normalized;
    }

    public function jsonDesigns(): string
    {
        return json_encode($this->designs->map(function ($d) {
            return [
                'id' => $d->id,
                'nama' => $d->nama,
                'harga' => (int)$d->harga,
                'deskripsi' => $d->deskripsi ?? '',
                'gambar' => $d->gambarUrl(),
                'sizes' => $d->pilihanUkuran(),
            ];
        })->values()->all(), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);
    }

    public function jsonInitialDraft(): string
    {
        return json_encode($this->draft, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);
    }

    public function jsonSelectedId(): string
    {
        return json_encode($this->selectedId, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);
    }

    public function jsonMode(): string
    {
        return json_encode($this->mode, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);
    }

    public function jsonConfig(): string
    {
        return json_encode([
            'formAction' => $this->formAction,
            'showSidebar' => $this->showSidebar,
            'sizeGuideRoute' => $this->sizeGuideRoute,
            'minDonasi' => config('donation.min_kaos_donasi', 50000),
            'presetDonasi' => config('donation.preset_kaos_donasi', [50000, 100000, 250000, 500000, 1000000]),
        ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);
    }

    public function render()
    {
        return view('components.kaos-selector');
    }
}
