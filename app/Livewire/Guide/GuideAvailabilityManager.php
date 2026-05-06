<?php

namespace App\Livewire\Guide;

use App\Models\GuideAvailability;
use DateTimeInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class GuideAvailabilityManager extends Component
{
    /**
     * @var array<string, mixed>
     */
    public array $form = [
        'date' => '',
        'status' => 'available',
        'slots' => '',
        'note' => '',
        'special_price' => '',
    ];

    public ?int $editingId = null;

    public function save(): void
    {
        if (! Schema::hasTable('guide_availability')) {
            return;
        }

        $guideId = (int) Auth::id();
        $validated = $this->validate([
            'form.date' => ['required', 'date'],
            'form.status' => ['required', 'string', 'in:available,fully_booked,fiesta,limited_slots'],
            'form.slots' => ['nullable', 'integer', 'min:1', 'max:200', 'required_if:form.status,limited_slots'],
            'form.note' => ['nullable', 'string', 'max:1000'],
            'form.special_price' => ['nullable', 'numeric', 'min:0'],
        ]);

        $payload = [
            'guide_id' => $guideId,
            'date' => $validated['form']['date'],
            'status' => $validated['form']['status'],
            'slots' => $validated['form']['status'] === 'limited_slots' ? (int) $validated['form']['slots'] : null,
            'note' => trim((string) $validated['form']['note']) !== '' ? $validated['form']['note'] : null,
            'special_price' => $validated['form']['special_price'] !== '' ? (float) $validated['form']['special_price'] : null,
        ];

        if ($this->editingId !== null) {
            GuideAvailability::query()
                ->whereKey($this->editingId)
                ->where('guide_id', $guideId)
                ->update($payload);
        } else {
            GuideAvailability::query()->updateOrCreate(
                ['guide_id' => $guideId, 'date' => $payload['date']],
                $payload,
            );
        }

        $this->resetForm();
        $this->editingId = null;
    }

    public function edit(int $availabilityId): void
    {
        if (! Schema::hasTable('guide_availability')) {
            return;
        }

        $availability = GuideAvailability::query()
            ->whereKey($availabilityId)
            ->where('guide_id', (int) Auth::id())
            ->firstOrFail();

        $availabilityDate = $availability->date;

        $this->editingId = (int) $availability->id;
        $this->form = [
            'date' => $availabilityDate instanceof DateTimeInterface ? $availabilityDate->format('Y-m-d') : (string) ($availabilityDate ?? ''),
            'status' => $availability->status,
            'slots' => $availability->slots !== null ? (string) $availability->slots : '',
            'note' => (string) ($availability->note ?? ''),
            'special_price' => $availability->special_price !== null ? (string) $availability->special_price : '',
        ];
    }

    public function delete(int $availabilityId): void
    {
        if (! Schema::hasTable('guide_availability')) {
            return;
        }

        GuideAvailability::query()
            ->whereKey($availabilityId)
            ->where('guide_id', (int) Auth::id())
            ->delete();

        if ($this->editingId === $availabilityId) {
            $this->editingId = null;
            $this->resetForm();
        }
    }

    public function cancel(): void
    {
        $this->editingId = null;
        $this->resetForm();
    }

    public function render()
    {
        $rows = collect();

        if (Schema::hasTable('guide_availability')) {
            $rows = GuideAvailability::query()
                ->where('guide_id', (int) Auth::id())
                ->whereDate('date', '>=', now()->toDateString())
                ->orderBy('date')
                ->limit(60)
                ->get();
        }

        return view('livewire.guide.guide-availability-manager', [
            'rows' => $rows,
        ]);
    }

    private function resetForm(): void
    {
        $this->form = [
            'date' => '',
            'status' => 'available',
            'slots' => '',
            'note' => '',
            'special_price' => '',
        ];
    }
}
