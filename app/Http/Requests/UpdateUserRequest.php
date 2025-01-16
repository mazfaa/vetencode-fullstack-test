<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
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
      'role_id' => 'required',
      'name' => 'required|string|max:255',
      'phone' => ['required', 'string', 'unique:users,phone,' . $this->route('id'), 'regex:/^(62|0)[8][1-9][0-9]{6,11}$/'], // Pola untuk nomor telepon Indonesia],
      'email' => 'required|email|unique:users,email,' . $this->route('id'),
      'address' => 'required|string',
      'photo' => 'nullable|image',
    ];
  }
}
