<?php

namespace App\Http\Controllers;

use App\Models\MasterKelas;
use App\Models\Refleksi;
use Illuminate\Http\Request;

/**
 * Form refleksi publik — tanpa login: peserta isi nama + kelas, 2 isian, 3 bintang.
 */
class RefleksiController extends Controller
{
    public function create()
    {
        $kelasList = MasterKelas::orderBy('name')->get();

        return view('refleksi.form', [
            'kelasList' => $kelasList,
            'statements' => Refleksi::STATEMENTS,
        ]);
    }

    public function store(Request $request)
    {
        $rules = [
            'participant_name' => ['required', 'string', 'max:150'],
            'kelas_id' => ['required', 'integer', 'exists:zd_master_kelas,id'],
            'learned' => ['required', 'string', 'max:2000'],
            'confusing' => ['nullable', 'string', 'max:2000'],
        ];
        $messages = [
            'participant_name.required' => 'Please enter your full name.',
            'kelas_id.required' => 'Please choose your class.',
            'kelas_id.exists' => 'Please choose your class.',
            'learned.required' => 'Please tell us what you learned today.',
        ];
        foreach (array_keys(Refleksi::STATEMENTS) as $field) {
            $rules[$field] = ['required', 'integer', 'between:1,5'];
            $messages["{$field}.required"] = 'Please rate every statement (1-5 stars).';
            $messages["{$field}.between"] = 'Please rate every statement (1-5 stars).';
        }

        $data = $request->validate($rules, $messages);

        Refleksi::create([
            'participant_name' => trim($data['participant_name']),
            'kelas_id' => $data['kelas_id'],
            'learned' => trim($data['learned']),
            'confusing' => isset($data['confusing']) ? trim($data['confusing']) : null,
            'rating_understand' => $data['rating_understand'],
            'rating_teamwork' => $data['rating_teamwork'],
            'rating_fun' => $data['rating_fun'],
        ]);

        return redirect()->route('refleksi.create')->with('submitted', true);
    }
}
