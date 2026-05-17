<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ebook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class EbookController extends Controller
{
    public function index(Request $request)
    {
        $query = Ebook::withCount([
            'approvedPurchases as sales_count',
        ]);

        if ($search = $request->get('search')) {
            $query->where(function ($query) use ($search) {
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('author', 'like', "%{$search}%");
            });
        }

        $ebooks = $query->latest()->paginate(15);
        return view('admin.ebooks.index', compact('ebooks'));
    }

    public function create()
    {
        $categories = $this->categories();

        return view('admin.ebooks.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'author'      => ['required', 'string', 'max:255'],
            'price'       => ['required', 'numeric', 'min:0'],
            'category'    => ['required', 'string', 'max:100', Rule::in($this->categories())],
            'status'      => ['required', 'in:draft,published'],
            'cover_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'file_path'   => ['nullable', 'file', 'mimes:pdf', 'max:51200'],
        ]);

        $slug = Str::slug($data['title']);
        $original = $slug;
        $count = 1;
        while (Ebook::where('slug', $slug)->exists()) {
            $slug = $original . '-' . $count++;
        }
        $data['slug'] = $slug;

        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $request->file('cover_image')->store('covers', 'public');
        }

        if ($request->hasFile('file_path')) {
            $data['file_path'] = $request->file('file_path')->store('ebooks', 'public');
        }

        Ebook::create($data);

        return redirect()->route('admin.ebooks.index')->with('success', 'Ebook created successfully.');
    }

    public function edit(Ebook $ebook)
    {
        $categories = $this->categories($ebook->category);

        return view('admin.ebooks.edit', compact('ebook', 'categories'));
    }

    public function update(Request $request, Ebook $ebook)
    {
        $data = $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'author'      => ['required', 'string', 'max:255'],
            'price'       => ['required', 'numeric', 'min:0'],
            'category'    => ['required', 'string', 'max:100', Rule::in($this->categories($ebook->category))],
            'status'      => ['required', 'in:draft,published'],
            'cover_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'file_path'   => ['nullable', 'file', 'mimes:pdf', 'max:51200'],
        ]);

        if ($request->hasFile('cover_image')) {
            if ($ebook->cover_image) {
                Storage::disk('public')->delete($ebook->cover_image);
            }
            $data['cover_image'] = $request->file('cover_image')->store('covers', 'public');
        } else {
            unset($data['cover_image']);
        }

        if ($request->hasFile('file_path')) {
            if ($ebook->file_path) {
                Storage::disk('public')->delete($ebook->file_path);
            }
            $data['file_path'] = $request->file('file_path')->store('ebooks', 'public');
        } else {
            unset($data['file_path']);
        }

        $ebook->update($data);

        return redirect()->route('admin.ebooks.index')->with('success', 'Ebook updated successfully.');
    }

    public function destroy(Ebook $ebook)
    {
        if ($ebook->cover_image) {
            Storage::disk('public')->delete($ebook->cover_image);
        }
        if ($ebook->file_path) {
            Storage::disk('public')->delete($ebook->file_path);
        }
        $ebook->delete();

        return redirect()->route('admin.ebooks.index')->with('success', 'Ebook deleted successfully.');
    }

    private function categories(?string $currentCategory = null): array
    {
        $categories = [
            'Novel',
            'Fiksi',
            'Seri Novel',
            'Motivasi',
            'Bisnis',
            'Ekonomi',
            'Self Dev',
            'Pengembangan Diri',
            'Pendidikan',
            'Sejarah',
            'Teknologi',
            'Kesehatan',
            'Sci-Fi',
            'Programming',
            'Web Development',
            'Software Architecture',
        ];

        if ($currentCategory && !in_array($currentCategory, $categories, true)) {
            array_unshift($categories, $currentCategory);
        }

        return $categories;
    }
}
