<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function userComments()
    {
        $title = "لیست نظرات";
        return view('admin.comments.list', compact('title'));
    }
}
