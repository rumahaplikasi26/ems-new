<?php

namespace App\Livewire\Component;

use Livewire\Component;

class FilePreview extends Component
{
    public $fileUrl;
    public $fileName;
    public $fileType;
    public $showPreview = false;

    public function mount($fileUrl, $fileName = null)
    {
        $this->fileUrl = $fileUrl;
        $this->fileName = $fileName ?: basename($fileUrl);
        $this->fileType = strtolower(pathinfo($this->fileName, PATHINFO_EXTENSION));
    }

    public function togglePreview()
    {
        $this->showPreview = !$this->showPreview;
    }

    public function getFileIcon()
    {
        return match($this->fileType) {
            'pdf' => 'mdi-file-pdf-box text-danger',
            'doc', 'docx' => 'mdi-file-word-box text-primary',
            'xls', 'xlsx' => 'mdi-file-excel-box text-success',
            'ppt', 'pptx' => 'mdi-file-powerpoint-box text-warning',
            'jpg', 'jpeg', 'png', 'gif' => 'mdi-file-image text-info',
            'txt' => 'mdi-file-document-outline text-secondary',
            default => 'mdi-file-document-outline text-secondary'
        };
    }

    public function canPreview()
    {
        return in_array($this->fileType, ['pdf', 'jpg', 'jpeg', 'png', 'gif', 'txt']);
    }

    public function render()
    {
        return view('livewire.component.file-preview');
    }
}
