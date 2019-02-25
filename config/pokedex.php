<?php

return [

    /*
    * If set to true the table seeder will strip newlines out of the description.
    */
    'strip_description' => env('POKEDEX_STRIP_DESCRIPTION', true),

    /*
    * Sets the amount of pokemon listed per page.
    */
    'paginate_size' => env('POKEDEX_PAGINATE_SIZE', 10),

];
