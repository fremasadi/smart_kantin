@if ($image)
    <img src="{{ Storage::url($image) }}" alt="Bukti Transfer" class="w-full rounded shadow">
@else
    <p class="text-gray-500">Tidak ada bukti transfer tersedia.</p>
@endif
