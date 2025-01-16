<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateUserRequest extends FormRequest
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
      'role_id' => 'required|exists:roles,id',
      'name' => 'required|string|max:255',
      'phone' => 'required|string|max:15',
      'email' => 'required|email|unique:users,email',
      'address' => 'required|string',
      // 'photo' => 'nullable|image|max:2048',
      'photo' => 'nullable|image',
    ];
  }
}
