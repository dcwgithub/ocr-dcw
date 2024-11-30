<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use thiagoalessio\TesseractOCR\TesseractOCR;

class OcrController extends Controller
{

    // 显示上传表单
    public function showUploadForm()
    {
        return view('ocr.upload');
    }

    public function ocr(Request $request)
    {
        // 检查是否有文件上传
        if (!$request->hasFile('image') || !$request->file('image')->isValid()) {
            return response()->json(['error' => '没有上传有效的图片文件'], 400);
        }

        // 获取上传的图片文件
        $image = $request->file('image');
        $lang = $request->input('lang', 'chi_sim');  // 默认为简体中文

        // 临时保存文件
        $imagePath = $image->storeAs('ocr_images', 'image.png');

        // 使用 Tesseract 进行 OCR 识别
        try {
            $ocr = new TesseractOCR(storage_path('app/'.$imagePath));
            $ocr->lang($lang);
            $text = $ocr->run();

            return response()->json(['text' => $text]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'OCR 识别失败: '.$e->getMessage()], 500);
        }
    }
}
