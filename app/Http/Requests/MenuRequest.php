<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MenuRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nama_makanan'=> 'required',
            'harga'=> 'required',
            'stok'=> 'required',
            'kategori'=> 'required',
        ];
        if ($this->isMethod('post')) {
        $rules['gambar'] = 'required|image|mimes:jpeg,png,jpg,gif|max:5000';
    } else {
        $rules['gambar'] = 'nullable|image|mimes:jpeg,png,jpg,gif|max:5000';
    }

    return $rules;
    }
}