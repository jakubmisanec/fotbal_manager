<?php

namespace App\Models\SoccerDecoder;

abstract class FieldZone {
    const GoalA = 1;
    const FieldA = 2;
    const MidField = 3;
    const FieldB = 4;
    const GoalB = 5;
}

class MatchTeam {
    public string $name;
    public float $goalkeeperSkill;
    public float $defensiveSkill;
    public float $midfieldSkill;
    public float $offensiveSkill;
    public float $athleticDecay;
    
    public function __construct($name, $goalkeeperSkill, $midfieldSkill, $offensiveSkill) {
        $this->name = $name;
        $this->goalkeeperSkill = $goalkeeperSkill;
        $this->midfieldSkill = $midfieldSkill;
        $this->offensiveSkill = $offensiveSkill;
        $this->defensiveSkill = 0;
        $this->athleticDecay = 0;
    }
}

class MatchType {
    public MatchTeam $teamA, $teamB;
    public MatchTeam $attackingTeam, $defendingTeam;
    public int $currentFieldZone; //FieldZone enum
    public int $scoreA, $scoreB;
}

class Match {
    
    const MATCH_ACTIONS = 100;
    
    private Team $teamA, $teamB;
    public MatchType $match;
    
    public function __construct(Team $teamA, Team $teamB) {
        $this->teamA = $teamA;
        $this->teamB = $teamB;
        
        $match = new MatchType();
        
        $match->teamA = new MatchTeam($this->teamA->name, 0, 0, 0, 0);
        $match->teamB = new MatchTeam($this->teamB->name, 0, 0, 0, 0);
        
        $match->attackingTeam = $match->teamA;
        $match->defendingTeam = $match->teamB;
        
        $match->currentFieldZone = FieldZone::MidField;
        
        $match->scoreA = $match->scoreB = 0;
        $match->teamA->goalkeeperSkill = $this->teamA->goalkeeper->skill;
        $match->teamB->goalkeeperSkill = $this->teamB->goalkeeper->skill;
        
        for($i = 0; $i < 10; $i++) {
            $matchTeams = [ $match->teamA, $match->teamB ];
            $teams = [ $this->teamA, $this->teamB ];
            
            for ($j = 0; $j < 2; $j++) {
                $matchTeams[$j]->athleticDecay += $teams[$j]->players[$i]->athleticDecay;
                
                switch($teams[$j]->players[$i]->position) {
                    case PlayerPosition::GoalKeeper:
                        break;
                    case PlayerPosition::Defender:
                        $matchTeams[$j]->defensiveSkill += $teams[$j]->players[$i]->skill + $this->calculateAdditionalSkill($teams[$j]->players[$i]->experience, $teams[$j]->trainerSkill);
                        break;
                    case PlayerPosition::Midfielder:
                        $matchTeams[$j]->defensiveSkill += 0.5 * $teams[$j]->players[$i]->skill + ($this->calculateAdditionalSkill($teams[$j]->players[$i]->experience, $teams[$j]->trainerSkill)/0.5);
                        $matchTeams[$j]->midfieldSkill += $teams[$j]->players[$i]->skill + $this->calculateAdditionalSkill($teams[$j]->players[$i]->experience, $teams[$j]->trainerSkill);
                        $matchTeams[$j]->offensiveSkill += 0.5 * $teams[$j]->players[$i]->skill + ($this->calculateAdditionalSkill($teams[$j]->players[$i]->experience, $teams[$j]->trainerSkill)/0.5);
                        break;
                    case PlayerPosition::Striker:
                        $matchTeams[$j]->offensiveSkill += $teams[$j]->players[$i]->skill + $this->calculateAdditionalSkill($teams[$j]->players[$i]->experience, $teams[$j]->trainerSkill);
                        break;
                    default:
                        throw new Exception("Not a valid position!");
                }
            }
            
            
        }
        
        $match->teamA->athleticDecay /= 10; //AVG 10 hráčů
        $match->teamB->athleticDecay /= 10; //AVG 10 hráčů
        
   
        
        $this->match = $match;
    }
    
    /**
     * Protože algoritmus nepočítal s levelem hráče a trenéra, dovolil jsem si napsat vlastní.
     */
    private function calculateAdditionalSkill(int $playerLevel, int $trainerLevel): float {
        return (sqrt($playerLevel + 1) * log($trainerLevel, 1.5))/5.0;
    }
    
    public function simulate() {
        
        for ($i = 0; $i < self::MATCH_ACTIONS; $i++) {
            $winner = $this->matchBattle();
            
            if ($winner == $this->match->attackingTeam) {
                if ($this->match->attackingTeam == $this->match->teamA) {
                    if ($this->match->currentFieldZone == FieldZone::GoalB) {
                        $this->match->scoreA++;
                        $this->match->currentFieldZone = FieldZone::MidField;
                        $this->changeBall();
                    } else {
                        $this->match->currentFieldZone++;
                    }
                } else {
                    if ($this->match->currentFieldZone == FieldZone::GoalA) {
                        $this->match->scoreB++;
                        $this->match->currentFieldZone = FieldZone::MidField;
                        $this->changeBall();
                    } else {
                        $this->match->currentFieldZone--;
                    }
                }
            } else {
                $this->match->currentFieldZone = FieldZone::MidField;
                $this->changeBall();
            }
        }
    }
    
    private function matchBattle(): MatchTeam {
        $teamAttackSkill = 0;
        $teamDefensiveSkill = 0;
        
        switch($this->match->currentFieldZone) {
            case FieldZone::FieldA:
            case FieldZone::FieldB:
                $teamAttackSkill = $this->match->attackingTeam->offensiveSkill;
                $teamDefensiveSkill = $this->match->defendingTeam->defensiveSkill;
                break;
            case FieldZone::GoalA:
            case FieldZone::GoalB:
                $teamAttackSkill = $this->match->attackingTeam->offensiveSkill;
                $teamDefensiveSkill = $this->match->defendingTeam->goalkeeperSkill;
            case FieldZone::MidField:
                $teamAttackSkill = $this->match->attackingTeam->midfieldSkill;
                $teamDefensiveSkill = $this->match->defendingTeam->midfieldSkill;
                break;
        }
        
        $randomAttack = Distributions::randomGamma($teamAttackSkill, 0.5);
        $randomDefensive = Distributions::randomGamma($teamDefensiveSkill, 0.5);
        
        $this->match->attackingTeam->offensiveSkill -= ($this->match->attackingTeam->offensiveSkill) * ($this->match->attackingTeam->athleticDecay - Distributions::randomGamma(1, 50));
        $this->match->attackingTeam->defensiveSkill -= ($this->match->attackingTeam->defensiveSkill) * ($this->match->attackingTeam->athleticDecay - Distributions::randomGamma(1, 50));
        
        
        $this->match->defendingTeam->offensiveSkill -= ($this->match->defendingTeam->offensiveSkill) * ($this->match->defendingTeam->athleticDecay - Distributions::randomGamma(1, 50));
        $this->match->defendingTeam->defensiveSkill -= ($this->match->defendingTeam->defensiveSkill) * ($this->match->defendingTeam->athleticDecay - Distributions::randomGamma(1, 50));
        
        
        if ($randomAttack > $randomDefensive) {
            return $this->match->attackingTeam;
        } else {
            return $this->match->defendingTeam;
        }
            
        
    }
    
    private function changeBall() {
        $this->match->attackingTeam = $this->match->attackingTeam == $this->match->teamA ? $this->match->teamB : $this->match->teamA;
        $this->match->defendingTeam = $this->match->defendingTeam == $this->match->teamA ? $this->match->teamB : $this->match->teamA;
    }
    
    
}