<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(Request $request): View
    {
        $userId = $request->user()->id;

        // Kategori "Sisa" dibuat otomatis kalau belum ada
        Category::firstOrCreate(
            ['user_id' => $userId, 'is_system' => true],
            ['name' => 'Sisa', 'percentage' => 0, 'balance' => 0]
        );

        $categories = Category::where('user_id', $userId)
            ->orderBy('is_system')
            ->orderBy('name')
            ->get();

        $usedPercentage = (float) $categories->where('is_system', false)->sum('percentage');

        return view('categories.index', compact('categories', 'usedPercentage'));
    }

    public function store(Request $request): RedirectResponse
    {
        $userId = $request->user()->id;

        $data = $request->validate([
            'name' => ['required', 'string', 'max:50'],
            'percentage' => ['required', 'numeric', 'min:0', 'max:100'],
            'balance' => ['nullable', 'numeric', 'min:0'],
        ]);

        $used = (float) Category::where('user_id', $userId)
            ->where('is_system', false)
            ->sum('percentage');

        if ($used + $data['percentage'] > 100) {
            return back()
                ->withErrors(['percentage' => 'Total persentase melebihi 100%. Yang masih tersedia: ' . (100 - $used) . '%'])
                ->withInput();
        }

        Category::create([
            'user_id' => $userId,
            'name' => $data['name'],
            'percentage' => $data['percentage'],
            'balance' => $data['balance'] ?? 0,
        ]);

        return redirect()->route('categories.index');
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        abort_unless($category->user_id === $request->user()->id, 403);
        abort_if($category->is_system, 403);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:50'],
            'percentage' => ['required', 'numeric', 'min:0', 'max:100'],
            'balance' => ['required', 'numeric', 'min:0'],
        ]);

        $used = (float) Category::where('user_id', $request->user()->id)
            ->where('is_system', false)
            ->where('id', '!=', $category->id)
            ->sum('percentage');

        if ($used + $data['percentage'] > 100) {
            return back()
                ->withErrors(['percentage' => 'Total persentase melebihi 100%. Yang masih tersedia: ' . (100 - $used) . '%'])
                ->withInput();
        }

        $category->update($data);

        return redirect()->route('categories.index');
    }

    public function destroy(Request $request, Category $category): RedirectResponse
    {
        abort_unless($category->user_id === $request->user()->id, 403);
        abort_if($category->is_system, 403);

        $category->delete();

        return redirect()->route('categories.index');
    }
}
