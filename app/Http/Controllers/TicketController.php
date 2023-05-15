<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTicketReplyRequest;
use App\Http\Requests\UpdateTicketStatusRequest;
use App\Models\Reply;
use App\Models\Ticket;
use App\Http\Requests\StoreTicketRequest;
use App\Http\Requests\UpdateTicketRequest;
use App\Models\User;
use App\Notifications\TicketUpdatedNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user    = auth()->user();
        $tickets = $user->isAdmin ? Ticket::latest()->get() : $user->tickets;
        return view('ticket.index', compact('tickets'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('ticket.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTicketRequest $request)
    {
        $ticket = Ticket::create([
            'title' => $request->title,
            'description' => $request->description,
            'user_id' => auth()->id()
        ]);

        if ($request->file('attachment')) {
            $this->storeAttachment($request, $ticket);
        }

        return redirect(route('ticket.index'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Ticket $ticket)
    {
        return view('ticket.show', compact('ticket'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Ticket $ticket)
    {
        return view('ticket.edit', compact('ticket'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTicketRequest $request, Ticket $ticket)
    {

        $ticket->update([
            'title' => $request->title,
            'description' => $request->description
        ]);

        if ($request->file('attachment')) {
            Storage::disk('public')->delete($ticket->attachment);
            $this->storeAttachment($request, $ticket);
        }

        return redirect(route('ticket.index'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function updateStatus(UpdateTicketStatusRequest $request, Ticket $ticket) {
        $updateTicket = $ticket->update(['status' => $request->status]);
        if ($updateTicket) {
            $user = User::find($ticket->user_id);
            $user->notify(new TicketUpdatedNotification($ticket));
//            return (new TicketUpdatedNotification($ticket))->toMail($user);
        }
        return redirect(route('ticket.index'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ticket $ticket)
    {
        $ticket->delete();
        return redirect(route('ticket.index'));
    }

    public function replyTicket(StoreTicketReplyRequest $request, Ticket $ticket)
    {
        $reply = Reply::create([
            'body' => $request->body,
            'user_id' => Auth::id(),
            'ticket_id' => $ticket->id
        ]);

        return back();
    }

    protected function storeAttachment($request, $ticket) {
        $ext = $request->file('attachment')-> extension();
        $content = file_get_contents($request->file('attachment'));
        $filename = Str::random(25);
        $path = "attachments/$filename.$ext";
        Storage::disk('public')->put($path, $content);
        $ticket->update(['attachment' => $path]);
    }
}
