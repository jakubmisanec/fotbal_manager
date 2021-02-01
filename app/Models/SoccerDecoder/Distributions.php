<?php

namespace App\Models\SoccerDecoder;
    
class Distributions {
    
    // Returns 1 with probability p and 0 with probability (1 - p)
    public static function randomBernoulli(float $p): bool {
        
        $u = self::randomUniform(0.0, 1.0);

        if ($p == 0)
            return false;
        else if ($p == 1)
            return true;
        else
            return $u < $p;
        
    }
    
    // Returns a random float sampled from a beta distribution with shape
    // (alpha, beta)
    public static function randomBeta(float $alpha, float $beta): float {

        $x = self::randomGamma($alpha, 1);
        $y = self::randomGamma($beta, 1);

        return $x / ($x + $y);
    }
    
    // Returns a random integer sampled from a binomial distribution with n trials
    // and per-trial success probability p
    public static function randomBinomial(int $n, float $p): int {

        $successes = 0;

        for ($i = 0; $i < $n; $i++) {
            $successes += self::randomBernoulli($p);
        }

        return $successes;
    }
    
    // Returns a random float sampled from an exponential distribution with given
    // rate lambda.
    public static function randomExponential(float $lambda): float {
        // Inverse transform sampling

        $u = self::randomUniform(0.0, 1.0);
        return -log($u) / $lambda;
    }
    
    // Returns a random float sampled from a gamma distribution with shape alpha and
    // rate beta
    public static function randomGamma(float $alpha, float $beta): float {

        // Use the Marsaglia and Tsang (2000) method to generate Z ~ Gamma(alpha, 1)

        $d = $alpha - 1.0 / 3.0;
        $c = 1.0 / sqrt(9.0 * $d);

        $z = 0.0;

        for (;;) {
            $x = 0.0;
            $v = 0.0;

            do {
                $x = self::randomNormal(0, 1);
                $v = 1.0 + $c * $x;
            } while ($v <= 0);

            $v = pow($v, 3.0);
            $u = self::randomUniform(0.0, 1.0);

            if ($u < 1.0 - 0.0331 * pow($x, 4.0)) {
                $z = $d * $v;
                break;
            }

            if (log($u) < 0.5 * pow($x, 2.0) + $d * (1 - $v + log($v))) {
                $z = $d * $v;
                break;
            }
        }

        // Scale to X ~ Gamma(alpha, beta)

        return $z / $beta;
    }
    
    // Returns a random float sampled from a normal dsitribution with given mean and
    // standard deviation
    public static function randomNormal(float $mu, float $sigma): float {

        // Box-Muller transformation from uniform to normal distribution

        $u1 = self::randomUniform(0.0, 1.0);
        $u2 = self::randomUniform(0.0, 1.0);

        $z0 = sqrt(-2 * log($u1)) * sin(2 * pi() * $u2); 
        return $mu + $z0 * $sigma;
    }
    
    // Returns a random integer sampled from the Poisson distribution with rate
    // lambda
    public static function randomPoisson(float $lambda): int {
        // Use Knuth's algorithm to generate the Poisson value

        $L = exp(-$lambda);
        $k = 0;
        $p = 1.0;

        do {
            $k++;
            $p *= randomUniform(0.0, 1.0);
        } while($p > $L);

        return $k - 1;
    }
    
    // Returns a random float sampled from a uniform distribution within the range
    // [a, b]
    public static function randomUniform(float $a, float $b): float {
        assert($a < $b);

        $u = (float) rand() / (float) getrandmax();
        return $a + $u * ($b - $a);
    }
    
}    