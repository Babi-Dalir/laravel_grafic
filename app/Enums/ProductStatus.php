<?php
namespace App\Enums;

enum ProductStatus: string
{
    case Incomplete = 'incomplete'; //اطلاعات ناقص
    case PendingReview = 'pending_review'; //ارسال شده برای بررسی
    case Approved = 'approved'; //تایید شده
    case Draft = 'draft'; //محصول ساخته شده
    case Rejected = 'rejected'; //رد شده
    case Archived = 'archived'; //غیر فعال
}

