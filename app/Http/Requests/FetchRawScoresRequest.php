<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FetchRawScoresRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Pastikan ini bernilai true
    }

    public function rules()
    {
        return [
            'classroom_id' => 'required|exists:classrooms,id',
            'subject_id' => 'required|exists:subjects,id',
            'academic_year_id' => 'required|exists:academic_years,id',
        ];
    }

    public function messages()
    {
        return [
            'classroom_id.required' => 'Pilihan Kelas tidak boleh kosong.',
            'subject_id.required' => 'Mata Pelajaran tidak boleh kosong.',
            'academic_year_id.required' => 'Tahun Ajaran aktif belum terdeteksi.',
        ];
    }
}
