<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAidOfferRequest extends FormRequest
{
    public function authorize(): bool
    {
        $offerId = $this->route('id');
        $offer = \App\Models\AidOffer::find($offerId);
        $token = $this->header('X-Secure-Token');
        
        return $offer && $offer->secure_token === $token;
    }

    public function rules(): array
    {
        return [
            'provider_name' => 'sometimes|required|string|max:150',
            'dni' => 'prohibited',
            'phone' => 'prohibited',
            'title' => 'sometimes|required|string|max:150',
            'description' => 'sometimes|required|string',
            'category' => 'sometimes|required|string|in:comida,ropa,vivienda,salud,transporte,otros',
            'latitude' => 'sometimes|required|numeric',
            'longitude' => 'sometimes|required|numeric',
            'address_description' => 'sometimes|required|string',
            'status' => 'sometimes|required|string|in:disponible,reservado,entregado,expirado',
        ];
    }
    
    public function messages()
    {
        return [
            'dni.prohibited' => 'El DNI no puede ser modificado después de la creación (Inmutabilidad).',
            'phone.prohibited' => 'El teléfono no puede ser modificado después de la creación (Inmutabilidad).',
        ];
    }
}
