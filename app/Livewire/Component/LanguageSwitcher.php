<?php

namespace App\Livewire\Component;

use Livewire\Component;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class LanguageSwitcher extends Component
{
    public $currentLocale;
    public $availableLocales;

    public function mount()
    {
        $this->currentLocale = App::getLocale();
        $this->availableLocales = [
            'id' => [
                'code' => 'id',
                'name' => 'Bahasa Indonesia',
                'flag' => '🇮🇩'
            ],
            'en' => [
                'code' => 'en',
                'name' => 'English',
                'flag' => '🇺🇸'
            ]
        ];
    }

    public function switchLanguage($locale)
    {
        // Validate locale
        if (!array_key_exists($locale, $this->availableLocales)) {
            return;
        }

        // Set locale
        App::setLocale($locale);
        Session::put('locale', $locale);
        
        // Update current locale
        $this->currentLocale = $locale;
        
        // Emit event to refresh the page
        $this->dispatch('language-changed', $locale);
        
        // Show success message
        session()->flash('success', __('messages.success.language_changed'));
    }

    public function render()
    {
        return view('livewire.component.language-switcher');
    }
}
