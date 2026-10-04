<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\View\View;

class MessageController extends Controller
{
    public function index(): View
    {
        $messages = Message::with('recipient.company')->latest()->paginate(30);
        return view('messages.index', compact('messages'));
    }
}
