<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAidOfferRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'provider_name' => 'required|string|max:150',
            'dni' => ['required', 'string', 'regex:/(V|E|J)-\d{5,9}/'],
            'phone' => ['required', 'string', 'regex:/^\+58(412|414|424|416|426|422)\d{7}$/'],
            'title' => 'required|string|max:150',
            'description' => 'required|string',
            'category' => 'required|string|in:comida,ropa,vivienda,salud,transporte,otros',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'address_description' => 'required|string',
            'status' => 'sometimes|required|string|in:disponible,reservado,entregado,expirado',
        ];
    }
}
