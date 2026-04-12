<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Lesson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function store(Request $request, Lesson $lesson)
    {
        $request->validate([
            'content'   => 'required|string|max:1000',
            'parent_id' => 'nullable|exists:comments,id'
        ]);

        $lesson->comments()->create([
            'user_id'   => Auth::id(),
            'parent_id' => $request->parent_id,
            'content'   => $request->content,
        ]);

        return back()->with('success', 'Comentário enviado!');
    }

    public function destroy(Comment $comment)
    {
        if ($comment->user_id !== Auth::id() && !Auth::user()->is_admin) {
            abort(403);
        }

        $comment->delete();

        return back()->with('success', 'Comentário removido.');
    }
}
