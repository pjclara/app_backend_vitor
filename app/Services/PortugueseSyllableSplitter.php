<?php

namespace App\Services;

class PortugueseSyllableSplitter
{
    /**
     * Vogais portuguesas (incluindo acentuadas).
     */
    private const VOWELS = ['a', 'e', 'i', 'o', 'u',
        'á', 'é', 'í', 'ó', 'ú',
        'â', 'ê', 'ô',
        'ã', 'õ',
        'à', 'ü'];

    /**
     * Clusters de consoantes que não se separam (ficam juntos na mesma sílaba).
     */
    private const INSEPARABLE_CLUSTERS = [
        'bl', 'br', 'cl', 'cr', 'dl', 'dr',
        'fl', 'fr', 'gl', 'gr', 'pl', 'pr',
        'tl', 'tr', 'vr',
    ];

    /**
     * Dígrafos que formam um único som e não se separam.
     */
    private const DIGRAPHS = ['ch', 'lh', 'nh', 'gu', 'qu'];

    /**
     * Ditongos que não se separam.
     */
    private const DIPHTHONGS = [
        'ai', 'ei', 'oi', 'ui', 'au', 'eu', 'ou',
        'ão', 'ãe', 'õe',
        'ái', 'éi', 'ói', 'úi', 'áu', 'éu', 'óu',
    ];

    /**
     * Divide uma palavra portuguesa em sílabas.
     *
     * @param string $word
     * @return array<string>
     */
    public function split(string $word): array
    {
        $word = mb_strtolower(trim($word));

        if (mb_strlen($word) <= 1) {
            return [$word];
        }

        $chars = $this->mbStringToArray($word);
        $len = count($chars);

        // Marcar posições de vogais e consoantes
        $isVowel = [];
        for ($i = 0; $i < $len; $i++) {
            $isVowel[$i] = in_array($chars[$i], self::VOWELS);
        }

        // Encontrar os pontos de corte
        $breaks = [];
        $i = 0;

        while ($i < $len) {
            // Se estamos numa vogal, verificar ditongo
            if ($isVowel[$i]) {
                // Verificar ditongo
                if ($i + 1 < $len && $isVowel[$i + 1]) {
                    $pair = $chars[$i] . $chars[$i + 1];
                    if (in_array($pair, self::DIPHTHONGS)) {
                        // Ditongo - não separa, avança 2
                        $i += 2;
                        continue;
                    } else {
                        // Hiato - separa entre as duas vogais
                        $breaks[] = $i + 1;
                        $i += 1;
                        continue;
                    }
                }
                $i++;
                continue;
            }

            // Estamos numa consoante
            // Contar quantas consoantes consecutivas há
            $consonantStart = $i;
            $consonantEnd = $i;
            while ($consonantEnd < $len && !$isVowel[$consonantEnd]) {
                $consonantEnd++;
            }
            $numConsonants = $consonantEnd - $consonantStart;

            // Se não há vogal antes, as consoantes ficam com a vogal seguinte
            if ($consonantStart === 0) {
                $i = $consonantEnd;
                continue;
            }

            // Se não há vogal depois, as consoantes ficam com a vogal anterior
            if ($consonantEnd >= $len) {
                $i = $consonantEnd;
                continue;
            }

            if ($numConsonants === 1) {
                // Uma consoante entre vogais: vai para a sílaba seguinte (V-CV)
                $breaks[] = $consonantStart;
                $i = $consonantEnd;
            } elseif ($numConsonants === 2) {
                $pair = $chars[$consonantStart] . $chars[$consonantStart + 1];

                // Verificar dígrafo
                if (in_array($pair, self::DIGRAPHS)) {
                    // Dígrafo não se separa - vai todo para a sílaba seguinte
                    $breaks[] = $consonantStart;
                    $i = $consonantEnd;
                }
                // Verificar cluster inseparável
                elseif (in_array($pair, self::INSEPARABLE_CLUSTERS)) {
                    // Cluster não se separa - vai todo para a sílaba seguinte
                    $breaks[] = $consonantStart;
                    $i = $consonantEnd;
                } else {
                    // Separa entre as duas consoantes (VC-CV)
                    $breaks[] = $consonantStart + 1;
                    $i = $consonantEnd;
                }
            } elseif ($numConsonants >= 3) {
                // 3+ consoantes: verificar se as duas últimas formam cluster/dígrafo
                $lastTwo = $chars[$consonantEnd - 2] . $chars[$consonantEnd - 1];

                if (in_array($lastTwo, self::INSEPARABLE_CLUSTERS) || in_array($lastTwo, self::DIGRAPHS)) {
                    // Separar antes do cluster/dígrafo
                    $breaks[] = $consonantEnd - 2;
                } else {
                    // Separar após a primeira consoante
                    $breaks[] = $consonantStart + 1;
                }
                $i = $consonantEnd;
            } else {
                $i++;
            }
        }

        // Construir sílabas a partir dos pontos de corte
        if (empty($breaks)) {
            return [$word];
        }

        $breaks = array_unique($breaks);
        sort($breaks);

        $syllables = [];
        $prev = 0;
        foreach ($breaks as $breakPoint) {
            if ($breakPoint > $prev) {
                $syllable = implode('', array_slice($chars, $prev, $breakPoint - $prev));
                if ($syllable !== '') {
                    $syllables[] = $syllable;
                }
                $prev = $breakPoint;
            }
        }
        // Última sílaba
        $remaining = implode('', array_slice($chars, $prev));
        if ($remaining !== '') {
            $syllables[] = $remaining;
        }

        // Verificar que cada sílaba tem pelo menos uma vogal
        // Se não tem, juntar à sílaba adjacente
        $syllables = $this->ensureVowelsInSyllables($syllables);

        return $syllables;
    }

    /**
     * Garante que cada sílaba contém pelo menos uma vogal.
     * Sílabas sem vogal são unidas à adjacente.
     */
    private function ensureVowelsInSyllables(array $syllables): array
    {
        if (count($syllables) <= 1) {
            return $syllables;
        }

        $result = [];
        $buffer = '';

        foreach ($syllables as $i => $syllable) {
            $buffer .= $syllable;

            if ($this->hasVowel($buffer)) {
                $result[] = $buffer;
                $buffer = '';
            }
        }

        // Se sobrou buffer sem vogal, juntar à última sílaba
        if ($buffer !== '') {
            if (!empty($result)) {
                $result[count($result) - 1] .= $buffer;
            } else {
                $result[] = $buffer;
            }
        }

        return $result;
    }

    /**
     * Verifica se uma string contém pelo menos uma vogal.
     */
    private function hasVowel(string $str): bool
    {
        $chars = $this->mbStringToArray(mb_strtolower($str));
        foreach ($chars as $char) {
            if (in_array($char, self::VOWELS)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Converte uma string multibyte num array de caracteres.
     */
    private function mbStringToArray(string $str): array
    {
        $chars = [];
        $len = mb_strlen($str);
        for ($i = 0; $i < $len; $i++) {
            $chars[] = mb_substr($str, $i, 1);
        }
        return $chars;
    }
}
