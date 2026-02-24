<?php

namespace App\Services;

class PortugueseSyllableService
{
    /**
     * Vogais portuguesas (incluindo acentuadas).
     */
    private const VOWELS = 'aeiouáéíóúâêôãõàüAEIOUÁÉÍÓÚÂÊÔÃÕÀÜ';

    /**
     * Clusters consonantais inseparáveis (início de sílaba).
     */
    private const INSEPARABLE_CLUSTERS = [
        'bl',
        'br',
        'cl',
        'cr',
        'dr',
        'fl',
        'fr',
        'gl',
        'gr',
        'pl',
        'pr',
        'tl',
        'tr',
        'vr',
    ];

    /**
     * Dígrafos inseparáveis.
     */
    private const DIGRAPHS = ['ch', 'lh', 'nh', 'qu', 'gu'];

    /**
     * Ditongos (ficam juntos na mesma sílaba).
     */
    private const DIPHTHONGS = [
        'ai',
        'au',
        'ei',
        'eu',
        'iu',
        'oi',
        'ou',
        'ui',
        'ãe',
        'ão',
        'õe',
        'ãi',
        'ui',
        'ai',
        'au',
        'ei',
        'eu',
        'iu',
        'oi',
        'ou',
        'ui',
    ];

    /**
     * Divide uma palavra em sílabas.
     *
     * @return string[]
     */
    public static function syllabify(string $word): array
    {
        $word = mb_strtolower(trim($word));

        if (mb_strlen($word) <= 1) {
            return [$word];
        }

        $chars = self::mbStringToArray($word);
        $len = count($chars);
        $syllables = [];
        $current = '';

        $i = 0;
        while ($i < $len) {
            $char = $chars[$i];

            if (self::isVowel($char)) {
                $current .= $char;

                // Verificar ditongo
                if ($i + 1 < $len && self::isVowel($chars[$i + 1])) {
                    $pair = $char . $chars[$i + 1];
                    if (self::isDiphthong($pair)) {
                        $current .= $chars[$i + 1];
                        $i++;
                    }
                }

                // Verificar o que vem depois da vogal/ditongo
                $consonantsAhead = self::countConsonantsAhead($chars, $i + 1);

                if ($consonantsAhead === 0) {
                    // Próximo é vogal ou fim da palavra
                    // Se próximo é vogal (hiato), corta aqui
                    if ($i + 1 < $len && self::isVowel($chars[$i + 1])) {
                        $syllables[] = $current;
                        $current = '';
                    }
                } elseif ($consonantsAhead === 1) {
                    // Uma consoante entre vogais: V-CV
                    $syllables[] = $current;
                    $current = '';
                } elseif ($consonantsAhead >= 2) {
                    $c1 = $chars[$i + 1];
                    $c2 = $chars[$i + 2] ?? '';
                    $cluster = $c1 . $c2;

                    if (self::isInseparableCluster($cluster) || self::isDigraph($cluster)) {
                        // Cluster inseparável: V-CCV
                        $syllables[] = $current;
                        $current = '';
                    } else {
                        // Duas consoantes separáveis: VC-CV
                        $current .= $c1;
                        $syllables[] = $current;
                        $current = '';
                        $i++;
                    }
                }
            } else {
                // Consoante
                $current .= $char;
            }

            $i++;
        }

        if ($current !== '') {
            // Se a última parte não tem vogal e existem sílabas anteriores,
            // juntar à última sílaba
            if (!empty($syllables) && !self::containsVowel($current)) {
                $syllables[count($syllables) - 1] .= $current;
            } else {
                $syllables[] = $current;
            }
        }

        // Limpar sílabas vazias
        $syllables = array_values(array_filter($syllables, fn($s) => $s !== ''));

        // Garantir que cada sílaba tem pelo menos uma vogal
        // (juntar sílabas sem vogal à anterior ou próxima)
        return self::fixSyllablesWithoutVowels($syllables);
    }

    /**
     * Divide uma palavra e retorna as sílabas separadas por hífen.
     */
    public static function syllabifyToString(string $word): string
    {
        return implode('-', self::syllabify($word));
    }

    private static function isVowel(string $char): bool
    {
        return mb_strpos(self::VOWELS, $char) !== false;
    }
    private static function containsVowel(string $str): bool
    {
        for ($i = 0; $i < mb_strlen($str); $i++) {
            if (self::isVowel(mb_substr($str, $i, 1))) {
                return true;
            }
        }
        return false;
    }

    private static function isDiphthong(string $pair): bool
    {
        return in_array(mb_strtolower($pair), self::DIPHTHONGS);
    }

    private static function isInseparableCluster(string $pair): bool
    {
        return in_array(mb_strtolower($pair), self::INSEPARABLE_CLUSTERS);
    }

    private static function isDigraph(string $pair): bool
    {
        return in_array(mb_strtolower($pair), self::DIGRAPHS);
    }

    private static function countConsonantsAhead(array $chars, int $from): int
    {
        $count = 0;
        for ($i = $from; $i < count($chars); $i++) {
            if (!self::isVowel($chars[$i])) {
                $count++;
            } else {
                break;
            }
        }
        return $count;
    }

    /**
     * Corrige sílabas que ficaram sem vogal, juntando-as à sílaba adjacente.
     */
    private static function fixSyllablesWithoutVowels(array $syllables): array
    {
        if (count($syllables) <= 1) {
            return $syllables;
        }

        $result = [];
        $buffer = '';

        foreach ($syllables as $i => $syl) {
            $buffer .= $syl;

            if (self::containsVowel($buffer)) {
                $result[] = $buffer;
                $buffer = '';
            }
        }

        // Se sobrou buffer sem vogal, juntar à última
        if ($buffer !== '' && !empty($result)) {
            $result[count($result) - 1] .= $buffer;
        } elseif ($buffer !== '') {
            $result[] = $buffer;
        }

        return $result;
    }

    /**
     * Converte string multibyte em array de caracteres.
     */
    private static function mbStringToArray(string $string): array
    {
        $chars = [];
        $len = mb_strlen($string);
        for ($i = 0; $i < $len; $i++) {
            $chars[] = mb_substr($string, $i, 1);
        }
        return $chars;
    }
}
