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
            'provider_name' => 'required|string|max:150|regex:/^[\pL\s\-]+$/u',
            'dni' => ['required', 'string', 'regex:/(V|E|J)-\d{5,9}/'],
            'phone' => ['required', 'string', 'regex:/^\+58(412|414|424|416|426|422)\d{7}$/'],
            'title' => 'required|string|max:150|not_regex:/[\{\}\<\>]/',
            'description' => 'required|string|not_regex:/[\{\}\<\>]/',
            'category' => 'required|string|in:comida,ropa,vivienda,salud,transporte,otros',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'address_description' => 'required|string|not_regex:/[\{\}\<\>]/',
            'status' => 'sometimes|required|string|in:disponible,reservado,entregado,expirado',
        ];
    }
}
