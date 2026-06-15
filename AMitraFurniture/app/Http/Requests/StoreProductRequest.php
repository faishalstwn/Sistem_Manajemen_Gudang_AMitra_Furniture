<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->is_admin;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:products',
            'description' => 'nullable|string',
            'category' => 'required|string|max:100',
            'price' => 'required|numeric|min:0|max:999999999',
            'stock' => 'required|integer|min:0|max:999999',
            'image' => 'nullable|string|max:255',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Nama produk harus diisi',
            'name.unique' => 'Nama produk sudah terdaftar',
            'category.required' => 'Kategori harus dipilih',
            'price.required' => 'Harga harus diisi',
            'price.numeric' => 'Harga harus berupa angka',
            'price.min' => 'Harga tidak boleh negatif',
            'stock.required' => 'Stok harus diisi',
            'stock.integer' => 'Stok harus berupa angka bulat',
        ];
    }
}
