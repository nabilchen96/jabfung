<?php

namespace App\Http\Requests;

use Carbon\CarbonImmutable;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AbsensiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'Admin';
    }

    public function rules(): array
    {
        // Parse only canonical YYYY-MM values; malformed input is rejected by validation.
        $bulan = $this->input('bulan');
        $days = is_string($bulan) && preg_match('/^[1-9][0-9]{3}-(0[1-9]|1[0-2])$/D', $bulan)
            ? CarbonImmutable::createFromFormat('!Y-m', $bulan)->daysInMonth
            : 31;
        $id = $this->route('absensi')?->id;
        $userId = filter_var($this->input('user_id'), FILTER_VALIDATE_INT) ?: 0;

        return [
            'user_id' => ['bail', 'required', 'integer', 'exists:users,id'],
            'bulan' => [
                'bail', 'required', 'string', 'date_format:Y-m', 'regex:/^[1-9][0-9]{3}-(0[1-9]|1[0-2])$/D',
                Rule::unique('absensis', 'bulan')->where(fn ($query) => $query->where('user_id', $userId))->ignore($id),
            ],
            'total_terlambat' => ['bail', 'required', 'string', 'regex:/^(?:[0-9]{2}|[1-9][0-9]{2}):[0-5][0-9]:[0-5][0-9]$/D'],
            'total_izin' => ['required', 'integer', 'min:0', 'max:'.$days],
            'total_cuti' => ['required', 'integer', 'min:0', 'max:'.$days],
            'total_dinas' => ['required', 'integer', 'min:0', 'max:'.$days],
            'total_pulang_cepat' => ['bail', 'required', 'string', 'regex:/^(?:[0-9]{2}|[1-9][0-9]{2}):[0-5][0-9]:[0-5][0-9]$/D'],
            'total_kehadiran' => ['required', 'integer', 'min:0', 'max:'.$days],
        ];
    }

    public function attributes(): array
    {
        return [
            'user_id' => 'pegawai', 'bulan' => 'bulan',
            'total_terlambat' => 'total terlambat', 'total_izin' => 'total izin',
            'total_cuti' => 'total cuti', 'total_dinas' => 'total dinas',
            'total_pulang_cepat' => 'total pulang cepat', 'total_kehadiran' => 'total kehadiran',
        ];
    }

    public function messages(): array
    {
        return [
            'total_terlambat.regex' => 'Total terlambat harus berformat HH:MM:SS (contoh: 02:30:00 atau 27:15:00).',
            'total_pulang_cepat.regex' => 'Total pulang cepat harus berformat HH:MM:SS (contoh: 01:15:00).',
            'bulan.unique' => 'Absensi pegawai untuk bulan ini sudah ada. Silakan edit catatan yang tersedia.',
            'bulan.date_format' => 'Bulan harus menggunakan format tahun-bulan (contoh: 2026-11).',
            'bulan.regex' => 'Bulan tidak valid.',
            'user_id.exists' => 'Pegawai yang dipilih tidak ditemukan.',
            '*.required' => ':attribute wajib diisi.',
            '*.integer' => ':attribute harus berupa bilangan bulat.',
            '*.min' => ':attribute tidak boleh kurang dari :min.',
            '*.max' => ':attribute tidak boleh melebihi :max hari pada bulan yang dipilih.',
        ];
    }
}
