<?php

return array_replace_recursive(require __DIR__.'/vendor/nesbot/carbon/src/Carbon/Lang/pt.php', [
    'period_recurrences' => 'uma|:count vez',
    'period_interval' => 'toda :interval',
    'formats' => [
        'LLL' => 'D [de] MMMM [de] YYYY [às] HH:mm',
        'LLLL' => 'dddd, D [de] MMMM [de] YYYY [às] HH:mm',
    ],
    'first_day_of_week' => 1,
    'day_of_first_week_of_year' => 1,
]);
