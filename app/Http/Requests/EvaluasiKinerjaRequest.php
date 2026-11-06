<?php

namespace App\Http\Requests;

use App\Models\EvaluasiKinerja;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EvaluasiKinerjaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'Admin';
    }

    public function rules(): array
    {
        return [
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'nilai' => ['required', Rule::in(EvaluasiKinerja::NILAI)],
            'keterangan' => ['nullable', 'string', 'max:5000'],
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required' => 'Nama pegawai wajib dipilih.',
            'user_id.integer' => 'Pegawai tidak valid.',
            'user_id.exists' => 'Pegawai yang dipilih tidak ditemukan.',
            'nilai.required' => 'Nilai wajib dipilih.',
            'nilai.in' => 'Nilai harus Sangat Baik, Baik, atau Cukup Baik.',
            'keterangan.string' => 'Keterangan harus berupa teks.',
            'keterangan.max' => 'Keterangan maksimal 5.000 karakter.',
        ];
    }
}
