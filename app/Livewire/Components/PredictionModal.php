<?php

namespace App\Livewire\Components;

use Livewire\Component;
use App\Models\Prediction;
use Illuminate\Support\Facades\Auth;
use App\Models\FootballMatch;

class PredictionModal extends Component
{
    public $isOpen = false;
    public $matchId;
    public $homeScore = null;
    public $awayScore = null;
    public $match;
    public $isReadonly = false;

    protected function rules()
    {
        return [
            'homeScore' => 'required|integer|min:0|max:20',
            'awayScore' => 'required|integer|min:0|max:20',
        ];
    }

    protected $messages = [
        'homeScore.required' => 'Попълни резултата за домакина.',
        'awayScore.required' => 'Попълни резултата за госта.',
    ];

    protected $listeners = [
        'open-prediction-modal' => 'openPredictionModal',
    ];

    public function openPredictionModal($matchId, $readonly = false)
    {
        $this->resetValidation();

        $this->matchId = $matchId;
        $this->match = FootballMatch::with(['homeTeam', 'awayTeam'])->findOrFail($matchId);
        $this->isOpen = true;

        $prediction = Prediction::where('user_id', Auth::id())
            ->where('football_match_id', $matchId)
            ->first();

        if ($prediction) {
            $this->homeScore = $prediction->home_score_prediction;
            $this->awayScore = $prediction->away_score_prediction;
        } else {
            $this->homeScore = null;
            $this->awayScore = null;
        }

        $this->isReadonly = $readonly || (bool) $prediction;
    }

    public function save()
    {
        if ($this->isReadonly) {
            return;
        }

        $this->validate();

        $existingPrediction = Prediction::where('user_id', Auth::id())
            ->where('football_match_id', $this->matchId)
            ->first();

        if ($existingPrediction) {
            $this->addError('empty', 'Вече си дал прогноза за този мач.');
            $this->isReadonly = true;
            return;
        }

        Prediction::create([
            'user_id' => Auth::id(),
            'football_match_id' => $this->matchId,
            'home_score_prediction' => (int) $this->homeScore,
            'away_score_prediction' => (int) $this->awayScore,
        ]);

        $this->isReadonly = true;

        $this->dispatch('prediction-saved', matchId: $this->matchId);
        session()->flash('success', 'Прогнозата е записана успешно!');
    }

    public function render()
    {
        return view('livewire.prediction-modal');
    }
}
