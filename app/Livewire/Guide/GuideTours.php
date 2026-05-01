<?php

namespace App\Livewire\Guide;

use App\Models\Booking;
use App\Models\Tour;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

#[Layout('components.layouts.app')]
class GuideTours extends Component
{
    use WithFileUploads;

    /**
     * @var array<int, string>
     */
    private const TRANSPORTATION_OPTIONS = [
        'private_transportation',
        'public_transportation',
        'walking_tour',
        'boat_bangka',
    ];

    /**
     * @var array<string, mixed>
     */
    public array $form = [];

    /**
     * @var array<int, TemporaryUploadedFile>
     */
    public array $tourPhotos = [];

    /**
     * @var array<int, string>
     */
    public array $existingGalleryImages = [];

    public ?int $editingTourId = null;

    public function mount(): void
    {
        $this->resetForm();

        $editTourId = request()->integer('edit');
        if ($editTourId <= 0) {
            return;
        }

        $tourExists = $this->getGuideToursQuery($this->guideId())
            ->whereKey($editTourId)
            ->exists();

        if (! $tourExists) {
            return;
        }

        $this->edit($editTourId);
    }

    public function save(): void
    {
        $guideId = $this->guideId();
        $wasEditing = $this->editingTourId !== null;

        $validated = $this->validate($this->rules());

        $tour = $wasEditing
            ? $this->getGuideToursQuery($guideId)->whereKey($this->editingTourId)->firstOrFail()
            : new Tour;

        $galleryImages = $this->resolveGalleryImages($tour);
        if ($this->tourPhotos !== []) {
            $galleryImages = collect($this->tourPhotos)
                ->take(5)
                ->map(fn (TemporaryUploadedFile $photo): string => $photo->store('guide/tour-gallery', 'public'))
                ->values()
                ->all();
        }

        $payload = $this->existingColumns($this->payloadForTour($validated['form'], $guideId, $galleryImages));
        $tour->forceFill($payload)->save();

        if (Schema::hasColumn('tours', 'is_featured')) {
            $tour->forceFill([
                'is_featured' => $this->tourBookingCount($tour) > 0,
            ])->save();
        }

        if ($wasEditing) {
            $this->redirectRoute('dashboard.guide');

            return;
        }

        $this->dispatch('tour-saved');
        $this->resetEditingState();
    }

    public function edit(int $tourId): void
    {
        $tour = $this->getGuideToursQuery($this->guideId())->whereKey($tourId)->firstOrFail()->fresh();

        $this->editingTourId = (int) $tour->id;
        $this->form = array_merge($this->form, $this->tourToForm($tour));
        $this->tourPhotos = [];
        $this->existingGalleryImages = $this->resolveGalleryImages($tour);
    }

    public function delete(int $tourId): void
    {
        $this->getGuideToursQuery($this->guideId())->whereKey($tourId)->delete();
        if ($this->editingTourId === $tourId) {
            $this->resetEditingState();
        }
    }

    public function cancelEdit(): void
    {
        $this->resetEditingState();
    }

    public function updated(string $propertyName): void
    {
        if (! str_starts_with($propertyName, 'form.')) {
            return;
        }

        $this->syncDraftTourState();
    }

    public function updatedTourPhotos(): void
    {
        $this->processTourPhotos();
    }

    public function processTourPhotos(): void
    {
        $this->resetValidation('tourPhotos');
        $this->resetValidation('tourPhotos.*');

        $this->tourPhotos = collect($this->tourPhotos)
            ->filter(fn (mixed $photo): bool => $photo instanceof TemporaryUploadedFile || $photo instanceof UploadedFile)
            ->take(5)
            ->values()
            ->all();

        if ($this->tourPhotos === []) {
            return;
        }

        $this->validateOnly('tourPhotos.*');
        $this->persistUploadedTourPhotos();
    }

    public function removeTourPhoto(int $index): void
    {
        if (! array_key_exists($index, $this->existingGalleryImages)) {
            return;
        }

        $removedPath = (string) $this->existingGalleryImages[$index];
        unset($this->existingGalleryImages[$index]);

        $galleryImages = array_values($this->existingGalleryImages);
        $this->existingGalleryImages = $galleryImages;

        if (! str_starts_with($removedPath, 'data:image/')) {
            Storage::disk('public')->delete($removedPath);
        }

        if ($this->editingTourId === null) {
            return;
        }

        $tour = $this->getGuideToursQuery($this->guideId())
            ->whereKey($this->editingTourId)
            ->first();

        if ($tour === null) {
            return;
        }

        $imagePath = $galleryImages[0] ?? null;

        $tour->forceFill($this->existingColumns([
            'gallery_images' => $galleryImages,
            'image_path' => $imagePath,
            'featured_image' => $imagePath,
        ]))->save();
    }

    public function toggleTransportation(string $option): void
    {
        if (! in_array($option, self::TRANSPORTATION_OPTIONS, true)) {
            return;
        }

        $selected = collect($this->form['transportation'] ?? [])
            ->filter(fn (mixed $value): bool => is_string($value) && in_array($value, self::TRANSPORTATION_OPTIONS, true))
            ->values()
            ->all();

        if (in_array($option, $selected, true)) {
            $selected = array_values(array_filter($selected, fn (string $value): bool => $value !== $option));
        } else {
            $selected[] = $option;
        }

        $this->form = array_merge($this->form, ['transportation' => $selected]);
    }

    public function render()
    {
        $guideId = $this->guideId();
        $guide = Auth::user();

        $tours = $this->getGuideToursQuery($guideId)
            ->latest()
            ->get();

        return view('livewire.guide.guide-tours', [
            'tours' => $tours,
            'title' => 'Guide Tours',
            'guideName' => (string) ($guide?->full_name ?? $guide?->name ?? 'Guide'),
            'guidePhotoPath' => $this->resolveGuidePhotoPath($guideId, (string) ($guide?->profile_photo_path ?? '')),
            'transportationOptions' => self::TRANSPORTATION_OPTIONS,
        ]);
    }

    /**
     * @return array<string, array<int, string>>
     */
    protected function rules(): array
    {
        return [
            'form.title' => ['required', 'string', 'max:255'],
            'form.region' => ['required', 'string', 'max:255'],
            'form.city' => ['nullable', 'string', 'max:255'],
            'form.summary' => ['required', 'string', 'max:1000'],
            'form.duration_label' => ['nullable', 'string', 'max:255'],
            'form.duration_hours' => ['required', 'integer', 'min:1', 'max:168'],
            'form.duration_unit' => ['required', 'string', 'in:hours,days'],
            'form.min_guests' => ['nullable', 'integer', 'min:1', 'max:50'],
            'form.max_guests' => ['nullable', 'integer', 'min:1', 'max:50', 'gte:form.min_guests'],
            'form.transportation' => ['nullable', 'array'],
            'form.transportation.*' => ['string', 'in:private_transportation,public_transportation,walking_tour,boat_bangka'],
            'form.price_per_person' => ['required', 'numeric', 'min:0'],
            'form.currency' => ['required', 'string', 'in:PHP'],
            'form.price_unit' => ['required', 'string', 'in:person,group'],
            'form.available_on' => ['nullable', 'date'],
            'form.status' => ['required', 'string', 'in:draft,pending_review,active,paused'],
            'tourPhotos' => ['nullable', 'array', 'max:5'],
            'tourPhotos.*' => ['image', 'max:4096'],
        ];
    }

    private function guideId(): int
    {
        return (int) Auth::id();
    }

    private function resolveGuidePhotoPath(int $guideId, string $fallback): string
    {
        if (trim($fallback) !== '') {
            return trim($fallback);
        }

        if (! Schema::hasTable('tour_guides_profile')) {
            return '';
        }

        $profile = (array) (DB::table('tour_guides_profile')->where('user_id', $guideId)->first() ?? []);

        return $this->firstFilledValue($profile, [
            'profile_photo_path',
            'profile_picture',
            'avatar_path',
            'selfie_path',
        ]);
    }

    /**
     * @return Builder<Tour>
     */
    private function getGuideToursQuery(int $guideId)
    {
        $hasGuideId = Schema::hasColumn('tours', 'guide_id');
        $hasCreatedBy = Schema::hasColumn('tours', 'created_by');

        return Tour::query()
            ->where(function ($query) use ($guideId, $hasGuideId, $hasCreatedBy): void {
                if ($hasGuideId && $hasCreatedBy) {
                    $query->where('guide_id', $guideId)
                        ->orWhere(function ($fallback) use ($guideId): void {
                            $fallback->whereNull('guide_id')->where('created_by', $guideId);
                        });

                    return;
                }

                if ($hasGuideId) {
                    $query->where('guide_id', $guideId);

                    return;
                }

                if ($hasCreatedBy) {
                    $query->where('created_by', $guideId);

                    return;
                }

                $query->whereRaw('1 = 0');
            });
    }

    /**
     * @param  array<string, mixed>  $form
     * @return array<string, mixed>
     */
    private function payloadForTour(array $form, int $guideId, array $galleryImages): array
    {
        $durationValue = (int) $form['duration_hours'];
        $durationUnit = $form['duration_unit'];
        $durationLabel = $durationValue.' '.($durationValue === 1 ? rtrim($durationUnit, 's') : $durationUnit);
        $priceAmount = (float) $form['price_per_person'];
        $featuredImage = $galleryImages[0] ?? null;
        $imagePath = $featuredImage;

        $payload = [
            'guide_id' => $guideId,
            'created_by' => $guideId,
            'title' => $form['title'],
            'name' => $form['title'],
            'region' => $form['region'],
            'city' => $form['city'] ?? null,
            'category' => $this->transportationSummary($form['transportation'] ?? []),
            'summary' => $form['summary'],
            'description' => $form['summary'],
            'duration_label' => $durationLabel,
            'duration_hours' => $durationValue,
            'duration_unit' => $durationUnit,
            'min_guests' => $form['min_guests'] !== '' ? (int) $form['min_guests'] : null,
            'max_guests' => $form['max_guests'] !== '' ? (int) $form['max_guests'] : null,
            'price_per_person' => $priceAmount,
            'price' => $priceAmount,
            'base_price' => $priceAmount,
            'available_on' => $form['available_on'] !== '' ? $form['available_on'] : null,
            'activities' => $this->serializeActivities($form['transportation'] ?? []),
            'gallery_images' => $galleryImages,
            'image_path' => $imagePath,
            'featured_image' => $imagePath,
            'status' => $form['status'],
        ];

        if (Schema::hasColumn('tours', 'short_description')) {
            $payload['short_description'] = $this->shortDescriptionForSummary((string) $form['summary']);
        }

        return $payload;
    }

    /**
     * @return array<string, mixed>
     */
    private function tourToForm(Tour $tour): array
    {
        $transportation = collect($this->deserializeActivities($tour->activities))
            ->filter(fn (mixed $value): bool => is_string($value) && in_array($value, self::TRANSPORTATION_OPTIONS, true))
            ->values()
            ->all();

        if ($transportation === [] && is_string($tour->category) && trim($tour->category) !== '') {
            $transportation = collect(explode(',', $tour->category))
                ->map(fn (string $value): string => trim($value))
                ->filter(fn (string $value): bool => $value !== '')
                ->values()
                ->all();
        }

        $attributes = (array) $tour->getAttributes();
        $city = $this->firstFilledValue($attributes, ['city', 'city_municipality', 'municipality', 'location']);
        if ($city === '') {
            $city = $this->resolveGuideDefaultCity((int) ($tour->guide_id ?? $tour->created_by ?? $this->guideId()));
        }
        $durationLabel = (string) ($tour->duration_label ?? $tour->duration ?? '');
        $durationHours = $tour->duration_hours;

        if ($durationHours === null || $durationHours === '') {
            $durationHours = $this->extractFirstInteger($durationLabel);
        }

        $durationUnit = (string) ($tour->duration_unit ?? '');
        if ($durationUnit === '') {
            $durationUnit = str_contains(strtolower($durationLabel), 'day') ? 'days' : 'hours';
        }

        $guestRange = $this->normalizedGuestRange($tour);
        $availableOn = $this->normalizeDateValue($tour->available_on);
        if ($availableOn === '') {
            $availableOn = $this->normalizeDateValue($attributes['created_at'] ?? null);
        }

        $status = $this->normalizeStatusValue($tour->status ?? $attributes['tour_status'] ?? null);

        return [
            'title' => (string) ($tour->title ?? $tour->name ?? ''),
            'region' => (string) $tour->region,
            'city' => $city,
            'summary' => (string) ($tour->summary ?? $tour->description ?? ''),
            'duration_label' => $durationLabel,
            'duration_hours' => (string) ($durationHours ?? ''),
            'duration_unit' => $durationUnit,
            'min_guests' => $guestRange['min_guests'],
            'max_guests' => $guestRange['max_guests'],
            'transportation' => $transportation,
            'price_per_person' => (string) ($tour->price_per_person ?? $tour->price ?? ''),
            'currency' => 'PHP',
            'price_unit' => 'person',
            'available_on' => $availableOn,
            'status' => $status,
        ];
    }

    private function resetForm(): void
    {
        $this->form = [
            'title' => '',
            'region' => '',
            'city' => '',
            'summary' => '',
            'duration_label' => '',
            'duration_hours' => '',
            'duration_unit' => 'hours',
            'min_guests' => '',
            'max_guests' => '',
            'transportation' => [],
            'price_per_person' => '',
            'currency' => 'PHP',
            'price_unit' => 'person',
            'available_on' => '',
            'status' => 'draft',
        ];
    }

    private function resetEditingState(): void
    {
        $this->editingTourId = null;
        $this->tourPhotos = [];
        $this->existingGalleryImages = [];
        $this->resetForm();
    }

    private function normalizeDateValue(mixed $value): string
    {
        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d');
        }

        if (is_string($value) && trim($value) !== '') {
            $normalized = trim($value);

            foreach (['Y-m-d', 'Y/m/d', 'm/d/Y', 'n/j/Y', 'm-d-Y', 'n-j-Y', 'Y-m-d H:i:s', 'Y/m/d H:i:s'] as $format) {
                try {
                    $date = Carbon::createFromFormat($format, $normalized);

                    if ($date !== false) {
                        return $date->format('Y-m-d');
                    }
                } catch (\Throwable) {
                    continue;
                }
            }

            try {
                return Carbon::parse($normalized)->format('Y-m-d');
            } catch (\Throwable) {
                return '';
            }
        }

        return '';
    }

    private function normalizeStatusValue(mixed $value): string
    {
        if (! is_string($value) || trim($value) === '') {
            return 'draft';
        }

        $normalized = strtolower(trim($value));
        $normalized = str_replace([' ', '-'], '_', $normalized);

        return in_array($normalized, ['draft', 'pending_review', 'active', 'paused'], true)
            ? $normalized
            : 'draft';
    }

    private function shortDescriptionForSummary(string $summary): string
    {
        return Str::limit(trim($summary), 255, '');
    }

    private function resolveGuideDefaultCity(int $guideId): string
    {
        if (! Schema::hasTable('tour_guides_profile')) {
            return '';
        }

        $profile = (array) (DB::table('tour_guides_profile')->where('user_id', $guideId)->first() ?? []);

        return $this->firstFilledValue($profile, ['city_municipality', 'city', 'municipality']);
    }

    private function extractFirstInteger(string $value): string
    {
        if (preg_match('/\d+/', $value, $matches) !== 1) {
            return '';
        }

        return (string) ((int) $matches[0]);
    }

    /**
     * @return array{min_guests: string, max_guests: string}
     */
    private function normalizedGuestRange(Tour $tour): array
    {
        $minGuests = (string) ($tour->min_guests ?? '');
        $maxGuests = (string) ($tour->max_guests ?? '');

        if ($minGuests === '' && $maxGuests === '') {
            $groupSize = $this->firstFilledValue((array) $tour->getAttributes(), ['group_size', 'max_people']);

            if ($groupSize !== '') {
                $minGuests = $groupSize;
                $maxGuests = $groupSize;
            }
        }

        return [
            'min_guests' => $minGuests,
            'max_guests' => $maxGuests,
        ];
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    private function existingColumns(array $payload): array
    {
        return collect($payload)
            ->filter(function (mixed $value, string $column): bool {
                return Schema::hasColumn('tours', $column);
            })
            ->all();
    }

    /**
     * @return array<int, string>
     */
    private function resolveGalleryImages(Tour $tour): array
    {
        if (! is_array($tour->gallery_images)) {
            return [];
        }

        return collect($tour->gallery_images)
            ->filter(fn (mixed $value): bool => is_string($value) && $value !== '')
            ->values()
            ->all();
    }

    private function persistUploadedTourPhotos(): void
    {
        $guideId = $this->guideId();

        $tour = $this->editingTourId !== null
            ? $this->getGuideToursQuery($guideId)->whereKey($this->editingTourId)->firstOrFail()
            : new Tour;

        if ($this->editingTourId === null) {
            $draftPayload = $this->existingColumns($this->draftPayload($guideId));
            $tour->forceFill($draftPayload)->save();
            $this->editingTourId = (int) $tour->id;
        }

        $storedNewImages = collect($this->tourPhotos)
            ->map(fn (TemporaryUploadedFile|UploadedFile $photo): string => $photo->store('guide/tour-gallery', 'public'))
            ->values()
            ->all();

        $existingImages = $this->resolveGalleryImages($tour);
        $galleryImages = collect(array_merge($existingImages, $storedNewImages))
            ->filter(fn (mixed $value): bool => is_string($value) && $value !== '')
            ->unique()
            ->take(5)
            ->values()
            ->all();

        $imagePath = $galleryImages[0] ?? null;

        $tour->forceFill($this->existingColumns([
            'gallery_images' => $galleryImages,
            'image_path' => $imagePath,
            'featured_image' => $imagePath,
        ]))->save();

        $this->existingGalleryImages = $galleryImages;
        $this->tourPhotos = [];
    }

    private function syncDraftTourState(): void
    {
        $guideId = $this->guideId();

        $tour = $this->editingTourId !== null
            ? $this->getGuideToursQuery($guideId)->whereKey($this->editingTourId)->firstOrFail()
            : new Tour;

        if ($this->editingTourId === null) {
            $draftPayload = $this->existingColumns($this->draftPayload($guideId));
            $tour->forceFill($draftPayload)->save();
            $this->editingTourId = (int) $tour->id;
        }

        $tour->forceFill($this->existingColumns($this->draftPayload($guideId)))->save();
    }

    /**
     * @return array<string, mixed>
     */
    private function draftPayload(int $guideId): array
    {
        $title = trim((string) ($this->form['title'] ?? ''));
        $region = trim((string) ($this->form['region'] ?? ''));
        $summary = trim((string) ($this->form['summary'] ?? ''));
        $durationValue = trim((string) ($this->form['duration_hours'] ?? ''));
        $durationUnit = trim((string) ($this->form['duration_unit'] ?? 'hours'));
        $pricePerPerson = trim((string) ($this->form['price_per_person'] ?? ''));
        $city = trim((string) ($this->form['city'] ?? ''));

        $title = $title !== '' ? $title : 'Untitled Tour';
        $region = $region !== '' ? $region : 'Region not set';
        $summary = $summary !== '' ? $summary : 'Draft tour package';
        $durationValue = $durationValue !== '' ? $durationValue : '1';
        $durationUnit = in_array($durationUnit, ['hours', 'days'], true) ? $durationUnit : 'hours';
        $legacyDuration = $durationValue.' '.((int) $durationValue === 1 ? rtrim($durationUnit, 's') : $durationUnit);
        $priceAmount = (float) ($pricePerPerson !== '' ? $pricePerPerson : 0);

        return [
            'guide_id' => $guideId,
            'created_by' => $guideId,
            'title' => $title,
            'name' => $title,
            'region' => $region,
            'city' => $city !== '' ? $city : null,
            'summary' => $summary,
            'description' => $summary,
            'duration' => $legacyDuration,
            'duration_label' => $legacyDuration,
            'duration_hours' => (int) $durationValue,
            'duration_unit' => $durationUnit,
            'price_per_person' => $priceAmount,
            'price' => $priceAmount,
            'base_price' => $priceAmount,
            'activities' => $this->serializeActivities([]),
            'status' => 'draft',
        ];
    }

    private function tourBookingCount(Tour $tour): int
    {
        if (! Schema::hasTable('bookings')) {
            return 0;
        }

        $query = Booking::query()->where('tour_id', $tour->id);

        return (int) $query->count();
    }

    /**
     * @param  array<int, mixed>  $transportation
     */
    private function transportationSummary(array $transportation): ?string
    {
        $labels = collect($transportation)
            ->filter(fn (mixed $value): bool => is_string($value) && in_array($value, self::TRANSPORTATION_OPTIONS, true))
            ->map(fn (string $value): string => str_replace('_', ' ', $value))
            ->map(fn (string $value): string => ucwords($value))
            ->values()
            ->all();

        if ($labels === []) {
            return null;
        }

        return implode(', ', $labels);
    }

    /**
     * @param  array<string, mixed>  $values
     * @param  array<int, string>  $keys
     */
    private function firstFilledValue(array $values, array $keys, string $fallback = ''): string
    {
        foreach ($keys as $key) {
            if (! array_key_exists($key, $values)) {
                continue;
            }

            $value = trim((string) $values[$key]);
            if ($value !== '') {
                return $value;
            }
        }

        return $fallback;
    }

    /**
     * @param  array<int, mixed>  $activities
     */
    private function serializeActivities(array $activities): string
    {
        $filteredActivities = collect($activities)
            ->filter(fn (mixed $value): bool => is_string($value) && in_array($value, self::TRANSPORTATION_OPTIONS, true))
            ->values()
            ->all();

        $encoded = json_encode($filteredActivities);

        return $encoded !== false ? $encoded : '[]';
    }

    /**
     * @return array<int, string>
     */
    private function deserializeActivities(mixed $activities): array
    {
        if (is_array($activities)) {
            return array_values(array_filter($activities, fn (mixed $value): bool => is_string($value)));
        }

        if (! is_string($activities) || trim($activities) === '') {
            return [];
        }

        $decoded = json_decode($activities, true);

        if (! is_array($decoded)) {
            return [];
        }

        return array_values(array_filter($decoded, fn (mixed $value): bool => is_string($value)));
    }
}
