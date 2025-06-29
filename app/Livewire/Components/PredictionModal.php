<?php

namespace App\Livewire\Components;

use Livewire\Component;
use App\Models\Prediction;
use Illuminate\Support\Facades\Auth;

class PredictionModal extends Component
{
    public $isOpen = false;
    public $matchId;
    public $homeScore = null;
    public $awayScore = null;
    public $resultSign = null;

    protected function rules()
    {
        return [
            'homeScore' => 'nullable|integer|min:0|max:20',
            'awayScore' => 'nullable|integer|min:0|max:20',
            'resultSign' => 'nullable|in:1,X,2',
        ];
    }

    protected $listeners = [
        'open-prediction-modal' => 'openPredictionModal',
    ];

    public function openPredictionModal($matchId)
    {
        $this->resetValidation();
        $this->matchId = $matchId;
        $this->isOpen = true;
    }

    public function save()
    {
        $this->validate();

        if (
            is_null($this->homeScore)
            && is_null($this->awayScore)
            && empty($this->resultSign)
        ) {
            $this->addError('empty', 'Трябва да въведеш поне знак или резултат.');
            return;
        }

        Prediction::create([
            'user_id' => Auth::id(),
            'football_match_id' => $this->matchId,
            'home_score_prediction' => $this->homeScore,
            'away_score_prediction' => $this->awayScore,
            'result_sign_prediction' => $this->resultSign ?: null,
        ]);

        session()->flash('success', 'Прогнозата е записана успешно!');
    }

    public function render()
    {
        return view('livewire.prediction-modal');
    }
}
