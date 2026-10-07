<?php

declare(strict_types=1);

return [

    /*
     * Human readable names for each score level.
     */
    'levels' => [
        'terrible' => 'Terrible',
        'bad' => 'Bad',
        'normal' => 'Normal',
        'good' => 'Good',
        'excellent' => 'Excellent',
    ],

    /*
     * Two luck texts for every stat and score level. The reader picks one of
     * the two deterministically, so consumers can translate or replace them.
     */
    'texts' => [

        'love' => [
            'terrible' => [
                'Love will feel distant and cold today.',
                'Keep your heart guarded; romance brings only heartache today.',
            ],
            'bad' => [
                'You will not meet your destined today.',
                'A misunderstanding may cloud your closest bond today.',
            ],
            'normal' => [
                'Love moves quietly, with no great highs or lows.',
                'A steady day for affection; enjoy the calm.',
            ],
            'good' => [
                'A warm conversation will brighten your romantic life.',
                'Someone special notices you more than you think today.',
            ],
            'excellent' => [
                'Love surrounds you; your destined one may be near.',
                'Today your heart opens and love answers gladly.',
            ],
        ],

        'career' => [
            'terrible' => [
                'Work will pile up and offers will fall through today.',
                'Avoid big career moves; the signs are against you.',
            ],
            'bad' => [
                'A colleague may test your patience at work today.',
                'Progress at work will feel slow and thankless today.',
            ],
            'normal' => [
                'A routine day at work; steady effort pays quietly.',
                'Your career holds its course without surprises today.',
            ],
            'good' => [
                'A superior will notice your effort today.',
                'An opportunity at work deserves your attention today.',
            ],
            'excellent' => [
                'A career breakthrough is within reach today.',
                'Your ambition shines and doors open at work today.',
            ],
        ],

        'money' => [
            'terrible' => [
                'Hold your wallet tight; unexpected costs loom today.',
                'A financial setback may catch you off guard today.',
            ],
            'bad' => [
                'Spending will outpace earning more easily today.',
                'A risky purchase is best postponed today.',
            ],
            'normal' => [
                'Money flows in and out in equal measure today.',
                'A balanced day for your finances; nothing dramatic.',
            ],
            'good' => [
                'A small but welcome sum may reach you today.',
                'Your finances steady and a bargain finds you today.',
            ],
            'excellent' => [
                'Abundance follows you; money finds its way to you today.',
                'A profitable opportunity is yours to claim today.',
            ],
        ],

        'health' => [
            'terrible' => [
                'Your body asks for rest; do not ignore its signals today.',
                'Low energy will shadow you; slow down today.',
            ],
            'bad' => [
                'Minor aches may distract you; pace yourself today.',
                'Stress weighs on you; guard your health today.',
            ],
            'normal' => [
                'Your health holds steady; keep your usual habits.',
                'A calm day for the body, with nothing out of order.',
            ],
            'good' => [
                'Vitality returns and your mood lifts today.',
                'Your body rewards you with steady energy today.',
            ],
            'excellent' => [
                'You radiate health and boundless energy today.',
                'Wellbeing fills you; make the most of this strength.',
            ],
        ],

        'social' => [
            'terrible' => [
                'Plans with friends may fall apart today.',
                'Solitude suits you better than company today.',
            ],
            'bad' => [
                'A friend may misunderstand your words today.',
                'Social friction is likely; choose your words with care.',
            ],
            'normal' => [
                'Company comes and goes without much fuss today.',
                'A quiet, even day for your social circle.',
            ],
            'good' => [
                'A friendly gesture warms your day unexpectedly.',
                'Good company and easy laughter find you today.',
            ],
            'excellent' => [
                'You are the heart of every gathering today.',
                'New bonds form and old ones deepen today.',
            ],
        ],

        'overall' => [
            'terrible' => [
                'The stars counsel caution; keep today simple.',
                'A heavy day all around; be gentle with yourself.',
            ],
            'bad' => [
                'The day resists you; pick your battles wisely.',
                'Setbacks gather, but they will pass; stay patient.',
            ],
            'normal' => [
                'An ordinary day, balanced and without drama.',
                'The day moves evenly; take it as it comes.',
            ],
            'good' => [
                'The stars smile on you; move with confidence today.',
                'A bright day favors your plans and hopes.',
            ],
            'excellent' => [
                'Fortune shines on every path you walk today.',
                'The universe conspires in your favor today.',
            ],
        ],

    ],

];
