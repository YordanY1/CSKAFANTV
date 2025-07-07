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


    protected function rules()
    {
        return [
            'homeScore' => 'nullable|integer|min:0|max:20',
            'awayScore' => 'nullable|integer|min:0|max:20',
        ];
    }

    protected $listeners = [
        'open-prediction-modal' => 'openPredictionModal',
    ];

    public function openPredictionModal($matchId)
    {
        $this->resetValidation();
        $this->matchId = $matchId;
        $this->match = FootballMatch::with(['homeTeam', 'awayTeam'])->findOrFail($matchId);
        $this->isOpen = true;
    }

    public function save()
    {
        $this->validate();

        if (!is_numeric($this->homeScore) || !is_numeric($this->awayScore)) {
            $this->addError('empty', 'Попълни резултат и за двата отбора.');
            return;
        }

        Prediction::create([
            'user_id' => Auth::id(),
            'football_match_id' => $this->matchId,
            'home_score_prediction' => $this->homeScore,
            'away_score_prediction' => $this->awayScore,
        ]);

        session()->flash('success', 'Прогнозата е записана успешно!');
    }


    public function render()
    {
        return view('livewire.prediction-modal');
    }
}
