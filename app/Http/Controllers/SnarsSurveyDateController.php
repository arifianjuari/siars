<?php

namespace App\Http\Controllers;

use App\Models\SnarsConfiguration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SnarsSurveyDateController extends Controller
{
    /**
     * Update survey date
     */
    public function update(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'survey_date' => 'required|date|after:today',
        ], [
            'survey_date.required' => 'Tanggal survey wajib diisi.',
            'survey_date.date' => 'Format tanggal survey tidak valid.',
            'survey_date.after' => 'Tanggal survey harus setelah hari ini.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Update the survey date
        $result = SnarsConfiguration::setValue(
            'survey_date',
            $request->input('survey_date'),
            Auth::id()
        );

        if ($result) {
            return redirect()->back()->with('success', 'Tanggal survey akreditasi berhasil diperbarui.');
        } else {
            return redirect()->back()->with('error', 'Gagal memperbarui tanggal survey akreditasi.');
        }
    }
}
