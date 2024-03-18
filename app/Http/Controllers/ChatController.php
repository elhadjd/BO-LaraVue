<?php

namespace App\Http\Controllers;

use App\Events\ChatEvent;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
class ChatController extends Controller
{
    public function sendMessage(Request $request)
    {
        $message = Message::create([
            'content' => $request->message,
        ]);

        event(new ChatEvent($message));

        return response()->json(['success' => $message]);
    }
}
