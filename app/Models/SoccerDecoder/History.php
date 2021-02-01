<?php

namespace App\Models\SoccerDecoder;

class HistoryScore {
    public string $score;
    
    public function __construct($score) {
        $this->score = $score;
    }
}

class HistoryType {
    public int $draws;
    public array $goals;
    public array $wons;
    public array $scores; //HistoryScore
}

class History {
    
    private HistoryType $history;
    
    public function __construct() {
        $this->history = new HistoryType();
        
        $this->history->wons = [0,0];
        $this->history->goals = [0,0];
        $this->history->scores = [];
        $this->history->draws = 0;
    }
    
    public function addMatch(MatchType $match) {
        if ($match->scoreA > $match->scoreB) {
            $this->history->wons[0]++;
        } else if ($match->scoreA < $match->scoreB) {
            $this->history->wons[1]++;
        } else
            $this->history->draws++;
        
        $this->history->goals[0] += $match->scoreA;
        $this->history->goals[1] += $match->scoreB;
        $matchScore = "$match->scoreA : $match->scoreB";
        
        $this->history->scores[count($this->history->scores)] = new HistoryScore($matchScore);
    }
    
    public function printHistory() {
        echo "team a wons: " . $this->history->wons[0] . " <br>";
        echo "team b wons: " . $this->history->wons[1] . " <br>";
        echo "draws: " . $this->history->draws . " <br>";
        
        echo "team a goals: " . $this->history->goals[0] . " <br>";
        echo "team b goals: " . $this->history->goals[1] . " <br>";
        
        echo "scores: " . $this->history->goals[0] . " <br>";
        echo "<ul>";
        for($i = 0; $i < count($this->history->scores); $i++) {
            echo "<li>" . $this->history->scores[$i]->score . "</li>";
        }
        echo "</ul>";
    }
    
}