<?php

declare(strict_types=1);

namespace App\Presentation\Http\Request;

use Illuminate\Foundation\Http\FormRequest;

final class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string'],
            'price' => ['required', 'numeric'],
            'currency' => ['nullable', 'string'],
            'categoryId' => ['required', 'string'],
        ];
    }
}