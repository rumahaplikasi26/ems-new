<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class LanguageController extends Controller
{
    /**
     * Change application language
     *
     * @param Request $request
     * @param string $locale
     * @return \Illuminate\Http\RedirectResponse
     */
    public function switchLanguage(Request $request, $locale)
    {
        // Validate locale
        $supportedLocales = ['id', 'en'];
        
        if (!in_array($locale, $supportedLocales)) {
            abort(404, 'Language not supported');
        }

        // Set locale
        App::setLocale($locale);
        
        // Store in session for persistence
        Session::put('locale', $locale);

        // Redirect back with success message
        return redirect()->back()->with('success', __('messages.success.language_changed'));
    }

    /**
     * Switch language via AJAX
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function switch(Request $request)
    {
        try {
            // Validate request
            $request->validate([
                'locale' => 'required|string|in:id,en'
            ]);

            $locale = $request->input('locale');
            
            // Set locale
            App::setLocale($locale);
            
            // Store in session for persistence
            Session::put('locale', $locale);

            return response()->json([
                'success' => true,
                'message' => 'Language changed successfully',
                'locale' => $locale,
                'language_name' => $this->getLanguageName($locale)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error changing language: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get current language
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCurrentLanguage()
    {
        return response()->json([
            'locale' => App::getLocale(),
            'name' => $this->getLanguageName(App::getLocale())
        ]);
    }

    /**
     * Get available languages
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAvailableLanguages()
    {
        $languages = [
            'id' => [
                'code' => 'id',
                'name' => 'Bahasa Indonesia',
                'native_name' => 'Bahasa Indonesia',
                'flag' => '🇮🇩'
            ],
            'en' => [
                'code' => 'en',
                'name' => 'English',
                'native_name' => 'English',
                'flag' => '🇺🇸'
            ]
        ];

        return response()->json($languages);
    }

    /**
     * Get language name by code
     *
     * @param string $locale
     * @return string
     */
    private function getLanguageName($locale)
    {
        $names = [
            'id' => 'Bahasa Indonesia',
            'en' => 'English'
        ];

        return $names[$locale] ?? 'Unknown';
    }
}
