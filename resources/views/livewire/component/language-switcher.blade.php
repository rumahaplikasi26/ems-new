<div class="language-switcher">
    <div class="dropdown">
        <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="languageDropdown" data-bs-toggle="dropdown" aria-expanded="false">
            <span class="flag">{{ $availableLocales[$currentLocale]['flag'] }}</span>
            <span class="language-name">{{ $availableLocales[$currentLocale]['name'] }}</span>
        </button>
        
        <ul class="dropdown-menu" aria-labelledby="languageDropdown">
            @foreach($availableLocales as $locale)
                <li>
                    <button 
                        class="dropdown-item {{ $currentLocale === $locale['code'] ? 'active' : '' }}" 
                        wire:click="switchLanguage('{{ $locale['code'] }}')"
                        type="button"
                    >
                        <span class="flag me-2">{{ $locale['flag'] }}</span>
                        {{ $locale['name'] }}
                        @if($currentLocale === $locale['code'])
                            <i class="fas fa-check ms-auto"></i>
                        @endif
                    </button>
                </li>
            @endforeach
        </ul>
    </div>
</div>

<style>
.language-switcher .dropdown-toggle {
    display: flex;
    align-items: center;
    gap: 8px;
    border: 1px solid #dee2e6;
    background: white;
    color: #495057;
    padding: 8px 12px;
    border-radius: 4px;
    transition: all 0.2s ease;
}

.language-switcher .dropdown-toggle:hover {
    background: #f8f9fa;
    border-color: #adb5bd;
}

.language-switcher .dropdown-toggle:focus {
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.language-switcher .flag {
    font-size: 16px;
}

.language-switcher .language-name {
    font-size: 14px;
    font-weight: 500;
}

.language-switcher .dropdown-item {
    display: flex;
    align-items: center;
    padding: 8px 16px;
    border: none;
    background: none;
    width: 100%;
    text-align: left;
    transition: background-color 0.2s ease;
}

.language-switcher .dropdown-item:hover {
    background-color: #f8f9fa;
}

.language-switcher .dropdown-item.active {
    background-color: #e3f2fd;
    color: #1976d2;
}

.language-switcher .dropdown-item .flag {
    font-size: 14px;
}

.language-switcher .dropdown-item .fas.fa-check {
    color: #28a745;
    font-size: 12px;
}
</style>

<script>
document.addEventListener('livewire:init', function () {
    Livewire.on('language-changed', function (locale) {
        // Reload the page to apply language changes
        setTimeout(() => {
            window.location.reload();
        }, 100);
    });
});
</script>
