<?php
namespace App\Enums;

enum UploadFileStatus:string
{
    case Uploading = 'uploading';         // در حال ارسال پارت‌ها از کلاینت
    case Processing = 'processing';               // در حال چسباندن، اسکن امنیتی و انتقال به S3
    case Ready = 'ready';   // بایگانی موفق و آماده دانلود
    case Failed = 'failed';       // شکست در پایپ‌لاین پردازش
}
