<?php

//
//Epson ESC/P printer command codes:
//
//ESC@  : Initialize printer
//ESC2  : Select 1/6 inch line spacing
//ESCx1 : Set NLQ (near letter quality) to ON
//ESCk1 : Select Typeface k0=Roman k1= Sans serif 
//ESCP  : Select 10CPI (ESCM = 12CPI )
//ESCC11 : Set page length in inches (here 11 inch)
//ESCN1 : Set bottom margin to 1 line
//

$initialize_string="@2x1k1P";
$empty_line="\n\n";
$begin_of_line="Px1k1";
$end_of_line="\n\n";
$form_feed="\n\n\n\n\n\n\n\n";
$number_of_characters_per_line=48;
$number_of_lines_per_page=33;  // TOES will send a form_feed string after printing this number of lines - these are "printable" lines)
$number_of_printable_lines_per_page=31;
$width_column_1 = 17;
$width_column_2 = 15;
$width_column_3 = 15;
$leftmarginforlocation = "      ";
$club_name_max_characters = 25;

$replace_texts = array(
    array( 'search'=>'&WHITE','replace'=>'&WH'),
    array( 'search'=>'CHOCOLATE','replace'=>'CHOC'),
    array( 'search'=>'CINNAMON','replace'=>'CIN'),
    array( 'search'=>'SEPIA','replace'=>'SEP'),
    array( 'search'=>'MACKEREL','replace'=>'MC'),
    array( 'search'=>'CLASSIC','replace'=>'CL'),
    array( 'search'=>'SPOTTED','replace'=>'SP'),
    array( 'search'=>'MARBLED','replace'=>'MRB'),
    array( 'search'=>'TICKED','replace'=>'TIC'),
    array( 'search'=>'TABBY','replace'=>'TB')
);

?>