<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        $categories = Category::with('parent')
            ->withCount('products')
            ->orderByRaw('COALESCE(parent_id, id), parent_id IS NOT NULL, name')
            ->paginate(15);

        return view('backend.categories.index', compact('categories'));
    }

    public function create(): View
    {
        $parentCategories = Category::roots()->where('is_active', true)->get();

        return view('backend.categories.create', compact('parentCategories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $shopId = session('shop_id') ?? auth()->user()->shop_id;

        $data = $request->validate([
            'name'      => ['required', 'string', 'max:128',
                Rule::unique('categories')->where('shop_id', $shopId)],
            'parent_id' => 'nullable|exists:categories,id',
            'is_active' => 'boolean',
        ]);

        $data['shop_id']   = $shopId;
        $data['is_active'] = $request->boolean('is_active');

        Category::create($data);

        return redirect()->route('backend.categories.index')->with('success', 'Kategoriya qo\'shildi.');
    }

    public function edit(Category $category): View
    {
        $parentCategories = Category::roots()->where('is_active', true)
            ->where('id', '!=', $category->id)
            ->get();

        return view('backend.categories.edit', compact('category', 'parentCategories'));
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        if ($request->parent_id == $category->id) {
            return back()->withErrors(['parent_id' => 'Kategoriya o\'ziga parent bo\'la olmaydi']);
        }

        $shopId = session('shop_id') ?? auth()->user()->shop_id;

        $data = $request->validate([
            'name'      => ['required', 'string', 'max:128',
                Rule::unique('categories')->where('shop_id', $shopId)->ignore($category->id)],
            'parent_id' => 'nullable|exists:categories,id',
            'is_active' => 'boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active');

        $category->update($data);

        return redirect()->route('backend.categories.index')->with('success', 'Kategoriya yangilandi.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        if ($category->children->count() > 0) {
            return back()->with('error', 'Avval ichki kategoriyalarni o\'chiring.');
        }

        if ($category->products()->exists()) {
            return back()->with('error', 'Kategoriyada mahsulotlar bor.');
        }

        $category->delete();

        return redirect()->route('backend.categories.index')->with('success', 'Kategoriya o\'chirildi.');
    }
}
