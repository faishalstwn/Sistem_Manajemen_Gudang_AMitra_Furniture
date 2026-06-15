<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\WarehouseLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class AdminLokasiGudangController extends Controller
{
    
    public function peta()
    {
        $locations = WarehouseLocation::with('products')->get();

        // Hitung ukuran grid berdasar data
        $maxBaris = $locations->max('baris') ?: 0;
        $maxKolom = $locations->max('kolom') ?: 0;

        // Buat grid 2D untuk mapping posisi
        $grid = [];
        foreach ($locations as $loc) {
            $grid[$loc->baris][$loc->kolom] = $loc;
        }

        // Zona dan statistik
        $zonaList = $locations->pluck('zona')->unique()->sort()->values();

        $stats = [
            'total_lokasi'  => $locations->count(),
            'total_terisi'  => $locations->filter(fn($l) => $l->totalTerisi() > 0)->count(),
            'total_kosong'  => $locations->filter(fn($l) => $l->totalTerisi() === 0)->count(),
            'total_penuh'   => $locations->filter(fn($l) => $l->persentaseTerisi() >= 90)->count(),
        ];

        return Inertia::render('Admin/LokasiGudang/Peta', [
            'locations' => $locations, 'grid' => $grid, 'maxBaris' => $maxBaris,
            'maxKolom' => $maxKolom, 'zonaList' => $zonaList, 'stats' => $stats,
        ]);
    }

    public function index(Request $request)
    {
        $query = WarehouseLocation::with('products');

        if ($request->filled('zona')) {
            $query->where('zona', $request->zona);
        }

        if ($request->filled('tipe')) {
            $query->where('tipe', $request->tipe);
        }

        if ($request->filled('cari')) {
            $query->where(function ($q) use ($request) {
                $q->where('kode', 'like', '%' . $request->cari . '%')
                  ->orWhere('keterangan', 'like', '%' . $request->cari . '%');
            });
        }

        $locations = $query->orderBy('zona')->orderBy('baris')->orderBy('kolom')
                          ->paginate(20)->withQueryString();

        $zonaList = WarehouseLocation::distinct()->pluck('zona')->sort();

        return Inertia::render('Admin/LokasiGudang/Index', ['locations' => $locations, 'zonaList' => $zonaList]);
    }

   
    public function create()
    {
        $zonaList = WarehouseLocation::distinct()->pluck('zona')->sort();
        return Inertia::render('Admin/LokasiGudang/Create', ['zonaList' => $zonaList]);
    }

   
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode'       => 'required|string|max:20|unique:warehouse_locations,kode',
            'zona'       => 'required|string|max:50',
            'baris'      => 'required|integer|min:1',
            'kolom'      => 'required|integer|min:1',
            'tipe'       => 'required|in:rak,lantai,palet',
            'kapasitas'  => 'required|integer|min:1',
            'keterangan' => 'nullable|string|max:500',
        ]);

      
        $exists = WarehouseLocation::where('baris', $validated['baris'])
                    ->where('kolom', $validated['kolom'])->exists();

        if ($exists) {
            return back()->withErrors(['baris' => 'Posisi baris & kolom ini sudah terpakai.'])
                         ->withInput();
        }

        WarehouseLocation::create($validated);

        return redirect()->route('admin.lokasi-gudang.index')
            ->with('success', 'Lokasi gudang berhasil ditambahkan.');
    }

   
    public function show(WarehouseLocation $location)
    {
        $location->load('products');

        $availableProducts = Product::where('stock', '>', 0)
            ->whereNotIn('id', $location->products->pluck('id'))
            ->orderBy('name')->get();

        return Inertia::render('Admin/LokasiGudang/Show', ['location' => $location, 'availableProducts' => $availableProducts]);
    }

  
    public function edit(WarehouseLocation $location)
    {
        $zonaList = WarehouseLocation::distinct()->pluck('zona')->sort();
        return Inertia::render('Admin/LokasiGudang/Edit', ['location' => $location, 'zonaList' => $zonaList]);
    }


    public function update(Request $request, WarehouseLocation $location)
    {
        $validated = $request->validate([
            'kode'       => ['required', 'string', 'max:20', Rule::unique('warehouse_locations')->ignore($location->id)],
            'zona'       => 'required|string|max:50',
            'baris'      => 'required|integer|min:1',
            'kolom'      => 'required|integer|min:1',
            'tipe'       => 'required|in:rak,lantai,palet',
            'kapasitas'  => 'required|integer|min:1',
            'keterangan' => 'nullable|string|max:500',
        ]);

        $exists = WarehouseLocation::where('baris', $validated['baris'])
                    ->where('kolom', $validated['kolom'])
                    ->where('id', '!=', $location->id)
                    ->exists();

        if ($exists) {
            return back()->withErrors(['baris' => 'Posisi baris & kolom ini sudah terpakai.'])
                         ->withInput();
        }

        $location->update($validated);

        return redirect()->route('admin.lokasi-gudang.index')
            ->with('success', 'Lokasi gudang berhasil diperbarui.');
    }

    public function destroy(WarehouseLocation $location)
    {
        if ($location->products()->count() > 0) {
            return back()->with('error', 'Tidak bisa menghapus lokasi yang masih menyimpan produk.');
        }

        $location->delete();

        return redirect()->route('admin.lokasi-gudang.index')
            ->with('success', 'Lokasi gudang berhasil dihapus.');
    }

       public function assignProduct(Request $request, WarehouseLocation $location)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'jumlah'     => 'required|integer|min:1',
        ]);

        
        $sisa = $location->sisaKapasitas();
        if ($validated['jumlah'] > $sisa) {
            return back()->with('error', "Kapasitas tidak cukup. Sisa kapasitas: {$sisa} unit.")
                         ->withInput();
        }

      
        $existing = $location->products()->where('product_id', $validated['product_id'])->first();

        if ($existing) {
            // Update jumlah
            $location->products()->updateExistingPivot($validated['product_id'], [
                'jumlah' => $existing->pivot->jumlah + $validated['jumlah'],
            ]);
        } else {
            $location->products()->attach($validated['product_id'], [
                'jumlah' => $validated['jumlah'],
            ]);
        }

        return back()->with('success', 'Produk berhasil ditambahkan ke lokasi ini.');
    }

  
    public function removeProduct(WarehouseLocation $location, Product $product)
    {
        $location->products()->detach($product->id);

        return back()->with('success', 'Produk berhasil dikeluarkan dari lokasi ini.');
    }

   
    public function updateProductQty(Request $request, WarehouseLocation $location, Product $product)
    {
        $validated = $request->validate([
            'jumlah' => 'required|integer|min:0',
        ]);

        if ($validated['jumlah'] === 0) {
            $location->products()->detach($product->id);
            return back()->with('success', 'Produk dikeluarkan dari lokasi.');
        }

        // Cek kapasitas (total - jumlah lama + jumlah baru)
        $existing = $location->products()->where('product_id', $product->id)->first();
        $jumlahLama = $existing ? $existing->pivot->jumlah : 0;
        $totalBaru = $location->totalTerisi() - $jumlahLama + $validated['jumlah'];

        if ($totalBaru > $location->kapasitas) {
            return back()->with('error', 'Jumlah melebihi kapasitas lokasi.');
        }

        $location->products()->updateExistingPivot($product->id, [
            'jumlah' => $validated['jumlah'],
        ]);

        return back()->with('success', 'Jumlah produk di lokasi berhasil diupdate.');
    }
}
