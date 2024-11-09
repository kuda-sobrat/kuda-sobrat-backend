<?php

namespace App\Services;

use App\Models\ContextPost;

class ImageTextExtractionService
{
    public function extractTextFromImages(ContextPost $post)
    {
//        foreach ($post->attachments as $attachment) {
//            if ($attachment->attachment_type === 'photo') {
//                $text = $this->extractText($attachment->url);
//                $attachment->description = $text;
//                $attachment->save();
//            }
//        }
//        // Обновление статуса поста
//        $post->status = 'ocr_processed';
//        $post->save();
    }

    protected function extractText($imagePath)
    {
//        $text = (new TesseractOCR($imagePath))->run();
//        return $text;
    }
}
