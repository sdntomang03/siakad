<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class KatrolNilaiRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'classroom_id' => 'required|exists:classrooms,id',
            'subject_id' => 'required|exists:subjects,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'target_min' => 'required|numeric|min:0|max:100',
            'target_max' => 'required|numeric|min:0|max:100|gt:target_min',
        ];
    }

    public function messages()
    {
        return [
            'required' => ':attribute tidak boleh dibiarkan kosong.',
            'exists' => ':attribute tidak valid di database kami.',
            'numeric' => ':attribute harus berupa angka.',
            'min' => ':attribute minimal bernilai :min.',
            'max' => ':attribute maksimal bernilai :max.',
            'gt' => ':attribute harus diisi dengan angka yang lebih besar dari KKM.',
        ];
    }

    public function attributes()
    {
        return [
            'classroom_id' => 'Data Kelas pada Form',
            'subject_id' => 'Mata Pelajaran',
            'academic_year_id' => 'Data Tahun Ajaran',
            'target_min' => 'Target Nilai Terendah (KKM)',
            'target_max' => 'Target Nilai Maksimal',
        ];
    }
}
