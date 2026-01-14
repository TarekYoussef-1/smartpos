<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Department;
use Illuminate\Support\Facades\File; // <-- استيراد File للحذف

class ProductController extends Controller
{
    // ... دالة index تبقى كما هي ...

    public function index(Request $request)
    {
        $q = $request->query('q', null);
        $query = Product::with('department:id,name')->orderBy('id')->select(['id','name','price','department_id','image','created_at']);

        if ($q) {
            $query->where(function($qr) use ($q) {
                $qr->where('name', 'like', "%{$q}%")
                ->orWhere('price', 'like', "%{$q}%");
            });
        }

        $products = $query->get();
        $departments = Department::orderBy('name')->get(['id','name']);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json($products);
        }

        return view('products.index', compact('products','departments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255|unique:products,name',
            'price' => 'required|numeric|min:0',
            'department_id' => 'required|exists:departments,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        // 1. إنشاء المنتج أولاً للحصول على الـ ID
        $product = Product::create($request->only(['name', 'price', 'department_id']));

        // 2. معالجة الصورة إذا تم رفعها
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $extension = $image->getClientOriginalExtension();
            
            // تسمية الصورة بمعرف المنتج + الامتداد
            $imageName = $product->id . '.' . $extension;
            
            // نقل الصورة إلى المسار الجديد داخل public
            $destinationPath = public_path('assets/images/products');
            $image->move($destinationPath, $imageName);
            
            // تحديث حقل الصورة في قاعدة البيانات
            $product->image = $imageName;
            $product->save();
        }

        return response()->json(['success' => 'تمت إضافة الصنف بنجاح', 'product' => $product]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|max:255|unique:products,name,'.$id,
            'price' => 'required|numeric|min:0',
            'department_id' => 'required|exists:departments,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $product = Product::findOrFail($id);
        $data = $request->only(['name', 'price', 'department_id']);

        // معالجة الصورة الجديدة
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $extension = $image->getClientOriginalExtension();
            
            // تسمية الصورة بنفس الاسم (ستقوم باستبدال القديمة)
            $imageName = $product->id . '.' . $extension;
            
            $destinationPath = public_path('assets/images/products');
            $image->move($destinationPath, $imageName);
            
            $data['image'] = $imageName;
        }

        $product->update($data);
        return response()->json(['success' => 'تم تعديل الصنف بنجاح']);
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        // حذف ملف الصورة من المجلد قبل حذف السجل
        if ($product->image) {
            $imagePath = public_path('assets/images/products/' . $product->image);
            if (File::exists($imagePath)) {
                File::delete($imagePath);
            }
        }

        $product->delete();
        return response()->json(['success' => 'تم حذف الصنف بنجاح']);
    }
}