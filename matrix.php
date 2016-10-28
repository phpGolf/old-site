<?php

//SIZE OF SQUARE MATRIX (CAN BE ANY DEPENDING UPON solve_matrix($m) FUNCTION)
$size = 3;
if (!isset($_POST['submit']))
{
echo "<form action=\"".$_SERVER['PHP_SELF']."\" method=\"post\"><center><br><br><h2>Calculate ".$size."x".$size." Matrix Inverse</h2><br>";
for ($i=0; $i<$size; $i++)
for ($j=0; $j<$size; $j++)
{
echo "<input type=\"text\" name=\"x_".$i."_".$j."\" style=\"width:50px;text-align:center;\">";
if ($j==$size-1) echo "<br>";
}
echo "<br><input name=\"submit\" type=\"submit\" value=\"Calculate Inverse\"></center></form>";

}

else
{

for ($i=0; $i<$size; $i++)
for ($j=0; $j<$size; $j++)
if (filter_var($_POST['x_'.$i.'_'.$j], FILTER_VALIDATE_INT)===false)
die ('<br><center><h3>Invalid Data Type</h3></center>');

echo "<center>";

$matrix = array();
for ($i=0; $i<$size; $i++)
for ($j=0; $j<$size; $j++)
$matrix[$i][$j] = $_POST['x_'.$i.'_'.$j];
unset($_POST);

display_matrix($matrix, "<b>Matrix</b>");

$I_matrix_I = solve_matrix($matrix);
if ($I_matrix_I==0) die('NULL MATRIX');

$adjoint = array();
$inverse = array();
for ($i=0; $i<$size; $i++)
for ($j=0; $j<$size; $j++)
{
$cofactor = array();
for ($k=0; $k<$size; $k++)
{
if ($i!=$k)
{
$temp = array();
for ($l=0; $l<$size; $l++)
{
if ($j!=$l)
array_push($temp, $matrix[$k][$l]);
}
array_push($cofactor, $temp);
}
}
display_matrix($cofactor, "<b>Cofactor</b> <sub>".($i+1).($j+1)."</sub>");
$adjoint[$j][$i] = solve_matrix($cofactor)*pow((-1),($i+$j)); //INTERCHANGING $i $j RESULTS TRANPOSE
}
display_matrix($adjoint, "<b>Adjoint</b>");

for ($i=0; $i<$size; $i++)
for ($j=0; $j<$size; $j++)
$inverse[$i][$j] = $adjoint[$i][$j]/$I_matrix_I;

display_matrix($inverse, "<b>Inverse</b>");
}

//WORKS ONLY FOR UPTO 3x3
function solve_matrix($m)
{
$_size = count($m);
switch ($_size)
{
case 1:
return $m[0][0];
case 2:
return (($m[0][0] * $m[1][1]) - ($m[0][1] * $m[1][0]));
case 3:
return ($m[0][0]*($m[1][1]*$m[2][2]-$m[1][2]*$m[2][1])) - ($m[0][1]*($m[1][0]*$m[2][2]-$m[2][0]*$m[1][2])) + ($m[0][2]*($m[1][0]*$m[2][1]-$m[2][0]*$m[1][1]));

//CAN BE INCREASED TO ANY NUMBER
//YOU CAN MAKE A BETTER FUNCTION WITH FOR LOOP WHICH COULD GO TO ANY LIMIT
//USE THE ABOVE COFACTOR LOGIC FOR THAT
default:
return 0;
}
}

//DISPLAY A MATRIX IN A TABLE
function display_matrix($m, $name)
{
echo $name."<br><table border=\"1px\" cellpadding=\"2px\" style=\"border-collapse:collapse;\">";
foreach($m as $row)
{
echo "<tr>";
foreach ($row as $value)
{
echo "<td>".$value."</td>";
}
echo "</tr>";
}
echo "</table>
<br>";
}
?>
