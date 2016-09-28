<?php
/**
 * $dbserver = "some host";
 * $dbusername = "some user";
 * $dbpassword = "some password";
 * $database = "some db";
 */
include_once 'db.php';
//其他配置
$title = '数据字典';
$mysqli_conn = @mysqli_connect("$dbserver", "$dbusername", "$dbpassword") or die("Mysql connect is error.");
//mysql_select_db($database, $mysql_conn);
mysqli_select_db($mysqli_conn, $database);
//mysql_query('SET NAMES utf8', $mysql_conn);
mysqli_query($mysqli_conn, 'SET NAMES utf8');
//$table_result = mysql_query('show tables', $mysql_conn);
$table_result = mysqli_query($mysqli_conn, 'show tables');
//取得所有的表名
//while ($row = mysql_fetch_array($table_result)) {
while ($row = mysqli_fetch_array($table_result)) {
    $tables[]['TABLE_NAME'] = $row[0];
}
//循环取得所有表的备注及表中列消息
foreach ($tables AS $k=>$v) {
    $sql = 'SELECT * FROM ';
    $sql .= 'INFORMATION_SCHEMA.TABLES ';
    $sql .= 'WHERE ';
    $sql .= "table_name = '{$v['TABLE_NAME']}' AND table_schema = '{$database}'";
    //$table_result = mysql_query($sql, $mysql_conn);
    $table_result = mysqli_query($mysqli_conn, $sql);
    //while ($t = mysql_fetch_array($table_result) ) {
    while ($t = mysqli_fetch_array($table_result) ) {
        $tables[$k]['TABLE_COMMENT'] = $t['TABLE_COMMENT'];
    }
    $sql = 'SELECT * FROM ';
    $sql .= 'INFORMATION_SCHEMA.COLUMNS ';
    $sql .= 'WHERE ';
    $sql .= "table_name = '{$v['TABLE_NAME']}' AND table_schema = '{$database}'";
    $fields = array();
    //$field_result = mysql_query($sql, $mysql_conn);
    $field_result = mysqli_query($mysqli_conn, $sql);
    //while ($t = mysql_fetch_array($field_result) ) {
    while ($t = mysqli_fetch_array($field_result) ) {
        $fields[] = $t;
    }
    $tables[$k]['COLUMN'] = $fields;
}
//mysql_close($mysql_conn);
mysqli_close($mysqli_conn);
$html = '';
//循环所有表
foreach ($tables AS $k=>$v) {
//$html .= '<p><h2>'. $v['TABLE_COMMENT'] . '&nbsp;</h2>';
    $html .= '<table border="1" cellspacing="0" cellpadding="0" align="center">';
    $html .= '<caption>' . $v['TABLE_NAME'] .' '. $v['TABLE_COMMENT']. '</caption>';
    $html .= '<thead><tr><th>字段名</th><th>数据类型</th><th>默认值</th>
                <th>允许非空</th>
                <th>自动递增</th><th>备注</th></tr></thead><tbody>';
    $html .= '';
    foreach ($v['COLUMN'] AS $f) {
        $html .= '<tr><td class="c1">' . $f['COLUMN_NAME'] . '</td>';
        $html .= '<td class="c2">' . $f['COLUMN_TYPE'] . '</td>';
        $html .= '<td class="c3">&nbsp;' . $f['COLUMN_DEFAULT'] . '</td>';
        $html .= '<td class="c4">&nbsp;' . $f['IS_NULLABLE'] . '</td>';
        $html .= '<td class="c5">' . ($f['EXTRA']=='auto_increment'?'是':'&nbsp;') . '</td>';
        $html .= '<td class="c6">&nbsp;' . $f['COLUMN_COMMENT'] . '</td>';
        $html .= '</tr>';
    }
    $html .= '</tbody></table></p>';
}
//输出
echo '<html>
<head>
<title>'.$title.'</title>
<style>
body{padding:0;margin:0;font:12px "\5FAE\8F6F\96C5\9ED1";color:#444;}
table{width:100%;border:0;text-align:center;border-collapse:collapse;border-spacing:0;}
table caption{text-align:left; background-color:#fff; line-height:2; font-size:16px; font-weight:bolder; }
table thead th{background:#0090D7;font-weight:normal;line-height:2;font-size:16px;color:#FFF;}
table tbody tr{cursor: pointer;}
table tbody tr:nth-child(odd){background:#F4F4F4;}
table tbody td:nth-child(even){color:#E91E63;line-height:2;}
table tbody tr:hover{background:#73B1E0;color:#FFF;}
table tbody td,table th{border:1px solid #EEE;}
.c1{ width: 120px;}
.c2{ width: 120px;}
.c3{ width: 70px;}
.c4{ width: 80px;}
.c5{ width: 80px;}
.c6{ width: 270px;}
</style>
</head>
<body>';
echo '<h1 style="text-align:center;">'.$title.'</h1>';
echo $html;
echo '</body></html>';