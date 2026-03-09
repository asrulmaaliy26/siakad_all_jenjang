<?php

namespace App\Livewire;

use Livewire\Component;

class KrsAdvisorChat extends Component
{
    public $dosenId;
    public $message;
    public $isAdminMode = false;
    public $limit = 20;

    protected $rules = [
        'message' => 'required|string|max:1000',
    ];

    public function mount($dosenId)
    {
        if ($dosenId === 'admin_select') {
            $this->isAdminMode = true;
            $this->dosenId = null;
        } else {
            $this->dosenId = $dosenId;
        }
    }

    public function loadMore()
    {
        $this->limit += 20;
    }

    public function sendMessage()
    {
        $this->validate();

        \App\Models\KrsChat::create([
            'id_dosen' => $this->dosenId,
            'user_id' => \Illuminate\Support\Facades\Auth::id(),
            'message' => $this->message,
        ]);

        $this->message = '';
        $this->dispatch('message-sent');
    }

    public function render()
    {
        $messages = \App\Models\KrsChat::where('id_dosen', $this->dosenId)
            ->with(['user.dosenData', 'user.siswaData'])
            ->orderBy('created_at', 'desc') // Fetch newest first for limit
            ->limit($this->limit)
            ->get()
            ->reverse(); // Reverse back to chronological for display

        return view('livewire.krs-advisor-chat', [
            'messages' => $messages,
        ]);
    }
}
