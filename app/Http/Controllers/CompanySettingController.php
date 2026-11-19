<?php

namespace App\Http\Controllers;

use App\Http\Requests\CompanySettingRequest;
use App\Models\CompanySetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class CompanySettingController extends Controller
{
    public function edit(): View
    {
        return view('company-settings.edit', [
            'setting' => CompanySetting::current(),
            'canEdit' => auth()->user()->can('editar-identidad'),
        ]);
    }

    public function update(CompanySettingRequest $request): RedirectResponse
    {
        $setting = CompanySetting::current();
        $data = $request->safe()->except(['logo', 'remove_logo']);

        if ($request->hasFile('logo')) {
            $this->deleteLogo($setting);
            $data['logo_path'] = $request->file('logo')->store('company', 'public');
        } elseif ($request->boolean('remove_logo')) {
            $this->deleteLogo($setting);
            $data['logo_path'] = null;
        }

        $setting->fill($data)->save();

        return redirect()
            ->route('company-settings.edit')
            ->with('status', 'La identidad corporativa se actualizó correctamente.');
    }

    private function deleteLogo(CompanySetting $setting): void
    {
        if ($setting->logo_path) {
            Storage::disk('public')->delete($setting->logo_path);
        }
    }
}