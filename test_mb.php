<?php
declare(strict_types=1);

// Program for testing the mbstring function emulations.  Run it in the MRBS directory on a
// system with the 'mbstring' extension enabled.

include 'defaultincludes.inc';

error_reporting(-1);
ini_set('display_errors', '1');
ini_set('max_execution_time', '5');


function test(string $function, string $haystack, string $needle, int $offset=0) : void
{
  echo "<tr>";
  echo "<td>$function</td><td>$haystack</td><td>$needle</td><td>$offset</td>";

  // Using the mbstring versions
  echo "<td>";
  try {
    $mbstring = call_user_func($function, $haystack, $needle, $offset);
  }
  catch (Throwable $t) {
    $mbstring = get_class($t);
  }
  echo var_export($mbstring, true);
  echo "</td>";

  // Using the MRBS emulations
  echo "<td>";
  try {
    $mrbs = call_user_func("mrbs_$function", $haystack, $needle, $offset);
  }
  catch (Throwable $t) {
    $mrbs = get_class($t);
  }
  echo var_export($mrbs, true);
  echo "</td>";

  // Compare the results
  $color = ($mbstring === $mrbs) ? "palegreen" : "pink";
  echo '<td style="background-color: ' . $color . '">';
  echo ($mbstring === $mrbs) ? 'Pass' : 'Fail';
  echo "</td>";

  echo "</tr>\n";
}


$loaded_extensions = get_loaded_extensions();

echo "PHP version: " . PHP_VERSION;
echo "<br>\n";
echo "mbstring enabled: " . var_export(in_array('mbstring', $loaded_extensions), true);
echo "<br>\n";
echo "intl enabled: " . var_export(in_array('intl', $loaded_extensions), true);
echo "<br>\n";
echo "<br>\n";

if (!in_array('mbstring', $loaded_extensions))
{
  die("This test needs the 'mbstring' PHP extension to be loaded.");
}

echo "<table>\n";
echo "<thead>\n";
echo "<tr>";
echo '<th>function</th><th>$haystack</th><th>$needle</th><th>$offset</th><th>result - mbstring</th><th>result - mrbs</th><th>Summary</th>';
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
test('mb_strrpos', '', 'A', 1);
test('mb_strrpos', 'A', 'A', 2);
test('mb_strrpos', 'A', 'A', -2);


// mb_strripos()
// ------------

// Case sensitivity
test('mb_strripos', 'AaBb', 'a');
test('mb_strripos', 'AaBb', 'A');


echo "</tbody>\n";
echo "</table>\n";