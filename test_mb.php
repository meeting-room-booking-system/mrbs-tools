<?php
declare(strict_types=1);

// Program for testing the mbstring function emulations.  Run it in the MRBS directory on systems with
// and without the mbstring extension enabled.  The results should be the same.

error_reporting(-1);
ini_set('display_errors', '1');
ini_set('max_execution_time', '5');

include 'defaultincludes.inc';

echo "PHP version: " . PHP_VERSION;
echo "<br>\n";
echo "mbstring enabled: " . var_export(in_array('mbstring', get_loaded_extensions()), true);
echo "<br>\n";
echo "intl enabled: " . var_export(in_array('intl', get_loaded_extensions()), true);
echo "<br>\n";
echo "<br>\n";

function test(string $function, string $haystack, string $needle, int $offset=0) : void
{
  echo "<tr>";
  echo "<td>$function</td><td>$haystack</td><td>$needle</td><td>$offset</td>";
  echo "<td>";
  echo var_export(call_user_func($function, $haystack, $needle, $offset), true);
  echo "</td>";
  echo "</tr>\n";
}


echo "<table>\n";
echo "<thead>\n";
echo "<tr>";
echo '<th>function</th><th>$haystack</th><th>$needle</th><th>$offset</th><th>result</th>';
echo "<tr>\n";
echo "</thead>\n";

echo "<tbody>\n";

// mb_strpos()
// -----------

test('mb_strpos', '0123456789a0123456789b0123456789c', 'c', 0);
test('mb_strpos', '0123456789a0123456789b0123456789c', 'd', 0);
test('mb_strpos', '0123456789a0123456789b0123456789c', 'c', -1);
test('mb_strpos', '0123456789a0123456789b0123456789c', 'c', -2);

test('mb_strpos', 'TRUE', 'E_', 0);

// Equivalence
test('mb_strpos', 'Jour précédent', 'e');
$old_locale = setlocale(LC_ALL, '0');
setlocale(LC_ALL, ['fr_FR', 'fr']);
test('mb_strpos', 'Jour précédent', 'e');
setlocale(LC_ALL, $old_locale);


// mb_stripos()
// -----------

test('mb_stripos', '0123456789a0123456789b0123456789c', 'c', -1);
test('mb_stripos', '0123456789a0123456789b0123456789c', 'C', -1);

// Multibyte
test('mb_stripos', '會C議室預約系統', 'C', 2);
test('mb_stripos', '會議C室預約系統', 'C', 2);
test('mb_stripos', '會議C室預約系統', '預約', 2);


// mb_strrpos()
// ------------

// Positive offsets, needle at start
test('mb_strrpos', '0123456789a0123456789b0123456789c', '0123456789a', 0);
test('mb_strrpos', '0123456789a0123456789b0123456789c', '0123456789a', 1);

// Positive offsets, needle partial match at end
test('mb_strrpos', '0123456789a0123456789b0123456789c', '89cd', 10);

// Positive offsets, needle longer than search area
test('mb_strrpos', 'abcde', 'cde', 2);
test('mb_strrpos', 'abcde', 'cde', 3);

// Negative offsets, needle longer than search area
test('mb_strrpos', 'abcdefg', 'cdefghi', -2);
test('mb_strrpos', 'abcdefg', 'cdefghij', -2);

// Negative offsets, needle in middle of haystack
test('mb_strrpos', '0123456789a0123456789b0123456789c', '234', 0);
test('mb_strrpos', '0123456789a0123456789b0123456789c', '234', -1);
test('mb_strrpos', '0123456789a0123456789b0123456789c', '234', -2);
test('mb_strrpos', '0123456789a0123456789b0123456789c', '234', -3);
test('mb_strrpos', '0123456789a0123456789b0123456789c', '234', -9);
test('mb_strrpos', '0123456789a0123456789b0123456789c', '234', -10);

// Negative offsets, needle at end of haystack
test('mb_strrpos', '0123456789a0123456789b0123456789c', '789c', 0);
test('mb_strrpos', '0123456789a0123456789b0123456789c', '789c', -1);
test('mb_strrpos', '0123456789a0123456789b0123456789c', '789c', -2);
test('mb_strrpos', '0123456789a0123456789b0123456789c', '789c', -3);
test('mb_strrpos', '0123456789a0123456789b0123456789c', '789c', -4);
test('mb_strrpos', '0123456789a0123456789b0123456789c', '789c', -5);

// Negative offsets, needle partial match at end
test('mb_strrpos', '0123456789a0123456789b0123456789c', '789cd', 0);
test('mb_strrpos', '0123456789a0123456789b0123456789c', '789cd', -1);
test('mb_strrpos', '0123456789a0123456789b0123456789c', '789cd', -2);
test('mb_strrpos', '0123456789a0123456789b0123456789c', '789cd', -3);

// Empty haystack
test('mb_strrpos', '', 'A', 0);

// Case sensitivity
test('mb_strrpos', 'AaBb', 'a');
test('mb_strrpos', 'AaBb', 'A');

// Offset outside haystack
try {
  test('mb_strrpos', '', 'A', 1);
}
catch (Throwable $e){
}
try {
  test('mb_strrpos', 'A', 'A', 2);
}
catch (Throwable $e) {
}
try {
  test('mb_strrpos', 'A', 'A', -2);
}
catch (Throwable $e){
}


// mb_strripos()
// ------------

// Case sensitivity
test('mb_strripos', 'AaBb', 'a');
test('mb_strripos', 'AaBb', 'A');


echo "</tbody>\n";
echo "</table>\n";